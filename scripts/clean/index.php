<?php

session_start();

include('../../scripts/verif/index.php');

$date = new DateTime();

$date->sub(new DateInterval('P3Y'));

echo 'date (-3 years) => ' . $date->format('Y-m-d H:i:s') . '<br>';

// SECTION - lic_compte

$sql1 = 'SELECT count(id) as items
        FROM lic_compte
        WHERE deleted_to <= ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($date->format('Y-m-d H:i:s')));

$donnee = $response1->fetch();

$sql = 'DELETE
        FROM lic_compte 
        WHERE deleted_to <= ?';

$response = $bdd->prepare($sql);
$response->execute(array($date->format('Y-m-d H:i:s')));

echo 'lic_compte => DELETE ' . $donnee['items'] . ' items <br>';

$response->closeCursor();
$response1->closeCursor();

// !SECTION - lic_compte
// SECTION - lic_idee

$sql1 = 'SELECT count(id) as items
        FROM lic_idee
        WHERE deleted_to <= ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($date->format('Y-m-d H:i:s')));

$donnee = $response1->fetch();

$sql = 'DELETE
        FROM lic_idee 
        WHERE deleted_to <= ?';

$response = $bdd->prepare($sql);
$response->execute(array($date->format('Y-m-d H:i:s')));

echo 'lic_idee => DELETE ' . $donnee['items'] . ' items <br>';

$response->closeCursor();
$response1->closeCursor();

// !SECTION - lic_idee
// SECTION - lic_liste

$sql1 = 'SELECT count(id) as items
        FROM lic_liste
        WHERE deleted_to <= ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($date->format('Y-m-d H:i:s')));

$donnee = $response1->fetch();

$sql = 'DELETE
        FROM lic_liste 
        WHERE deleted_to <= ?';

$response = $bdd->prepare($sql);
$response->execute(array($date->format('Y-m-d H:i:s')));

echo 'lic_liste => DELETE ' . $donnee['items'] . ' items <br>';

$response->closeCursor();
$response1->closeCursor();

// !SECTION - lic_liste
// SECTION - lic_notif

$sql1 = 'SELECT count(id) as items
        FROM lic_notif
        WHERE deleted_to <= ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($date->format('Y-m-d H:i:s')));

$donnee = $response1->fetch();

$sql = 'DELETE
        FROM lic_notif 
        WHERE deleted_to <= ?';

$response = $bdd->prepare($sql);
$response->execute(array($date->format('Y-m-d H:i:s')));

echo 'lic_notif => DELETE ' . $donnee['items'] . ' items <br>';

$response->closeCursor();
$response1->closeCursor();

// !SECTION - lic_notif
// SECTION - lic_connexion

$sql1 = 'SELECT count(id) as items
        FROM lic_connexion
        WHERE connected_to <= ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($date->format('Y-m-d H:i:s')));

$donnee = $response1->fetch();

$sql = 'DELETE
        FROM lic_connexion 
        WHERE connected_to <= ?';

$response = $bdd->prepare($sql);
$response->execute(array($date->format('Y-m-d H:i:s')));

echo 'lic_connexion => DELETE ' . $donnee['items'] . ' items <br>';

$response->closeCursor();
$response1->closeCursor();

// !SECTION - lic_connexion