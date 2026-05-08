<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$stmt = $pdo->query("
    SELECT facilities.*, buildings.name AS building_name
    FROM facilities
    JOIN buildings ON facilities.building_id = buildings.id
    ORDER BY buildings.id ASC, facilities.name ASC
");

$facilities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Einrichtungen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">
        <h2>Unsere Einrichtungen</h2>

        <?php
        $currentBuilding = '';

        foreach ($facilities as $facility):

            if ($currentBuilding !== $facility['building_name']):

                if ($currentBuilding !== '') {
                    echo '</div>';
                }

                $currentBuilding = $facility['building_name'];
        ?>

            <h2 style="margin-top:50px;">
                <?= htmlspecialchars($currentBuilding) ?>
            </h2>

            <div class="cards">

        <?php endif; ?>

            <div class="card">
                <h3><?= htmlspecialchars($facility['name']) ?></h3>

                <p><?= htmlspecialchars($facility['description']) ?></p>

                <p>
                    Öffnungszeiten:
                    <?= htmlspecialchars($facility['opening_hours']) ?>
                </p>

                <p>
                    Kapazität:
                    <?= htmlspecialchars($facility['capacity']) ?>
                    Personen
                </p>

                <a href="facility.php?id=<?= htmlspecialchars($facility['id']) ?>">
                    <button>Details ansehen</button>
                </a>
            </div>

        <?php endforeach; ?>

        <?php if ($currentBuilding !== ''): ?>
            </div>
        <?php endif; ?>

    </section>
</main>

</body>
</html>