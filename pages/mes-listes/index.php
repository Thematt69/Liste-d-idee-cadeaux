<?php include('script.php'); ?>
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
                            <a href="https://family.matthieudevilliers.fr/pages/modifListe/" class="stretched-link"></a>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <p class="card-text">Créer une nouvelle liste</p>
                            </div>
                        </div>
                        <br>
                    </div>
                    <?php

                    $sql = "SELECT nom, lien_partage
                            FROM lic_liste
                            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
                            WHERE lic_autorisation.id_compte = 1 AND lic_autorisation.type = 'proprietaire'";

                    $response = $bdd->prepare($sql);
                    $response->execute();

                    while ($donnees = $response->fetch()) {
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
                    }
                    $response->closeCursor();
                    ?>

                    <!-- <div class="col-md-4">
                        <div class="card text-dark bg-light" style="min-height: 6rem;">
                            <a href="https://family.matthieudevilliers.fr/pages/idees/?liste=123456" class="stretched-link"></a>
                            <div class="card-body d-flex align-items-center justify-content-center">
                                <p class="card-text">
                                    Liste Anniversaire 2019
                                    <br>
                                    <small class="text-muted d-flex justify-content-center" data-bs-toggle="tooltip" title="Partagée avec vous">de Mathilde</small>
                                </p>
                            </div>
                        </div>
                        <br>
                    </div> -->
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>