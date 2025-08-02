<?php
$pageTitle = "Search Results - SwiftCart";
include 'includes/header.php';

$query = $_GET['q'] ?? '';
$products = [];

if (!empty($query)) {
    $searchTerm = "%$query%";
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
        ORDER BY p.created_at DESC
    ");
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $products = $stmt->fetchAll();
}
?>

<main>
    <section style="padding: 40px 0;">
        <div class="container">
            <h1 class="section-title">
                <?php if (!empty($query)): ?>
                    Search Results for "<?php echo htmlspecialchars($query); ?>"
                <?php else: ?>
                    Search Products
                <?php endif; ?>
            </h1>
            
            <?php if (!empty($query)): ?>
                <p style="text-align: center; margin-bottom: 40px; color: #666;">
                    Found <?php echo count($products); ?> product(s)
                </p>
            <?php endif; ?>
            
            <?php if (empty($query)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <h3>Enter a search term to find products</h3>
                    <p>Try searching for "iPhone", "laptop", or "smartwatch"</p>
                </div>
            <?php elseif (empty($products)): ?>
                <div style="text-align: center; padding: 60px 20px;">
                    <h3>No products found</h3>
                    <p>Try different keywords or browse our categories</p>
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
                            $categorySlug = strtolower(str_replace(' ', '', $product['category_name']));
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
