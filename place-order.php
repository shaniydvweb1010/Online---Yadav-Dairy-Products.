<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to place an order.']);
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

// Basic validation
if (empty($data['deliveryInfo']) || empty($data['paymentMethod'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Delivery and payment information are required.']);
    exit;
}

$delivery_info = $data['deliveryInfo'];
$payment_method = $data['paymentMethod'];

// Start transaction
$pdo->beginTransaction();

try {
    // 1. Get cart items from DB
    $stmt = $pdo->prepare("
        SELECT c.product_id, c.quantity, p.price 
        FROM cart c
        JOIN products p ON c.product_id = p.id
        WHERE c.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $cart_items = $stmt->fetchAll();

    if (count($cart_items) === 0) {
        throw new Exception("Your cart is empty.");
    }

    // 2. Calculate total amount on server-side
    $total_amount = 0;
    foreach ($cart_items as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    // 3. Create a unique order ID
    $order_uid = 'YDF-' . uniqid();

    // 4. Insert into 'orders' table
    $stmt = $pdo->prepare("
        INSERT INTO orders (user_id, order_uid, total_amount, delivery_name, delivery_phone, delivery_address, delivery_instructions, payment_method) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([
        $user_id,
        $order_uid,
        $total_amount,
        $delivery_info['name'],
        $delivery_info['phone'],
        $delivery_info['address'],
        $delivery_info['instructions'],
        $payment_method
    ]);
    $order_id = $pdo->lastInsertId();

    // 5. Insert into 'order_items' table
    $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach ($cart_items as $item) {
        $stmt->execute([$order_id, $item['product_id'], $item['quantity'], $item['price']]);
    }

    // 6. Clear the user's cart
    $stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->execute([$user_id]);

    // 7. Commit the transaction
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => 'Order placed successfully!', 'orderId' => $order_uid]);

} catch (Exception $e) {
    // If anything fails, roll back
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to place order: ' . $e->getMessage()]);
}
?>