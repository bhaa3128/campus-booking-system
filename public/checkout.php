<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        cart_items.id AS cart_id,
        cart_items.quantity,
        products.id AS product_id,
        products.title,
        products.price,
        products.stock
    FROM cart_items
    JOIN products ON cart_items.product_id = products.id
    WHERE cart_items.user_id = ?
");

$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cartItems)) {
    die("Warenkorb ist leer");
}

$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $fullName = $_POST['full_name'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $postalCode = $_POST['postal_code'];
    $paymentMethod = $_POST['payment_method'];

    $orderStmt = $pdo->prepare("
        INSERT INTO orders
        (
            user_id,
            full_name,
            street,
            city,
            postal_code,
            payment_method,
            total_price
        )
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    $orderStmt->execute([
        $userId,
        $fullName,
        $street,
        $city,
        $postalCode,
        $paymentMethod,
        $total
    ]);

    $orderId = $pdo->lastInsertId();

    foreach ($cartItems as $item) {

        $itemStmt = $pdo->prepare("
            INSERT INTO order_items
            (
                order_id,
                product_id,
                quantity,
                price
            )
            VALUES (?, ?, ?, ?)
        ");

        $itemStmt->execute([
            $orderId,
            $item['product_id'],
            $item['quantity'],
            $item['price']
        ]);

        $stockStmt = $pdo->prepare("
            UPDATE products
            SET stock = stock - ?
            WHERE id = ?
        ");

        $stockStmt->execute([
            $item['quantity'],
            $item['product_id']
        ]);
    }

    $clearStmt = $pdo->prepare("
        DELETE FROM cart_items
        WHERE user_id = ?
    ");

    $clearStmt->execute([$userId]);

    header("Location: order_success.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2>Checkout</h2>

    <div class="card" style="max-width:800px; margin:0 auto 30px;">
        <h3>Bestellübersicht</h3>

        <?php foreach ($cartItems as $item): ?>
            <p>
                <?= htmlspecialchars($item['title']) ?>
                ×
                <?= htmlspecialchars($item['quantity']) ?>
                =
                <?= number_format($item['price'] * $item['quantity'], 2) ?> €
            </p>
        <?php endforeach; ?>

        <h3>
            Gesamt:
            <?= number_format($total, 2) ?> €
        </h3>
    </div>

    <form method="POST" class="auth-form">

        <input type="text" name="full_name" placeholder="Vollständiger Name" required>

        <input type="text" name="street" placeholder="Straße und Hausnummer" required>

        <input type="text" name="city" placeholder="Stadt" required>

        <input type="text" name="postal_code" placeholder="Postleitzahl" required>

        <select name="payment_method" required>
            <option value="">Zahlungsmethode wählen</option>
            <option value="PayPal">PayPal</option>
            <option value="Kreditkarte">Kreditkarte</option>
            <option value="Barzahlung">Barzahlung</option>
        </select>

        <button type="submit">
            Bestellung abschicken
        </button>

    </form>

</section>
</main>

</body>
</html>