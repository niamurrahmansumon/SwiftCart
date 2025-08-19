<?php
$pageTitle = "Shopping Cart - SwiftCart";
require_once 'includes/session.php';
requireLogin();
include 'includes/header.php';

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price, p.image 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$stmt->execute([getUserId()]);
$cartItems = $stmt->fetchAll();

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}
?>

<main>
    <div class="container" style="padding: 40px 20px;">
        <h1 class="section-title">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div style="text-align: center; padding: 60px 20px;">
                <h3>Your cart is empty</h3>
                <p>Add some products to get started!</p>
                <a href="index.php" class="cta-button">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="cart-items">
                <?php foreach ($cartItems as $item): ?>
                <div class="cart-item" data-product-id="<?php echo $item['product_id']; ?>">
                    <div class="cart-item-image">
                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                    </div>
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?php echo htmlspecialchars($item['name']); ?></div>
                        <div class="cart-item-price">৳<?php echo number_format($item['price'], 2); ?></div>
                    </div>
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="updateCartQuantity(<?php echo $item['product_id']; ?>, 'decrease')">-</button>
                        <span class="quantity"><?php echo $item['quantity']; ?></span>
                        <button class="quantity-btn" onclick="updateCartQuantity(<?php echo $item['product_id']; ?>, 'increase')">+</button>
                    </div>
                    <button class="remove-item" onclick="removeFromCartPHP(<?php echo $item['product_id']; ?>)">Remove</button>
                </div>
                <?php endforeach; ?>
            </div>
            
            <div style="background: white; padding: 30px; border-radius: 15px; margin-top: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3>Total: <span class="cart-total">৳<?php echo number_format($total, 2); ?></span></h3>
                </div>
                <a href="checkout.php" class="btn" style="text-decoration: none; display: block; text-align: center;">Proceed to Checkout</a>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
function updateCartQuantity(productId, action) {
    fetch('update-cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `product_id=${productId}&action=${action}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            showAlert(data.message || 'Error updating cart', 'error');
        }
    })
    .catch(error => {
        showAlert('Error updating cart', 'error');
    });
}

function removeFromCartPHP(productId) {
    if (confirm('Are you sure you want to remove this item?')) {
        fetch('remove-from-cart.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `product_id=${productId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showAlert(data.message || 'Error removing item', 'error');
            }
        })
        .catch(error => {
            showAlert('Error removing item', 'error');
        });
    }
}
</script>

<?php include 'includes/footer.php'; ?>