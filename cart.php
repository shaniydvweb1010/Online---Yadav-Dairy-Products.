<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to manage your cart.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Get cart items
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, p.name, p.price, p.image 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart = $stmt->fetchAll();
    echo json_encode(['status' => 'success', 'cart' => $cart]);

} elseif ($method === 'POST') {
    // Add item to cart
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $quantity = isset($data['quantity']) ? $data['quantity'] : 1;

    $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    $existing_item = $stmt->fetch();

    if ($existing_item) {
        $new_quantity = $existing_item['quantity'] + $quantity;
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$new_quantity, $user_id, $product_id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $product_id, $quantity]);
    }
    echo json_encode(['status' => 'success', 'message' => 'Item added to cart.']);

} elseif ($method === 'PUT') {
    // Update item quantity
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];
    $quantity = $data['quantity'];

    if ($quantity > 0) {
        $stmt = $pdo->prepare("UPDATE cart SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$quantity, $user_id, $product_id]);
        echo json_encode(['status' => 'success', 'message' => 'Cart updated.']);
    } else {
        // If quantity is 0 or less, remove the item
        $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        echo json_encode(['status' => 'success', 'message' => 'Item removed from cart.']);
    }

} elseif ($method === 'DELETE') {
    // Remove item from cart
    $data = json_decode(file_get_contents('php://input'), true);
    $product_id = $data['product_id'];

    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->execute([$user_id, $product_id]);
    echo json_encode(['status' => 'success', 'message' => 'Item removed from cart.']);
}
?>