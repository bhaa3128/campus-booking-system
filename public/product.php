<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$productId = $_GET['id'] ?? null;

if (!$productId) {
    die("Produkt nicht gefunden");
}

/* Produkt laden */
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

$viewStmt = $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
$viewStmt->execute([$productId]);

$product['views']++;

if (!$product) {
    die("Produkt nicht gefunden");
}


/* Bewertung speichern */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_review'])) {
    $rating = $_POST['rating'];
    $comment = $_POST['comment'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("
        INSERT INTO product_reviews (product_id, user_id, rating, comment)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$productId, $userId, $rating, $comment]);

    header("Location: product.php?id=" . $productId);
    exit;
}

/* Produkt kaufen */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['buy_product'])) {
    $userId = $_SESSION['user_id'];

    $stockStmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
    $stockStmt->execute([$productId]);
    $stock = $stockStmt->fetchColumn();

    if ($stock > 0) {
        $orderStmt = $pdo->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
        $orderStmt->execute([$userId, $productId]);

        $updateStmt = $pdo->prepare("UPDATE products SET stock = stock - 1 WHERE id = ?");
        $updateStmt->execute([$productId]);

        header("Location: product.php?id=" . $productId);
        exit;
    }
}

/* Bilder laden */
$mediaStmt = $pdo->prepare("SELECT * FROM product_media WHERE product_id = ?");
$mediaStmt->execute([$productId]);
$media = $mediaStmt->fetchAll(PDO::FETCH_ASSOC);

/* Reviews laden */
$reviewStmt = $pdo->prepare("
    SELECT product_reviews.*, users.first_name, users.last_name
    FROM product_reviews
    JOIN users ON product_reviews.user_id = users.id
    WHERE product_reviews.product_id = ?
    ORDER BY product_reviews.created_at DESC
");
$reviewStmt->execute([$productId]);
$reviews = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);

/* Durchschnitt berechnen */
$avgStmt = $pdo->prepare("
    SELECT AVG(rating) AS average_rating, COUNT(*) AS review_count
    FROM product_reviews
    WHERE product_id = ?
");
$avgStmt->execute([$productId]);
$ratingInfo = $avgStmt->fetch(PDO::FETCH_ASSOC);

$averageRating = round($ratingInfo['average_rating'] ?? 0, 1);
$reviewCount = $ratingInfo['review_count'] ?? 0;

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['title']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">

        <h2><?= htmlspecialchars($product['title']) ?></h2>

        <p style="font-weight: bold; margin-bottom: 10px;">
        👁️ <?= htmlspecialchars($product['views']) ?> Aufrufe insgesamt
</p>

        <p style="font-weight: bold; margin-bottom: 10px;">
    👁️ <?= htmlspecialchars($product['views']) ?> Aufrufe insgesamt
</p>
        <p style="font-size: 18px; margin-bottom: 20px;">
            ⭐ <strong><?= $averageRating ?></strong> / 5
            (<?= htmlspecialchars($reviewCount) ?> Bewertungen)
        </p>

        <div class="card">

            <h3>Produktbilder</h3>

            <?php if (!empty($media)): ?>
                <div style="display: flex; gap: 15px; flex-wrap: wrap; margin-bottom: 20px;">
                    <?php foreach ($media as $m): ?>
                        <?php if ($m['media_type'] === 'image'): ?>
                            <img 
                                src="/<?= htmlspecialchars($m['file_path']) ?>" 
                                style="width: 180px; height: 140px; object-fit: cover; border-radius: 15px; border: 2px solid rgba(255,255,255,0.4);"
                            >
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p>Noch keine Bilder vorhanden.</p>
            <?php endif; ?>

            <h3>Beschreibung</h3>
            <p><?= htmlspecialchars($product['description']) ?></p>

            <?php if (!empty($product['long_description'])): ?>
                <p><?= htmlspecialchars($product['long_description']) ?></p>
            <?php endif; ?>

            <p><strong>Preis:</strong> <?= htmlspecialchars($product['price']) ?> €</p>
            <p><strong>Lagerbestand:</strong> <?= htmlspecialchars($product['stock']) ?></p>

            <form method="POST">
                <button type="submit" name="buy_product">Kaufen</button>
            </form>

        </div>

        <h3>Bewertung schreiben</h3>

        <div class="card">
            <form method="POST" class="auth-form">
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ 5</option>
                    <option value="4">⭐⭐⭐⭐ 4</option>
                    <option value="3">⭐⭐⭐ 3</option>
                    <option value="2">⭐⭐ 2</option>
                    <option value="1">⭐ 1</option>
                </select>

                <textarea name="comment" placeholder="Dein Kommentar..." required></textarea>

                <button type="submit" name="add_review">Bewertung speichern</button>
            </form>
        </div>

        <h3>Bewertungen & Kommentare</h3>

        <?php if (!empty($reviews)): ?>
            <?php foreach ($reviews as $r): ?>
                <div class="card">
                    <strong>
                        <?= htmlspecialchars($r['first_name'] . ' ' . $r['last_name']) ?>
                    </strong>

                    <p>
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <?= $i <= $r['rating'] ? '⭐' : '☆' ?>
                        <?php endfor; ?>
                    </p>

                    <p><?= htmlspecialchars($r['comment']) ?></p>

                    <small>
                        Geschrieben am: <?= htmlspecialchars($r['created_at']) ?>
                    </small>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>Noch keine Bewertungen vorhanden.</p>
        <?php endif; ?>

    </section>
</main>

</body>
</html>