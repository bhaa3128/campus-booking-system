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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die('Ungültige Anfrage.');
    }

    if (isset($_POST['add_product'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $price = $_POST['price'];
        $stock = $_POST['stock'];

        $stmt = $pdo->prepare("
            INSERT INTO products (title, description, price, stock)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $price, $stock]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_product'])) {
        $productId = $_POST['product_id'];

        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$productId]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['update_product_stock'])) {
        $productId = $_POST['product_id'];
        $stock = $_POST['stock'];

        $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
        $stmt->execute([$stock, $productId]);

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

    if (isset($_POST['change_role'])) {
        $userId = $_POST['user_id'];
        $role = $_POST['role'];

        $stmt = $pdo->prepare("UPDATE users SET role = ? WHERE id = ?");
        $stmt->execute([$role, $userId]);

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_user'])) {
        $userId = $_POST['user_id'];

        if ($userId != $_SESSION['user_id']) {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$userId]);
        }

        header("Location: admin.php");
        exit;
    }

    if (isset($_POST['delete_service'])) {
        $serviceId = $_POST['service_id'];

        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$serviceId]);

        header("Location: admin.php");
        exit;
    }
}

$products = $pdo->query("
    SELECT *
    FROM products
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$rooms = $pdo->query("
    SELECT rooms.*, buildings.name AS building_name
    FROM rooms
    JOIN buildings ON rooms.building_id = buildings.id
    ORDER BY buildings.id ASC, rooms.room_number ASC
")->fetchAll(PDO::FETCH_ASSOC);

$users = $pdo->query("
    SELECT *
    FROM users
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

$roomBookings = $pdo->query("
    SELECT room_bookings.*, users.first_name, users.last_name, users.email,
           rooms.room_number, buildings.name AS building_name
    FROM room_bookings
    JOIN users ON room_bookings.user_id = users.id
    JOIN rooms ON room_bookings.room_id = rooms.id
    JOIN buildings ON rooms.building_id = buildings.id
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

$services = $pdo->query("
    SELECT *
    FROM services
    ORDER BY id DESC
")->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2>Admin Panel</h2>

    <h2>Produkt hinzufügen</h2>

    <form method="POST" class="auth-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

        <input type="text" name="title" placeholder="Produktname" required>
        <input type="text" name="description" placeholder="Beschreibung" required>
        <input type="number" step="0.01" name="price" placeholder="Preis" required>
        <input type="number" name="stock" placeholder="Bestand" min="0" required>

        <button type="submit" name="add_product">
            Produkt hinzufügen
        </button>
    </form>

    <h2>Produkte verwalten</h2>

    <div class="cards">
        <?php foreach ($products as $product): ?>
            <div class="card">
                <h3><?= htmlspecialchars($product['title']) ?></h3>
                <p><?= htmlspecialchars($product['description']) ?></p>
                <p><?= htmlspecialchars($product['price']) ?> €</p>
                <p>Bestand: <?= htmlspecialchars($product['stock']) ?></p>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                    <input type="number" name="stock" value="<?= htmlspecialchars($product['stock']) ?>" min="0" required>

                    <button type="submit" name="update_product_stock">
                        Bestand speichern
                    </button>
                </form>

                <form method="POST" enctype="multipart/form-data" class="admin-upload-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                    <label class="custom-file-button">
                        Produktbild auswählen
                        <input type="file" name="product_image" accept="image/png, image/jpeg, image/jpg" required>
                    </label>

                    <button type="submit" name="upload_product_image">
                        Produktbild hochladen
                    </button>
                </form>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">

                    <button
                        type="submit"
                        name="delete_product"
                        onclick="return confirm('Produkt wirklich löschen?')"
                    >
                        Produkt löschen
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Zimmer verwalten</h2>

    <div class="cards">
        <?php foreach ($rooms as $room): ?>
            <div class="card">
                <h3>Zimmer <?= htmlspecialchars($room['room_number']) ?></h3>

                <p><strong>Gebäude:</strong> <?= htmlspecialchars($room['building_name']) ?></p>
                <p><strong>Typ:</strong> <?= htmlspecialchars($room['room_type']) ?></p>
                <p><strong>Preis:</strong> <?= htmlspecialchars($room['price']) ?> €</p>
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

                <form method="POST" enctype="multipart/form-data" class="admin-upload-form">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="room_id" value="<?= htmlspecialchars($room['id']) ?>">

                    <label class="custom-file-button">
                        Galeriebild auswählen
                        <input type="file" name="room_image" accept="image/png, image/jpeg, image/jpg" required>
                    </label>

                    <button type="submit" name="upload_room_image">
                        Galeriebild hochladen
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Benutzer verwalten</h2>

    <div class="cards">
        <?php foreach ($users as $user): ?>
            <div class="card">
                <h3><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h3>
                <p><?= htmlspecialchars($user['email']) ?></p>
                <p>Rolle: <?= htmlspecialchars($user['role']) ?></p>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">

                    <select name="role">
                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>

                    <button type="submit" name="change_role">
                        Rolle ändern
                    </button>
                </form>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($user['id']) ?>">

                    <button
                        type="submit"
                        name="delete_user"
                        onclick="return confirm('User wirklich löschen?')"
                    >
                        Benutzer löschen
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Zimmerbuchungen</h2>

    <div class="cards">
        <?php foreach ($roomBookings as $booking): ?>
            <div class="card">
                <h3>Zimmer <?= htmlspecialchars($booking['room_number']) ?></h3>
                <p><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></p>
                <p><?= htmlspecialchars($booking['email']) ?></p>
                <p>Gebäude: <?= htmlspecialchars($booking['building_name']) ?></p>
                <p>Status: <?= htmlspecialchars($booking['status']) ?></p>
                <small>
                    Von <?= htmlspecialchars($booking['start_date']) ?>
                    bis <?= htmlspecialchars($booking['end_date']) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Einrichtungsbuchungen</h2>

    <div class="cards">
        <?php foreach ($facilityBookings as $booking): ?>
            <div class="card">
                <h3><?= htmlspecialchars($booking['facility_name']) ?></h3>
                <p><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></p>
                <p><?= htmlspecialchars($booking['email']) ?></p>
                <p>Gebäude: <?= htmlspecialchars($booking['building_name']) ?></p>
                <p>Status: <?= htmlspecialchars($booking['status']) ?></p>
                <small>
                    Gebucht am: <?= htmlspecialchars($booking['booking_date']) ?>
                </small>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>Alte Services verwalten</h2>

    <div class="cards">
        <?php foreach ($services as $service): ?>
            <div class="card">
                <h3><?= htmlspecialchars($service['title']) ?></h3>
                <p><?= htmlspecialchars($service['description']) ?></p>
                <p><?= htmlspecialchars($service['price']) ?> €</p>

                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
                    <input type="hidden" name="service_id" value="<?= htmlspecialchars($service['id']) ?>">

                    <button type="submit" name="delete_service">
                        Service löschen
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

</section>
</main>

</body>
</html>