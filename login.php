<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Email and password are required.']);
    exit;
}

$email = $data['email'];
$password = $data['password'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    
    // Unset password before sending user data
    unset($user['password']);

    echo json_encode(['status' => 'success', 'message' => 'Login successful.', 'user' => $user]);
} else {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email or password.']);
}
?>