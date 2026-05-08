<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = $_POST['service_id'];

    $userId = $_SESSION['user_id'];

$checkStmt = $pdo->prepare("SELECT id FROM bookings WHERE user_id = ? AND service_id = ?");
$checkStmt->execute([$userId, $serviceId]);

if ($checkStmt->fetch()) {
    $message = 'Diese Buchung existiert bereits.';
} else {
    $stmt = $pdo->prepare("INSERT INTO bookings (user_id, service_id) VALUES (?, ?)");
    $stmt->execute([$userId, $serviceId]);

    $message = 'Buchung erfolgreich!';
    $recommendationStmt = $pdo->prepare("
   SELECT products.id, products.title, products.price, recommendations.reason
    FROM recommendations
    JOIN products ON recommendations.product_id = products.id
    WHERE recommendations.service_id = ?
");
$recommendationStmt->execute([$serviceId]);
$recommendations = $recommendationStmt->fetchAll(PDO::FETCH_ASSOC);

$_SESSION['recommendations'] = $recommendations;
}
}

$stmt = $pdo->query("SELECT * FROM services");
$services = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Angebote</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">
        <h2>Unsere Angebote</h2>

        <?php if ($message): ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

       <?php if (!empty($_SESSION['recommendations'])): ?>
    <h2>Passend zu deiner Buchung</h2>

    <div class="cards">
        <?php foreach ($_SESSION['recommendations'] as $rec): ?>
            <div class="card">
                <h3><?= htmlspecialchars($rec['title']) ?></h3>
                <p><?= htmlspecialchars($rec['reason']) ?></p>
                <p><?= htmlspecialchars($rec['price']) ?> €</p>

                <a href="product.php?id=<?= htmlspecialchars($rec['id']) ?>">
                    <button>Zum Produkt</button>
                </a>
            </div>
        <?php endforeach; ?>
    </div>

    <?php unset($_SESSION['recommendations']); ?>
<?php endif; ?>

        <div class="cards">
            <?php foreach ($services as $service): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                    <p><?= htmlspecialchars($service['description']) ?></p>
                    <p><?= htmlspecialchars($service['price']) ?> €</p>

                    <form method="POST">
                        <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['id']) ?>">
                        <button type="submit">Buchen</button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>