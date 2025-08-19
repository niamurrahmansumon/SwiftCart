<?php
$pageTitle = "Checkout - SwiftCart";
require_once 'includes/session.php';
requireLogin();
include 'includes/header.php';

// Get cart items
$stmt = $pdo->prepare("
    SELECT c.*, p.name, p.price 
    FROM cart c 
    JOIN products p ON c.product_id = p.id 
    WHERE c.user_id = ?
");
$stmt->execute([getUserId()]);
$cartItems = $stmt->fetchAll();

if (empty($cartItems)) {
    header('Location: cart.php');
    exit();
}

$total = 0;
foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $shipping_address = trim($_POST['shipping_address']);
    $payment_method = $_POST['payment_method'];
    
    if (empty($shipping_address) || empty($payment_method)) {
        $error = 'Please fill in all required fields';
    } else {
        try {
            $pdo->beginTransaction();
            
            // Create order
            $tracking_number = 'SC' . time() . rand(1000, 9999);
            $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, tracking_number) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([getUserId(), $total, $shipping_address, $payment_method, $tracking_number]);
            $orderId = $pdo->lastInsertId();
            
            // Add order items
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product_id'], $item['quantity'], $item['price']]);
            }
            
            // Clear cart
            $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $stmt->execute([getUserId()]);
            
            $pdo->commit();
            
            // Redirect to thank you page
            header("Location: thank-you.php?order_id=$orderId");
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Error processing order. Please try again.';
        }
    }
}
?>

<main>
    <div class="container" style="padding: 40px 20px;">
        <h1 class="section-title">Checkout</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div style="display: grid; grid-template-columns: 1fr 400px; gap: 40px; max-width: 1000px; margin: 0 auto;">
            <div>
                <form method="POST">
                    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <h3 style="margin-bottom: 20px;">Shipping Information</h3>
                        
                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="shipping_address">Shipping Address *</label>
                            <textarea id="shipping_address" name="shipping_address" rows="4" required placeholder="Enter your complete shipping address"><?php echo htmlspecialchars($_POST['shipping_address'] ?? $user['address'] ?? ''); ?></textarea>
                        </div>
                    </div>
                    
                    <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 20px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                        <h3 style="margin-bottom: 20px;">Payment Method</h3>
                        
                        <div class="form-group">
                            <label>
                                <input type="radio" name="payment_method" value="credit_card" required> Credit Card
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="radio" name="payment_method" value="bkash" required> bkash
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label>
                                <input type="radio" name="payment_method" value="cash_on_delivery" required> Cash on Delivery
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Place Order</button>
                </form>
            </div>
            
            <div>
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h3 style="margin-bottom: 20px;">Order Summary</h3>
                    
                    <?php foreach ($cartItems as $item): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #eee;">
                        <div>
                            <div style="font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></div>
                            <div style="color: #666; font-size: 0.9rem;">Qty: <?php echo $item['quantity']; ?></div>
                        </div>
                        <div style="font-weight: bold;">৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></div>
                    </div>
                    <?php endforeach; ?>
                    
                    <div style="display: flex; justify-content: space-between; font-size: 1.2rem; font-weight: bold; margin-top: 20px; padding-top: 20px; border-top: 2px solid #667eea;">
                        <span>Total:</span>
                        <span>৳<?php echo number_format($total, 2); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
