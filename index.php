<?php

session_start();

include('../../scripts/verif/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
} else {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}
