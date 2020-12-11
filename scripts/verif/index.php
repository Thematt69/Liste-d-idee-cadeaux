<?php

if (!$_SERVER['HTTPS']) {
    header('Location: https://family.matthieudevilliers.fr' . $_SERVER['PHP_SELF'] . '');
}

include($_SERVER['DOCUMENT_ROOT'] . '/family.matthieudevilliers.fr/sql/bdd.php');
