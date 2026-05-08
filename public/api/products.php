<?php

require_once __DIR__ . '/../../app/Models/Database.php';

header('Content-Type: application/json; charset=utf-8');

$pdo = Database::connect();

$stmt = $pdo->query("
    SELECT p.*, 
           (SELECT file_path 
            FROM product_media 
            WHERE product_id = p.id 
            LIMIT 1) AS file_path
    FROM products p
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($products, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
