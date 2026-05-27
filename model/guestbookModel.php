<?php
# model/guestbookModel.php
/********************************
 * Model de la page livre d'or
 *******************************/

// SELECTION de tous les messages du livre d'or
/**
 * @param PDO $db
 * @return array
 * Fonction qui récupère tous les messages depuis la table 'guestbook'
 * Renvoie un tableau associatif avec tous les messages, triés du plus récent au plus ancien
 */
function getAllGuestbook(PDO $db): array
{
    try {
        // Requête SQL pour récupérer tous les messages, du plus récent au plus ancien
        $stmt = $db->query("SELECT * FROM `guestbook` ORDER BY `datemessage` DESC");
        // On récupère tous les résultats sous forme de tableau associatif
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Bonne pratique : on ferme le curseur
        $stmt->closeCursor();
        // On renvoie le tableau des messages (vide si aucun message)
        return $result;
    } catch (PDOException $e) {
        // En cas d'erreur SQL, on arrête le script et on affiche l'erreur
        die("Erreur SQL (getAllGuestbook) : " . $e->getMessage());
    }
}

// INSERTION d'un message dans le livre d'or
/**
 * @param PDO $db
 * @param string $firstname
 * @param string $lastname
 * @param string $usermail
 * @param string $phone
 * @param string $postcode
 * @param string $message
 * @return bool
 * Fonction qui insère un message dans la base de données 'ti2web2026' et sa table 'guestbook'
 * Renvoie true si l'insertion a réussi, false sinon
 * Une requête préparée est utilisée pour éviter les injections SQL
 * Les données sont nettoyées et validées pour la sécurité backend
 */
function addGuestbook(
    PDO $db,
    string $firstname,
    string $lastname,
    string $usermail,
    string $phone,
    string $postcode,
    string $message
): bool {
    // --- TRAITEMENT DES DONNÉES BACKEND (SÉCURITÉ) ---

    // On supprime les espaces avant/arrière de tous les champs
    $firstname = trim($firstname);
    $lastname = trim($lastname);
    $phone = trim($phone);
    $postcode = trim($postcode);

    // Le message ne peut avoir ni tags HTML
    $message = strip_tags($message);
    // Ni espaces avant/arrière
    $message = trim($message);
    // On encode les caractères spéciaux (protection XSS)
    $message = htmlspecialchars($message);

    // Validation de l'email : doit avoir un format valide
    $mail = filter_var($usermail, FILTER_VALIDATE_EMAIL);

    // --- VÉRIFICATIONS : si une donnée ne correspond pas à nos attentes, on renvoie false ---

    // Prénom et nom obligatoires (max 100 caractères)
    if (empty($firstname) || mb_strlen($firstname) > 100) {
        return false;
    }
    if (empty($lastname) || mb_strlen($lastname) > 100) {
        return false;
    }

    // Email invalide
    if ($mail === false) {
        return false;
    }

    // Téléphone : optionnel mais si renseigné, ne doit contenir que des chiffres (max 20)
    if (!empty($phone)) {
        // On nettoie les espaces, tirets, points pour ne garder que les chiffres
        $phoneClean = preg_replace('/[\s\-\.]/', '', $phone);
        if (!ctype_digit($phoneClean) || mb_strlen($phoneClean) > 20) {
            return false;
        }
        $phone = $phoneClean;
    }

    // Code postal : exactement 4 chiffres (obligatoire selon le schéma DB : NOT NULL)
    if (!preg_match('/^\d{4}$/', $postcode)) {
        return false;
    }

    // Message obligatoire et max 500 caractères
    if (empty($message) || mb_strlen($message) > 500) {
        return false;
    }

    // --- INSERTION EN BASE DE DONNÉES ---
    try {
        // Requête préparée pour éviter les injections SQL
        $stmt = $db->prepare(
            "INSERT INTO guestbook (firstname, lastname, usermail, phone, postcode, message, datemessage)
             VALUES (?, ?, ?, ?, ?, ?, NOW())"
        );
        $result = $stmt->execute([
            $firstname,
            $lastname,
            $mail,
            $phone,
            $postcode,
            $message
        ]);
        // Bonne pratique : on ferme le curseur
        $stmt->closeCursor();
        // On renvoie true si l'insertion a réussi, false sinon
        return $result;
    } catch (PDOException $e) {
        // En cas d'erreur SQL, on arrête le script et on affiche l'erreur
        die("Erreur SQL (addGuestbook) : " . $e->getMessage());
    }
}

