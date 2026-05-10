<?php

session_start();

require_once __DIR__ . '/../../app/Models/Database.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bitte zuerst einloggen.'
    ]);
    exit;
}

$pdo = Database::connect();

$data = json_decode(file_get_contents('php://input'), true);
$productId = $data['product_id'] ?? null;

if (!$productId) {
    echo json_encode([
        'success' => false,
        'message' => 'Produkt nicht gefunden.'
    ]);
    exit;
}

$userId = $_SESSION['user_id'];

$checkStmt = $pdo->prepare("
    SELECT id, quantity
    FROM cart_items
    WHERE user_id = ?
    AND product_id = ?
");

$checkStmt->execute([$userId, $productId]);
$item = $checkStmt->fetch(PDO::FETCH_ASSOC);

if ($item) {
    $updateStmt = $pdo->prepare("
        UPDATE cart_items
        SET quantity = quantity + 1
        WHERE id = ?
    ");
    $updateStmt->execute([$item['id']]);
} else {
    $insertStmt = $pdo->prepare("
        INSERT INTO cart_items (user_id, product_id, quantity)
        VALUES (?, ?, 1)
    ");
    $insertStmt->execute([$userId, $productId]);
}

echo json_encode([
    'success' => true,
    'message' => 'Produkt wurde zum Warenkorb hinzugefügt.'
]);