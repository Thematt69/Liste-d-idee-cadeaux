<?php
try {
    if (!$_SERVER['HTTPS']) {
        header('Location: https://family.matthieudevilliers.fr' . $_SERVER['PHP_SELF']);
    }

    // On se connecte à MySQL
    $bdd = new PDO('mysql:host=185.98.131.128;dbname=matth1371558;charset=utf8', 'matth1371558', 'bejq29xnon', array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
} catch (Exception $e) {
    // En cas d'erreur, on affiche un message et on arrête tout
    die('Erreur : ' . $e->getMessage());
}
