<?php

session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'];
        $_SESSION['role'] = $user['role'];

        header("Location: services.php");
        exit;
    }

    $message = "E-Mail oder Passwort ist falsch.";
}

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header>
    <nav>
        <h1>Campus Booking</h1>
        <ul>
            <li><a href="index.php">Startseite</a></li>
            <li><a href="services.php">Angebote</a></li>
            <li><a href="meine_buchungen.php">Meine Buchungen</a></li>
            <li><a href="register.php">Registrieren</a></li>
        </ul>
    </nav>
</header>

<main>
    <section class="services">
        <h2>Login</h2>

        <?php if ($message): ?>
            <p class="success-message"><?= htmlspecialchars($message) ?></p>
        <?php endif; ?>

        <form method="POST" class="auth-form">
            <input type="email" name="email" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Passwort" required>
            <button type="submit">Einloggen</button>
        </form>
    </section>
</main>

</body>
</html>