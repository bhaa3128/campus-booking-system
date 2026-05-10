<?php

session_start();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: services.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    if (
    isset($_POST['ajax_action']) &&
    $_POST['ajax_action'] === 'update_product_stock'
) {
    $stmt = $pdo->prepare("
        UPDATE products
        SET stock = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['stock'],
        $_POST['product_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Bestand wurde aktualisiert.',
        'newStock' => $_POST['stock']
    ]);

    exit;
}
    if (
    isset($_POST['ajax_action']) &&
    $_POST['ajax_action'] === 'change_role'
) {
    $stmt = $pdo->prepare("
        UPDATE users
        SET role = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['role'],
        $_POST['user_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Rolle wurde aktualisiert.'
    ]);

    exit;
}
    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'delete_user') {

    if ($_POST['user_id'] != $_SESSION['user_id']) {

        $stmt = $pdo->prepare("
            DELETE FROM users
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['user_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Benutzer wurde gelöscht.'
        ]);

    } else {

        echo json_encode([
            'success' => false,
            'message' => 'Du kannst dich nicht selbst löschen.'
        ]);

    }

    exit;
}
    header('Content-Type: application/json');

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo json_encode([
            'success' => false,
            'message' => 'Ungültige Anfrage.'
        ]);
        exit;
    }

    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'update_order_status') {
        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['status'],
            $_POST['order_id']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Bestellstatus erfolgreich aktualisiert.'
        ]);
        exit;
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'delete_product') {

    $stmt = $pdo->prepare("
        DELETE FROM products
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['product_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Produkt wurde gelöscht.'
    ]);

    exit;
}
    if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'toggle_coupon') {
    $newStatus = $_POST['is_active'] == 1 ? 0 : 1;

    $stmt = $pdo->prepare("
        UPDATE coupons
        SET is_active = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $newStatus,
        $_POST['coupon_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => $newStatus == 1
            ? 'Coupon wurde aktiviert.'
            : 'Coupon wurde deaktiviert.',
        'newStatus' => $newStatus
    ]);
    exit;
}
if (isset($_POST['ajax_action']) && $_POST['ajax_action'] === 'delete_coupon') {

    $stmt = $pdo->prepare("
        DELETE FROM coupons
        WHERE id = ?
    ");

    $stmt->execute([
        $_POST['coupon_id']
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Coupon wurde gelöscht.'
    ]);

    exit;
}
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ungültige Anfrage.');
    }

    if (isset($_POST['add_product'])) {
        $stmt = $pdo->prepare("
            INSERT INTO products (title, description, price, stock)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $_POST['title'],
            $_POST['description'],
            $_POST['price'],
            $_POST['stock']
        ]);

        header("Location: admin.php?success=product_added");
        exit;
    }

    if (isset($_POST['delete_product'])) {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$_POST['product_id']]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['update_product_stock'])) {
        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$_POST['stock'], $_POST['product_id']]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['upload_product_image'])) {
        $productId = $_POST['product_id'];
        $image = $_FILES['product_image'];

        if ($image['error'] === 0) {
            $fileName = time() . '_' . basename($image['name']);
            $uploadDir = __DIR__ . '/uploads/products/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $serverPath = $uploadDir . $fileName;
            $dbPath = 'uploads/products/' . $fileName;

            if (move_uploaded_file($image['tmp_name'], $serverPath)) {
                $stmt = $pdo->prepare("
                    INSERT INTO product_media (product_id, media_type, file_path)
                    VALUES (?, 'image', ?)
                ");
                $stmt->execute([$productId, $dbPath]);
            }
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['update_category'])) {
        $stmt = $pdo->prepare("
            UPDATE room_categories
            SET name = ?,
                description = ?,
                price = ?,
                total_rooms = ?,
                available_rooms = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['name'],
            $_POST['description'],
            $_POST['price'],
            $_POST['total_rooms'],
            $_POST['available_rooms'],
            $_POST['category_id']
        ]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['upload_category_image'])) {
        $categoryId = $_POST['category_id'];
        $image = $_FILES['category_image'];

        if ($image['error'] === 0) {
            $fileName = time() . '_' . basename($image['name']);
            $uploadDir = __DIR__ . '/uploads/categories/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $serverPath = $uploadDir . $fileName;
            $dbPath = 'uploads/categories/' . $fileName;

            if (move_uploaded_file($image['tmp_name'], $serverPath)) {
                $stmt = $pdo->prepare("
                    UPDATE room_categories
                    SET image_path = ?
                    WHERE id = ?
                ");
                $stmt->execute([$dbPath, $categoryId]);
            }
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['upload_room_main_image'])) {
        $roomId = $_POST['room_id'];
        $image = $_FILES['room_image'];

        if ($image['error'] === 0) {
            $fileName = time() . '_' . basename($image['name']);
            $uploadDir = __DIR__ . '/uploads/rooms/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $serverPath = $uploadDir . $fileName;
            $dbPath = 'uploads/rooms/' . $fileName;

            if (move_uploaded_file($image['tmp_name'], $serverPath)) {
                $stmt = $pdo->prepare("
                    UPDATE rooms
                    SET main_image = ?
                    WHERE id = ?
                ");
                $stmt->execute([$dbPath, $roomId]);
            }
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['upload_room_image'])) {
        $roomId = $_POST['room_id'];
        $image = $_FILES['room_image'];

        if ($image['error'] === 0) {
            $fileName = time() . '_' . basename($image['name']);
            $uploadDir = __DIR__ . '/uploads/rooms/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $serverPath = $uploadDir . $fileName;
            $dbPath = 'uploads/rooms/' . $fileName;

            if (move_uploaded_file($image['tmp_name'], $serverPath)) {
                $stmt = $pdo->prepare("
                    INSERT INTO room_media (room_id, media_type, file_path)
                    VALUES (?, 'image', ?)
                ");
                $stmt->execute([$roomId, $dbPath]);
            }
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['add_coupon'])) {
        $assignedUserId = !empty($_POST['assigned_user_id'])
            ? $_POST['assigned_user_id']
            : null;

        $stmt = $pdo->prepare("
            INSERT INTO coupons
            (
                code,
                discount_percent,
                valid_from,
                valid_until,
                max_uses,
                used_count,
                assigned_user_id,
                created_by,
                is_active
            )
            VALUES (?, ?, ?, ?, ?, 0, ?, ?, ?)
        ");

        $stmt->execute([
            $_POST['code'],
            $_POST['discount_percent'],
            $_POST['valid_from'],
            $_POST['valid_until'],
            $_POST['max_uses'],
            $assignedUserId,
            $_SESSION['user_id'],
            isset($_POST['is_active']) ? 1 : 0
        ]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['toggle_coupon'])) {
        $newStatus = $_POST['is_active'] == 1 ? 0 : 1;

        $stmt = $pdo->prepare("
            UPDATE coupons
            SET is_active = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $newStatus,
            $_POST['coupon_id']
        ]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_coupon'])) {
        $stmt = $pdo->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->execute([$_POST['coupon_id']]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['approve_student'])) {
        $verificationId = $_POST['verification_id'];
        $verificationUserId = $_POST['user_id'];

        $stmt = $pdo->prepare("
            UPDATE student_verifications
            SET status = 'approved'
            WHERE id = ?
        ");
        $stmt->execute([$verificationId]);

        $userStmt = $pdo->prepare("
            UPDATE users
            SET is_verified_student = 1
            WHERE id = ?
        ");
        $userStmt->execute([$verificationUserId]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['reject_student'])) {
        $verificationId = $_POST['verification_id'];

        $stmt = $pdo->prepare("
            UPDATE student_verifications
            SET status = 'rejected'
            WHERE id = ?
        ");
        $stmt->execute([$verificationId]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['change_role'])) {
        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$_POST['role'], $_POST['user_id']]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_user'])) {
        if ($_POST['user_id'] != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$_POST['user_id']]);
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['update_order_status'])) {
        $stmt = $pdo->prepare("
            UPDATE orders
            SET status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $_POST['status'],
            $_POST['order_id']
        ]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_service'])) {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$_POST['service_id']]);

        header("Location: admin.php");
        exit;
    }
}

$products = $pdo->query("
    SELECT *
    FROM products
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$roomCategories = $pdo->query("
    SELECT *
    FROM room_categories
    ORDER BY price ASC
")->fetchAll(PDO::FETCH_ASSOC);

$rooms = $pdo->query("
    SELECT 
        rooms.*,
        buildings.name AS building_name,
        floors.floor_number,
        room_categories.name AS category_name
    FROM rooms
    JOIN buildings ON rooms.building_id = buildings.id
    LEFT JOIN floors ON rooms.floor_id = floors.id
    LEFT JOIN room_categories ON rooms.category_id = room_categories.id
    ORDER BY buildings.id ASC, floors.floor_number ASC, rooms.room_number ASC
")->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query("
    SELECT *
    FROM users
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$coupons = $pdo->query("
    SELECT coupons.*, users.email AS assigned_email
    FROM coupons
    LEFT JOIN users ON coupons.assigned_user_id = users.id
    ORDER BY coupons.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$roomBookings = $pdo->query("
    SELECT 
        room_bookings.*,
        users.first_name,
        users.last_name,
        users.email,
        users.is_verified_student,
        rooms.room_number,
        rooms.status AS room_status,
        buildings.name AS building_name,
        room_categories.name AS category_name
    FROM room_bookings
    JOIN users ON room_bookings.user_id = users.id
    JOIN rooms ON room_bookings.room_id = rooms.id
    JOIN buildings ON rooms.building_id = buildings.id
    LEFT JOIN room_categories ON rooms.category_id = room_categories.id
    ORDER BY room_bookings.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$facilityBookings = $pdo->query("
    SELECT facility_bookings.*, users.first_name, users.last_name, users.email,
           facilities.name AS facility_name, buildings.name AS building_name
    FROM facility_bookings
    JOIN users ON facility_bookings.user_id = users.id
    JOIN facilities ON facility_bookings.facility_id = facilities.id
    JOIN buildings ON facilities.building_id = buildings.id
    ORDER BY facility_bookings.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$orders = $pdo->query("
    SELECT orders.*, users.email
    FROM orders
    JOIN users ON orders.user_id = users.id
    ORDER BY orders.id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$studentVerifications = $pdo->query("
    SELECT student_verifications.*, users.first_name, users.last_name, users.email
    FROM student_verifications
    JOIN users ON student_verifications.user_id = users.id
    ORDER BY student_verifications.created_at DESC
")->fetchAll(PDO::FETCH_ASSOC);

$services = $pdo->query("
    SELECT *
    FROM services
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$roomsByBuilding = [];

foreach ($rooms as $room) {
    $buildingName = $room['building_name'];
    $floorNumber = $room['floor_number'] ?? 'Ohne Etage';

    $roomsByBuilding[$buildingName][$floorNumber][] = $room;
}

$ordersByUser = [];

foreach ($orders as $order) {
    $email = $order['email'] ?? 'Unbekannt';
    $ordersByUser[$email][] = $order;
}

$totalUsers = count($users);
$totalProducts = count($products);
$totalOrders = count($orders);
$totalRoomBookings = count($roomBookings);
$totalFacilityBookings = count($facilityBookings);

$totalRevenue = 0;

foreach ($orders as $order) {
    $totalRevenue += (float) ($order['total_price'] ?? 0);
}

$availableRooms = 0;
$totalRooms = count($rooms);
$verifiedStudents = 0;

foreach ($rooms as $room) {
    if ($room['status'] === 'frei') {
        $availableRooms++;
    }
}

foreach ($users as $user) {
    if (!empty($user['is_verified_student'])) {
        $verifiedStudents++;
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
<div class="toast-container" id="toastContainer"></div>
<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

<h2>Admin Panel</h2>

<div class="admin-layout">

<aside class="admin-sidebar">
    <a href="#products"> Produkte verwalten</a>
    <a href="#categories"> Zimmerkategorien</a>
    <a href="#rooms"> Zimmer verwalten</a>
    <a href="#room-bookings"> Zimmerbuchungen</a>
    <a href="#coupons"> Coupons verwalten</a>
    <a href="#students"> Studentenverifizierungen</a>
    <a href="#users"> Benutzer verwalten</a>
    <a href="#facility-bookings"> Einrichtungsbuchungen</a>
    <a href="#orders"> Bestellungen</a>
    <a href="#services"> Alte Services</a>
</aside>
<div class="admin-content">

<div class="cards">

    <div class="card">
        <h3>Benutzer</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= htmlspecialchars($totalUsers) ?>
        </p>
    </div>

    <div class="card">
        <h3>Bestellungen</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= htmlspecialchars($totalOrders) ?>
        </p>
    </div>

    <div class="card">
        <h3>Umsatz</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= number_format($totalRevenue, 2) ?> €
        </p>
    </div>

    <div class="card">
        <h3>Zimmerbuchungen</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= htmlspecialchars($totalRoomBookings) ?>
        </p>
    </div>

    <div class="card">
        <h3>Freie Zimmer</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= htmlspecialchars($availableRooms) ?> / <?= htmlspecialchars($totalRooms) ?>
        </p>
    </div>

    <div class="card">
        <h3>Verifizierte Studenten</h3>
        <p style="font-size:34px; font-weight:900;">
            <?= htmlspecialchars($verifiedStudents) ?>
        </p>
    </div>

</div>
<div class="cards admin-charts">

    <div class="card">
        <h3>Bestellungen Übersicht</h3>
        <canvas id="ordersChart"></canvas>
    </div>

    <div class="card">
        <h3>Zimmer Status</h3>
        <canvas id="roomsChart"></canvas>
    </div>

</div>
<details id="products" class="card admin-section">
    <summary class="admin-summary">
        Produkte verwalten
    </summary>

    <div style="margin-top:25px;">

        <details class="card" style="max-width:520px; margin:0 auto 25px;">
    <summary class="admin-sub-summary" style="text-align:center;">
        + Neues Produkt hinzufügen
    </summary>>

            <form method="POST" class="auth-form" style="margin-top:22px;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <input type="text" name="title" placeholder="Produktname" required>
                <input type="text" name="description" placeholder="Beschreibung" required>
                <input type="number" step="0.01" name="price" placeholder="Preis" required>
                <input type="number" name="stock" placeholder="Bestand" min="0" required>

                <button type="submit" name="add_product">
                    Produkt hinzufügen
                </button>
            </form>
        </details>

        <?php foreach ($products as $product): ?>

            <details class="card product-card" style="margin-bottom:16px;">
                <summary style="cursor:pointer; font-size:20px; font-weight:900;">
                    <?= htmlspecialchars($product['title']) ?>
                    —
                    <?= htmlspecialchars($product['price']) ?> €
                    —
                   Bestand:
<span class="product-stock-value">
    <?= htmlspecialchars($product['stock']) ?>
</span>
                </summary>

                <div style="margin-top:18px;">
                    <p><?= htmlspecialchars($product['description']) ?></p>

                   <form method="POST" class="ajax-update-stock-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="ajax_action" value="update_product_stock">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <input
        type="number"
        name="stock"
        value="<?= htmlspecialchars($product['stock']) ?>"
        min="0"
        required
    >

    <button type="submit">
        Bestand speichern
    </button>
</form>

                    <form method="POST" enctype="multipart/form-data" class="admin-upload-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                        <label class="drop-upload">
    <span>📸 Produktbild hier ablegen oder klicken</span>
    <br>
    <small>PNG, JPG oder JPEG</small>

    <input
        type="file"
        name="product_image"
        accept="image/png, image/jpeg, image/jpg"
        required
        hidden
    >
</label>

                        <button type="submit" name="upload_product_image">
                            Produktbild hochladen
                        </button>
                    </form>

                   <form method="POST" class="ajax-delete-product-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="ajax_action" value="delete_product">
    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

    <button type="submit">
        Produkt löschen
    </button>
</form>
                </div>
            </details>

        <?php endforeach; ?>

    </div>
</details>

<details id="categories" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Zimmerkategorien verwalten
    </summary>

    <div class="cards" style="margin-top:25px;">
        <?php foreach ($roomCategories as $category): ?>
            <details class="card">
                <summary style="cursor:pointer; font-size:22px; font-weight:900;">
                    <?= htmlspecialchars($category['name']) ?>
                    —
                    <?= htmlspecialchars($category['price']) ?> €
                    —
                    <?= htmlspecialchars($category['available_rooms']) ?> / <?= htmlspecialchars($category['total_rooms']) ?> verfügbar
                </summary>

                <div style="margin-top:20px;">

                    <?php if (!empty($category['image_path'])): ?>
                        <img src="/<?= htmlspecialchars($category['image_path']) ?>" style="width:100%; height:180px; object-fit:cover; border-radius:18px; margin-bottom:18px;">
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']) ?>">

                        <input type="text" name="name" value="<?= htmlspecialchars($category['name']) ?>" required>
                        <textarea name="description" required><?= htmlspecialchars($category['description']) ?></textarea>
                        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($category['price']) ?>" required>
                        <input type="number" name="total_rooms" value="<?= htmlspecialchars($category['total_rooms']) ?>" required>
                        <input type="number" name="available_rooms" value="<?= htmlspecialchars($category['available_rooms']) ?>" required>

                        <button type="submit" name="update_category">
                            Änderungen speichern
                        </button>
                    </form>

                    <form method="POST" enctype="multipart/form-data" class="admin-upload-form">
                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                        <input type="hidden" name="category_id" value="<?= htmlspecialchars($category['id']) ?>">

                        <label class="custom-file-button">
                            Neues Kategorie-Bild auswählen
                            <input type="file" name="category_image" accept="image/png, image/jpeg, image/jpg" required>
                        </label>

                        <button type="submit" name="upload_category_image">
                            Bild hochladen
                        </button>
                    </form>

                </div>
            </details>
        <?php endforeach; ?>
    </div>
</details>

<details id="rooms" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Zimmer verwalten
    </summary>

    <div style="margin-top:25px;">
        <?php foreach ($roomsByBuilding as $buildingName => $floorsList): ?>
            <details class="card">
                <summary style="cursor:pointer; font-size:22px; font-weight:900;">
                    <?= htmlspecialchars($buildingName) ?>
                </summary>

                <?php foreach ($floorsList as $floorNumber => $roomsList): ?>
                    <details class="card" style="margin-top:18px;">
                        <summary style="cursor:pointer; font-size:18px; font-weight:900;">
                            Etage <?= htmlspecialchars($floorNumber) ?>
                        </summary>

                        <div class="cards">
                            <?php foreach ($roomsList as $room): ?>
                                <div class="card">
                                    <h3>Zimmer <?= htmlspecialchars($room['room_number']) ?></h3>
                                    <p><strong>Kategorie:</strong> <?= htmlspecialchars($room['category_name'] ?? 'Keine Kategorie') ?></p>
                                    <p><strong>Status:</strong> <?= htmlspecialchars($room['status']) ?></p>

                                    <form method="POST" enctype="multipart/form-data" class="admin-upload-form">
                                        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                                        <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

                                        <label class="custom-file-button">
                                            Hauptbild auswählen
                                            <input type="file" name="room_image" accept="image/png, image/jpeg, image/jpg" required>
                                        </label>

                                        <button type="submit" name="upload_room_main_image">
                                            Hauptbild hochladen
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </details>
        <?php endforeach; ?>
    </div>
</details>

<details id="room-bookings" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Zimmerbuchungen
    </summary>

    <div style="margin-top:25px;">

        <div class="card" style="margin-bottom:20px;">
            <input
                type="text"
                id="roomBookingSearch"
                placeholder="Zimmerbuchung suchen nach Name, E-Mail, Zimmer, Gebäude, Kategorie oder Buchungs-ID..."
            >
        </div>

        <div class="card" style="overflow-x:auto;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Kunde</th>
                        <th>Zimmer</th>
                        <th>Gebäude</th>
                        <th>Kategorie</th>
                        <th>Zeitraum</th>
                        <th>Zahlung</th>
                        <th>Student</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($roomBookings as $booking): ?>
                        <tr class="room-booking-card"
                            data-search="<?= htmlspecialchars(
                                ($booking['id'] ?? '') . ' ' .
                                ($booking['first_name'] ?? '') . ' ' .
                                ($booking['last_name'] ?? '') . ' ' .
                                ($booking['email'] ?? '') . ' ' .
                                ($booking['room_number'] ?? '') . ' ' .
                                ($booking['building_name'] ?? '') . ' ' .
                                ($booking['category_name'] ?? '') . ' ' .
                                ($booking['payment_method'] ?? '') . ' ' .
                                ($booking['status'] ?? '')
                            ) ?>"
                        >
                            <td>#<?= htmlspecialchars($booking['id']) ?></td>

                            <td>
                                <strong><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></strong><br>
                                <small><?= htmlspecialchars($booking['email']) ?></small>
                            </td>

                            <td><?= htmlspecialchars($booking['room_number']) ?></td>
                            <td><?= htmlspecialchars($booking['building_name']) ?></td>
                            <td><?= htmlspecialchars($booking['category_name'] ?? 'Keine Kategorie') ?></td>

                            <td>
                                <?= htmlspecialchars($booking['start_date']) ?><br>
                                bis <?= htmlspecialchars($booking['end_date']) ?>
                            </td>

                            <td><?= htmlspecialchars($booking['payment_method'] ?? '-') ?></td>
                            <td><?= !empty($booking['is_verified_student']) ? 'Ja' : 'Nein' ?></td>
                            <td><?= htmlspecialchars($booking['status'] ?? 'offen') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    </div>
</details>

<details id="coupons" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Coupons verwalten
    </summary>

    <div style="margin-top:25px;">

        <details class="card" style="max-width:520px; margin:0 auto 25px;">
            <summary style="cursor:pointer; font-size:22px; font-weight:900; text-align:center;">
                + Neuen Coupon erstellen
            </summary>

            <form method="POST" class="auth-form" style="margin-top:22px;">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <input type="text" name="code" placeholder="Coupon Code" required>

                <input type="number" name="discount_percent" placeholder="Rabatt %" min="1" max="100" required>

                <label>Gültig ab</label>
                <input type="date" name="valid_from" required>

                <label>Gültig bis</label>
                <input type="date" name="valid_until" required>

                <input type="number" name="max_uses" placeholder="Maximale Nutzungen" min="1" required>

                <select name="assigned_user_id">
                    <option value="">Für alle Benutzer</option>

                    <?php foreach ($users as $user): ?>
                        <option value="<?= htmlspecialchars($user['id']) ?>">
                            <?= htmlspecialchars($user['email']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label style="margin-top:10px;">
                    <input type="checkbox" name="is_active" checked>
                    Coupon direkt aktivieren
                </label>

                <button type="submit" name="add_coupon">
                    Coupon erstellen
                </button>
            </form>
        </details>

        <div class="cards">
            <?php foreach ($coupons as $coupon): ?>
                <div class="card coupon-card">
                    <h3><?= htmlspecialchars($coupon['code']) ?></h3>

                    <p><strong>Rabatt:</strong> <?= htmlspecialchars($coupon['discount_percent']) ?>%</p>

                    <p>
                        <strong>Status:</strong>
                        <?= !empty($coupon['is_active']) ? 'Aktiv' : 'Inaktiv' ?>
                    </p>

                    <p>
                        <strong>Gültig:</strong>
                        <?= htmlspecialchars($coupon['valid_from'] ?? 'Nicht gesetzt') ?>
                        →
                        <?= htmlspecialchars($coupon['valid_until'] ?? 'Nicht gesetzt') ?>
                    </p>

                    <p>
                        <strong>Nutzungen:</strong>
                        <?= htmlspecialchars($coupon['used_count'] ?? 0) ?>
                        /
                        <?= htmlspecialchars($coupon['max_uses'] ?? 'Unbegrenzt') ?>
                    </p>

                    <p>
                        <strong>Benutzer:</strong>
                        <?= !empty($coupon['assigned_email'])
                            ? htmlspecialchars($coupon['assigned_email'])
                            : 'Alle Benutzer'
                        ?>
                    </p>

                    <form method="POST" class="ajax-coupon-toggle-form">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
    <input type="hidden" name="ajax_action" value="toggle_coupon">
    <input type="hidden" name="coupon_id" value="<?= htmlspecialchars($coupon['id']) ?>">
    <input type="hidden" name="is_active" value="<?= htmlspecialchars($coupon['is_active'] ?? 0) ?>">

    <button type="submit">
        <?= !empty($coupon['is_active']) ? 'Deaktivieren' : 'Aktivieren' ?>
    </button>
</form>

                    <form method="POST" class="ajax-delete-coupon-form">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
    >

    <input
        type="hidden"
        name="ajax_action"
        value="delete_coupon"
    >

    <input
        type="hidden"
        name="coupon_id"
        value="<?= htmlspecialchars($coupon['id']) ?>"
    >

    <button type="submit">
        Coupon löschen
    </button>

</form>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</details>

<details id="students" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Studentenverifizierungen
    </summary>

    <div style="margin-top:25px;">

        <div class="cards">
            <?php foreach ($studentVerifications as $verification): ?>

                <div class="card coupon-card">

                    <h3>
                        <?= htmlspecialchars($verification['first_name'] . ' ' . $verification['last_name']) ?>
                    </h3>

                    <p>
                        <strong>Email:</strong>
                        <?= htmlspecialchars($verification['email']) ?>
                    </p>

                    <p>
                        <strong>Uni-Mail:</strong>
                        <?= htmlspecialchars($verification['university_email']) ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars($verification['status']) ?>
                    </p>

                    <?php if (!empty($verification['student_file'])): ?>

                        <a
                            href="/<?= htmlspecialchars($verification['student_file']) ?>"
                            target="_blank"
                        >
                            Nachweis ansehen
                        </a>

                    <?php endif; ?>

                    <?php if ($verification['status'] === 'pending'): ?>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                            >

                            <input
                                type="hidden"
                                name="verification_id"
                                value="<?= htmlspecialchars($verification['id']) ?>"
                            >

                            <input
                                type="hidden"
                                name="user_id"
                                value="<?= htmlspecialchars($verification['user_id']) ?>"
                            >

                            <button type="submit" name="approve_student">
                                Student bestätigen
                            </button>

                        </form>

                        <form method="POST">

                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                            >

                            <input
                                type="hidden"
                                name="verification_id"
                                value="<?= htmlspecialchars($verification['id']) ?>"
                            >

                            <button type="submit" name="reject_student">
                                Ablehnen
                            </button>

                        </form>

                    <?php endif; ?>

                </div>

            <?php endforeach; ?>
        </div>

    </div>
</details>

<details id="users" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Benutzer verwalten
    </summary>

    <div style="margin-top:25px;">

        <div class="cards">
            <?php foreach ($users as $user): ?>

                <div class="card user-card">

                    <h3>
                        <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                    </h3>

                    <p>
                        <strong>Email:</strong>
                        <?= htmlspecialchars($user['email']) ?>
                    </p>

                    <p>
                        <strong>Rolle:</strong>
                        <?= htmlspecialchars($user['role']) ?>
                    </p>

                    <p>
                        <strong>Student verifiziert:</strong>
                        <?= !empty($user['is_verified_student']) ? 'Ja' : 'Nein' ?>
                    </p>

                    <form method="POST" class="ajax-change-role-form">

    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

    <input type="hidden" name="ajax_action" value="change_role">

    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">

    <select name="role">
        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>
            Student
        </option>

        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>
            Admin
        </option>
    </select>

    <button type="submit">
        Rolle ändern
    </button>

</form>

                    <form method="POST" class="ajax-delete-user-form">

    <input
        type="hidden"
        name="csrf_token"
        value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
    >

    <input
        type="hidden"
        name="ajax_action"
        value="delete_user"
    >

    <input
        type="hidden"
        name="user_id"
        value="<?= htmlspecialchars($user['id']) ?>"
    >

    <button type="submit">
        Benutzer löschen
    </button>

</form>

                </div>

            <?php endforeach; ?>
        </div>

    </div>
</details>

<details id="facility-bookings" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Einrichtungsbuchungen
    </summary>

    <div style="margin-top:25px;">

        <div class="cards">
            <?php foreach ($facilityBookings as $booking): ?>

                <div class="card">
                    <h3><?= htmlspecialchars($booking['facility_name']) ?></h3>

                    <p>
                        <strong>Name:</strong>
                        <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
                    </p>

                    <p>
                        <strong>Email:</strong>
                        <?= htmlspecialchars($booking['email']) ?>
                    </p>

                    <p>
                        <strong>Gebäude:</strong>
                        <?= htmlspecialchars($booking['building_name']) ?>
                    </p>

                    <p>
                        <strong>Status:</strong>
                        <?= htmlspecialchars($booking['status']) ?>
                    </p>

                    <small>
                        Gebucht am:
                        <?= htmlspecialchars($booking['booking_date']) ?>
                    </small>
                </div>

            <?php endforeach; ?>
        </div>

    </div>
</details>

<details id="orders" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Bestellungen
    </summary>

    <div style="margin-top:25px;">

        <div class="card" style="margin-bottom:25px;">
            <input
                type="text"
                id="orderSearch"
                placeholder="Bestellung suchen nach Name, E-Mail, Adresse oder ID..."
            >
        </div>

        <?php foreach ($ordersByUser as $email => $userOrders): ?>

            <details class="card order-user-group" style="margin-bottom:18px;">

                <summary style="cursor:pointer; font-size:22px; font-weight:900;">
                    <?= htmlspecialchars($email) ?>
                    —
                    <?= count($userOrders) ?> Bestellungen
                </summary>

                <div class="cards" style="margin-top:20px;">

                    <?php foreach ($userOrders as $order): ?>

                        <?php
                        $itemsStmt = $pdo->prepare("
                            SELECT order_items.*, products.title
                            FROM order_items
                            JOIN products ON order_items.product_id = products.id
                            WHERE order_items.order_id = ?
                        ");

                        $itemsStmt->execute([$order['id']]);

                        $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <div
                            class="card order-card"
                            data-search="<?= htmlspecialchars(
                                ($order['id'] ?? '') . ' ' .
                                ($order['full_name'] ?? '') . ' ' .
                                ($order['email'] ?? '') . ' ' .
                                ($order['street'] ?? '') . ' ' .
                                ($order['city'] ?? '') . ' ' .
                                ($order['postal_code'] ?? '') . ' ' .
                                ($order['payment_method'] ?? '')
                            ) ?>"
                        >

                            <h3>
                                Bestellung #<?= htmlspecialchars($order['id']) ?>
                            </h3>

                            <p>
                                <strong>Kunde:</strong>
                                <?= htmlspecialchars($order['full_name'] ?? 'Nicht angegeben') ?>
                            </p>

                            <p>
                                <strong>E-Mail:</strong>
                                <?= htmlspecialchars($order['email'] ?? 'Nicht angegeben') ?>
                            </p>

                            <p>
                                <strong>Adresse:</strong>
                                <?= htmlspecialchars($order['street'] ?? 'Nicht angegeben') ?>,
                                <?= htmlspecialchars($order['postal_code'] ?? '') ?>
                                <?= htmlspecialchars($order['city'] ?? '') ?>
                            </p>

                            <p>
                                <strong>Zahlungsmethode:</strong>
                                <?= htmlspecialchars($order['payment_method'] ?? 'Nicht angegeben') ?>
                            </p>

                            <p>
                                <strong>Gesamtpreis:</strong>
                                <?= number_format((float)($order['total_price'] ?? 0), 2) ?> €
                            </p>

                            <p>
                                <strong>Status:</strong>
                                <?= htmlspecialchars($order['status'] ?? 'Neu') ?>
                            </p>

                                <form method="POST" class="ajax-order-status-form" style="margin-top:15px;">
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                                >
                                <input
    type="hidden"
    name="ajax_action"
    value="update_order_status"
>

                                <input
                                    type="hidden"
                                    name="order_id"
                                    value="<?= htmlspecialchars($order['id']) ?>"
                                >

                                <select name="status">

                                    <option value="Neu" <?= ($order['status'] ?? '') === 'Neu' ? 'selected' : '' ?>>
                                        Neu
                                    </option>

                                    <option value="In Bearbeitung" <?= ($order['status'] ?? '') === 'In Bearbeitung' ? 'selected' : '' ?>>
                                        In Bearbeitung
                                    </option>

                                    <option value="Versandt" <?= ($order['status'] ?? '') === 'Versandt' ? 'selected' : '' ?>>
                                        Versandt
                                    </option>

                                    <option value="Abgeschlossen" <?= ($order['status'] ?? '') === 'Abgeschlossen' ? 'selected' : '' ?>>
                                        Abgeschlossen
                                    </option>

                                    <option value="Storniert" <?= ($order['status'] ?? '') === 'Storniert' ? 'selected' : '' ?>>
                                        Storniert
                                    </option>

                                </select>

                                <button type="submit" name="update_order_status">
                                    Status speichern
                                </button>

                            </form>

                            <hr style="border:none; border-top:1px solid rgba(255,255,255,0.12); margin:15px 0;">

                            <h4>Produkte</h4>

                            <?php if (empty($items)): ?>

                                <p>Keine Produkte gefunden.</p>

                            <?php else: ?>

                                <?php foreach ($items as $item): ?>

                                    <p>
                                        <?= htmlspecialchars($item['title']) ?>
                                        ×
                                        <?= htmlspecialchars($item['quantity']) ?>
                                        —
                                        <?= number_format((float)$item['price'], 2) ?> €
                                    </p>

                                <?php endforeach; ?>
                            <?php endif; ?>

                        </div>

                    <?php endforeach; ?>

                </div>

            </details>

        <?php endforeach; ?>
                    <div id="ordersPagination" class="pagination"></div>

    </div>
</details>

<details id="services" class="card admin-section">
    <summary style="cursor:pointer; font-size:28px; font-weight:900;">
        Alte Services verwalten
    </summary>

    <div style="margin-top:25px;">

        <div class="cards">

            <?php foreach ($services as $service): ?>

                <div class="card">

                    <h3>
                        <?= htmlspecialchars($service['title']) ?>
                    </h3>

                    <p>
                        <?= htmlspecialchars($service['description']) ?>
                    </p>

                    <p>
                        <strong>
                            <?= htmlspecialchars($service['price']) ?> €
                        </strong>
                    </p>

                    <form method="POST">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>"
                        >

                        <input
                            type="hidden"
                            name="service_id"
                            value="<?= htmlspecialchars($service['id']) ?>"
                        >

                        <button
                            type="submit"
                            name="delete_service"
                            onclick="return confirm('Service wirklich löschen?')"
                        >
                            Service löschen
                        </button>

                    </form>

                </div>

            <?php endforeach; ?>

        </div>

    </div>
</details>

</div>
</div>

</section>
</main>
<script src="/assets/js/admin.js"></script>
</body>
</html>