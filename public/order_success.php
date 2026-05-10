<?php

session_start();

?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Bestellung erfolgreich</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
<section class="services">

    <div class="card" style="
        max-width:700px;
        margin:60px auto;
        text-align:center;
        padding:50px;
    ">

        <h2 style="font-size:42px;">
            Bestellung erfolgreich 🎉
        </h2>

        <p style="
            font-size:18px;
            color:#cbd5e1;
            margin-top:20px;
        ">
            Vielen Dank für deine Bestellung.
        </p>

        <p style="
            color:#94a3b8;
            margin-top:10px;
        ">
            Deine Bestellung wurde erfolgreich gespeichert.
        </p>

        <a href="shop.php">
            <button style="margin-top:30px;">
                Zurück zum Shop
            </button>
        </a>

    </div>

</section>
</main>

</body>
</html>