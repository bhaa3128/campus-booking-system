<?php
session_start();
?>

<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <title>Hilfe-Center | Campus Booking</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include __DIR__ . '/includes/navbar.php'; ?>

<main>

    <section class="help-hero">
        <span class="home-badge">Support · FAQ · Campus Booking</span>

        <h1>Hilfe-Center</h1>

        <p>
            Antworten auf die wichtigsten Fragen rund um Buchung,
            Einzug, Wohnen, Auszug und Services.
        </p>
    </section>

    <section class="help-center">

        <details class="help-category">
            <summary>
                <span>Buchung</span>
                <strong>+</strong>
            </summary>

            <div class="help-questions">
                <details>
                    <summary>Wie kann ich ein Zimmer buchen?</summary>
                    <p>
                        Du gehst auf „Zimmer“, wählst eine passende Kategorie
                        und sendest deine Anmeldung über das Formular ab.
                    </p>
                </details>

                <details>
                    <summary>Was ist im Zimmerpreis enthalten?</summary>
                    <p>
                        Der Preis umfasst die Nutzung des Zimmers. Weitere Details
                        findest du in der Beschreibung der jeweiligen Kategorie.
                    </p>
                </details>

                <details>
                    <summary>Kann ich meine Buchung verfolgen?</summary>
                    <p>
                        Ja. Unter „Meine Buchungen“ kannst du deine aktuellen
                        Zimmer- und Einrichtungsbuchungen ansehen.
                    </p>
                </details>

                <details>
                    <summary>Welche Zahlungsmethoden gibt es?</summary>
                    <p>
                        Je nach Bereich stehen PayPal, Kreditkarte, SEPA oder Barzahlung
                        vor Ort zur Verfügung.
                    </p>
                </details>
            </div>
        </details>

        <details class="help-category">
            <summary>
                <span>Einzug</span>
                <strong>+</strong>
            </summary>

            <div class="help-questions">
                <details>
                    <summary>Wann kann ich einziehen?</summary>
                    <p>
                        Das hängt vom gewählten Startdatum und der Verfügbarkeit
                        deiner Zimmerkategorie ab.
                    </p>
                </details>

                <details>
                    <summary>Welche Dokumente benötige ich?</summary>
                    <p>
                        In der Regel benötigst du deine persönlichen Daten,
                        Kontaktdaten und bei Studentenrabatten einen Nachweis.
                    </p>
                </details>

                <details>
                    <summary>Kann ich Möbel mitbringen?</summary>
                    <p>
                        Das hängt von der Zimmerart ab. Details stehen in der
                        Beschreibung der jeweiligen Kategorie.
                    </p>
                </details>
            </div>
        </details>

        <details class="help-category">
            <summary>
                <span>Wohnen</span>
                <strong>+</strong>
            </summary>

            <div class="help-questions">
                <details>
                    <summary>Was ist mit Lärm?</summary>
                    <p>
                        Rücksichtnahme ist wichtig. Gemeinschaftsbereiche sollen
                        so genutzt werden, dass andere Bewohner nicht gestört werden.
                    </p>
                </details>

                <details>
                    <summary>Gibt es Gemeinschaftsräume?</summary>
                    <p>
                        Ja. Über „Einrichtungen“ kannst du verfügbare Räume
                        und Bereiche ansehen und buchen.
                    </p>
                </details>

                <details>
                    <summary>Darf ich Gäste einladen?</summary>
                    <p>
                        Gäste sind grundsätzlich möglich, solange die Hausregeln
                        eingehalten werden.
                    </p>
                </details>
            </div>
        </details>

        <details class="help-category">
            <summary>
                <span>Auszug</span>
                <strong>+</strong>
            </summary>

            <div class="help-questions">
                <details>
                    <summary>Wie läuft der Auszug ab?</summary>
                    <p>
                        Nach Ablauf des Buchungszeitraums endet deine Reservierung.
                        Weitere Details können über die Verwaltung geklärt werden.
                    </p>
                </details>

                <details>
                    <summary>Kann ich früher ausziehen?</summary>
                    <p>
                        Das hängt von den Bedingungen deiner Buchung ab.
                        Kontaktiere dafür den Support.
                    </p>
                </details>
            </div>
        </details>

    </section>

    <section class="help-contact">
        <div>
            <h2>Need something else?</h2>
            <p>
                Wenn deine Frage nicht beantwortet wurde,
                kannst du uns direkt kontaktieren.
            </p>

            <a href="contact.php">
                <button>Kontakt aufnehmen</button>
            </a>
        </div>
    </section>

</main>

<script src="/assets/js/admin.js"></script>

</body>
</html>