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

/// Helper: cleanup old connexions (keep 10 per user, remove older than 1 year)
function cleanup_old_connexions($bdd, $id_compte = null)
{
    try {
        if ($id_compte) {
            // For a specific user (on login) - more efficient
            $sql = "DELETE FROM lic_connexion 
                    WHERE id_compte = ? 
                    AND connected_to < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                    AND id NOT IN (
                        SELECT id FROM (
                            SELECT id FROM lic_connexion 
                            WHERE id_compte = ?
                            ORDER BY connected_to DESC 
                            LIMIT 10
                        ) temp
                    )";

            $stmt = $bdd->prepare($sql);
            $stmt->execute(array($id_compte, $id_compte));
        } else {
            // For all users (on signup) - global cleanup
            $sql = "DELETE FROM lic_connexion 
                    WHERE connected_to < DATE_SUB(NOW(), INTERVAL 1 YEAR)
                    AND (SELECT COUNT(*) FROM lic_connexion co2 
                         WHERE co2.id_compte = lic_connexion.id_compte 
                         AND co2.connected_to >= lic_connexion.connected_to) > 10
                    LIMIT 1000";

            $bdd->exec($sql);
        }
    } catch (Exception $e) {
        // Silently log errors to avoid breaking login/signup
        error_log("cleanup_old_connexions error: " . $e->getMessage());
    }
}
