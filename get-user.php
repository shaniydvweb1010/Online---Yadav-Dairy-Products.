<?php
require 'db.php';

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT id, name, email, phone, address FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    echo json_encode(['status' => 'success', 'loggedIn' => true, 'user' => $user]);
} else {
    echo json_encode(['status' => 'success', 'loggedIn' => false]);
}
?>