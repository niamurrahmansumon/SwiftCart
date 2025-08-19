<?php
$pageTitle = "Track Order - SwiftCart";
include 'includes/header.php';

$trackingNumber = $_GET['tracking'] ?? '';
$order = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $trackingNumber = trim($_POST['tracking_number']);
}

if (!empty($trackingNumber)) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE tracking_number = ?");
    $stmt->execute([$trackingNumber]);
    $order = $stmt->fetch();
    
    if (!$order) {
        $error = 'Order not found. Please check your tracking number.';
    }
}
?>

<main>
    <div class="container" style="padding: 40px 20px;">
        <h1 class="section-title">Track Your Order</h1>
        
        <div style="max-width: 600px; margin: 0 auto;">
            <div style="background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                <form method="POST">
                    <div class="form-group">
                        <label for="tracking_number">Enter Tracking Number</label>
                        <input type="text" id="tracking_number" name="tracking_number" value="<?php echo htmlspecialchars($trackingNumber); ?>" placeholder="e.g., SC1234567890" required>
                    </div>
                    <button type="submit" class="btn">Track Order</button>
                </form>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($order): ?>
                <div style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1);">
                    <h3>Order Details</h3>
                    <div style="margin: 20px 0;">
                        <p><strong>Order ID:</strong> #<?php echo $order['id']; ?></p>
                        <p><strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?></p>
                        <p><strong>Total Amount:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p><strong>Status:</strong> 
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </p>
                    </div>
                    
                    <div style="margin-top: 30px;">
                        <h4>Order Progress</h4>
                        <div style="margin: 20px 0;">
                            <?php
                            $statuses = ['pending', 'processing', 'shipped', 'delivered'];
                            $currentStatusIndex = array_search($order['status'], $statuses);
                            
                            foreach ($statuses as $index => $status) {
                                $isActive = $index <= $currentStatusIndex;
                                $color = $isActive ? '#667eea' : '#ddd';
                                echo "<div style='display: flex; align-items: center; margin: 10px 0;'>";
                                echo "<div style='width: 20px; height: 20px; border-radius: 50%; background: $color; margin-right: 15px;'></div>";
                                echo "<span style='color: " . ($isActive ? '#333' : '#999') . "; font-weight: " . ($isActive ? 'bold' : 'normal') . ";'>" . ucfirst($status) . "</span>";
                                echo "</div>";
                            }
                            ?>
                        </div>
                    </div>
                    
                    <?php if ($order['status'] === 'delivered'): ?>
                        <div class="alert alert-success">
                            🎉 Your order has been delivered! Thank you for shopping with SwiftCart.
                        </div>
                    <?php elseif ($order['status'] === 'shipped'): ?>
                        <div class="alert alert-info">
                            📦 Your order is on its way! Expected delivery in 2-3 business days.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>