/**************************
 * Pour le Bonus Pagination
 **************************/

// SELECTION du nombre total de messages
/**
 * @param PDO $db
 * @return int
 * Fonction qui compte le nombre total de messages dans la table 'guestbook'
 */
function getNbTotalGuestbook(PDO $db): int
{
    try {
        $stmt = $db->query("SELECT COUNT(*) AS total FROM guestbook");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return (int) ($result['total'] ?? 0);
    } catch (PDOException $e) {
        die("Erreur SQL (getNbTotalGuestbook) : " . $e->getMessage());
    }
}

// SELECTION paginée des messages
/**
 * @param PDO $db
 * @param int $pageActu
 * @param int $limit
 * @return array
 * Fonction qui récupère les messages d'une page donnée (pagination)
 */
function getGuestbookPagination(PDO $db, int $pageActu = 1, int $limit = 5): array
{
    // Calcul de l'offset pour la pagination
    $offset = ($pageActu - 1) * $limit;

    try {
        // Requête préparée pour éviter les injections SQL
        $stmt = $db->prepare(
            "SELECT * FROM `guestbook` ORDER BY `datemessage` DESC LIMIT :limit OFFSET :offset"
        );
        // On lie les valeurs entières avec PDO::PARAM_INT
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // Bonne pratique : on ferme le curseur
        $stmt->closeCursor();
        // On renvoie le tableau des messages (vide si aucun résultat)
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL (getGuestbookPagination) : " . $e->getMessage());
    }
}

// FONCTION de génération de la pagination HTML
/**
 * @param int $nbtotalMessage
 * @param string $url
 * @param string $get
 * @param int $pageActu
 * @param int $perPage
 * @return string
 * Fonction qui génère le code HTML de la pagination
 * si le nombre de pages est supérieur à une.
 */
function pagination(int $nbtotalMessage, string $url = "./?", string $get = "page", int $pageActu = 1, int $perPage = 5): string
{
    $sortie = "";

    // Si aucun message, on ne génère rien
    if ($nbtotalMessage === 0)
        return "";

    // Calcul du nombre total de pages
    $nbPages = ceil($nbtotalMessage / $perPage);

    // Si une seule page, pas besoin de pagination
    if ($nbPages == 1)
        return "";

    $sortie .= "<p>";

    for ($i = 1; $i <= $nbPages; $i++) {
        if ($i === 1) {
            // Première page
            if ($pageActu === 1) {
                // On est sur la première page : liens désactivés
                $sortie .= "<< < 1 |";
            } elseif ($pageActu === 2) {
                $sortie .= " <a href='{$url}'><<</a> <a href='{$url}'><</a> <a href='{$url}'>1</a> |";
            } else {
                $sortie .= " <a href='{$url}'><<</a> <a href='{$url}&{$get}=" . ($pageActu - 1) . "'><</a> <a href='{$url}'>1</a> |";
            }
        } elseif ($i < $nbPages) {
            // Pages intermédiaires
            if ($i === $pageActu) {
                $sortie .= "  $i |";
            } else {
                $sortie .= "  <a href='{$url}&{$get}=$i'>$i</a> |";
            }
        } else {
            // Dernière page
            if ($i === $pageActu) {
                $sortie .= "  $nbPages > >>";
            } else {
                $sortie .= "  <a href='{$url}&{$get}=$nbPages'>$nbPages</a> <a href='{$url}&{$get}=" . ($pageActu + 1) . "'>></a> <a href='{$url}&{$get}=$nbPages'>>></a>";
            }
        }
    }

    $sortie .= "</p>";
    return $sortie;
}
