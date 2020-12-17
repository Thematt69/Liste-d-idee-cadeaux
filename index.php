<?php

session_start();

include('../../scripts/verif/index.php');

if (isset($_SESSION['mail'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/mes-listes/');
} else {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}
