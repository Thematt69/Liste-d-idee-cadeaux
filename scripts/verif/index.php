<?php
// Configure secure session settings
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1);
    ini_set('session.cookie_samesite', 'Strict');
    ini_set('session.use_strict_mode', 1);
}

try {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header('Location: https://family.matthieudevilliers.fr' . $_SERVER['PHP_SELF'], true, 307);
        exit();
    }

    // On se connecte à MySQL
    $bdd = new PDO('mysql:host=185.98.131.128;dbname=matth1371558;charset=utf8', 'matth1371558', 'nR3_gjQKxWmHF7f', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}

/// Helper: decode stored HTML entities, escape for HTML output.
function safe_output($str)
{
    if ($str === null || $str === '') return '';
    // First decode any HTML entities stored in DB (legacy data like "Id&eacute;e")
    $decoded = html_entity_decode((string)$str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Then escape for HTML output
    return htmlspecialchars($decoded, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}
