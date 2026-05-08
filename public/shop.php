<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

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

        <p style="color:#cbd5e1; max-width:750px; margin:0 auto 30px;">
            Kaufe praktische Produkte für deinen Campus-Alltag: Snacks, Zubehör,
            Lernmaterialien und mehr.
        </p>

        <div class="cards" id="products-container"></div>
    </section>
</main>

<script>
function loadProducts() {
    fetch('/api/products.php')
        .then(response => response.json())
        .then(products => {
            const container = document.getElementById('products-container');
            container.innerHTML = '';

            products.forEach(product => {
                const imagePath = product.file_path
                    ? '/' + product.file_path
                    : 'assets/images/default.png';

                const stockText = product.stock > 0
                    ? `Lagerbestand: ${product.stock}`
                    : 'Nicht verfügbar';

                const buyButton = product.stock > 0
                    ? `<button onclick="buyProduct(${product.id})">Kaufen</button>`
                    : `<button disabled>Nicht verfügbar</button>`;

                container.innerHTML += `
                    <div class="card">
                        <img
                            src="${imagePath}"
                            style="width:100%; height:180px; object-fit:cover; border-radius:16px; margin-bottom:15px;"
                        >

                        <h3>${product.title}</h3>

                        <p>${product.description}</p>

                        <p><strong>${product.price} €</strong></p>

                        <p>${stockText}</p>

                        ${buyButton}

                        <a href="product.php?id=${product.id}">
                            <button>Details ansehen</button>
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
    toast.style.background = 'rgba(15,23,42,0.95)';
    toast.style.color = '#fff';
    toast.style.padding = '12px 20px';
    toast.style.borderRadius = '14px';
    toast.style.zIndex = '9999';
    toast.style.boxShadow = '0 15px 35px rgba(0,0,0,0.35)';
    toast.style.fontWeight = '800';

    document.body.appendChild(toast);

    setTimeout(() => {
        toast.remove();
    }, 2200);
}

loadProducts();
</script>

</body>
</html>