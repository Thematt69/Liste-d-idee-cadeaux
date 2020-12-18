<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete']) && isset($_GET['liste'])) {

    $sql = 'UPDATE lic_liste
            SET deleted_to = ?
            WHERE id = ?;';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:m:i'), $_GET['liste']));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/mes-lites/');
} elseif (isset($_POST['Nom']) && isset($_GET['liste'])) {
    # code...
} elseif (isset($_POST['Nom'])) {
    $sql = 'INSERT INTO lic_liste (nom, partage, lien_partage)
            VALUES (?,?,?)';

    $str = rand();
    $rand = md5($str);

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Partage']), $rand));

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
                <h1 class="text-center">Création / Modification de liste</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <?php

                            $sql = 'SELECT id,nom,partage
                                    FROM lic_liste
                                    WHERE lien_partage = ?';

                            $response = $bdd->prepare($sql);
                            $response->execute(array($_GET['liste']));

                            $donnees = $response->fetch();

                            ?>
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
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
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
                            <?php $response->closeCursor(); ?>
                        </form>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>