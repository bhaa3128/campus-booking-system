<?php

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$firstName, $lastName, $email, $password]);

    $message = "Registrierung erfolgreich!";
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Registrieren</title>
</head>
<body>

<h2>Registrieren</h2>

<?php if ($message): ?>
    <p><?= $message ?></p>
<?php endif; ?>

<form method="POST">
    <input type="text" name="first_name" placeholder="Vorname" required><br>
    <input type="text" name="last_name" placeholder="Nachname" required><br>
    <input type="email" name="email" placeholder="E-Mail" required><br>
    <input type="password" name="password" placeholder="Passwort" required><br>
    <button type="submit">Registrieren</button>
</form>

</body>
</html>