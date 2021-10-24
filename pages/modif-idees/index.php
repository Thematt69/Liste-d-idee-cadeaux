<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete'])) {

    // Enregistrement de la suppresion
    $sql = 'UPDATE lic_idee
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:m:i'), $_POST['delete']));

    $response->closeCursor();

    $sql = 'SELECT lic_liste.lien_partage
            FROM lic_liste
            INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
            WHERE lic_idee.id = ? AND lic_liste.deleted_to IS NULL AND lic_idee.deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array($_POST['delete']));

    $donnee = $response->fetch();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien_partage']);

    $response->closeCursor();
} elseif (isset($_POST['Nom']) && $_POST['save'] != "") {

    // Modification de l'idée
    $sql = 'UPDATE lic_idee
            SET nom = ?, commentaire = ?, lien = ?, image = ?, is_buy = ?
            WHERE id = ?;';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), '', htmlentities($_POST['Achat']) ?? 0, htmlentities($_POST['save'])));

    $response->closeCursor();

    $sql = 'SELECT lic_liste.lien_partage
            FROM lic_liste
            INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
            WHERE lic_idee.nom = ? AND lic_liste.deleted_to IS NULL AND lic_idee.deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom'])));

    $donnee = $response->fetch();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien_partage']);

    $response->closeCursor();
} elseif (isset($_POST['Nom'])) {

    // Création de l'idée
    $sql = 'INSERT INTO lic_idee (id_liste, nom, commentaire, lien, image, is_buy)
            VALUES (?,?,?,?,?,?)';

    $sql1 = 'SELECT id
            FROM lic_liste
            WHERE lien_partage = ? AND deleted_to IS NULL';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_GET['liste']));

    $donnee1 = $response1->fetch();

    $response = $bdd->prepare($sql);
    $response->execute(array($donnee1['id'], htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), '', htmlentities($_POST['Achat'])));

    $response1->closeCursor();

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeaux - Création / Modification d'une idée</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
                <br>
                <?php

                $sql = 'SELECT lic_autorisation.type as droit
                        FROM lic_autorisation
                        INNER JOIN lic_idee ON lic_idee.id_liste = lic_autorisation.id_liste
                        INNER JOIN lic_liste ON lic_liste.id = lic_autorisation.id_liste
                        WHERE lic_autorisation.id_compte = ? AND lic_idee.id = ? AND lic_autorisation.deleted_to IS NULL AND lic_idee.deleted_to IS NULL AND lic_liste.deleted_to IS NULL';

                $response1 = $bdd->prepare($sql);
                $response1->execute(array($_SESSION['id_compte'], $_GET['idee']));

                $donnee = $response1->fetch();

                if (isset($_GET['idee']) && ($donnee == null || $donnee['droit'] == "lecteur")) {
                ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <br>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Vous n'avez pas les droits !</strong> Nous sommes désolées, mais il semblerai que vous n'avez pas les droits pour cette idée. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>

                <?php
                } elseif (!isset($_GET['liste']) && !isset($_GET['idee'])) {
                ?>
                    <div class="container">
                        <div class="row">
                            <div class="col-sm-12">
                                <br>
                                <div class="alert alert-danger" role="alert">
                                    <strong>Idée introuvable !</strong> Nous sommes désolées, mais nous n'avons pas trouvé votre idée. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>
                <?php
                } else {

                    $sql = 'SELECT id,nom,commentaire,lien,is_buy
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
                                    <div class="col-md-7">
                                        <div class="form-floating">
                                            <input name="Nom" type="text" value="<?php echo ($donnees['nom']) ?>" class="form-control" id="LabelNom" placeholder="Nom de l'idée" required>
                                            <label for="LabelNom">Nom de l'idée</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-floating">
                                            <input name="Lien" type="url" value="<?php echo ($donnees['lien']) ?>" class="form-control" id="LabelLien" placeholder="Lien de l'idée">
                                            <label for="LabelLien">Lien de l'idée</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-floating">
                                            <input name="Commentaire" type="text" value="<?php echo ($donnees['commentaire']) ?>" class="form-control" id="LabelCommentaire" placeholder="Commentaire">
                                            <label for="LabelCommentaire">Commentaire</label>
                                        </div>
                                        <br>
                                    </div>
                                    <!-- <input name="Image" class="form-control" type="file" accept="image/*"> -->
                                    <div class="col-md-5">
                                        <div class="form-check">
                                            <input name="Achat" value="1" class="form-check-input" type="checkbox" id="LabelAchat" <?php if ($donnees['is_buy']) echo 'checked' ?>>
                                            <label class="form-check-label" for="LabelAchat">
                                                Déjà acheté l'idée
                                            </label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                    <?php
                                    if (isset($_GET['idee']) && $donnees != null && $donnee['droit'] == "proprietaire") {
                                    ?>
                                        <br>
                                        <br>
                                        <form action="" method="post">
                                            <div class="col-sm-6 text-center">
                                                <button type="submit" name="delete" value="<?php echo $donnees['id'] ?>" class="btn btn-danger">Supprimer</button>
                                            </div>
                                        </form>
                                    <?php
                                    }
                                    ?>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php
                    $response->closeCursor();
                }
                $response1->closeCursor();
                ?>
                <br>
                <!-- TODO - Historique de modification -->
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>