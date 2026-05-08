<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$stmt = $pdo->query("
    SELECT 
        buildings.*,
        COUNT(DISTINCT rooms.id) AS room_count,
        COUNT(DISTINCT facilities.id) AS facility_count
    FROM buildings
    LEFT JOIN rooms ON rooms.building_id = buildings.id
    LEFT JOIN facilities ON facilities.building_id = buildings.id
    GROUP BY buildings.id
    ORDER BY buildings.id ASC
");

$buildings = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Wohnheime</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">

        <h2>Unsere Wohnheime</h2>

        <p style="color:#cbd5e1; max-width:800px; margin:0 auto 35px;">
            Entdecke unsere vier modernen Studentenwohnheime mit Zimmern,
            Einrichtungen, Arbeitsbereichen und Gemeinschaftsräumen.
        </p>

        <div class="cards">

            <?php foreach ($buildings as $building): ?>

                <div class="card" style="text-align:center; padding:30px;">

                    <h3><?= htmlspecialchars($building['name']) ?></h3>

                    <p style="font-weight:700;">
                        <?= htmlspecialchars($building['address']) ?>
                    </p>

                    <p>
                        <?= htmlspecialchars($building['description']) ?>
                    </p>

                    <div style="
                        display:flex;
                        justify-content:center;
                        gap:12px;
                        flex-wrap:wrap;
                        margin:24px 0;
                    ">
                        <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                             <?= htmlspecialchars($building['floors_count']) ?> Etagen
                        </span>

                        <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                             <?= htmlspecialchars($building['room_count']) ?> Zimmer
                        </span>

                        <span style="background:rgba(255,255,255,0.14); padding:10px 16px; border-radius:999px;">
                             <?= htmlspecialchars($building['facility_count']) ?> Einrichtungen
                        </span>
                    </div>

                    <a href="building.php?id=<?= htmlspecialchars($building['id']) ?>">
                        <button>Details ansehen</button>
                    </a>

                </div>

            <?php endforeach; ?>

        </div>

    </section>
</main>

</body>
</html>