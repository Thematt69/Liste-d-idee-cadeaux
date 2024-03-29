<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['AchatFrom'])) {
    $sql1 = 'SELECT buy_from
            FROM lic_idee
            WHERE id = ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_POST['AchatFrom']));
    $donnee1 = $response1->fetch();

    $buyFrom = $donnee1['buy_from'] == $_SESSION['id_compte'] ? NULL : $_SESSION['id_compte'];

    $response1->closeCursor();

    // Modification de la réservation
    $sql = 'UPDATE lic_idee
            SET buy_from = ?
            WHERE id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array($buyFrom, htmlentities($_POST['AchatFrom'])));

    $response->closeCursor();
}

$sql = 'SELECT id, partage
        FROM lic_liste
        WHERE lien_partage = ? AND deleted_to IS NULL';

$response = $bdd->prepare($sql);
$response->execute(array($_GET['liste']));
$donnee = $response->fetch();

$sql1 = 'SELECT type
        FROM lic_autorisation
        WHERE id_liste = ? AND id_compte = ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($donnee['id'], $_SESSION['id_compte']));
$donnee1 = $response1->fetch();

if ($donnee['partage'] == 'public' && $donnee1['type'] == null) {
    $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
            VALUES (?, ?, "lecteur")';

    $response2 = $bdd->prepare($sql2);
    $response2->execute(array($_SESSION['id_compte'], $donnee['id']));
    $response2->closeCursor();
}

