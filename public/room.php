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

$statusColor = '#aaa';

if ($room['status'] === 'frei') {
    $statusColor = '#22c55e';
} elseif ($room['status'] === 'reserviert') {
    $statusColor = '#facc15';
} elseif ($room['status'] === 'belegt') {
    $statusColor = '#ef4444';
}

$viewStmt = $pdo->prepare("
    UPDATE rooms
    SET views = views + 1
    WHERE id = ?
");
$viewStmt->execute([$roomId]);
$room['views']++;

$mediaStmt = $pdo->prepare("
    SELECT *
    FROM room_media
    WHERE room_id = ?
    ORDER BY id ASC
");
$mediaStmt->execute([$roomId]);
$media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

$mainImage = $room['main_image'] ?? null;

if (empty($mainImage) && !empty($media)) {
    $mainImage = $media[0]['file_path'];
}

$featureStmt = $pdo->prepare("
    SELECT *
    FROM room_features
    WHERE room_id = ?
");
$featureStmt->execute([$roomId]);
$features = $featureStmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room'])) {
    if ($room['status'] !== 'frei') {
        die("Zimmer nicht verfügbar");
    }

    $userId = $_SESSION['user_id'];

    $bookingStmt = $pdo->prepare("
        INSERT INTO room_bookings (user_id, room_id, start_date, end_date)
        VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 6 MONTH))
    ");
    $bookingStmt->execute([$userId, $roomId]);

    $updateStmt = $pdo->prepare("
        UPDATE rooms
        SET status = 'reserviert'
        WHERE id = ?
    ");
    $updateStmt->execute([$roomId]);

    header("Location: room.php?id=" . $roomId);
    exit;
}

$recommendStmt = $pdo->prepare("
    SELECT rooms.*,
        (
            SELECT file_path
            FROM room_media
            WHERE room_media.room_id = rooms.id
            LIMIT 1
        ) AS preview_image
    FROM rooms
    WHERE room_type = ?
    AND id != ?
    LIMIT 3
");
$recommendStmt->execute([$room['room_type'], $room['id']]);
$recommendedRooms = $recommendStmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Zimmer <?= htmlspecialchars($room['room_number']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2>Zimmer <?= htmlspecialchars($room['room_number']) ?></h2>

    <p style="color:#cbd5e1;">
        <?= htmlspecialchars($room['building_name']) ?>
    </p>

    <div class="card">

        <?php if (!empty($mainImage)): ?>
            <img
                src="/<?= htmlspecialchars($mainImage) ?>"
                style="
                    width:100%;
                    height:430px;
                    object-fit:cover;
                    border-radius:24px;
                    margin-bottom:18px;
                    box-shadow:0 14px 35px rgba(0,0,0,0.28);
                "
            >
        <?php else: ?>
            <div style="
                height:260px;
                border-radius:24px;
                background:rgba(255,255,255,0.10);
                display:flex;
                align-items:center;
                justify-content:center;
                margin-bottom:18px;
            ">
                Keine Bilder vorhanden
            </div>
        <?php endif; ?>

        <?php if (!empty($media)): ?>
            <div style="
                display:flex;
                gap:12px;
                flex-wrap:wrap;
                margin-bottom:25px;
            ">
                <?php foreach ($media as $m): ?>
                    <img
                        src="/<?= htmlspecialchars($m['file_path']) ?>"
                        style="
                            width:120px;
                            height:85px;
                            object-fit:cover;
                            border-radius:14px;
                            border:1px solid rgba(255,255,255,0.22);
                        "
                    >
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <h3>Beschreibung</h3>
        <p><?= htmlspecialchars($room['description']) ?></p>

        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:15px;
            margin:22px 0;
        ">
            <div class="card">
                <strong>Typ</strong>
                <p><?= htmlspecialchars($room['room_type']) ?></p>
            </div>

            <div class="card">
                <strong>Preis</strong>
                <p><?= htmlspecialchars($room['price']) ?> €</p>
            </div>

            <div class="card">
                <strong>Status</strong>
                <p>
                    <span style="
                        background:<?= $statusColor ?>;
                        color:white;
                        padding:7px 13px;
                        border-radius:999px;
                        font-size:14px;
                    ">
                        <?= htmlspecialchars($room['status']) ?>
                    </span>
                </p>
            </div>
        </div>

        <h3>Ausstattung</h3>

        <?php if (!empty($features)): ?>
            <div style="
                display:flex;
                flex-wrap:wrap;
                gap:10px;
                margin-top:15px;
                margin-bottom:25px;
            ">
                <?php foreach ($features as $feature): ?>
                    <span style="
                        background:rgba(255,255,255,0.12);
                        padding:10px 14px;
                        border-radius:999px;
                        font-size:14px;
                        border:1px solid rgba(255,255,255,0.15);
                    ">
                        <?= htmlspecialchars($feature['feature_name']) ?>
                    </span>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Keine Ausstattung vorhanden.</p>
        <?php endif; ?>

        <?php if ($room['status'] === 'frei'): ?>
            <a href="room_application.php?id=<?= htmlspecialchars($room['id']) ?>">
    <button>Zimmer buchen</button>
</a>
        <?php else: ?>
            <button disabled>
                Nicht verfügbar
            </button>
        <?php endif; ?>

    </div>

    <h2>Ähnliche Zimmer</h2>

    <div class="cards">
        <?php foreach ($recommendedRooms as $recommended): ?>
            <div class="card">

                <?php if (!empty($recommended['preview_image'])): ?>
                    <img
                        src="/<?= htmlspecialchars($recommended['preview_image']) ?>"
                        style="
                            width:100%;
                            height:180px;
                            object-fit:cover;
                            border-radius:16px;
                            margin-bottom:15px;
                        "
                    >
                <?php endif; ?>

                <h3>Zimmer <?= htmlspecialchars($recommended['room_number']) ?></h3>
                <p>Typ: <?= htmlspecialchars($recommended['room_type']) ?></p>
                <p>Preis: <?= htmlspecialchars($recommended['price']) ?> €</p>

                <a href="room.php?id=<?= htmlspecialchars($recommended['id']) ?>">
                    <button>Zimmer ansehen</button>
                </a>

            </div>
        <?php endforeach; ?>
    </div>

</section>
</main>

</body>
</html>