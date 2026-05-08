<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// تغيير الاسم
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_name'])) {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];

    $stmt = $pdo->prepare("UPDATE users SET first_name = ?, last_name = ? WHERE id = ?");
    $stmt->execute([$firstName, $lastName, $userId]);

    header("Location: settings.php");
    exit;
}

// تغيير الصورة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    $image = $_FILES['image'];

    if ($image['error'] === 0) {

        $fileName = time() . '_' . basename($image['name']);
        $filePath = 'uploads/' . $fileName;

        move_uploaded_file($image['tmp_name'], $filePath);

        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$filePath, $userId]);

        $_SESSION['profile_image'] = $filePath;

        header("Location: settings.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Einstellungen</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">
        <h2>Einstellungen</h2>

        <div class="card">

            <?php if (!empty($user['profile_image'])): ?>
                <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="profile-image">
            <?php endif; ?>

            <h3>Name ändern</h3>

            <form method="POST" class="auth-form">
                <input type="text" name="first_name" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                <input type="text" name="last_name" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                <button type="submit" name="update_name">Speichern</button>
            </form>

            <h3>Profilbild ändern</h3>

            <form method="POST" enctype="multipart/form-data" class="auth-form">
                <input type="file" name="image" required>
                <button type="submit">Upload</button>
            </form>

        </div>
    </section>
</main>

</body>
</html>