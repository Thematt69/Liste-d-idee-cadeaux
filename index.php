<?php

require_once __DIR__ . '/scripts/session/index.php';
require_once __DIR__ . '/scripts/verif/index.php';

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
} else {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
}
