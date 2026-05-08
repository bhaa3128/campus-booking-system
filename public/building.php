<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$buildingId = $_GET['id'] ?? null;

if (!$buildingId) {
    die("Gebäude nicht gefunden");
}

$stmt = $pdo->prepare("
    SELECT *
    FROM buildings
    WHERE id = ?
");
$stmt->execute([$buildingId]);
$building = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$building) {
    die("Gebäude nicht gefunden");
}

$floorStmt = $pdo->prepare("
    SELECT *
    FROM floors
    WHERE building_id = ?
    ORDER BY floor_number ASC
");
$floorStmt->execute([$buildingId]);
$floors = $floorStmt->fetchAll(PDO::FETCH_ASSOC);

$roomStmt = $pdo->prepare("
    SELECT *
    FROM rooms
    WHERE building_id = ?
    ORDER BY room_number ASC
");
$roomStmt->execute([$buildingId]);
$rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

$facilityStmt = $pdo->prepare("
    SELECT *
    FROM facilities
    WHERE building_id = ?
    ORDER BY name ASC
");
$facilityStmt->execute([$buildingId]);
$facilities = $facilityStmt->fetchAll(PDO::FETCH_ASSOC);

$freeRooms = 0;

foreach ($rooms as $room) {
    if ($room['status'] === 'frei') {
        $freeRooms++;
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($building['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">

        <h2><?= htmlspecialchars($building['name']) ?></h2>

        <p style="color:#cbd5e1; max-width:800px; margin:0 auto 30px;">
            <?= htmlspecialchars($building['description']) ?>
        </p>

        <div class="card" style="text-align:center; margin-bottom:35px;">
            <p><strong>Adresse:</strong> <?= htmlspecialchars($building['address']) ?></p>

            <div style="
                display:flex;
                justify-content:center;
                gap:12px;
                flex-wrap:wrap;
                margin-top:22px;
            ">
                <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                     <?= count($floors) ?> Etagen
                </span>

                <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                     <?= count($rooms) ?> Zimmer
                </span>

                <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                      <?= $freeRooms ?> frei
                </span>

                <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                      <?= count($facilities) ?> Einrichtungen
                </span>
            </div>
        </div>

        <h2>Etagen</h2>

        <div class="cards">
            <?php foreach ($floors as $floor): ?>
                <div class="card" style="text-align:center;">
                    <h3>Etage <?= htmlspecialchars($floor['floor_number']) ?></h3>

                    <a href="floor.php?id=<?= htmlspecialchars($floor['id']) ?>">
                        <button>Etage ansehen</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Zimmer in diesem Wohnheim</h2>

        <div class="cards">
            <?php foreach ($rooms as $room): ?>
                <div class="card">
                    <h3>Zimmer <?= htmlspecialchars($room['room_number']) ?></h3>

                    <p>Typ: <?= htmlspecialchars($room['room_type']) ?></p>
                    <p>Preis: <?= htmlspecialchars($room['price']) ?> €</p>
                    <p>Status: <?= htmlspecialchars($room['status']) ?></p>

                    <a href="room.php?id=<?= htmlspecialchars($room['id']) ?>">
                        <button>Zimmer ansehen</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

        <h2>Einrichtungen</h2>

        <div class="cards">
            <?php foreach ($facilities as $facility): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($facility['name']) ?></h3>

                    <p><?= htmlspecialchars($facility['description']) ?></p>
                    <p>Öffnungszeiten: <?= htmlspecialchars($facility['opening_hours']) ?></p>
                    <p>Kapazität: <?= htmlspecialchars($facility['capacity']) ?> Personen</p>

                    <a href="facility.php?id=<?= htmlspecialchars($facility['id']) ?>">
                        <button>Einrichtung ansehen</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </section>
</main>

</body>
</html>