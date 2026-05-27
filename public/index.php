<?php
# public/index.php


/*
 * Front Controller de la gestion du livre d'or
 */

/*
 * Chargement des dépendances
 */
// chargement de configuration
require_once "../config.php";
// chargement du modèle de la table guestbook
require_once URL_BASE . "/model/guestbookModel.php";

/*
 * Connexion à la base de données en utilisant PDO
 * Avec un try catch pour gérer les erreurs de connexion
 * Utilisez les constantes de config.php
 * Activez le mode d'erreur de PDO à Exception et
 * le mode fetch à tableau associatif
 */
try {

    $connexion = new PDO(
        DB_DRIVER . ":host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT . ";charset=" . DB_CHARSET,
        DB_LOGIN,
        DB_PWD
    );

    $connexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connexion->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    echo "Erreur de connexion à la base de données : " . $e->getMessage();
    die();

}
/*
 * Si le formulaire a été soumis
 */
if (
    isset(
    $_POST["firstname"],
    $_POST["lastname"],
    $_POST["usermail"],
    $_POST["phone"],
    $_POST["postcode"],
    $_POST["message"]
)
    && !empty($_POST["firstname"])
    && !empty($_POST["message"])
) {
    // Récupération et nettoyage basique des données POST
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $usermail = $_POST["usermail"];
    $phone = $_POST["phone"];
    $postcode = $_POST["postcode"];
    $message = $_POST["message"];

    $success = addGuestbook($connexion, $firstname, $lastname, $usermail, $phone, $postcode, $message);

    if ($success) {
        // Insertion réussie → redirection pour éviter la re-soumission (Post/Redirect/Get)
        header("Location: " . $_SERVER["PHP_SELF"] . "?status=success");
        exit();
    } else {
        // Échec de l'insertion
        $feedbackMessage = "Problème lors de l'envoi du message.";
        $feedbackType = "error";
    }
}
// on appelle la fonction d'insertion dans la DB (addGuestbook())

// si l'insertion a réussi
if (isset($_GET["status"]) && $_GET["status"] === "success") {
    $feedbackMessage = "Merci pour votre nouveau message !";
    $feedbackType = "success";
}

// on redirige vers la page actuelle (ou on affiche un message de succès)

// sinon, on affiche un message d'erreur

/*
 * On récupère les messages du livre d'or
 */

// on appelle la fonction de récupération de la DB (getAllGuestbook())

/*********************
 * Ou Bonus Pagination
 *********************/

// on vérifie sur quelle page on est (et que c'est un string qui contient que des numériques sans "." ni "-" => ctype_digit) en utilisant la variable $_GET et les constantes de config.php
$pageActu = 1;
if (isset($_GET[PAGINATION_GET]) && ctype_digit($_GET[PAGINATION_GET]) && (int) $_GET[PAGINATION_GET] >= 1) {
    $pageActu = (int) $_GET[PAGINATION_GET];
}

# on compte le nombre total de messages (SQL)
$nbTotalMessages = getNbTotalGuestbook($connexion);

// Génération du HTML de la pagination
$paginationHtml = pagination(
    $nbTotalMessages,
    "./?",
    PAGINATION_GET,
    $pageActu,
    PAGINATION_NB
);

# on récupère la pagination

# pour obtenir le $offset pour les messages (calcul)
$guestbook = getGuestbookPagination($connexion, $pageActu, PAGINATION_NB);

# on veut récupérer les messages de la page courante

/**************************
 * Fin du Bonus Pagination
 **************************/

// Appel de la vue

include URL_BASE . "/view/guestbookView.php";

// fermeture de la connexion (bonne pratique)
$connexion = null;
