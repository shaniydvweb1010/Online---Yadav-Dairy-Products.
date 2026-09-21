<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['phone']) || empty($data['address'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT);
$phone = $data['phone'];
$address = $data['address'];

// Check if email already exists
$stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    http_response_code(409);
    echo json_encode(['status' => 'error', 'message' => 'Email already registered.']);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
if ($stmt->execute([$name, $email, $password, $phone, $address])) {
    echo json_encode(['status' => 'success', 'message' => 'Registration successful. Please login.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Registration failed.']);
}
?>