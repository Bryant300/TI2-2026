<?php
require_once "../config.php";
require_once URL_BASE . "/model/guestbookModel.php";

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
    $firstname = $_POST["firstname"];
    $lastname = $_POST["lastname"];
    $usermail = $_POST["usermail"];
    $phone = $_POST["phone"];
    $postcode = $_POST["postcode"];
    $message = $_POST["message"];

    $success = addGuestbook($connexion, $firstname, $lastname, $usermail, $phone, $postcode, $message);

    if ($success) {
        header("Location: " . $_SERVER["PHP_SELF"] . "?status=success");
        exit();
    } else {
        $feedbackMessage = "Problème lors de l'envoi du message.";
        $feedbackType = "error";
    }
}

if (isset($_GET["status"]) && $_GET["status"] === "success") {
    $feedbackMessage = "Merci pour votre nouveau message !";
    $feedbackType = "success";
}


$pageActu = 1;
if (isset($_GET[PAGINATION_GET]) && ctype_digit($_GET[PAGINATION_GET]) && (int) $_GET[PAGINATION_GET] >= 1) {
    $pageActu = (int) $_GET[PAGINATION_GET];
}

$nbTotalMessages = getNbTotalGuestbook($connexion);

$paginationHtml = pagination(
    $nbTotalMessages,
    "./?",
    PAGINATION_GET,
    $pageActu,
    PAGINATION_NB
);

$guestbook = getGuestbookPagination($connexion, $pageActu, PAGINATION_NB);


include URL_BASE . "/view/guestbookView.php";

$connexion = null;
