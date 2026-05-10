<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['update_quantity'])) {
        $cartId = $_POST['cart_id'];
        $quantity = max(1, (int) $_POST['quantity']);

        $stmt = $pdo->prepare("
            UPDATE cart_items
            SET quantity = ?
            WHERE id = ?
            AND user_id = ?
        ");

        $stmt->execute([$quantity, $cartId, $userId]);

        unset($_SESSION['checkout_discount']);
        unset($_SESSION['checkout_coupon_id']);
        unset($_SESSION['checkout_coupon_code']);
        unset($_SESSION['checkout_student_discount']);

        header("Location: cart.php");
        exit;
    }

    if (isset($_POST['remove_item'])) {
        $cartId = $_POST['cart_id'];

        $stmt = $pdo->prepare("
            DELETE FROM cart_items
            WHERE id = ?
            AND user_id = ?
        ");

        $stmt->execute([$cartId, $userId]);

        unset($_SESSION['checkout_discount']);
        unset($_SESSION['checkout_coupon_id']);
        unset($_SESSION['checkout_coupon_code']);
        unset($_SESSION['checkout_student_discount']);

        header("Location: cart.php");
        exit;
    }

    if (isset($_POST['apply_discount'])) {
        $discount = 0;
        $discountMessage = '';
        $couponId = null;
        $couponCode = null;
        $studentDiscountUsed = 0;

        $coupon = trim($_POST['coupon'] ?? '');
        $studentDiscount = isset($_POST['student_discount']);

        if (!empty($coupon)) {
            $couponStmt = $pdo->prepare("
                SELECT *
                FROM coupons
                WHERE code = ?
                AND is_active = 1
                AND (valid_from IS NULL OR valid_from <= CURDATE())
                AND (valid_until IS NULL OR valid_until >= CURDATE())
                AND (max_uses IS NULL OR used_count < max_uses)
                AND (assigned_user_id IS NULL OR assigned_user_id = ?)
            ");

            $couponStmt->execute([$coupon, $userId]);
            $couponData = $couponStmt->fetch(PDO::FETCH_ASSOC);

            if ($couponData) {
                $discount += (int) $couponData['discount_percent'];
                $couponId = $couponData['id'];
                $couponCode = $couponData['code'];
                $discountMessage .= 'Coupon ' . $couponCode . ' angewendet. ';
            } else {
                $discountMessage .= 'Coupon ungültig oder abgelaufen. ';
            }
        }

        if ($studentDiscount) {
            $studentStmt = $pdo->prepare("
                SELECT is_verified_student
                FROM users
                WHERE id = ?
            ");

            $studentStmt->execute([$userId]);
            $student = $studentStmt->fetch(PDO::FETCH_ASSOC);

            if (!empty($student['is_verified_student'])) {
                $discount += 5;
                $studentDiscountUsed = 1;
                $discountMessage .= 'Studentenrabatt angewendet.';
            } else {
                header("Location: student_verification.php");
                exit;
            }
        }

        $_SESSION['checkout_discount'] = $discount;
        $_SESSION['checkout_coupon_id'] = $couponId;
        $_SESSION['checkout_coupon_code'] = $couponCode;
        $_SESSION['checkout_student_discount'] = $studentDiscountUsed;
        $_SESSION['discount_message'] = $discountMessage;

        header("Location: cart.php");
        exit;
    }
}

$stmt = $pdo->prepare("
    SELECT 
        cart_items.id AS cart_id,
        cart_items.quantity,
        products.id AS product_id,
        products.title,
        products.description,
        products.price,
        products.stock,
        (
            SELECT file_path
            FROM product_media
            WHERE product_media.product_id = products.id
            LIMIT 1
        ) AS file_path
    FROM cart_items
    JOIN products ON cart_items.product_id = products.id
    WHERE cart_items.user_id = ?
    ORDER BY cart_items.created_at DESC
");

$stmt->execute([$userId]);
$cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total = 0;

foreach ($cartItems as $item) {
    $total += $item['price'] * $item['quantity'];
}

$discount = $_SESSION['checkout_discount'] ?? 0;
$discountMessage = $_SESSION['discount_message'] ?? '';

$discountAmount = $total * ($discount / 100);
$finalTotal = $total - $discountAmount;

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Warenkorb</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2>Warenkorb</h2>

    <?php if (empty($cartItems)): ?>

        <div class="card" style="text-align:center;">
            <h3>Dein Warenkorb ist leer</h3>
            <p>Füge Produkte aus dem Campus Shop hinzu.</p>

            <a href="shop.php">
                <button>Zum Shop</button>
            </a>
        </div>

    <?php else: ?>

        <div class="cards">

            <?php foreach ($cartItems as $item): ?>

                <div class="card">

                    <?php if (!empty($item['file_path'])): ?>
                        <img
                            src="/<?= htmlspecialchars($item['file_path']) ?>"
                            style="
                                width:100%;
                                height:170px;
                                object-fit:cover;
                                border-radius:16px;
                                margin-bottom:15px;
                            "
                        >
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($item['title']) ?></h3>

                    <p><?= htmlspecialchars($item['description']) ?></p>

                    <p>Preis: <?= htmlspecialchars($item['price']) ?> €</p>

                    <p>Lagerbestand: <?= htmlspecialchars($item['stock']) ?></p>

                    <p>
                        Zwischensumme:
                        <strong>
                            <?= number_format($item['price'] * $item['quantity'], 2) ?> €
                        </strong>
                    </p>

                    <form method="POST">
                        <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">

                        <input
                            type="number"
                            name="quantity"
                            min="1"
                            max="<?= htmlspecialchars($item['stock']) ?>"
                            value="<?= htmlspecialchars($item['quantity']) ?>"
                            required
                        >

                        <button type="submit" name="update_quantity">
                            Menge aktualisieren
                        </button>
                    </form>

                    <form method="POST">
                        <input type="hidden" name="cart_id" value="<?= htmlspecialchars($item['cart_id']) ?>">

                        <button
                            type="submit"
                            name="remove_item"
                            onclick="return confirm('Produkt aus dem Warenkorb entfernen?')"
                        >
                            Entfernen
                        </button>
                    </form>

                </div>

            <?php endforeach; ?>

        </div>

        <div class="card" style="text-align:center; max-width:500px; margin:30px auto;">
            <h3>Rabatt & Gesamtbetrag</h3>

            <form method="POST">
                <input
                    type="text"
                    name="coupon"
                    placeholder="Coupon Code"
                >

                <label style="display:block; margin-top:12px;">
                    <input type="checkbox" name="student_discount">
                    Studentenrabatt anwenden
                </label>

                <button type="submit" name="apply_discount">
                    Rabatt anwenden
                </button>
            </form>

            <?php if (!empty($discountMessage)): ?>
                <p style="color:#bbf7d0;">
                    <?= htmlspecialchars($discountMessage) ?>
                </p>
            <?php endif; ?>

            <p>Zwischensumme: <?= number_format($total, 2) ?> €</p>
            <p>Rabatt: <?= number_format($discountAmount, 2) ?> €</p>

            <p style="font-size:24px;">
                <strong><?= number_format($finalTotal, 2) ?> €</strong>
            </p>

            <a href="checkout.php">
                <button>Zur Kasse</button>
            </a>
        </div>

    <?php endif; ?>

</section>
</main>

</body>
</html>