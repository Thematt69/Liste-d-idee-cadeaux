<?php
session_start();
session_destroy();
header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
