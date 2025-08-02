<?php
require_once 'config/database.php';
require_once 'includes/session.php';

// Get cart count
$cartCount = 0;
if (isLoggedIn()) {
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([getUserId()]);
    $result = $stmt->fetch();
    $cartCount = $result['total'] ?? 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'SwiftCart - Your Ultimate Shopping Destination'; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>🛒</text></svg>">
</head>
<body>
    <header>
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">SwiftCart</a>
                
                <div class="search-bar">
                    <input type="text" placeholder="Search for products..." id="searchInput">
                    <button class="search-btn" type="button">🔍</button>
                </div>
                
                <div class="header-actions">
                    <?php if (isLoggedIn()): ?>
                        <a href="profile.php">👤 <?php echo getUserName(); ?></a>
                        <a href="cart.php">🛒 Cart <span class="cart-count"><?php echo $cartCount; ?></span></a>
                        <a href="logout.php">Logout</a>
                    <?php else: ?>
                        <a href="login.php">Login</a>
                        <a href="signup.php">Sign Up</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <nav>
        <div class="container">
            <div class="nav-content">
                <div class="categories">
                    <a href="index.php">Home</a>
                    <a href="category.php?slug=mobile">📱 Mobile</a>
                    <a href="category.php?slug=laptop">💻 Laptop</a>
                    <a href="category.php?slug=smartwatch">⌚ Smartwatch</a>
                    <a href="category.php?slug=gadgets">🔌 Gadgets</a>
                    <a href="about.php">About</a>
                    <a href="contact.php">Contact</a>
                </div>
            </div>
        </div>
    </nav>
