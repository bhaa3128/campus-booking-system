<?php
session_start();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Kontakt | Campus Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body class="contact-basecamp-page">

<?php include 'includes/navbar.php'; ?>

<main class="contact-basecamp-main">

    <section class="contact-basecamp-hero">
        <h1>Kontaktiere uns</h1>

        <div class="contact-basecamp-grid">
            <div class="contact-basecamp-text">
                <h2>Kontaktiere uns</h2>

                <p>
                    Wenn du mit uns in Kontakt treten möchtest,
                    fülle einfach das Formular aus und unser Team
                    wird sich schnellstmöglich mit dir in Verbindung setzen.
                </p>

                <p>
                    Suchst du nach einer direkten Ansprechperson für
                    Partnerschaften, Karrieremöglichkeiten oder PR-Anfragen?
                    Unten findest du die passenden Kontaktbereiche.
                </p>
            </div>

            <form class="contact-basecamp-form">
                <label>Dein Name *</label>
                <input type="text" required>

                <label>E-Mail-Adresse *</label>
                <input type="email" required>

                <label>Telefonnummer</label>
                <input type="text">

                <label>Ort *</label>
                <select required>
                    <option value="">Please Select</option>
                    <option>Erfurt</option>
                    <option>Göttingen</option>
                    <option>Leipzig</option>
                    <option>Berlin</option>
                </select>

                <label>Deine Nachricht</label>
                <textarea placeholder="Hello!"></textarea>

                <label class="contact-basecamp-check">
                    <input type="checkbox" required>
                    Ich habe die Bedingungen und Konditionen gelesen und akzeptiere sie
                </label>

                <button type="submit" class="contact-red-btn">Senden</button>
            </form>
        </div>
    </section>

    <section class="contact-basecamp-accordion-section">

        <div class="contact-basecamp-kicker">
            <span>● Nimm Kontakt auf</span>
            <span>Mit Campus Booking</span>
        </div>

        <div class="contact-basecamp-accordion-grid">

            <h2>Finde die richtigen Ansprechpartner</h2>

            <div class="contact-basecamp-accordion">

                <details>
                    <summary>Büros.</summary>
                    <div class="contact-basecamp-detail-grid">
                        <div>
                            <h4>Erfurt</h4>
                            <p>Campus Booking<br>Altonaer Straße 25<br>99085 Erfurt</p>
                        </div>

                        <div>
                            <h4>Support</h4>
                            <p>support@campus-booking.de<br>+49 123 456789</p>
                        </div>
                    </div>
                </details>

                <details>
                    <summary>Impressum.</summary>
                    <div class="contact-basecamp-detail-grid">
                        <div>
                            <h4>Unternehmen</h4>
                            <p>Campus Booking System<br>Erfurt, Deutschland</p>
                        </div>

                        <div>
                            <h4>E-Mail</h4>
                            <p>info@campus-booking.de</p>
                        </div>

                        <div>
                            <h4>Geschäftsführer</h4>
                            <p>Campus Booking Team</p>
                        </div>
                    </div>
                </details>

                <details>
                    <summary>Partner.</summary>
                    <p>
                        Für Kooperationen mit Hochschulen, Wohnheimen oder Servicepartnern
                        erreichst du uns hier.
                    </p>
                    <h4>E-Mail</h4>
                    <p>partners@campus-booking.de</p>
                </details>

                <details>
                    <summary>Universitäten.</summary>
                    <p>
                        Wir arbeiten gerne mit Hochschulen zusammen, um Studierenden
                        den Einstieg in eine neue Stadt zu erleichtern.
                    </p>
                    <h4>E-Mail</h4>
                    <p>university@campus-booking.de</p>
                </details>

                <details>
                    <summary>PR & Media.</summary>
                    <p>
                        Für Medienanfragen, Interviews oder Projektinformationen
                        kontaktiere bitte unser PR-Team.
                    </p>
                    <h4>E-Mail</h4>
                    <p>media@campus-booking.de</p>
                </details>

                <details>
                    <summary>Karriere.</summary>
                    <p>
                        Du möchtest am Campus Booking System mitarbeiten?
                        Dann kontaktiere uns gerne.
                    </p>
                    <h4>E-Mail</h4>
                    <p>careers@campus-booking.de</p>
                </details>

            </div>
        </div>
    </section>

    <section class="contact-basecamp-cards">

        <div class="contact-basecamp-card">
            <span>◎</span>
            <h3>My Campus</h3>
            <p>
                Bist du Bewohner und brauchst Unterstützung?
                Nutze dein Profil, um Buchungen und Nachrichten zu verwalten.
            </p>
            <a href="profile.php">Anmeldung zum Portal →</a>
        </div>

        <div class="contact-basecamp-card">
            <span>?</span>
            <h3>Hilfe-Center</h3>
            <p>
                Du hast eine kurze Frage? Schau zuerst in unser Hilfe-Center.
                Viele Antworten findest du dort sofort.
            </p>
            <a href="help.php">Hilfe-Center besuchen →</a>
        </div>

        <div class="contact-basecamp-image-card">
            <img src="assets/images/community.jpg" alt="Campus Community">
            <a href="rooms.php">Standorte erkunden</a>
        </div>

    </section>

</main>

</body>
</html>