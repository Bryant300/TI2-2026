<?php
# model/guestbookModel.php

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
    $usermail = trim($usermail);
    $phone = trim($phone);
    $postcode = trim($postcode);
    $message = trim($message);

    $cleanPhone = preg_replace("/[\s.\-]/", "", $phone);

    if (strlen($firstname) < 2) return false;
    if (strlen($lastname) < 2) return false;
    if (!filter_var($usermail, FILTER_VALIDATE_EMAIL)) return false;
    if (!preg_match("/^[0-9]{4}$/", $postcode)) return false;
    if ((int) $postcode < 1000 || (int) $postcode > 9999) return false;
    if (!preg_match("/^(04[0-9]{8}|\+324[0-9]{8}|00324[0-9]{8})$/", $cleanPhone)) return false;
    if (strlen($message) < 10 || strlen($message) > 500) return false;

    $sql = "INSERT INTO guestbook (firstname, lastname, usermail, phone, postcode, message)
            VALUES (:firstname, :lastname, :usermail, :phone, :postcode, :message)";

    try {
        $stmt = $db->prepare($sql);
        return $stmt->execute([
            ":firstname" => $firstname,
            ":lastname" => $lastname,
            ":usermail" => $usermail,
            ":phone" => $phone,
            ":postcode" => $postcode,
            ":message" => $message,
        ]);
    } catch (PDOException $e) {
        return false;
    }
}

function getAllGuestbook(PDO $db): array
{
    $sql = "SELECT id, firstname, lastname, usermail, phone, postcode, message, datemessage
            FROM guestbook
            ORDER BY datemessage DESC";

    try {
        $stmt = $db->query($sql);
        $messages = $stmt->fetchAll();
        $stmt->closeCursor();

        return $messages;
    } catch (PDOException $e) {
        return [];
    }
}

function getNbTotalGuestbook(PDO $db): int
{
    try {
        $stmt = $db->query("SELECT COUNT(*) AS total FROM guestbook");
        $result = $stmt->fetch();
        $stmt->closeCursor();

        return (int) $result["total"];
    } catch (PDOException $e) {
        return 0;
    }
}

function getGuestbookPagination(PDO $db, int $pageActu = 1, int $limit = 5): array
{
    $offset = ($pageActu - 1) * $limit;

    try {
        $stmt = $db->prepare(
            "SELECT id, firstname, lastname, usermail, phone, postcode, message, datemessage
             FROM guestbook
             ORDER BY datemessage DESC
             LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();

        $messages = $stmt->fetchAll();
        $stmt->closeCursor();

        return $messages;
    } catch (PDOException $e) {
        return [];
    }
}

<<<<<<< Updated upstream
function pagination(
    int $nbtotalMessage,
    string $url = "./?",
    string $get = "page",
    int $pageActu = 1,
    int $perPage = 5,
    string $anchor = ""
): string
=======
/**
 * @param int $nbtotalMessage
 * @param string $url
 * @param string $get
 * @param int $pageActu
 * @param int $perPage
 * @return string
 */
function pagination(int $nbtotalMessage, string $url = "./?", string $get = "page", int $pageActu = 1, int $perPage = 5): string
>>>>>>> Stashed changes
{
    if ($nbtotalMessage === 0) return "";

    $nbPages = (int) ceil($nbtotalMessage / $perPage);
    if ($nbPages === 1) return "";

    $separator = str_contains($url, "?") ? "&" : "?";
    $sortie = "<nav class=\"pagination\" aria-label=\"Pagination\">";

    for ($i = 1; $i <= $nbPages; $i++) {
        if ($i === $pageActu) {
            $sortie .= "<strong>$i</strong>";
        } elseif ($i === 1) {
            $sortie .= "<a href=\"$url$anchor\">$i</a>";
        } else {
            $sortie .= "<a href=\"$url$separator$get=$i$anchor\">$i</a>";
        }
    }

    $sortie .= "</nav>";

    return $sortie;
}
