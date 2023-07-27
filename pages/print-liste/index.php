<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

?>

<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Imprimer une liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body onload="window.print()">

    <?php

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
                    <div class="card text-dark bg-light">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table table-light text-center align-middle justify-content-">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th style="max-width: 30rem;">Commentaire</th>
                                            <th style="min-width: 8rem;">Prix</th>
                                            <th>Infos</th>
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
                                                <td style=" max-width: 40rem">
                                                    <?php
                                                    if ($donnees['commentaire'] == null) echo ('//');
                                                    else echo ($donnees['commentaire']);
                                                    ?>
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
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
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

</html>