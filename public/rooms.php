<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$stmt = $pdo->query("
    SELECT
        room_categories.*,
        (
            SELECT COUNT(*)
            FROM rooms
            WHERE rooms.category_id = room_categories.id
            AND rooms.status = 'frei'
        ) AS available_rooms
    FROM room_categories
    ORDER BY price ASC
");

$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

        <?php foreach ($categories as $category): ?>

            <div class="card">

                <?php if (!empty($category['image_path'])): ?>
                    <img
                        src="/<?= htmlspecialchars($category['image_path']) ?>"
                        style="
                            width:100%;
                            height:220px;
                            object-fit:cover;
                            border-radius:18px;
                            margin-bottom:18px;
                        "
                    >
                <?php endif; ?>

                <h3>
                    <?= htmlspecialchars($category['name']) ?>
                </h3>

                <p>
                    <?= htmlspecialchars($category['description']) ?>
                </p>

                <p>
                    Preis:
                    <?= htmlspecialchars($category['price']) ?> €
                </p>

                <p>
                    Verfügbare Zimmer:
                    <?= htmlspecialchars($category['available_rooms']) ?>
                </p>

                <a href="room.php?category_id=<?= htmlspecialchars($category['id']) ?>">
                    <button>
                        Kategorie ansehen
                    </button>
                </a>

            </div>

        <?php endforeach; ?>

    </div>

</section>

</main>

</body>
</html>