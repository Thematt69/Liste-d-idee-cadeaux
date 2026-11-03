<?php
include('../session/index.php');
session_destroy();
header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
