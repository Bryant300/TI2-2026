<?php
# view/guestbookView.php
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>TI2 | Livre d'or</title>
    <link rel="icon" type="image/png" href="img/favicon.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header class="site-header">
        <div>
            <p class="site-kicker">Projet TI2</p>
            <h1>Livre d'or</h1>
        </div>
        <button type="button" id="toggle-theme">Dark Mode</button>
    </header>

    <main class="page">
        <a class="anchor-link" href="#messages-section">
            <img class="ancre" src="img/chat.png" alt="Aller aux commentaires">
        </a>
        <section class="form-section">
            <h2>Ajouter un message</h2>

            <?php if (!empty($info)): ?>
                <p class="alert success"><?= htmlspecialchars($info) ?></p>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <p class="alert error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <form method="post" action="" id="guestbook-form" novalidate>
                <div id="messages"></div>

                <div class="form-grid">
                    <div class="field">
                        <label for="firstname">Prenom</label>
                        <input type="text" name="firstname" id="firstname"
                            value="<?= htmlspecialchars($_POST["firstname"] ?? "") ?>">
                    </div>

                    <div class="field">
                        <label for="lastname">Nom</label>
                        <input type="text" name="lastname" id="lastname"
                            value="<?= htmlspecialchars($_POST["lastname"] ?? "") ?>">
                    </div>

                    <div class="field">
                        <label for="usermail">Email</label>
                        <input type="email" name="usermail" id="usermail"
                            value="<?= htmlspecialchars($_POST["usermail"] ?? "") ?>">
                    </div>

                    <div class="field">
                        <label for="phone">Telephone</label>
                        <input type="text" name="phone" id="phone" placeholder="0470 12 34 56"
                            value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>">
                    </div>

                    <div class="field">
                        <label for="postcode">Code postal</label>
                        <input type="text" name="postcode" id="postcode" maxlength="4"
                            value="<?= htmlspecialchars($_POST["postcode"] ?? "") ?>">
                    </div>
                </div>

                <div class="field">
                    <label for="message">Message</label>
                    <textarea name="message" id="message"
                        maxlength="300"><?= htmlspecialchars($_POST["message"] ?? "") ?></textarea>
                    <p id="message-counter">0 / 300 caracteres</p>
                </div>

                <button type="submit" class="submit-button">Envoyer</button>
            </form>
        </section>

        <section class="messages-section" id="messages-section">
            <?php if ($nbTotalMessages === 0): ?>
                <h2>Pas encore de message</h2>
            <?php elseif ($nbTotalMessages === 1): ?>
                <h2>Il y a 1 message</h2>
            <?php else: ?>
                <h2>Il y a <?= $nbTotalMessages ?> messages</h2>
            <?php endif; ?>

            <?php if (!empty($messages)): ?>


                <ul class="guestbook-list">
                    <?php foreach ($messages as $msg): ?>
                        <li class="guestbook-item">
                            <p class="guestbook-author">
                                <?= htmlspecialchars($msg["firstname"]) ?>
                                <?= htmlspecialchars($msg["lastname"]) ?>
                            </p>
                            <p class="guestbook-date"><?= htmlspecialchars($msg["datemessage"]) ?></p>
                            <p class="guestbook-message"><?= nl2br(htmlspecialchars($msg["message"])) ?></p>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <?= $pagination ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="js/validation.js"></script>
</body>

</html>