<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Campus Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <h1>Campus Booking</h1>

        <ul class="nav-links">
            <li><a href="index.php">Startseite</a></li>
            <li><a href="services.php">Angebote</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="meine_buchungen.php">Meine Buchungen</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>

        <div class="nav-profile">
            <a href="profile.php">
                <?php if (!empty($_SESSION['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['profile_image']) ?>" class="avatar">
                <?php else: ?>
                    <div class="avatar default">👤</div>
                <?php endif; ?>
            </a>
        </div>
    </nav>
</header>

<main>
    <section class="services">
        <h2>Campus Shop</h2>

        <div class="cards">
            <?php foreach ($products as $product): ?>
                <div class="card">
                    <h3><?= htmlspecialchars($product['title']) ?></h3>
                    <p><?= htmlspecialchars($product['description']) ?></p>
                    <p><?= htmlspecialchars($product['price']) ?> €</p>
                    <p>Lagerbestand: <?= htmlspecialchars($product['stock']) ?></p>
                    <button>Kaufen</button>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

</body>
</html>