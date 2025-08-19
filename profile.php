<?php
$pageTitle = "My Profile - SwiftCart";
require_once 'includes/session.php';
requireLogin();
include 'includes/header.php';

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([getUserId()]);
$user = $stmt->fetch();

// Get user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([getUserId()]);
$orders = $stmt->fetchAll();

$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    if (empty($full_name)) {
        $error = 'Full name is required';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
            $stmt->execute([$full_name, $phone, $address, getUserId()]);
            $success = 'Profile updated successfully!';
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([getUserId()]);
            $user = $stmt->fetch();
            
        } catch (Exception $e) {
            $error = 'Error updating profile. Please try again.';
        }
    }
}
?>

<main>
    <div class="profile-container">
        <div class="profile-header">
            <div class="profile-avatar">👤</div>
            <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
            <p><?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <div class="profile-content">
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="profile-section">
                <h3>Personal Information</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="full_name">Full Name</label>
                        <input type="text" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background: #f8f9fa;">
                    </div>
                    
                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea id="address" name="address" rows="3"><?php echo htmlspecialchars($user['address'] ?? ''); ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn">Update Profile</button>
                </form>
            </div>
            
            <div class="profile-section">
                <h3>Order History</h3>
                <?php if (empty($orders)): ?>
                    <p>No orders found. <a href="index.php">Start shopping!</a></p>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                    <div class="order-item">
                        <div class="order-header">
                            <span class="order-id">Order #<?php echo $order['id']; ?></span>
                            <span class="order-status status-<?php echo $order['status']; ?>">
                                <?php echo ucfirst($order['status']); ?>
                            </span>
                        </div>
                        <p><strong>Date:</strong> <?php echo date('F j, Y', strtotime($order['created_at'])); ?></p>
                        <p><strong>Total:</strong> ৳<?php echo number_format($order['total_amount'], 2); ?></p>
                        <p><strong>Tracking:</strong> <?php echo $order['tracking_number']; ?></p>
                        <?php if ($order['status'] !== 'delivered'): ?>
                            <a href="track-order.php?tracking=<?php echo $order['tracking_number']; ?>" style="color: #667eea; text-decoration: none;">Track Order →</a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include 'includes/footer.php'; ?>