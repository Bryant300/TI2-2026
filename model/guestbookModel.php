<?php

/**
 * @param PDO $db
 * @return array
 * Fonction qui récupère tous les messages depuis la table 'guestbook'
 * Renvoie un tableau associatif avec tous les messages, triés du plus récent au plus ancien
 */
function getAllGuestbook(PDO $db): array
{
    try {
        $stmt = $db->query("SELECT * FROM `guestbook` ORDER BY `datemessage` DESC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL (getAllGuestbook) : " . $e->getMessage());
    }
}

/**
 * @param PDO $db
 * @param string $firstname
 * @param string $lastname
 * @param string $usermail
 * @param string $phone
 * @param string $postcode
 * @param string $message
 * @return bool
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

    $firstname = trim($firstname);
    $lastname = trim($lastname);
    $phone = trim($phone);
    $postcode = trim($postcode);

    $message = strip_tags($message);
    $message = trim($message);
    $message = htmlspecialchars($message);

    $mail = filter_var($usermail, FILTER_VALIDATE_EMAIL);


    if (empty($firstname) || mb_strlen($firstname) > 100) {
        return false;
    }
    if (empty($lastname) || mb_strlen($lastname) > 100) {
        return false;
    }

    if ($mail === false) {
        return false;
    }

    if (!empty($phone)) {
        $phoneClean = preg_replace('/[\s\-\.]/', '', $phone);
        if (!ctype_digit($phoneClean) || mb_strlen($phoneClean) > 20) {
            return false;
        }
        $phone = $phoneClean;
    }

    if (!preg_match('/^\d{4}$/', $postcode)) {
        return false;
    }

    if (empty($message) || mb_strlen($message) > 500) {
        return false;
    }

    try {
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
        $stmt->closeCursor();
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL (addGuestbook) : " . $e->getMessage());
    }
}

/**
 * @param PDO $db
 * @return int
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

/**
 * @param PDO $db
 * @param int $pageActu
 * @param int $limit
 * @return array
 */
function getGuestbookPagination(PDO $db, int $pageActu = 1, int $limit = 5): array
{
    $offset = ($pageActu - 1) * $limit;

    try {
        $stmt = $db->prepare(
            "SELECT * FROM `guestbook` ORDER BY `datemessage` DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt->closeCursor();
        return $result;
    } catch (PDOException $e) {
        die("Erreur SQL (getGuestbookPagination) : " . $e->getMessage());
    }
}

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

    if ($nbtotalMessage === 0)
        return "";

    $nbPages = ceil($nbtotalMessage / $perPage);

    if ($nbPages == 1)
        return "";

    $sortie .= "<p>";

    for ($i = 1; $i <= $nbPages; $i++) {
        if ($i === 1) {
            if ($pageActu === 1) {
                $sortie .= "<< < 1 |";
            } elseif ($pageActu === 2) {
                $sortie .= " <a href='{$url}'><<</a> <a href='{$url}'><</a> <a href='{$url}'>1</a> |";
            } else {
                $sortie .= " <a href='{$url}'><<</a> <a href='{$url}&{$get}=" . ($pageActu - 1) . "'><</a> <a href='{$url}'>1</a> |";
            }
        } elseif ($i < $nbPages) {
            if ($i === $pageActu) {
                $sortie .= "  $i |";
            } else {
                $sortie .= "  <a href='{$url}&{$get}=$i'>$i</a> |";
            }
        } else {
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
