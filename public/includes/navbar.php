<header class="main-header">
    <nav class="modern-navbar">

        <a href="index.php" class="nav-logo">
            <span class="logo-icon">🏠</span>
            <span>Campus Booking</span>
        </a>

        <ul class="nav-links">
            <li><a href="index.php">Startseite</a></li>
            <li><a href="buildings.php">Wohnheime</a></li>
            <li><a href="rooms.php">Zimmer</a></li>
            <li><a href="facilities.php">Einrichtungen</a></li>
            <li><a href="services.php">Angebote</a></li>
            <li><a href="shop.php">Shop</a></li>
            <li><a href="meine_buchungen.php">Meine Buchungen</a></li>
            <li><a href="cart.php">Warenkorb</a>‚</li>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <li><a href="admin.php">Admin</a></li>
            <?php endif; ?>
        </ul>
<div style="display:flex; align-items:center; gap:14px;">

    <button id="themeToggle" class="theme-toggle-navbar" type="button">

        🌙

    </button>
        <div class="profile-menu">
            <button class="profile-button">
                <?php if (!empty($_SESSION['profile_image'])): ?>
                    <img src="<?= htmlspecialchars($_SESSION['profile_image']) ?>" class="avatar">
                <?php else: ?>
                    <span class="avatar default">👤</span>
                <?php endif; ?>
            </button>

            <div class="profile-dropdown">
                <a href="profile.php">Mein Profil</a>
                <a href="settings.php">Einstellungen</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>

    </nav>
</header>