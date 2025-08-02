<?php
require_once 'config/database.php';
require_once 'includes/session.php';

header('Content-Type: application/json');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Please login first']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['product_id']) || !isset($_POST['action'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

$productId = (int)$_POST['product_id'];
$action = $_POST['action'];
$userId = getUserId();

try {
    // Get current cart item
    $stmt = $pdo->prepare("SELECT id, quantity FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$userId, $productId]);
    $cartItem = $stmt->fetch();
    
    if (!$cartItem) {
        echo json_encode(['success' => false, 'message' => 'Item not found in cart']);
        exit();
    }
    
    $newQuantity = $cartItem['quantity'];
    
    if ($action === 'increase') {
        $newQuantity++;
    } elseif ($action === 'decrease' && $newQuantity > 1) {
        $newQuantity--;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
    
    // Update quantity
    $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
    $stmt->execute([$newQuantity, $cartItem['id']]);
    
    echo json_encode(['success' => true]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error updating cart']);
}
?>
