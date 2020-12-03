<?php

if (!$_SERVER['HTTPS']) {
    header('Location: https://familly.matthieudevilliers.fr' . $_SERVER['PHP_SELF'] . '');
}

include($_SERVER['DOCUMENT_ROOT'] . '/familly.matthieudevilliers.fr/sql/bdd.php');
