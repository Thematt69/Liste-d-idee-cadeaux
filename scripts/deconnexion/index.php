<?php

if (!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] !== 'on') {
    header('Location: https://family.matthieudevilliers.fr/scripts/deconnexion/', true, 307);
    exit();
}

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
    header('Allow: POST');
    http_response_code(405);
    exit('Méthode non autorisée.');
}

require_once __DIR__ . '/../session/index.php';
require_once __DIR__ . '/../csrf/index.php';

if (!csrf_request_is_valid()) {
    http_response_code(403);
    exit('Requête refusée. Rechargez la page et réessayez.');
}

$_SESSION = array();
if (ini_get('session.use_cookies')) {
    $cookie = session_get_cookie_params();
    setcookie(session_name(), '', array(
        'expires' => time() - 3600,
        'path' => $cookie['path'],
        'domain' => $cookie['domain'],
        'secure' => $cookie['secure'],
        'httponly' => $cookie['httponly'],
        'samesite' => $cookie['samesite'] ?? 'Strict',
    ));
}
session_destroy();

header('Location: https://family.matthieudevilliers.fr/pages/connexion/', true, 303);
exit();
