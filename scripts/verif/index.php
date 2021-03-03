<?php

if (!$_SERVER['HTTPS']) {
    header('Location: https://family.matthieudevilliers.fr' . $_SERVER['PHP_SELF'] . '');
}

// NOTE - BDD import

include('../../sql/bdd.php');
