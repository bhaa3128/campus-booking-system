<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {

    $productId = $_POST['product_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO orders (user_id, product_id) VALUES (?, ?)");
    $stmt->execute([$userId, $productId]);

    header("Location: shop.php");
    exit;
}


//$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
//$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Campus Shop</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>
    <section class="services">
        <h2>Campus Shop</h2>

        <div class="cards" id="products-container"></div>
    </section>
</main>

<script>
function loadProducts() {
    fetch('api/products.php')
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('products-container');
            container.innerHTML = '';

            products.forEach(product => {
    container.innerHTML += `
       container.innerHTML += `
    container.innerHTML += `
    <div class="card">
        <img src="/${product.file_path ?? 'assets/images/default.png'}" 
             style="width:100%; height:150px; object-fit:cover; border-radius:10px;">

        <h3>${product.title}</h3>
        <p>${product.description}</p>
        <p>${product.price} €</p>
        <p>Lagerbestand: ${product.stock}</p>

        <button onclick="buyProduct(${product.id})">Kaufen</button>

        <br><br>

        <a href="product.php?id=${product.id}">
            <button>Details</button>
        </a>
    </div>
`;
});
        });
}

function buyProduct(productId) {
    fetch('api/buy.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ product_id: productId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast("Produkt gekauft");
            loadProducts();
        } else {
            showToast(data.message);
        }
    });
}

function showToast(message) {
    const toast = document.createElement('div');
    toast.innerText = message;
    toast.style.position = 'fixed';
    toast.style.bottom = '20px';
    toast.style.right = '20px';
    toast.style.background = '#333';
    toast.style.color = '#fff';
    toast.style.padding = '10px 20px';
    toast.style.borderRadius = '10px';
    toast.style.zIndex = '9999';

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 2000);
}

loadProducts();
</script>

</body>
</html>