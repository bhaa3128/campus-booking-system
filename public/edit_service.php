<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: services.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$id = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, price = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $id]);

    header("Location: admin.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Service</title>
</head>
<body>

<h2>Service bearbeiten</h2>

<form method="POST">
    <input type="text" name="title" value="<?= htmlspecialchars($service['title']) ?>"><br>
    <input type="text" name="description" value="<?= htmlspecialchars($service['description']) ?>"><br>
    <input type="number" name="price" value="<?= htmlspecialchars($service['price']) ?>"><br>

    <button type="submit">Speichern</button>
</form>

</body>
</html>