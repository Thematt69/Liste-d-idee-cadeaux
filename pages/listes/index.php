<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
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

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <h1 class="text-center mb-4">Mes listes</h1>
                    <div class="d-flex flex-wrap justify-content-center gap-3 mb-4">
                        <a class="btn btn-primary btn-lg" href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=solo">
                            <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Créer une liste
                        </a>
                        <a class="btn btn-outline-primary btn-lg" href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=duo">
                            <i class="bi bi-people me-2" aria-hidden="true"></i>Créer une liste en duo
                        </a>
                    </div>
                    <ul class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-3 list-unstyled">
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
                            <li class="col">
                                <div class="card h-100">
                                    <div class="card-body d-flex align-items-center justify-content-center text-center">
                                        <p class="card-text mb-0">
                                            <a href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo safe_output($donnees['lien_partage']) ?>" class="stretched-link list-card-link"><span class="fw-bold"><?php echo safe_output($donnees['nom']) ?></span></a>
                                            <small class="d-block text-body-secondary">de
                                                <?php
                                                $totalCount = count($proprietaire);

                                                if ($totalCount <= 2) {
                                                    // Affichage normal pour 1 ou 2 propriétaires
                                                    for ($i = 0; $i < $totalCount; $i++) {
                                                        if ($i > 0) echo " et ";
                                                        if ($proprietaire[$i]['id'] == $_SESSION['id_compte']) {
                                                            echo "<strong>&thinsp;vous&thinsp;</strong>";
                                                        } else {
                                                            echo safe_output($proprietaire[$i]['prenom']);
                                                        }
                                                    }
                                                } else {
                                                    // Plus de 2 propriétaires : affichage limité
                                                    $isUserOwner = false;
                                                    foreach ($proprietaire as $owner) {
                                                        if ($owner['id'] == $_SESSION['id_compte']) {
                                                            $isUserOwner = true;
                                                            break;
                                                        }
                                                    }

                                                    if ($isUserOwner) {
                                                        echo "<strong>&thinsp;vous&thinsp;</strong>";
                                                    } else {
                                                        echo safe_output($proprietaire[0]['prenom']);
                                                    }

                                                    $remaining = $totalCount - 1;
                                                    echo " et " . $remaining . " autre" . ($remaining > 1 ? "s" : "");
                                                }
                                                ?>
                                            </small>
                                        </p>
                                    </div>
                                </div>
                            </li>
                        <?php
                            $response1->closeCursor();
                        }
                        $response->closeCursor();
                        ?>

                    </ul>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>