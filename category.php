<?php
$pageTitle = "Category - SwiftCart";
include 'includes/header.php';

$categorySlug = $_GET['slug'] ?? '';

if (empty($categorySlug)) {
    header('Location: index.php');
    exit();
}

// Get category info
$stmt = $pdo->prepare("SELECT * FROM categories WHERE slug = ?");
$stmt->execute([$categorySlug]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: index.php');
    exit();
}

$pageTitle = $category['name'] . " - SwiftCart";

// Get products in this category
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE c.slug = ? 
    ORDER BY p.created_at DESC
");
$stmt->execute([$categorySlug]);
$products = $stmt->fetchAll();
?>

<main>
    <section class="products-section">
        <div class="container">
            <h1 class="section-title"><?php echo htmlspecialchars($category['name']); ?> Products</h1>
            
            <?php if (empty($products)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <h3>No products found in this category</h3>
                    <p>Check back later for new arrivals!</p>
                    <a href="index.php" class="cta-button">Browse All Products</a>
                </div>
            <?php else: ?>
                <p style="text-align: center; margin-bottom: 20px; color: #666;">
                    Found <?php echo count($products); ?> product(s)
                </p>
                
                <!-- Price Filter Controls -->
                <div style="display: flex; justify-content: center; align-items: center; gap: 20px; margin-bottom: 40px; flex-wrap: wrap;">
                    <label style="font-weight: 500; color: #333;">Sort by Price:</label>
                    <select id="priceFilter" style="padding: 8px 15px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 14px; background: white; cursor: pointer;">
                        <option value="">Default</option>
                        <option value="low-to-high">Price: Low to High</option>
                        <option value="high-to-low">Price: High to Low</option>
                    </select>
                    
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <label style="font-weight: 500; color: #333;">Price Range:</label>
                        <input type="number" id="minPrice" placeholder="Min" style="width: 80px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 8px;">
                        <span>-</span>
                        <input type="number" id="maxPrice" placeholder="Max" style="width: 80px; padding: 8px; border: 2px solid #e1e5e9; border-radius: 8px;">
                        <button onclick="applyPriceRange()" style="padding: 8px 15px; background: #667eea; color: white; border: none; border-radius: 8px; cursor: pointer;">Apply</button>
                    </div>
                    
                    <button onclick="clearFilters()" style="padding: 8px 15px; background: #6c757d; color: white; border: none; border-radius: 8px; cursor: pointer;">Clear Filters</button>
                </div>
                
                <div class="products-grid" id="productsGrid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>" data-price="<?php echo $product['price']; ?>">
                        <div class="product-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-price">
                                <span class="current-price">৳<?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                    <span class="original-price">৳<?php echo number_format($product['original_price'], 2); ?></span>
                                    <span class="discount">
                                        <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <button class="add-to-cart" onclick="addToCartPHP(<?php echo $product['id']; ?>)">Add to Cart</button>
                            <?php else: ?>
                                <a href="login.php" class="login-to-buy">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div id="noResultsMessage" style="display: none; text-align: center; padding: 60px 20px;">
                    <h3>No products found in this price range</h3>
                    <p>Try adjusting your price filters</p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<script>
let originalProducts = [];

// Store original product data when page loads
document.addEventListener('DOMContentLoaded', function() {
    const productCards = document.querySelectorAll('.product-card');
    originalProducts = Array.from(productCards);
});

// Price filter functionality
document.getElementById('priceFilter')?.addEventListener('change', function() {
    const filterValue = this.value;
    const productsGrid = document.getElementById('productsGrid');
    const productCards = Array.from(productsGrid.querySelectorAll('.product-card'));
    
    if (filterValue === '') {
        restoreOriginalOrder();
        return;
    }
    
    productCards.sort((a, b) => {
        const priceA = parseFloat(a.dataset.price);
        const priceB = parseFloat(b.dataset.price);
        
        if (filterValue === 'low-to-high') {
            return priceA - priceB;
        } else if (filterValue === 'high-to-low') {
            return priceB - priceA;
        }
        return 0;
    });
    
    productsGrid.innerHTML = '';
    productCards.forEach(card => {
        productsGrid.appendChild(card);
    });
    
    productCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.3s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 50);
    });
});

function applyPriceRange() {
    const minPrice = parseFloat(document.getElementById('minPrice').value) || 0;
    const maxPrice = parseFloat(document.getElementById('maxPrice').value) || Infinity;
    const productsGrid = document.getElementById('productsGrid');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const productCards = productsGrid.querySelectorAll('.product-card');
    
    let visibleCount = 0;
    
    productCards.forEach(card => {
        const price = parseFloat(card.dataset.price);
        
        if (price >= minPrice && price <= maxPrice) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    if (visibleCount === 0) {
        noResultsMessage.style.display = 'block';
        productsGrid.style.display = 'none';
    } else {
        noResultsMessage.style.display = 'none';
        productsGrid.style.display = 'grid';
    }
    
    updateResultsCount(visibleCount);
}

function clearFilters() {
    document.getElementById('priceFilter').value = '';
    document.getElementById('minPrice').value = '';
    document.getElementById('maxPrice').value = '';
    
    const productsGrid = document.getElementById('productsGrid');
    const noResultsMessage = document.getElementById('noResultsMessage');
    const productCards = productsGrid.querySelectorAll('.product-card');
    
    productCards.forEach(card => {
        card.style.display = 'block';
    });
    
    noResultsMessage.style.display = 'none';
    productsGrid.style.display = 'grid';
    
    restoreOriginalOrder();
    updateResultsCount(productCards.length);
}

function restoreOriginalOrder() {
    const productsGrid = document.getElementById('productsGrid');
    productsGrid.innerHTML = '';
    
    originalProducts.forEach(card => {
        productsGrid.appendChild(card.cloneNode(true));
    });
    
    attachEventListeners();
}

function updateResultsCount(count) {
    const countElement = document.querySelector('p[style*="text-align: center"]');
    if (countElement && countElement.textContent.includes('Found')) {
        countElement.textContent = `Found ${count} product(s)`;
    }
}

function attachEventListeners() {
    const addToCartButtons = document.querySelectorAll('.add-to-cart[onclick]');
    addToCartButtons.forEach(button => {
        const productId = button.getAttribute('onclick').match(/\d+/)[0];
        button.onclick = () => addToCartPHP(productId);
    });
}

function addToCartPHP(productId) {
    fetch('add-to-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Product added to cart!', 'success');
            const cartCount = document.querySelector('.cart-count');
            if (cartCount) {
                cartCount.textContent = data.cartCount;
                cartCount.style.display = data.cartCount > 0 ? 'inline' : 'none';
            }
        } else {
            showAlert(data.message || 'Error adding product to cart', 'error');
        }
    })
    .catch(error => {
        showAlert('Error adding product to cart', 'error');
    });
}

document.getElementById('minPrice')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyPriceRange();
    }
});

document.getElementById('maxPrice')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        applyPriceRange();
    }
});
</script>

<?php include 'includes/footer.php'; ?>
