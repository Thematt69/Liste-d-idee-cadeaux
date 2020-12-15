<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Mes listes</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12">
                <br>
                <h1 class="text-center">Mes listes</h1>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-dark bg-light" style="min-height: 6rem;">
                            <a href="https://family.matthieudevilliers.fr/pages/modif-listes/" class="stretched-link"></a>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <p class="card-text">Créer une nouvelle liste</p>
                            </div>
                        </div>
                        <br>
                    </div>
                    <?php

                    $sql = 'SELECT lic_liste.nom as nom, lic_liste.lien_partage as lien_partage,lic_autorisation.type as roles,lic_liste.id as id
                            FROM lic_liste
                            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
                            WHERE lic_autorisation.id_compte = 1
                            ORDER BY lic_liste.created_to DESC';

                    $response = $bdd->prepare($sql);
                    $response->execute();

                    while ($donnees = $response->fetch()) {
                        if ($donnees['roles'] == 'proprietaire') {
                    ?>
                            <div class="col-md-4">
                                <div class="card text-dark bg-light" style="min-height: 6rem;">
                                    <a href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $donnees['lien_partage'] ?>" class="stretched-link"></a>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <p class="card-text"><?php echo $donnees['nom'] ?></p>
                                    </div>
                                </div>
                                <br>
                            </div>
                        <?php
                        } else {

                            $sql1 = 'SELECT lic_compte.prenom as prenom
                                    FROM lic_compte
                                    INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                    WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ' . $donnees['id'] . '';

                            $response1 = $bdd->prepare($sql1);
                            $response1->execute();

                            $donnees1 = $response1->fetch();
                        ?>
                            <div class="col-md-4">
                                <div class="card text-dark bg-light" style="min-height: 6rem;">
                                    <a href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $donnees['lien_partage'] ?>" class="stretched-link"></a>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <p class="card-text">
                                            <?php echo $donnees['nom'] ?>
                                            <br>
                                            <small class="text-muted d-flex justify-content-center">de <?php echo $donnees1['prenom'] ?></small>
                                        </p>
                                    </div>
                                </div>
                                <br>
                            </div>
                    <?php
                            $response1->closeCursor();
                        }
                    }
                    $response->closeCursor();
                    ?>

                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>