<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

/* Rooms laden */
$stmt = $pdo->query("
    SELECT rooms.*, buildings.name AS building_name,
        (
            SELECT file_path
            FROM room_media
            WHERE room_media.room_id = rooms.id
            LIMIT 1
        ) AS preview_image
    FROM rooms
    JOIN buildings ON rooms.building_id = buildings.id
    ORDER BY buildings.id ASC, rooms.room_number ASC
");

$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">

    <title>Zimmerangebote</title>

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>

<section class="services">

    <h2>Unsere Zimmerangebote</h2>

    <div class="cards">

        <?php foreach ($rooms as $room): ?>

    <div class="card">

        <?php if (!empty($room['preview_image'])): ?>
            <img
                src="/<?= htmlspecialchars($room['preview_image']) ?>"
                style="
                    width:100%;
                    height:180px;
                    object-fit:cover;
                    border-radius:15px;
                    margin-bottom:15px;
                "
            >
        <?php endif; ?>

        <h3>
            Zimmer <?= htmlspecialchars($room['room_number']) ?>
        </h3>

                <p>
                    Gebäude:
                    <?= htmlspecialchars($room['building_name']) ?>
                </p>

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
                        Details ansehen
                    </button>
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</section>

</main>

</body>
</html>