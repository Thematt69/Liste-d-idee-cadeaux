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
    <title>Listes d'idées cadeau - Mes listes</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10">
                <br>
                <h1 class="text-center">Mes listes</h1>
                <br>
                <div class="row">
                    <div class="col-6 text-center">
                        <a class="btn btn-primary btn-lg" href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=solo" role="button">Créer une liste</a>
                    </div>
                    <div class="col-6 text-center">
                        <a class="btn btn-primary btn-lg" href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=duo" role="button">Créer une liste en duo</a>
                    </div>
                </div>
                <br>
                <div class="row">
                    <?php

                    $sql = 'SELECT lic_liste.nom as nom, lic_liste.lien_partage as lien_partage, lic_liste.id as id
                            FROM lic_liste
                            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
                            WHERE lic_autorisation.id_compte = ? AND lic_liste.deleted_to IS NULL
                            UNION
                            SELECT lic_liste.nom as nom, lic_liste.lien_partage as lien_partage, lic_liste.id as id
                            FROM lic_liste
                            WHERE lic_liste.partage = "public" AND lic_liste.deleted_to IS NULL  
                            ORDER BY nom ASC';

                    $response = $bdd->prepare($sql);
                    $response->execute(array($_SESSION['id_compte']));

                    while ($donnees = $response->fetch()) {

                        $sql1 = 'SELECT lic_compte.prenom as prenom, lic_compte.id as id
                                    FROM lic_compte
                                    INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                    WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL
                                    ORDER BY lic_compte.prenom ASC';

                        $response1 = $bdd->prepare($sql1);
                        $response1->execute(array($donnees['id']));

                        $proprietaire = array();

                        while ($donnees1 = $response1->fetch()) {
                            array_push($proprietaire, $donnees1);
                        }

                    ?>
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xxl-2">
                            <div class="card text-dark bg-light" style="min-height: 6rem;">
                                <a href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $donnees['lien_partage'] ?>" class="stretched-link"></a>
                                <div class="card-body d-flex align-items-center justify-content-center text-center">
                                    <p class="card-text">
                                        <?php echo $donnees['nom'] ?>
                                        <br>
                                        <small class="text-muted d-flex justify-content-center">de
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
                                    </p>
                                </div>
                            </div>
                            <br>
                        </div>
                    <?php
                        $response1->closeCursor();
                    }
                    $response->closeCursor();
                    ?>

                </div>
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>