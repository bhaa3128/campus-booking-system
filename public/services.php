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

<header>
    <nav>
        <h1>Campus Booking</h1>
        <ul>
            <li><a href="index.php">Startseite</a></li>
            <li><a href="services.php">Angebote</a></li>
            <li><a href="meine_buchungen.php">Meine Buchungen</a></li>
            <li><a href="logout.php">Logout</a></li>
            
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin.php">Admin Panel</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>

<main>
    <section class="services">
        <h2>Unsere Angebote</h2>

        <?php if ($message): ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
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