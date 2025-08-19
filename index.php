<?php
$pageTitle = "SwiftCart - Your Ultimate Shopping Destination";
include 'includes/header.php';

// Get featured products
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.featured = 1 
    ORDER BY p.created_at DESC 
    LIMIT 8
");
$stmt->execute();
$featuredProducts = $stmt->fetchAll();

// Get all products for general display
$stmt = $pdo->prepare("
    SELECT p.*, c.name as category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.created_at DESC 
    LIMIT 12
");
$stmt->execute();
$allProducts = $stmt->fetchAll();
?>

<main>
    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Welcome to SwiftCart</h1>
            <p>Discover the latest in mobile technology, laptops, smartwatches, and innovative gadgets</p>
            <a href="#products" class="cta-button">Shop Now</a>
        </div>
    </section>
    
    <!-- Featured Products -->
    <?php if (!empty($featuredProducts)): ?>
    <section class="products-section" id="featured">
        <div class="container">
            <h2 class="section-title">Featured Products</h2>
            <div class="products-grid">
                <?php foreach ($featuredProducts as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
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
        </div>
    </section>
    <?php endif; ?>
    
    <!-- All Products -->
    <section class="products-section" id="products">
        <div class="container">
            <h2 class="section-title">All Products</h2>
            <div class="products-grid">
                <?php foreach ($allProducts as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
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
        </div>
    </section>
</main>

<script>
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
            // Update cart count
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
</script>

<?php include 'includes/footer.php'; ?>
