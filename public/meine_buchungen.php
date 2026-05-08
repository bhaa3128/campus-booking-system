<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['cancel_room_booking'])) {
        $bookingId = $_POST['booking_id'];

        $roomIdStmt = $pdo->prepare("
            SELECT room_id
            FROM room_bookings
            WHERE id = ?
            AND user_id = ?
        ");
        $roomIdStmt->execute([$bookingId, $userId]);
        $roomId = $roomIdStmt->fetchColumn();

        if ($roomId) {
            $deleteStmt = $pdo->prepare("
                DELETE FROM room_bookings
                WHERE id = ?
                AND user_id = ?
            ");
            $deleteStmt->execute([$bookingId, $userId]);

            $updateRoomStmt = $pdo->prepare("
                UPDATE rooms
                SET status = 'frei'
                WHERE id = ?
            ");
            $updateRoomStmt->execute([$roomId]);
        }

        header("Location: meine_buchungen.php");
        exit;
    }

    if (isset($_POST['cancel_facility_booking'])) {
        $bookingId = $_POST['booking_id'];

        $deleteStmt = $pdo->prepare("
            DELETE FROM facility_bookings
            WHERE id = ?
            AND user_id = ?
        ");
        $deleteStmt->execute([$bookingId, $userId]);

        header("Location: meine_buchungen.php");
        exit;
    }
}

$roomStmt = $pdo->prepare("
    SELECT 
        room_bookings.id,
        room_bookings.start_date,
        room_bookings.end_date,
        room_bookings.status,
        rooms.room_number,
        rooms.room_type,
        rooms.price,
        buildings.name AS building_name
    FROM room_bookings
    JOIN rooms ON room_bookings.room_id = rooms.id
    JOIN buildings ON rooms.building_id = buildings.id
    WHERE room_bookings.user_id = ?
    ORDER BY room_bookings.created_at DESC
");
$roomStmt->execute([$userId]);
$roomBookings = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityStmt = $pdo->prepare("
    SELECT 
        facility_bookings.id,
        facility_bookings.booking_date,
        facility_bookings.status,
        facilities.name AS facility_name,
        facilities.opening_hours,
        buildings.name AS building_name
    FROM facility_bookings
    JOIN facilities ON facility_bookings.facility_id = facilities.id
    JOIN buildings ON facilities.building_id = buildings.id
    WHERE facility_bookings.user_id = ?
    ORDER BY facility_bookings.created_at DESC
");
$facilityStmt->execute([$userId]);
$facilityBookings = $facilityStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Meine Buchungen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">

        <h2>Meine Buchungen</h2>

        <h2>Zimmerbuchungen</h2>

        <div class="cards">
            <?php if (empty($roomBookings)): ?>
                <div class="card">
                    <p>Du hast noch keine Zimmer gebucht.</p>
                </div>
            <?php else: ?>
                <?php foreach ($roomBookings as $booking): ?>
                    <div class="card">
                        <h3>Zimmer <?= htmlspecialchars($booking['room_number']) ?></h3>

                        <p>Gebäude: <?= htmlspecialchars($booking['building_name']) ?></p>
                        <p>Typ: <?= htmlspecialchars($booking['room_type']) ?></p>
                        <p>Preis: <?= htmlspecialchars($booking['price']) ?> €</p>
                        <p>Von: <?= htmlspecialchars($booking['start_date']) ?></p>
                        <p>Bis: <?= htmlspecialchars($booking['end_date']) ?></p>
                        <p>Status: <?= htmlspecialchars($booking['status']) ?></p>

                        <form method="POST">
                            <input
                                type="hidden"
                                name="booking_id"
                                value="<?= htmlspecialchars($booking['id']) ?>"
                            >

                            <button
                                type="submit"
                                name="cancel_room_booking"
                                onclick="return confirm('Willst du diese Zimmerbuchung wirklich stornieren?')"
                            >
                                Buchung stornieren
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h2>Einrichtungsbuchungen</h2>

        <div class="cards">
            <?php if (empty($facilityBookings)): ?>
                <div class="card">
                    <p>Du hast noch keine Einrichtungen gebucht.</p>
                </div>
            <?php else: ?>
                <?php foreach ($facilityBookings as $booking): ?>
                    <div class="card">
                        <h3><?= htmlspecialchars($booking['facility_name']) ?></h3>

                        <p>Gebäude: <?= htmlspecialchars($booking['building_name']) ?></p>
                        <p>Buchungsdatum: <?= htmlspecialchars($booking['booking_date']) ?></p>
                        <p>Öffnungszeiten: <?= htmlspecialchars($booking['opening_hours']) ?></p>
                        <p>Status: <?= htmlspecialchars($booking['status']) ?></p>

                        <form method="POST">
                            <input
                                type="hidden"
                                name="booking_id"
                                value="<?= htmlspecialchars($booking['id']) ?>"
                            >

                            <button
                                type="submit"
                                name="cancel_facility_booking"
                                onclick="return confirm('Willst du diese Einrichtungsbuchung wirklich stornieren?')"
                            >
                                Buchung stornieren
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </section>
</main>

</body>
</html>