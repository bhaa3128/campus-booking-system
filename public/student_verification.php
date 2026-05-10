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

    $universityEmail = $_POST['university_email'];

    $filePath = null;

    if (!empty($_FILES['student_file']['name'])) {

        $file = $_FILES['student_file'];

        if ($file['error'] === 0) {

            $fileName =
                time() . '_' . basename($file['name']);

            $uploadDir =
                __DIR__ . '/uploads/student_verifications/';

            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $serverPath =
                $uploadDir . $fileName;

            $filePath =
                'uploads/student_verifications/' . $fileName;

            move_uploaded_file(
                $file['tmp_name'],
                $serverPath
            );
        }
    }

    $stmt = $pdo->prepare("
        INSERT INTO student_verifications
        (
            user_id,
            university_email,
            student_file,
            status
        )
        VALUES (?, ?, ?, 'pending')
    ");

    $stmt->execute([
        $userId,
        $universityEmail,
        $filePath
    ]);

    header("Location: profile.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Studentenverifizierung</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <h2>Studentenverifizierung</h2>

    <div class="card" style="max-width:700px; margin:0 auto 30px; text-align:left;">
        <p>
            Verifiziere deinen Studentenstatus, um Studentenrabatte
            und exklusive Angebote zu erhalten.
        </p>

        <ul>
            <li>Universitäts-E-Mail eingeben</li>
            <li>Studentenausweis hochladen</li>
            <li>Admin überprüft deine Anfrage</li>
        </ul>
    </div>

    <form method="POST" enctype="multipart/form-data" class="auth-form">

        <input
            type="email"
            name="university_email"
            placeholder="Universitäts-E-Mail"
            required
        >

        <label class="custom-file-button">
            Studentenausweis auswählen

            <input
                type="file"
                name="student_file"
                accept="image/png, image/jpeg, image/jpg, application/pdf"
                required
            >
        </label>

        <button type="submit">
            Verifizierung senden
        </button>

    </form>

</section>
</main>

</body>
</html>