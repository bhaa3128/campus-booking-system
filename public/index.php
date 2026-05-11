<?php
session_start();

require_once __DIR__ . '/../app/Models/Database.php';

$pdo = Database::connect();

$totalRooms = $pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
$freeRooms = $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'frei'")->fetchColumn();
$totalBuildings = $pdo->query("SELECT COUNT(*) FROM buildings")->fetchColumn();
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

$featuredCategories = $pdo->query("
    SELECT *
    FROM room_categories
    ORDER BY price ASC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

$featuredProducts = $pdo->query("
    SELECT products.*,
        (
            SELECT file_path
            FROM product_media
            WHERE product_media.product_id = products.id
            LIMIT 1
        ) AS image_path
    FROM products
    ORDER BY id DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Campus Booking System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="toast-container" id="toastContainer"></div>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>

    <section class="home-hero">
        <div class="home-hero-content">
            <span class="home-badge">Campus Living · Booking · Shop</span>

            <h1>
                Dein digitales Zuhause
                <span class="gradient-text">auf dem Campus</span>
            </h1>

            <p>
                Finde dein Zimmer, buche Einrichtungen, entdecke Angebote,
                nutze Studentenrabatte und verwalte alles bequem online.
            </p>

            <div class="home-actions">
                <a href="rooms.php"><button>Zimmer ansehen</button></a>
                <a href="shop.php"><button>Campus Shop öffnen</button></a>
                <a href="facilities.php"><button>Einrichtungen buchen</button></a>
            </div>
        </div>

        <div class="home-hero-media">
            <div class="video-card floating-card">
                <div class="video-placeholder">
                    ▶
                </div>

                <h3>Willkommensvideo</h3>
                <p>Campus Booking kurz erklärt.</p>

                <div class="home-mini-progress">
                    <span>Online Buchung</span>
                    <div><strong style="width:92%;"></strong></div>
                </div>
            </div>
        </div>
    </section>
    <section class="premium-campus-section">

    <div class="premium-campus-header">

        <span class="premium-campus-badge">
            Modern Student Living
        </span>

        <h2>
            Modernes Studentenwohnheim
            in Erfurt, Deutschland
        </h2>

        <p>
            Ab <span>€599</span> pro Monat,
            all-inclusive Wohnen für Studierende.
        </p>

    </div>

    <div class="premium-booking-bar">

    <div class="booking-input">
        <span>SEMESTER</span>

        <input
            type="date"
            id="startDate"
        >
    </div>

    <div class="booking-input">
        <span>ENDE</span>

        <input
            type="date"
            id="endDate"
        >
    </div>

    <div class="booking-input">
        <span>GÄSTE</span>

        <select id="guestCount">
            <option value="1">1 Gast</option>
            <option value="2">2 Gäste</option>
            <option value="3">3 Gäste</option>
            <option value="4">4 Gäste</option>
        </select>
    </div>

    <div class="booking-input">
        <span>PROMOCODE</span>

        <input
            type="text"
            id="promoCode"
            placeholder="Optional"
        >
    </div>

    <button
        class="premium-search-button"
        id="heroSearchButton"
    >
        🔍
    </button>

</div>

    <div class="premium-campus-grid">

        <div class="premium-main-image">
            <img src="/uploads/rooms/1778077889_room.jpg">
        </div>

        <div class="premium-side-image">
            <img src="/uploads/rooms/1778077889_room.jpg">
        </div>

        <div class="premium-side-image">
            <img src="/uploads/rooms/1778077889_room.jpg">
        </div>

        <div class="premium-side-image">
            <img src="/uploads/rooms/1778077889_room.jpg">
        </div>

        <div class="premium-side-image">
            <img src="/uploads/rooms/1778077889_room.jpg">
        </div>

    </div>

</section>

    <section class="home-stats">
        <div class="card stat-card">
            <h3 class="counter" data-target="<?= htmlspecialchars($totalRooms) ?>">0</h3>
            <p>Zimmer insgesamt</p>
        </div>

        <div class="card stat-card">
            <h3 class="counter" data-target="<?= htmlspecialchars($freeRooms) ?>">0</h3>
            <p>Freie Zimmer</p>
        </div>

        <div class="card stat-card">
            <h3 class="counter" data-target="<?= htmlspecialchars($totalBuildings) ?>">0</h3>
            <p>Wohnheime</p>
        </div>

        <div class="card stat-card">
            

<h3 class="counter" data-target="<?= htmlspecialchars($totalProducts) ?>">0</h3>
            <p>Shop-Produkte</p>
        </div>
    </section>

    <section class="announcement-banner">
        <span>🔥 Neu:</span>
        <p>Studentenrabatt, moderne Zimmerkategorien und digitale Buchungen jetzt verfügbar.</p>
        <a href="student_verification.php">Jetzt verifizieren</a>
    </section>

    <section class="home-section">
        <h2>Schnellzugriff</h2>

        <div class="cards">
            <a href="rooms.php" class="home-link-card card">
                <h3>🏠 Zimmer finden</h3>
                <p>Vergleiche Zimmerkategorien und starte deine Anmeldung.</p>
            </a>

            <a href="facilities.php" class="home-link-card card">
                <h3>🏢 Einrichtungen buchen</h3>
                <p>Buche Lernräume, Fitnessbereiche oder Gemeinschaftsräume.</p>
            </a>

            <a href="shop.php" class="home-link-card card">
                <h3>🛒 Campus Shop</h3>
                <p>Entdecke Produkte für dein Studentenleben.</p>
            </a>

            <a href="meine_buchungen.php" class="home-link-card card">
                <h3>📅 Meine Buchungen</h3>
                <p>Behalte Reservierungen und Bestellungen im Blick.</p>
            </a>
        </div>
    </section>

    <section class="home-section">
        <h2>Beliebte Zimmerkategorien</h2>

        <div class="cards">
            <?php foreach ($featuredCategories as $category): ?>
                <div class="card feature-room-card">

                    <?php if (!empty($category['image_path'])): ?>
                        <img src="/<?= htmlspecialchars($category['image_path']) ?>">
                    <?php else: ?>
                        <div class="room-placeholder">🏠</div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($category['name']) ?></h3>

                    <p><?= htmlspecialchars($category['description']) ?></p>

                    <p>
                        <strong><?= number_format((float)$category['price'], 2) ?> €</strong>
                    </p>

                    <p>
                        Verfügbar:
                        <?= htmlspecialchars($category['available_rooms']) ?>
                        /
                        <?= htmlspecialchars($category['total_rooms']) ?>
                    </p>

                    <a href="rooms.php">
                        <button>Zimmer ansehen</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="home-section">
        <h2>Campus Shop Highlights</h2>

        <div class="cards">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="card feature-room-card">

                    <?php if (!empty($product['image_path'])): ?>
                        <img src="/<?= htmlspecialchars($product['image_path']) ?>">
                    <?php else: ?>
                        <div class="room-placeholder">🛒</div>
                    <?php endif; ?>

                    <h3><?= htmlspecialchars($product['title']) ?></h3>

                    <p><?= htmlspecialchars($product['description']) ?></p>

                    <p>
                        <strong><?= number_format((float)$product['price'], 2) ?> €</strong>
                    </p>

                    <a href="shop.php">
                        <button>Zum Shop</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="home-section">
        <h2>Aktuelle Highlights</h2>

        <div class="cards">
            <div class="card promo-card">
                <span class="promo-label">Neu</span>
                <h3>Moderne Zimmerkategorien</h3>
                <p>Wähle direkt deine gewünschte Kategorie statt aus hunderten Zimmern.</p>
                <a href="rooms.php"><button>Zimmer entdecken</button></a>
            </div>

            <div class="card promo-card">
                <span class="promo-label">Angebot</span>
                <h3>Studentenrabatt</h3>
                <p>Verifiziere deinen Studentenstatus und sichere dir Vorteile.</p>
                <a href="student_verification.php"><button>Student verifizieren</button></a>
            </div>

            <div class="card promo-card">
                <span class="promo-label">Campus</span>
                <h3>Alles an einem Ort</h3>
                <p>Zimmer, Buchungen, Shop und Services sind verbunden.</p>
                <a href="services.php"><button>Angebote ansehen</button></a>
            </div>
        </div>
    </section>

    <section class="how-section">
    <h2>So funktioniert es</h2>

    <div class="how-grid">
        <div class="how-card">
            <span>1</span>
            <h3>Kategorie wählen</h3>
            <p>Suche dir die passende Zimmerkategorie aus.</p>
        </div>

        <div class="how-card">
            <span>2</span>
            <h3>Anmeldung senden</h3>
            <p>Fülle deine Daten aus und schicke die Anfrage ab.</p>
        </div>

        <div class="how-card">
            <span>3</span>
            <h3>Status verfolgen</h3>
            <p>Behalte Buchungen und Bestellungen online im Blick.</p>
        </div>
    </div>
</section>

    <section class="home-section">
        <h2>Warum Campus Booking?</h2>

        <div class="cards">
            <div class="card">
                <h3>⚡ Schnell</h3>
                <p>Reservierungen und Bestellungen laufen digital.</p>
            </div>

            <div class="card">
                <h3>🔍 Transparent</h3>
                <p>Preise, Kategorien, Verfügbarkeiten und Status sind klar.</p>
            </div>

            <div class="card">
                <h3>🎓 Für Studierende</h3>
                <p>Das System ist speziell für das Campusleben gedacht.</p>
            </div>

            <div class="card">
                <h3>📱 Modern</h3>
                <p>Responsive, interaktiv und mit Admin-Dashboard.</p>
            </div>
        </div>
    </section>

    <section class="home-faq">
        <h2>Häufige Fragen</h2>

        <details class="card">
            <summary class="admin-sub-summary">Kann ich direkt ein Zimmer buchen?</summary>
            <p>Du wählst eine Kategorie und sendest eine Anmeldung ab.</p>
        </details>

        <details class="card">
            <summary class="admin-sub-summary">Gibt es Studentenrabatt?</summary>
            <p>Ja, nach erfolgreicher Studentenverifizierung.</p>
        </details>

        <details class="card">
            <summary class="admin-sub-summary">Kann ich meine Buchungen sehen?</summary>
            <p>Ja, unter „Meine Buchungen“ findest du deine aktuellen Reservierungen.</p>
        </details>
    </section>
    <section class="home-location">
    <div class="location-header">
        <span class="home-badge">● Umgebung & Anbindung</span>
        <h2>Anbindung</h2>
        <p>
            Alles Wichtige in deiner Nähe: Bahnhof, Universität,
            Supermarkt, Park und Stadtzentrum.
        </p>
    </div>

    <div class="location-grid">

        <div class="location-item">
            <span class="location-icon">🚆</span>
            <div>
                <p>Fußweg zum nächsten Bahnhof</p>
                <h3>29min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🍽️</span>
            <div>
                <p>Fußweg zum nächsten Restaurant</p>
                <h3>06min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🌳</span>
            <div>
                <p>Fußweg zum nächsten Park</p>
                <h3>11min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🎓</span>
            <div>
                <p>Busfahrt zum Südcampus der Universität</p>
                <h3>20min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🚌</span>
            <div>
                <p>Busfahrt zum Nordcampus der Universität</p>
                <h3>25min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🛒</span>
            <div>
                <p>Fußweg zum nächsten Supermarkt</p>
                <h3>02min.</h3>
            </div>
        </div>

        <div class="location-item">
            <span class="location-icon">🏙️</span>
            <div>
                <p>Fußweg zum Stadtzentrum</p>
                <h3>20min.</h3>
            </div>
        </div>

    </div>

    <a href="https://www.google.com/maps" target="_blank">
        <button class="location-button">Siehe Karte →</button>
    </a>
</section>
                        <section class="home-events">
    <div class="events-header">
        <span class="home-badge">● Campus Community</span>
        <h2>Wie wir Spaß haben</h2>
        <p>
            Nicht nur wohnen: Campus Booking verbindet Zimmer, Services
            und echte Community-Erlebnisse.
        </p>
    </div>

    <div class="events-grid">

        <article class="event-card">
            <div class="event-image event-image-one">
                <span>01</span>
            </div>

            <h3>Community Choice</h3>

            <p>
                Gemeinsame Events, Spieleabende und Aktivitäten,
                bei denen Studierende zusammenkommen und neue Kontakte knüpfen.
            </p>
        </article>

        <article class="event-card">
            <div class="event-image event-image-two">
                <span>02</span>
            </div>

            <h3>Food Club</h3>

            <p>
                Gemeinsames Kochen, Essen und Entdecken verschiedener Kulturen
                direkt im Campus-Alltag.
            </p>
        </article>

        <article class="event-card">
            <div class="event-image event-image-three">
                <span>03</span>
            </div>

            <h3>Campus Party</h3>

            <p>
                Kleine Veranstaltungen, Semesterstarts und gemütliche Abende
                machen das Campusleben lebendig.
            </p>
        </article>

    </div>
</section>

<section class="home-social-wall">
    <div class="social-wall-top">
        <span class="home-badge">● Do it social</span>
        <span>Neueste Beiträge aus dem Campusleben</span>
    </div>

    <h2>
        Folge uns und lass dich vom Campusleben inspirieren
    </h2>

    <div class="social-post-grid">

        <article class="social-post-card">
            <div class="social-post-image social-img-one">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Welcome Night</h3>
                <p>Neue Studierende, neue Freundschaften und ein gemeinsamer Start ins Semester.</p>
                <small>#campusbooking #studentlife</small>
                <span class="social-date">08 Oktober 2025</span>
            </div>
        </article>

        <article class="social-post-card">
            <div class="social-post-image social-img-two">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Living Space</h3>
                <p>Moderne Zimmer, Lernbereiche und Orte zum Entspannen direkt auf dem Campus.</p>
                <small>#living #campuslife</small>
                <span class="social-date">23 Juni 2025</span>
            </div>
        </article>

        <article class="social-post-card">
            <div class="social-post-image social-img-three">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Food Club</h3>
                <p>Gemeinsam kochen, essen und neue Kulturen kennenlernen.</p>
                <small>#foodclub #community</small>
                <span class="social-date">09 Mai 2025</span>
            </div>
        </article>

        <article class="social-post-card">
            <div class="social-post-image social-img-four">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Study Vibes</h3>
                <p>Ruhige Lernbereiche, gute Atmosphäre und produktive Tage.</p>
                <small>#study #studentliving</small>
                <span class="social-date">28 März 2025</span>
            </div>
        </article>

        <article class="social-post-card">
            <div class="social-post-image social-img-five">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Good People</h3>
                <p>Campusleben bedeutet mehr als Wohnen: Menschen, Events und Gemeinschaft.</p>
                <small>#community #friends</small>
                <span class="social-date">28 Februar 2025</span>
            </div>
        </article>

        <article class="social-post-card">
            <div class="social-post-image social-img-six">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Coffee Spots</h3>
                <p>Die besten Orte für Kaffee, Gespräche und kleine Pausen im Alltag.</p>
                <small>#coffee #campus</small>
                <span class="social-date">20 Februar 2025</span>
            </div>
        </article>

        <article class="social-post-card social-extra-post">
            <div class="social-post-image social-img-seven">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Pizza Night</h3>
                <p>Gemeinsame Abende in der Küche machen das Wohnheim lebendig.</p>
                <small>#pizza #studenthome</small>
                <span class="social-date">18 Februar 2025</span>
            </div>
        </article>

        <article class="social-post-card social-extra-post">
            <div class="social-post-image social-img-eight">
                <span class="social-play">▶</span>
            </div>
            <div class="social-post-body">
                <h3>Game Evening</h3>
                <p>Spieleabend mit Nachbarn, Snacks und guter Stimmung.</p>
                <small>#games #campusevents</small>
                <span class="social-date">14 Februar 2025</span>
            </div>
        </article>

    </div>

    <button type="button" id="loadMoreSocial" class="social-load-button">
        Load More
    </button>
</section>
<section class="home-section">
    <h2>Was Studierende sagen</h2>

    <div class="cards">
        <div class="card testimonial-card">
            <p>
                „Die Zimmerauswahl ist übersichtlich und die Buchung ging sehr schnell.“
            </p>
            <h3>— Lina, Informatik</h3>
        </div>

        <div class="card testimonial-card">
            <p>
                „Ich finde gut, dass Zimmer, Shop und Buchungen an einem Ort sind.“
            </p>
            <h3>— Jonas, Medieninformatik</h3>
        </div>

        <div class="card testimonial-card">
            <p>
                „Das System wirkt modern und ist viel einfacher als Papierformulare.“
            </p>
            <h3>— Sara, Angewandte Informatik</h3>
        </div>
    </div>
</section>

    <section class="home-newsletter card">
        <h2>Bereit für dein Campusleben?</h2>
        <p>Starte jetzt mit deiner Zimmerauswahl oder entdecke Services rund um deinen Alltag.</p>

        <div class="home-actions center">
            <a href="rooms.php"><button>Jetzt Zimmer ansehen</button></a>
            <a href="facilities.php"><button>Einrichtungen entdecken</button></a>
        </div>
    </section>

</main>

<script src="/assets/js/admin.js"></script>

<script>
window.addEventListener('load', () => {
    if (typeof showToast === 'function') {
        showToast('Willkommen bei Campus Booking 🏠', 'info');
    }
});
</script>
<div class="floating-actions">
    <a href="rooms.php">🏠</a>
    <a href="shop.php">🛒</a>
    <a href="help.php">❓</a>
</div>
</body>
</html>