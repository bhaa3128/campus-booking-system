<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: services.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_service'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];

        $stmt = $pdo->prepare("INSERT INTO services (title, description, price) VALUES (?, ?, ?)");
        $stmt->execute([$title, $description, $price]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_booking'])) {
        $bookingId = $_POST['booking_id'];

        $stmt = $pdo->prepare("DELETE FROM bookings WHERE id = ?");
        $stmt->execute([$bookingId]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_service'])) {
        $serviceId = $_POST['service_id'];

        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);

        header("Location: admin.php");
        exit;
    }
}

$stmt = $pdo->query("
    SELECT bookings.id, users.first_name, users.last_name, users.email,
           services.title, services.price, bookings.created_at
    FROM bookings
    JOIN users ON bookings.user_id = users.id
    JOIN services ON bookings.service_id = services.id
    ORDER BY bookings.created_at DESC
");

$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$serviceStmt = $pdo->query("SELECT * FROM services ORDER BY id DESC");
$services = $serviceStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
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
        </ul>
    </nav>
</header>

<main>
    <section class="services">
        <h2>Admin Panel</h2>

        <h2>Service hinzufügen</h2>

        <form method="POST" class="auth-form">
            <input type="text" name="title" placeholder="Titel" required>
            <input type="text" name="description" placeholder="Beschreibung" required>
            <input type="number" name="price" placeholder="Preis" required>
            <button type="submit" name="add_service">Hinzufügen</button>
        </form>

        <h2>Services verwalten</h2>

        <div class="cards">
            <?php foreach ($services as $service): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($service['title']) ?></h3>
                    <p><?= htmlspecialchars($service['description']) ?></p>
                    <p><?= htmlspecialchars($service['price']) ?> €</p>

                    <form method="POST">
                        <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['id']) ?>">
                        <button type="submit" name="delete_service" onclick="return confirm('Willst du diesen Service wirklich löschen?')">
                            Service löschen
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Alle Buchungen</h2>

        <div class="cards">
            <?php foreach ($bookings as $booking): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($booking['title']) ?></h3>
                    <p><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></p>
                    <p><?= htmlspecialchars($booking['email']) ?></p>
                    <p><?= htmlspecialchars($booking['price']) ?> €</p>
                    <small>Gebucht am: <?= htmlspecialchars($booking['created_at']) ?></small>

                    <form method="POST">
                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['id']) ?>">
                        <button type="submit" name="delete_booking" onclick="return confirm('Willst du diese Buchung wirklich löschen?')">
                            Löschen
                        </button>
                    </form>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>