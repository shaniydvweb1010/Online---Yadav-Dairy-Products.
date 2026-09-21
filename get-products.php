<?php
require 'db.php';
$stmt = $pdo->query("SELECT * FROM products ORDER BY id ASC");
$products = $stmt->fetchAll();
echo json_encode($products);
?>