<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$roomId = $_GET['id'] ?? null;

if (!$roomId) {
    die("Zimmer nicht gefunden");
}

$stmt = $pdo->prepare("
    SELECT rooms.*, buildings.name AS building_name
    FROM rooms
    JOIN buildings ON rooms.building_id = buildings.id
    WHERE rooms.id = ?
");
$stmt->execute([$roomId]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    die("Zimmer nicht gefunden");
}

if ($room['status'] !== 'frei') {
    die("Dieses Zimmer ist nicht verfügbar.");
}

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $birthdate = $_POST['birthdate'];
    $startDate = $_POST['start_date'];
    $endDate = $_POST['end_date'];
    $paymentMethod = $_POST['payment_method'];
    $comment = $_POST['comment'];

    $bookingStmt = $pdo->prepare("
        INSERT INTO room_bookings
        (
            user_id,
            room_id,
            start_date,
            end_date,
            phone,
            address,
            birthdate,
            payment_method,
            comment
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $bookingStmt->execute([
        $_SESSION['user_id'],
        $roomId,
        $startDate,
        $endDate,
        $phone,
        $address,
        $birthdate,
        $paymentMethod,
        $comment
    ]);

    $updateStmt = $pdo->prepare("
        UPDATE rooms
        SET status = 'reserviert'
        WHERE id = ?
    ");
    $updateStmt->execute([$roomId]);

    header("Location: meine_buchungen.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Anmeldung Zimmer <?= htmlspecialchars($room['room_number']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">

        <h2>Anmeldung für Zimmer <?= htmlspecialchars($room['room_number']) ?></h2>

        <div class="card">
            <p><strong>Gebäude:</strong> <?= htmlspecialchars($room['building_name']) ?></p>
            <p><strong>Typ:</strong> <?= htmlspecialchars($room['room_type']) ?></p>
            <p><strong>Preis:</strong> <?= htmlspecialchars($room['price']) ?> €</p>
        </div>

        <form method="POST" class="auth-form">

            <input
                type="text"
                value="<?= htmlspecialchars($user['first_name'] ?? '') ?>"
                disabled
            >

            <input
                type="text"
                value="<?= htmlspecialchars($user['last_name'] ?? '') ?>"
                disabled
            >

            <input
                type="email"
                value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                disabled
            >

            <input
                type="text"
                name="phone"
                placeholder="Telefonnummer"
                required
            >

            <input
                type="text"
                name="address"
                placeholder="Adresse"
                required
            >

            <input
                type="date"
                name="birthdate"
                required
            >

            <input
                type="date"
                name="start_date"
                required
            >

            <input
                type="date"
                name="end_date"
                required
            >

            <select name="payment_method" required>
                <option value="">Zahlungsmethode auswählen</option>
                <option value="PayPal">PayPal</option>
                <option value="Kreditkarte">Kreditkarte</option>
                <option value="SEPA">SEPA Lastschrift</option>
                <option value="Bar">Bar vor Ort</option>
            </select>

            <textarea
                name="comment"
                placeholder="Kommentar oder besondere Wünsche..."
            ></textarea>

            <button type="submit">
                Anmeldung absenden
            </button>

        </form>

    </section>
</main>

</body>
</html>