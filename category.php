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
                <div class="products-grid">
                    <?php foreach ($products as $product): ?>
                    <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                        <div class="product-image">
                            <?php
                            $icons = [
                                'mobile' => '📱',
                                'laptop' => '💻', 
                                'smartwatch' => '⌚',
                                'gadgets' => '🔌'
                            ];
                            echo $icons[$categorySlug] ?? '📦';
                            ?>
                        </div>
                        <div class="product-info">
                            <h3 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="product-price">
                                <span class="current-price">$<?php echo number_format($product['price'], 2); ?></span>
                                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                                    <span class="original-price">$<?php echo number_format($product['original_price'], 2); ?></span>
                                    <span class="discount">
                                        <?php echo round((($product['original_price'] - $product['price']) / $product['original_price']) * 100); ?>% OFF
                                    </span>
                                <?php endif; ?>
                            </div>
                            <?php if (isLoggedIn()): ?>
                                <button class="add-to-cart" onclick="addToCartPHP(<?php echo $product['id']; ?>)">Add to Cart</button>
                            <?php else: ?>
                                <a href="login.php" class="add-to-cart" style="text-align: center; text-decoration: none; display: block;">Login to Buy</a>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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
