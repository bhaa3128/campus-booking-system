<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$floorId = $_GET['id'] ?? null;

if (!$floorId) {
    die("Etage nicht gefunden");
}

/* Floor laden */
$stmt = $pdo->prepare("
    SELECT floors.*, buildings.name AS building_name
    FROM floors
    JOIN buildings ON floors.building_id = buildings.id
    WHERE floors.id = ?
");

$stmt->execute([$floorId]);

$floor = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$floor) {
    die("Etage nicht gefunden");
}

/* Rooms laden */
$roomStmt = $pdo->prepare("
    SELECT *
    FROM rooms
    WHERE floor_id = ?
    ORDER BY room_number ASC
");

$roomStmt->execute([$floorId]);

$rooms = $roomStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">

    <title>
        Etage <?= htmlspecialchars($floor['floor_number']) ?>
    </title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>

<section class="services">

    <h2>
        <?= htmlspecialchars($floor['building_name']) ?>
    </h2>

    <div class="card">

        <h3>
            Etage <?= htmlspecialchars($floor['floor_number']) ?>
        </h3>

        <p>
            Zimmer auf dieser Etage:
            <?= count($rooms) ?>
        </p>

    </div>

    <h2>Zimmer</h2>

    <div class="cards">

        <?php foreach ($rooms as $room): ?>

            <div class="card">

                <h3>
                    Zimmer
                    <?= htmlspecialchars($room['room_number']) ?>
                </h3>

                <p>
                    Typ:
                    <?= htmlspecialchars($room['room_type']) ?>
                </p>

                <p>
                    Preis:
                    <?= htmlspecialchars($room['price']) ?> €
                </p>

                <p>
                    Status:
                    <?= htmlspecialchars($room['status']) ?>
                </p>

                <p>
                    <?= htmlspecialchars($room['description']) ?>
                </p>

                <a href="room.php?id=<?= $room['id'] ?>">
                    <button>
                        Zimmer ansehen
                    </button>
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</section>

</main>

</body>
</html>