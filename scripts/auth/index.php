<?php

function refresh_session_account(PDO $bdd)
{
    // Le rôle mémorisé ne constitue jamais une autorisation pour cette requête.
    unset($_SESSION['fonction']);
    if (!isset($_SESSION['id_compte'])) {
        return;
    }

    $idCompte = $_SESSION['id_compte'];
    $compte = false;
    if ((is_int($idCompte) || (is_string($idCompte) && ctype_digit($idCompte)))
        && (int) $idCompte > 0) {
        $response = $bdd->prepare('SELECT id, fonction FROM lic_compte WHERE id = ? AND deleted_to IS NULL');
        try {
            $response->execute(array($idCompte));
            $compte = $response->fetch(PDO::FETCH_ASSOC);
        } finally {
            $response->closeCursor();
        }
    }

    if (!$compte) {
        $_SESSION = array();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
        return;
    }

    $_SESSION['id_compte'] = $compte['id'];
    $_SESSION['fonction'] = $compte['fonction'];
}
