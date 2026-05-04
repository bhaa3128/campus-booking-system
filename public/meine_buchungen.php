<?php

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingId = $_POST['booking_id'];

    $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
    $stmt->execute([$bookingId]);

    header("Location: meine_buchungen.php");
    exit;
}

$stmt = $pdo->query("
    SELECT bookings.id, services.title, services.description, services.price, bookings.created_at
    FROM bookings
    JOIN services ON bookings.service_id = services.id
    ORDER BY bookings.created_at DESC
");

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Buchungen</title>
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
        </ul>
    </nav>
</header>

<main>
    <section class="services">
        <h2>Meine Buchungen</h2>

        <div class="cards">
    <?php if (count($bookings) === 0): ?>
        <p>Keine Buchungen vorhanden.</p>
    <?php else: ?>
        <?php foreach ($bookings as $booking): ?>
            <div class="card">
                <h3><?= htmlspecialchars($booking['title']) ?></h3>
                <p><?= htmlspecialchars($booking['description']) ?></p>
                <p><?= htmlspecialchars($booking['price']) ?> €</p>
                <small>Gebucht am: <?= htmlspecialchars($booking['created_at']) ?></small>

                <form method="POST">
                    <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                    <button type="submit">Löschen</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
  
    </section>
</main>

</body>
</html>

