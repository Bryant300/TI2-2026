<?php
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI2</title>
    <link rel="stylesheet" href="css/style.css">
    <script src="js/jquery-3.7.1.min.js"></script>
</head>

<body>

    <?php if (!isset($paginationHtml))
        $paginationHtml = ''; ?>

    <header>
        <h1>TI2 2026 </h1>
        <img src="https://tse2.mm.bing.net/th/id/OIP.1QEo143gogvxQ2jKRLRNwgHaHa?r=0&rs=1&pid=ImgDetMain&o=7&rm=3"
            alt="Logo TI2" class="logo">
        <button id="toggle-theme"> Dark Mode</button>

    </header>

    <main>
        <!-- Zone de feedback (succès / erreur après soumission PHP) -->
        <?php if (!empty($feedbackMessage)): ?>
            <div class="feedback <?= htmlspecialchars($feedbackType) ?>">
                <?= htmlspecialchars($feedbackMessage) ?>
            </div>
        <?php endif; ?>

        <!-- Formulaire d'ajout d'un message -->
        <section class="form-section">
            <h2>Laisser un message</h2>


            <!-- Zone de messages d'erreur / succès jQuery -->
            <div id="messages"></div>

            <form id="guestbook-form" method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstname">Prénom <span class="required">*</span></label>
                        <input type="text" id="firstname" name="firstname" placeholder="Ex : Marie"
                            value="<?= isset($_POST['firstname']) ? htmlspecialchars($_POST['firstname']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lastname">Nom <span class="required">*</span></label>
                        <input type="text" id="lastname" name="lastname" placeholder="Ex : Dupont"
                            value="<?= isset($_POST['lastname']) ? htmlspecialchars($_POST['lastname']) : '' ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="usermail">Email <span class="required">*</span></label>
                        <input type="email" id="usermail" name="usermail" placeholder="Ex : marie.dupont@example.be"
                            value="<?= isset($_POST['usermail']) ? htmlspecialchars($_POST['usermail']) : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">Téléphone belge <span class="required">*</span></label>
                        <input type="tel" id="phone" name="phone" placeholder="Ex : 0470123456"
                            value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="postcode">Code postal belge <span class="required">*</span></label>
                    <input type="number" id="postcode" name="postcode" placeholder="Ex : 1000" min="1000" max="9999"
                        title="Le code postal belge doit être un nombre entre 1000 et 9999"
                        value="<?= isset($_POST['postcode']) ? htmlspecialchars($_POST['postcode']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="message">Votre message <span class="required">*</span></label>
                    <textarea id="message" name="message" rows="5" maxlength="300"
                        placeholder="Ce que vous avez pensé de votre visite..."><?= isset($_POST['message']) ? htmlspecialchars($_POST['message']) : '' ?></textarea>
                    <!-- Compteur de caractères (Bonus) -->
                    <span id="char-counter" class="char-counter">
                        <?= isset($_POST['message']) ? mb_strlen($_POST['message']) : 0 ?> / 300 caractères
                    </span>
                </div>

                <button type="submit" class="submit-btn"> Publier le message</button>
            </form>
        </section>

        <!-- Section d'affichage des messages -->
        <section class="messages-section">
            <h2>Les messages précédents</h2>

            <?php
            $nb = count($guestbook);
            if ($nb === 0): ?>
                <h3>Pas encore de message — soyez le premier !</h3>
            <?php elseif ($nb === 1): ?>
                <h3>Il y a 1 message</h3>
            <?php else: ?>
                <h3>Il y a <?= $nbTotalMessages ?> message<?= $nbTotalMessages > 1 ? 's' : '' ?></h3>
            <?php endif; ?>

            <!-- Pagination en haut (BONUS) -->
            <?= $paginationHtml ?>

            <!-- Liste des messages -->
            <?php if ($nb > 0): ?>
                <ul class="guestbook-list">
                    <?php foreach ($guestbook as $entry): ?>
                        <li class="guestbook-entry">
                            <div class="entry-header">
                                <strong class="entry-name">
                                    <?= htmlspecialchars($entry['firstname']) ?>
                                    <?= htmlspecialchars($entry['lastname']) ?>
                                </strong>
                                <em class="entry-date">
                                    <?php
                                    // Format français demandé : "Le ( 27/04/2026 à 10h29 )"
                                    echo 'Le ( ' . date('d/m/Y à H\hi', strtotime($entry['datemessage'])) . ' )';
                                    ?>
                                </em>
                            </div>
                            <p class="entry-message">
                                <!-- nl2br active le retour automatique à la ligne (Bonus) -->
                                <?= nl2br(htmlspecialchars($entry['message'])) ?>
                            </p>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- Pagination en bas (BONUS) -->
            <?= $paginationHtml ?>
        </section>
    </main>

    <footer>
        <p>TI2 Web 2026 &mdash;Bryan Benois </p>
    </footer>


    <script>
        // Compteur de caractères : mise à jour au chargement de la page
        document.addEventListener('DOMContentLoaded', function () {
            var textarea = document.getElementById('message');
            var counter = document.getElementById('char-counter');
            if (textarea && counter) {
                var updateCounter = function () {
                    counter.textContent = textarea.value.length + ' / 300 caractères';
                };
                updateCounter();
                textarea.addEventListener('input', updateCounter);
            }
        });
    </script>
    <script src="js/validation.js"></script>
</body>

</html>