<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete'])) {

    // Enregistrement de la suppresion
    $sql = 'UPDATE lic_liste
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:m:i'), $_POST['delete']));

    $response->closeCursor();

    //Récupération de l'id le plus récent supprimée
    $sql = 'SELECT id FROM lic_liste ORDER BY deleted_to DESC LIMIT 1';

    $response = $bdd->prepare($sql);
    $response->execute();

    $donnee = $response->fetch();

    // Enregistrement de la suppresion des autorisations
    $sql1 = 'UPDATE lic_autorisation
            SET deleted_to = ?
            WHERE id_liste = ?';

    $date = new DateTime();

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($date->format('Y-m-d H:m:i'), $donnee['id']));

    $response1->closeCursor();

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/mes-listes/');
} elseif (isset($_POST['Nom']) && $_POST['save'] != "") {

    // Modification de la liste
    $sql = 'UPDATE lic_liste
            SET nom = ?,partage = ?
            WHERE id = ?;';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Partage']), $_POST['save']));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
} elseif (isset($_POST['Nom'])) {

    // Création de la liste
    $sql = 'INSERT INTO lic_liste (nom, partage, lien_partage)
            VALUES (?,?,?)';

    $str = rand();
    $rand = md5($str);

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Partage']), $rand));

    $response->closeCursor();

    //Récupération de l'id le plus récent
    $sql = 'SELECT id FROM lic_liste ORDER BY created_to DESC LIMIT 1';

    $response = $bdd->prepare($sql);
    $response->execute();

    $donnee = $response->fetch();

    // Création de l'autorisation 'propriétaire'
    $sql1 = 'INSERT INTO lic_autorisation (id_compte, id_liste, `type`)
            VALUES (?,?,"proprietaire")';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_SESSION['id_compte'], $donnee['id']));

    $response1->closeCursor();

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $rand);
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Création / Modification de liste</title>

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

                $sql = 'SELECT id,nom,partage
                        FROM lic_liste
                        WHERE lien_partage = ? AND deleted_to = null';

                $response = $bdd->prepare($sql);
                $response->execute(array($_GET['liste']));

                $donnees = $response->fetch();

                if (isset($_GET['liste']) && $donnees != null) {
                    echo ('<h1 class="text-center">Modification de liste</h1>');
                } else {
                    echo ('<h1 class="text-center">Création de liste</h1>');
                }
                ?>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input name="Nom" value="<?php echo $donnees['nom'] ?>" type="text" class="form-control" id="LabelNom" placeholder="Nom de la liste" required>
                                        <label for="LabelNom">Nom de la liste</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="Partage" aria-label="Paramètres de partage">
                                        <option <?php if ($donnees['partage'] == 'prive') echo ('selected') ?> value="prive">Privé</option>
                                        <option <?php if ($donnees['partage'] == 'lien') echo ('selected') ?> value="lien">Partagée par lien</option>
                                        <option <?php if ($donnees['partage'] == 'secure') echo ('selected') ?> value="secure">Partagée par lien (sécurisé)</option>
                                    </select>
                                    <br>
                                </div>
                                <div class="col-6 col-md-4 text-center">
                                    <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                    <br>
                                </div>
                                <?php
                                if (isset($_GET['liste']) && $donnees != null) {
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