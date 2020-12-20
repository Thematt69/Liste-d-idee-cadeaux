<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete'])) {

    // Enregistrement de la suppresion
    $sql = 'UPDATE lid_idee
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:m:i'), $_POST['delete']));

    $response->closeCursor();

    $sql = 'SELECT lic_liste.lien_partage
            FROM lic_liste
            INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
            WHERE lic_idee.id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array($_POST['delete']));

    $donnee = $response->fetch();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien_partage']);

    $response->closeCursor();
} elseif (isset($_POST['Nom']) && $_POST['save'] != "") {

    // Modification de la liste
    $sql = 'UPDATE lic_idee
            SET nom = ?, lien = ?, image = ?, is_buy = ?
            WHERE id = ?;';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Lien']), '', htmlentities($_POST['Achat'], htmlentities($_POST['save']))));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
} elseif (isset($_POST['Nom'])) {


    
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Création / Modification d'une idée</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
                <br>
                <?php

                $sql = 'SELECT id,nom,lien,is_buy
                        FROM lic_idee
                        WHERE id = ? AND deleted_to IS NULL';

                $response = $bdd->prepare($sql);
                $response->execute(array($_GET['idee']));

                $donnees = $response->fetch();

                if (isset($_GET['idee']) && $donnees != null) {
                    echo ('<h1 class="text-center">Modification d\'une idée</h1>');
                } else {
                    echo ('<h1 class="text-center">Création d\'une idée</h1>');
                }
                ?>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input name="Nom" type="text" value="<?php echo ($donnees['nom']) ?>" class="form-control" id="LabelNom" placeholder="Nom de l'idée" required>
                                        <label for="LabelNom">Nom de l'idée</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input name="Lien" type="url" value="<?php echo ($donnees['lien']) ?>" class="form-control" id="LabelLien" placeholder="Lien de l'idée">
                                        <label for="LabelLien">Lien de l'idée</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-8">
                                    <input name="Image" class="form-control" type="file" accept="image/*">
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input name="Achat" class="form-check-input" type="checkbox" id="LabelAchat" <?php if ($donnees['is_buy']) echo 'checked' ?>>
                                        <label class="form-check-label" for="LabelAchat">
                                            Déjà acheté l'idée
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-6 col-md-4 text-center">
                                    <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                    <br>
                                </div>
                                <?php
                                if (isset($_GET['idee']) && $donnees != null) {
                                ?>
                                    <form action="" method="post">
                                        <div class="col-6 col-md-4 text-center">
                                            <button type="submit" name="delete" value="<?php echo $donnees['id'] ?>" class="btn btn-danger">Supprimer</button>
                                            <br>
                                        </div>
                                    </form>
                                <?php
                                }
                                ?>
                            </div>
                        </form>
                    </div>
                </div>
                <?php $response->closeCursor(); ?>
                <br>
            </div>
        </div>
    </div>

</body>

</html>