<?php
$pageTitle = "Thank You - SwiftCart";
require_once 'includes/session.php';
requireLogin();
include 'includes/header.php';

$orderId = $_GET['order_id'] ?? '';

if (empty($orderId)) {
    header('Location: index.php');
    exit();
}

// Get order details
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, getUserId()]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit();
}

// Get order items
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM order_items oi 
    JOIN products p ON oi.product_id = p.id 
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll();
?>

<main>
    <div class="thank-you-container">
        <div class="thank-you-icon">✅</div>
        <h1 class="thank-you-title">Thank You for Your Order!</h1>
        <p class="thank-you-message">
            Your order has been successfully placed and is being processed. 
            We'll send you updates about your order status via email.
        </p>
        
        <div class="order-summary">
            <h3>Order Details</h3>
            <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
            <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
            <p><strong>Total Amount:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
            <p><strong>Payment Method:</strong> <?php echo ($order['payment_method'] === 'bkash') ? 'bkash' : ucwords(str_replace('_', ' ', $order['payment_method'])); ?></p>
        </div>
        
        <div class="tracking-info">
            <h4>Tracking Information</h4>
            <p>Your tracking number is: <span class="tracking-number"><?php echo $order['tracking_number']; ?></span></p>
            <p>You can track your order using this number on our <a href="track-order.php">order tracking page</a>.</p>
        </div>
        
        <div style="margin-top: 30px;">
            <h4>Items Ordered:</h4>
            <?php foreach ($orderItems as $item): ?>
            <div style="display: flex; justify-content: space-between; margin: 10px 0; padding: 10px; background: #f8f9fa; border-radius: 5px;">
                <span><?php echo htmlspecialchars($item['name']); ?> (x<?php echo $item['quantity']; ?>)</span>
                <span>৳<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div style="margin-top: 40px;">
            <a href="index.php" class="cta-button">Continue Shopping</a>
            <a href="profile.php" class="btn btn-secondary" style="margin-left: 15px; text-decoration: none; display: inline-block;">View Orders</a>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>