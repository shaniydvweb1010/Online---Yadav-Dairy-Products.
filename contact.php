<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Name, email, and message are required.']);
    exit;
}

$name = $data['name'];
$email = $data['email'];
$phone = $data['phone'] ?? null;
$message = $data['message'];

$stmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, message) VALUES (?, ?, ?, ?)");
if ($stmt->execute([$name, $email, $phone, $message])) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you for your message. We will get back to you soon!']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
}
?>