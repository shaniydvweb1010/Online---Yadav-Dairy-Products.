<?php
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to view your orders.']);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // 1. Get all orders for the user, newest first
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$user_id]);
    $orders = $stmt->fetchAll();

    // 2. For each order, get its associated items
    $orders_with_items = [];
    foreach ($orders as $order) {
        $stmt_items = $pdo->prepare("
            SELECT oi.quantity, oi.price, p.name 
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = ?
        ");
        $stmt_items->execute([$order['id']]);
        $items = $stmt_items->fetchAll();
        $order['items'] = $items;
        $orders_with_items[] = $order;
    }

    echo json_encode(['status' => 'success', 'orders' => $orders_with_items]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
}
?>