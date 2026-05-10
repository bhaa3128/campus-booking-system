<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$categoryId = $_GET['id'] ?? $_GET['category_id'] ?? null;

if (!$categoryId) {
    die("Zimmerkategorie nicht gefunden");
}

$stmt = $pdo->prepare("
    SELECT *
    FROM room_categories
    WHERE id = ?
");

$stmt->execute([$categoryId]);
$category = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$category) {
    die("Zimmerkategorie nicht gefunden");
}

$availableStmt = $pdo->prepare("
    SELECT COUNT(*) AS available_count
    FROM rooms
    WHERE category_id = ?
    AND status = 'frei'
");

$availableStmt->execute([$categoryId]);
$availableCount = $availableStmt->fetch(PDO::FETCH_ASSOC)['available_count'];

$features = [
    'WLAN',
    'Schreibtisch',
    'Bett',
    'Kleiderschrank',
    'Heizung',
    'Campusnähe'
];

$recommendedStmt = $pdo->prepare("
    SELECT *
    FROM room_categories
    WHERE id != ?
    ORDER BY price ASC
    LIMIT 3
");

$recommendedStmt->execute([$categoryId]);
$recommendedCategories = $recommendedStmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($category['name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2><?= htmlspecialchars($category['name']) ?></h2>

    <p style="color:#cbd5e1;">
        Zimmerkategorie im Campus Booking System
    </p>

    <div class="card">

        <?php if (!empty($category['image_path'])): ?>
            <img
                src="/<?= htmlspecialchars($category['image_path']) ?>"
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
                Kein Bild vorhanden
            </div>
        <?php endif; ?>

        <h3>Beschreibung</h3>

        <p>
            <?= htmlspecialchars($category['description']) ?>
        </p>

        <div style="
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:15px;
            margin:22px 0;
        ">
            <div class="card">
                <strong>Kategorie</strong>
                <p><?= htmlspecialchars($category['name']) ?></p>
            </div>

            <div class="card">
                <strong>Preis</strong>
                <p><?= htmlspecialchars($category['price']) ?> €</p>
            </div>

            <div class="card">
                <strong>Verfügbar</strong>
                <p>
                    <span style="
                        background:#22c55e;
                        color:white;
                        padding:7px 13px;
                        border-radius:999px;
                        font-size:14px;
                    ">
                        <?= htmlspecialchars($availableCount) ?> Zimmer
                    </span>
                </p>
            </div>
        </div>

        <h3>Ausstattung</h3>

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
                    <?= htmlspecialchars($feature) ?>
                </span>
            <?php endforeach; ?>
        </div>

        <?php if ($availableCount > 0): ?>
            <a href="room_application.php?category_id=<?= htmlspecialchars($category['id']) ?>">
                <button>Diese Kategorie buchen</button>
            </a>
        <?php else: ?>
            <button disabled>Nicht verfügbar</button>
        <?php endif; ?>

    </div>

    <h2>Ähnliche Kategorien</h2>

    <div class="cards">
        <?php foreach ($recommendedCategories as $recommended): ?>
            <div class="card">

                <?php if (!empty($recommended['image_path'])): ?>
                    <img
                        src="/<?= htmlspecialchars($recommended['image_path']) ?>"
                        style="
                            width:100%;
                            height:180px;
                            object-fit:cover;
                            border-radius:16px;
                            margin-bottom:15px;
                        "
                    >
                <?php endif; ?>

                <h3><?= htmlspecialchars($recommended['name']) ?></h3>

                <p><?= htmlspecialchars($recommended['description']) ?></p>

                <p>Preis: <?= htmlspecialchars($recommended['price']) ?> €</p>

                <a href="room.php?category_id=<?= htmlspecialchars($recommended['id']) ?>">
                    <button>Kategorie ansehen</button>
                </a>

            </div>
        <?php endforeach; ?>
    </div>

</section>
</main>

</body>
</html>