$response->closeCursor();
$response1->closeCursor();

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Idées de la liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php
    include('../../widgets/navbar/index.php');

    $sql = 'SELECT lic_liste.id as id, lic_liste.nom as nom, lic_liste.partage as partage, lic_autorisation.type as droit
            FROM lic_liste
            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
            WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array($_SESSION['id_compte'], $_GET['liste']));

    $donnee = $response->fetch();

    if ($donnee == null) {
    ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <br>
                    <div class="alert alert-danger" role="alert">
                        Nous sommes désolées, mais nous n'avons pas trouvé votre liste. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.<br>
                        Il se peut aussi que vous n'ayez pas les droits nécessaires pour y accéder.
                    </div>
                    <br>
                </div>
            </div>
        </div>
    <?php
    } else {
    ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <br>
                    <h1 class="text-center">
                        <?php
                        switch ($donnee['partage']) {
                            case 'public':
                                echo '<i class="fas fa-users fa-2x" style="font-size: 1.5rem;"></i> ';
                                break;

                            case 'limite':
                                echo '<i class="fas fa-user-lock fa-2x" style="font-size: 1.5rem;"></i> ';
                                break;

                            default:
                                echo '<i class="fas fa-lock fa-2x" style="font-size: 1.5rem;"></i> ';
                                break;
                        }
                        echo $donnee['nom'];



                        $sql1 = 'SELECT lic_compte.prenom as prenom, lic_compte.id as id
                                FROM lic_compte
                                INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL
                                ORDER BY lic_compte.prenom ASC';

                        $response1 = $bdd->prepare($sql1);
                        $response1->execute(array($donnee['id']));

                        $proprietaire = array();

                        while ($donnees1 = $response1->fetch()) {
                            array_push($proprietaire, $donnees1);
                        }

                        ?>
                        <small class="text-muted">de
                            <?php
                            for ($i = 0; $i < count($proprietaire); $i++) {
                                if ($i > 0) {
                                    echo " et ";
                                }
                                if ($proprietaire[$i]['id'] == $_SESSION['id_compte']) {
                                    echo "<strong>&thinsp;vous&thinsp;</strong>";
                                } else {
                                    echo $proprietaire[$i]['prenom'];
                                }
                            }

                            ?>
                        </small>
                        <?php

                        $response1->closeCursor();

                        if ($donnee['droit'] == "proprietaire") {
                        ?>
                            <a href="https://family.matthieudevilliers.fr/pages/modif-listes/?liste=<?php echo ($_GET['liste']) ?>" class="link-dark">
                                <i class="fas fa-pen fa-2x" style="font-size: 1.5rem;"></i>
                            </a>
                        <?php
                        }
                        ?>


                        <a href="https://family.matthieudevilliers.fr/pages/print-liste/?liste=<?php echo ($_GET['liste']) ?>" class="link-dark">
                            <i class="fas fa-print fa-2x" style="font-size: 1.5rem;"></i>
                        </a>

                    </h1>
                    <br>

                    <div class="card text-dark bg-light">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table table-light text-center align-middle justify-content-">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th style="max-width: 30rem;">Commentaire</th>
                                            <th style="max-width: 40rem;">Lien</th>
                                            <th style="min-width: 8rem;">Prix</th>
                                            <th>Infos</th>
                                            <?php
                                            if ($donnee['droit'] != "lecteur")
                                                echo '<th>Actions</th>';
                                            else
                                                echo '<th>Réservé</th>';
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql1 = 'SELECT  id, nom, commentaire, lien, is_buy, buy_from, price
                                            FROM lic_idee
                                            WHERE id_liste = ? AND deleted_to IS NULL';

                                        $response1 = $bdd->prepare($sql1);
                                        $response1->execute(array($donnee['id']));

                                        while ($donnees = $response1->fetch()) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($donnees['nom'] == null) echo ('//');
                                                    else echo ($donnees['nom']);
                                                    ?>
                                                </td>
                                                <td style="max-width: 40rem">
                                                    <?php
                                                    if ($donnees['commentaire'] == null) echo ('//');
                                                    else echo ($donnees['commentaire']);
                                                    ?>
                                                </td>
                                                <td style="max-width: 40rem;">
                                                    <?php
                                                    if ($donnees['lien'] == null)
                                                        echo ('//');
                                                    else
                                                    ?>
                                                    <a href="<?php echo ($donnees['lien']) ?>" target="_blank">
                                                        <?php
                                                        if ($donnees['lien'] != '') echo (substr($donnees['lien'], 0, 60));
                                                        if (strlen($donnees['lien']) > 60) echo '...';
                                                        ?>
                                                    </a>
                                                </td>
                                                <td style="min-width: 8rem;">
                                                    <?php
                                                    if ($donnees['price'] == null) echo ('//');
                                                    else echo ($donnees['price']);
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php
                                                    if ($donnees['is_buy']) {

                                                        $sql2 = 'SELECT lic_compte.prenom as prenom, lic_compte.id as id
                                                                FROM lic_compte
                                                                INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                                                WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL
                                                                ORDER BY lic_compte.prenom ASC';

                                                        $response2 = $bdd->prepare($sql2);
                                                        $response2->execute(array($donnee['id']));

                                                        $proprietaire = array();

                                                        while ($donnee1 = $response2->fetch()) {
                                                            array_push($proprietaire, $donnee1);
                                                        }

                                                        echo 'Acheté par ';

                                                        for ($i = 0; $i < count($proprietaire); $i++) {
                                                            if ($i > 0) echo " et ";
                                                            echo $proprietaire[$i]['prenom'];
                                                        }

                                                        $response2->closeCursor();
                                                    } elseif ($donnees['buy_from'] && $donnee['droit'] != "proprietaire") {

                                                        $sql2 = 'SELECT prenom
                                                            FROM lic_compte
                                                            WHERE id = ? AND deleted_to IS NULL';

                                                        $response2 = $bdd->prepare($sql2);
                                                        $response2->execute(array($donnees['buy_from']));

                                                        $donnee1 = $response2->fetch();

                                                        echo 'Réservé par ' . $donnee1['prenom'];

                                                        $response2->closeCursor();
                                                    } else {
                                                        echo '//';
                                                    }
                                                    ?>
                                                </td>
                                                <?php
                                                if ($donnee['droit'] != "lecteur") {
                                                ?>
                                                    <td>
                                                        <a class="btn btn-outline-secondary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?idee=<?php echo $donnees['id']; ?>">
                                                            <i class="far fa-edit"></i>
                                                        </a>
                                                    </td>
                                                    <?php
                                                } else {
                                                    if ($donnees['is_buy'] || (isset($donnees['buy_from']) && $donnees['buy_from'] != $_SESSION['id_compte'])) {
                                                    ?>
                                                        <td>
                                                            <input class="form-check-input" type="checkbox" checked disabled>
                                                        </td>
                                                    <?php
                                                    } else {
                                                    ?>
                                                        <td>
                                                            <form action="" method="post">
                                                                <input name='AchatFrom' value="<?php echo $donnees['id']; ?>" type='hidden'>
                                                                <input name="AchatFrom" value="<?php echo $donnees['id']; ?>" class="form-check-input" type="checkbox" onChange="submit();" <?php if ($donnees['buy_from'] == $_SESSION['id_compte']) echo 'checked'; ?> />
                                                            </form>
                                                        </td>
                                                    <?php
                                                    }
                                                    ?>

                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            if ($donnee['droit'] == "proprietaire" || $donnee['droit'] == "moderateur") {
                            ?>
                                <br>
                                <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?liste=<?php echo $_GET['liste']; ?>">
                                    Ajouter une idée
                                </a>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    <?php
    }

    $response->closeCursor();

    ?>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>