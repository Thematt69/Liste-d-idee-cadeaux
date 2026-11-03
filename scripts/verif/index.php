<?php
require_once __DIR__ . '/../csrf/index.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !csrf_request_is_valid()) {
    http_response_code(403);
    exit('Requête refusée. Rechargez la page et réessayez.');
}

try {
    if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
        header('Location: https://family.matthieudevilliers.fr' . $_SERVER['PHP_SELF'], true, 307);
        exit();
    }

    // On se connecte à MySQL
    $bdd = new PDO('mysql:host=185.98.131.128;dbname=matth1371558;charset=utf8mb4', 'matth1371558', 'nR3_gjQKxWmHF7f', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}

require_once __DIR__ . '/../auth/index.php';
try {
    refresh_session_account($bdd);
} catch (Throwable $e) {
    error_log('Impossible de vérifier le compte de la session.');
    http_response_code(503);
    exit('Service temporairement indisponible.');
}

/// Helper: decode stored HTML entities, escape for HTML output.
function safe_output($str)
{
    if ($str === null || $str === '') return '';
    // First decode any HTML entities stored in DB (legacy data like "Id&eacute;e")
    $decoded = html_entity_decode((string)$str, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    // Then escape for HTML output
    return htmlspecialchars($decoded, ENT_QUOTES | ENT_HTML5 | ENT_SUBSTITUTE, 'UTF-8');
}

/// Accept only absolute HTTP(S) URLs without embedded credentials.
function is_safe_http_url($url)
{
    if (!is_string($url) || trim($url) === '') {
        return false;
    }

    $url = trim($url);
    $parts = parse_url($url);

    return is_array($parts)
        && isset($parts['scheme'], $parts['host'])
        && in_array(strtolower($parts['scheme']), array('http', 'https'), true)
        && !isset($parts['user'])
        && !isset($parts['pass'])
        && filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/// Decode legacy HTML-escaped URLs before validating them for link rendering.
function validated_http_url($url)
{
    if (!is_string($url)) {
        return null;
    }

    $decoded = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return is_safe_http_url($decoded) ? $decoded : null;
}
