<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete'])) {

    // Suppresion des autorisations
    $sql1 = 'UPDATE lic_autorisation
            SET deleted_to = ?
            WHERE id_liste = ?';

    $date = new DateTime();

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($date->format('Y-m-d H:m:i'), htmlentities($_POST['delete'])));

    $response1->closeCursor();

    // Suppresion de la liste
    $sql = 'UPDATE lic_liste
            SET deleted_to = ?
            WHERE id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:m:i'), htmlentities($_POST['delete'])));

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
} elseif (isset($_POST['Nom']) && $_POST['save'] != "") {

    // Modification de la liste
    $sql = 'UPDATE lic_liste
            SET nom = ?,partage = ?
            WHERE id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Partage']), htmlentities($_POST['save'])));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
} elseif (isset($_POST['Nom'])) {

    // Création de la liste
    $sql = 'INSERT INTO lic_liste (nom, partage, lien_partage)
            VALUES (?,?,?)';

    // Lien random de partage
    $str = rand();
    $rand = md5($str);

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Partage']), htmlentities($rand)));

    $response->closeCursor();

    //Récupération de l'id le plus récent
    $sql = 'SELECT id FROM lic_liste WHERE deleted_to IS NULL ORDER BY created_to DESC LIMIT 1';

    $response = $bdd->prepare($sql);
    $response->execute();

    $donnee = $response->fetch();

    // Création de l'autorisation 'propriétaire'
    $sql1 = 'INSERT INTO lic_autorisation (id_compte, id_liste, `type`)
            VALUES (?,?,"proprietaire")';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_SESSION['id_compte'], $donnee['id']));

    $response1->closeCursor();

    // TODO - Ajouter les autres autorisations en fonction du partage

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $rand);
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeaux - Création / Modification de liste</title>

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

                $sql = 'SELECT lic_autorisation.type as droit, lic_liste.id as id, lic_liste.nom as nom, lic_liste.partage as partage
                        FROM lic_autorisation
                        INNER JOIN lic_liste ON lic_liste.id = lic_autorisation.id_liste
                        WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_autorisation.deleted_to IS NULL AND lic_liste.deleted_to IS NULL';

                $response1 = $bdd->prepare($sql);
                $response1->execute(array($_SESSION['id_compte'], $_GET['liste']));

                $donnees = $response1->fetch();

                if (isset($_GET['liste']) && ($donnees == null || $donnees['droit'] != "proprietaire")) {
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
                } else {
                    if (isset($_GET['liste'])) {
                        echo ('<h1 class="text-center">Modification de liste</h1>');
                    } else {
                        echo ('<h1 class="text-center">Création de liste</h1>');
                    }
                ?>
                    <!-- <br>
                    <p class="text-center">Lien de partage :
                        <a class="link-primary" target="_blank" href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste']; ?>">
                            https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste']; ?>
                        </a>
                    </p> -->
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
                                        <select class="form-select" name="Partage" aria-label="Paramètres de partage" disabled>
                                            <option <?php if ($donnees['partage'] == 'prive') echo ('selected') ?> value="prive">Privé</option>
                                            <option <?php if ($donnees['partage'] == 'limite') echo ('selected') ?> value="lien">Limité</option>
                                            <option <?php if ($donnees['partage'] == 'public') echo ('selected') ?> value="secure">Public</option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="col-sm-6 text-center">
                                        <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                    </div>
                                    <?php
                                    if (isset($_GET['liste']) && $donnees != null) {
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
                }
                $response1->closeCursor();
                ?>
                <br>
                <!-- FIXME - A enlever une fois le partage via lien effectif -->
                <div class="alert alert-warning alert-dismissible fade show" role="warning">
                    <strong>
                        Le partage d'une liste n'est pas encore disponible.
                        Veuillez contacter le support pour pouvoir partager votre liste.
                    </strong>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Type de partage</h3>
                                <ul>
                                    <li><strong>Privé</strong> - La liste n'est accessible que par vous.</li>
                                    <li><strong>Limité</strong> - Seules les personnes que vous avez autorisé pourront avoir accès à votre liste.</li>
                                    <li><strong>Public</strong> - Toutes les personnes disposants du lien pourront accèder à votre liste.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <!-- TODO - Historique des modifications -->
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>