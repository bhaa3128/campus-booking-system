<?php
session_start();
require_once __DIR__ . '/../../app/Models/Database.php';

$pdo = Database::connect();

$data = json_decode(file_get_contents("php://input"), true);

$productId = $data['product_id'];
$userId = $_SESSION['user_id'];

// 1. نتأكد في stock
$stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

if ($product['stock'] <= 0) {
    echo json_encode([
        "success" => false,
        "message" => "Out of stock "
    ]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
$stmt->execute([$userId, $productId]);

$stmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
$stmt->execute([$productId]);

echo json_encode([
    "success" => true
]);