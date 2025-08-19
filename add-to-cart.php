<?php
require_once 'config/database.php';
require_once 'includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$productId = (int)$_POST['product_id'];
$userId = getUserId();

try {
    // Check if product exists
    $stmt = $pdo->prepare("SELECT id, stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    
    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Product not found']);
        exit();
    }
    
    // Check if item already in cart
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $cartItem = $stmt->fetch();
    
    if ($cartItem) {
        // Update quantity
        $newQuantity = $cartItem['quantity'] + 1;
        if ($newQuantity > $product['stock_quantity']) {
            echo json_encode(['success' => false, 'message' => 'Not enough stock available']);
            exit();
        }
        
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $stmt->execute([$newQuantity, $cartItem['id']]);
    } else {
        // Add new item
        if ($product['stock_quantity'] < 1) {
            echo json_encode(['success' => false, 'message' => 'Product out of stock']);
            exit();
        }
        
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $stmt->execute([$userId, $productId]);
    }
    
    // Get updated cart count
    $stmt = $pdo->prepare("SELECT SUM(quantity) as total FROM cart WHERE user_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetch();
    $cartCount = $result['total'] ?? 0;
    
    echo json_encode(['success' => true, 'cartCount' => $cartCount]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error adding to cart']);
}
?>