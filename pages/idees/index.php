<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Idées de ma liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php
    include('../../widgets/navbar/index.php');

    $sql = 'SELECT id,nom
            FROM lic_liste
            WHERE lien_partage = "' . $_GET['liste'] . '"';

    $response = $bdd->prepare($sql);
    $response->execute();

    $donnee = $response->fetch();

    if ($donnee == null) {
        $donnee['nom'] = 'Liste introuvable';
        $donnee['id'] = 0;
    }

    $response->closeCursor();

    ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-sm-12">
                <br>
                <h1 class="text-center"><?php echo $donnee['nom'] ?></h1>
                <br>

                <div class="card text-dark bg-light">
                    <div class="card-body text-center">
                        <div class="table-responsive">
                            <table class="table table-light text-center align-middle">
                                <thead>
                                    <tr>
                                        <th class="col">Nom</th>
                                        <th class="col lien">Lien</th>
                                        <th class="col image">Image</th>
                                        <th class="col">Déja acheté</th>
                                        <th class="col"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    // FIXME - Faire la vérification des droits
                                    $sql1 = 'SELECT id,nom,lien,image,is_buy
                                            FROM lic_idee
                                            WHERE id_liste = ' . $donnee['id'] . '';

                                    $response1 = $bdd->prepare($sql1);
                                    $response1->execute();

                                    while ($donnees = $response1->fetch()) {
                                    ?>
                                        <tr>
                                            <td><?php echo ($donnees['nom']) ?></td>
                                            <td class="lien"><a href="<?php echo ($donnees['lien']) ?>"><?php if ($donnees['lien'] != '') echo (substr($donnees['lien'], 0, 40) . '...') ?></a></td>
                                            <td class="image">
                                                <?php if ($donnees['image'] == null) {
                                                    echo ('Aucune');
                                                } else {
                                                ?>
                                                    <a href="https://family.matthieudevilliers.fr/images/<?php echo ($donnees['image']) ?>">
                                                        <?php echo ($donnees['image']) ?>
                                                    </a>
                                                <?php
                                                }
                                                ?>
                                            </td>
                                            <td><input class="form-check-input" type="checkbox" <?php if ($donnees['is_buy']) echo ('checked') ?> disabled></td>
                                            <td><a class="btn btn-outline-secondary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?idee=<?php echo ($donnees['id']) ?>">Modifier</a></td>
                                        </tr>
                                    <?php
                                    }

                                    $response1->closeCursor();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/">Ajouter une idée</a>
                    </div>
                </div>

                <br>
            </div>
        </div>
    </div>

</body>

</html>