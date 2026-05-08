<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $image = $_FILES['image'];

    if ($image['error'] === 0) {
        $fileName = time() . '_' . basename($image['name']);
        $filePath = 'uploads/' . $fileName;

        move_uploaded_file($image['tmp_name'], $filePath);

        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$filePath, $_SESSION['user_id']]);

        header("Location: profile.php");
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {

    $image = $_FILES['image'];

    if ($image['error'] === 0) {

        $fileName = time() . '_' . $image['name'];
        $filePath = 'uploads/' . $fileName;

        move_uploaded_file($image['tmp_name'], $filePath);

        $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
        $stmt->execute([$filePath, $_SESSION['user_id']]);

        header("Location: profile.php");
        exit;
    }

}

$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$_SESSION['profile_image'] = $user['profile_image'];


?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Profil</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <h2>Mein Profil</h2>

    <div class="card">
        <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name']) ?></p>
        <p><strong>Nachname:</strong> <?= htmlspecialchars($user['last_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Rolle:</strong> <?= htmlspecialchars($user['role']) ?></p>

        <?php if (!empty($user['profile_image'])): ?>
    <img src="<?= htmlspecialchars($user['profile_image']) ?>" width="120" style="border-radius:50%;">

    <?php if (!empty($user['profile_image'])): ?>
    <img src="<?= htmlspecialchars($user['profile_image']) ?>" class="profile-image">
<?php else: ?>
    <div class="profile-image-placeholder">👤</div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="auth-form">
    <input type="file" name="image" accept="image/png, image/jpeg, image/jpg" required>
    <button type="submit">Profilbild speichern</button>
</form>
<?php endif; ?>
    </div>

    <form method="POST" enctype="multipart/form-data">
    <input type="file" name="image" required>
    <button type="submit">Upload Image</button>
</form>

</main>

</body>
</html>