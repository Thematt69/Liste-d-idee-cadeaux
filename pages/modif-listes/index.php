<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

$alert = false;
$info = false;

if (isset($_POST['cancel'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
} elseif (isset($_POST['share_delete'])) {
    // Suppresion de l'autorisation
    $sql1 = 'DELETE FROM lic_autorisation
            WHERE id = ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array(htmlentities($_POST['share_delete'])));

    $response1->closeCursor();
} elseif (isset($_POST['delete'])) {

    // Suppresion des autorisations
    $sql1 = 'DELETE FROM lic_autorisation
            WHERE id_liste = ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array(htmlentities($_POST['delete'])));

    $response1->closeCursor();

    // Suppresion de la liste
    $sql = 'UPDATE lic_liste
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:i:s'), htmlentities($_POST['delete'])));

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
} elseif (isset($_POST['Nom']) && htmlentities($_POST['save']) != "") {

    $sql = 'SELECT partage
            FROM lic_liste
            WHERE id = ?';

    $response1 = $bdd->prepare($sql);
    $response1->execute(array(htmlentities($_POST['save'])));

    $donnees = $response1->fetch();

    // Modification des autorisations en fonction du partage
    switch (htmlentities($_POST['Partage'])) {
        case 'prive':
            switch ($donnees['partage']) {
                case 'prive': # Privé -> Privé : Nothing to do
                    break;
                case 'limite': # Limité -> Privé
                case 'public': # Public -> Privé
                    // Suppresion des autorisations (hors propriétaire)
                    $sql2 = 'DELETE FROM lic_autorisation
                            WHERE id_liste = ? AND type != "proprietaire"';

                    $response2 = $bdd->prepare($sql2);
                    $response2->execute(array(htmlentities($_POST['save'])));
                    $response2->closeCursor();
                    break;
            }
        case 'limite':
            switch ($donnees['partage']) {
                case 'prive': # Privé -> Limité : Nothing to do
                    // Ajout des autorisations pour les admins
                    $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
                            VALUES ((SELECT id FROM `lic_compte` WHERE fonction = "admin"), ?, "moderateur")';

                    $response2 = $bdd->prepare($sql2);
                    $response2->execute(array(htmlentities($_POST['save'])));
                    $response2->closeCursor();
                    break;
                case 'limite': # Limité -> Limité : Nothing to do
                case 'public': # Public -> Limité : Nothing to do
                    break;
            }
        case 'public':
            switch ($donnees['partage']) {
                case 'prive': # Privé -> Public : Nothing to do
                    // Ajout des autorisations pour les admins
                    $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
                            VALUES ((SELECT id FROM `lic_compte` WHERE fonction = "admin"), ?, "moderateur")';

                    $response2 = $bdd->prepare($sql2);
                    $response2->execute(array(htmlentities($_POST['save'])));
                    $response2->closeCursor();
                case 'limite': # Limité -> Public : Nothing to do
                case 'public': # Public -> Public : Nothing to do
                    break;
            }
    }

    $response1->closeCursor();

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

    if (isset($_POST['Co-propriétaire'])) {
        // Création de l'autorisation 'propriétaire' au co-propriétaire
        $sql1 = 'INSERT INTO lic_autorisation (id_compte, id_liste, `type`)
            VALUES (?,?,"proprietaire")';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($_POST['Co-propriétaire'], $donnee['id']));

        $response1->closeCursor();
    }

    if (htmlentities($_POST['Partage']) != 'prive') {
        // Récupération des id des comptes admin
        $sql1 = 'SELECT id 
                FROM lic_compte
                WHERE fonction = "admin"';

        $response1 = $bdd->prepare($sql1);
        $response1->execute();

        while ($donnee1 = $response1->fetch()) {
            // Si le compte admin parcouru est différent de celui actuel
            if ($donnee1['id'] != $_SESSION['id_compte']) {
                // Ajout des autorisations pour les admins
                $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
                        VALUES (?, ?, "moderateur")';

                $response2 = $bdd->prepare($sql2);
                $response2->execute(array($donnee1['id'], $donnee['id']));
                $response2->closeCursor();
            }
        }

        $response1->closeCursor();
    }
    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $rand);
} elseif (isset($_POST['share'])) {
    $sql1 = 'SELECT id
            FROM lic_compte
            WHERE mail = ? AND deleted_to IS NULL';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array(htmlspecialchars($_POST['share_email'])));
    $donnees1 = $response1->fetch();

    if ($donnees1 != null) {
        $sql3 = 'SELECT id
            FROM lic_autorisation
            WHERE id_compte = ? AND id_liste = ?';

        $response3 = $bdd->prepare($sql3);
        $response3->execute(array($donnees1['id'], htmlspecialchars($_POST['share'])));
        $donnees3 = $response3->fetch();

        if ($donnees3 == null) {
            $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
            VALUES (?, ?, "lecteur")';

            $response2 = $bdd->prepare($sql2);
            $response2->execute(array($donnees1['id'], htmlspecialchars($_POST['share'])));
            $response2->closeCursor();
            $info = "L'utilisateur a bien été ajouté.";
        } else {
            $alert = "L'utilisateur est déjà autorisé à accéder à votre liste.";
        }

        $response3->closeCursor();
    } else {
        $alert = "Aucun utilisateur ne correspond à l'adresse mail fournie !";
    }
    $response1->closeCursor();
} elseif (isset($_POST['new_droit_auth_id'])) {
    // Modification de l'autorisation
    $sql = 'UPDATE lic_autorisation
            SET type = ?
            WHERE id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['new_droit']), htmlentities($_POST['new_droit_auth_id'])));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/modif-listes/?liste=' . $_GET['liste']);
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Création / Modification de liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-10">

                <br>
                <?php
                if ($alert) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><?php echo $alert; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                } elseif ($info) {
                ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $info; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                }

                $sql = 'SELECT lic_autorisation.type as droit, lic_liste.id as id, lic_liste.nom as nom, lic_liste.partage as partage
                        FROM lic_autorisation
                        INNER JOIN lic_liste ON lic_liste.id = lic_autorisation.id_liste
                        WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL';

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
                                    <strong>Vous n'avez pas les droits !</strong> Nous sommes désolées, mais il semblerait que vous n'ayez pas les droits pour cette idée. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.
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
                                            <option <?php if ($donnees['partage'] == 'limite') echo ('selected') ?> value="limite">Limité</option>
                                            <option <?php if ($donnees['partage'] == 'public') echo ('selected') ?> value="public">Public</option>
                                        </select>
                                        <br>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <div class="col-md-6 text-center">
                                            <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                            <button type="submit" name="cancel" value="<?php echo $donnees['id'] ?>" class="btn btn-secondary">Annuler</button>
                                        </div>
                                        <?php
                                        if (isset($_GET['liste']) && $donnees != null) {
                                        ?>
                                            <br>
                                            <br>
                                            <div class="col-md-6 text-center">
                                                <button type="submit" name="delete" value="<?php echo $donnees['id'] ?>" class="btn btn-danger">Supprimer</button>
                                            </div>
                                        <?php
                                        } elseif (isset($_GET['type']) && $_GET['type'] == 'duo') {
                                        ?>
                                            <br>
                                            <br>
                                            <div class="col-md-6">
                                                <select class="form-select" name="Co-propriétaire" aria-label="Co-propriétaire">
                                                    <?php

                                                    $sql1 = 'SELECT id,prenom,nom,mail
                                                            FROM lic_compte
                                                            WHERE id != ? AND deleted_to IS NULL';

                                                    $response1 = $bdd->prepare($sql1);
                                                    $response1->execute(array($_SESSION['id_compte']));

                                                    while ($donnees = $response1->fetch()) {
                                                    ?>
                                                        <option value="<?php echo ($donnees['id']) ?>"><?php echo ($donnees['prenom'] . " " . strtoupper($donnees['nom']) . " - " . $donnees['mail']); ?></option>
                                                    <?php
                                                    }

                                                    $response1->closeCursor();

                                                    ?>
                                                </select>
                                                <br>
                                            </div>
                                        <?php
                                        }
                                        ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php
                }
                ?>
                <br>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Type de partage</h3>
                                <p>Les administrateurs ont un accès par défaut pour tout partage de type "Limité" ou "Public".</p>
                                <ul>
                                    <li><strong>Privé</strong> - La liste n'est accessible que par vous.</li>
                                    <li><strong>Limité</strong> - Seules les personnes que vous avez autorisées pourront avoir accès à votre liste.</li>
                                    <li><strong>Public</strong> - Toutes les personnes disposants du lien pourront accéder à votre liste.</li>
                                </ul>
                                <?php
                                if (isset($_GET['liste'])) {
                                    echo ("<p>Un modérateur a la possibilité d'ajouter et modifier les idées de votre liste ; par contre, il ne pourra pas gérer, modifier ou supprimer votre liste.</p>");
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <?php
                if ($donnees['droit'] == "proprietaire" && $donnees['partage'] != 'prive') {
                ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <h3 class="text-center">Les personnes à qui vous partagez votre liste</h3>
                            <br>
                            <?php
                            if ($donnees['partage'] == 'limite') {
                            ?>
                                <form action="" method="post">
                                    <div class="input-group">
                                        <input type="email" class="form-control" name="share_email" placeholder="Adresse mail" aria-label="Adresse mail" aria-describedby="email" required aria-required="true">
                                        <button type="submit" name="share" value="<?php echo $donnees['id'] ?>" id="email" class="btn btn-outline-primary">Ajouter</button>
                                    </div>
                                </form>
                                <br>
                            <?php
                            }
                            ?>
                            <div class="table-responsive">
                                <table class="table text-center align-middle justify-content-">
                                    <thead>
                                        <tr>
                                            <th class="col">Adresse mail</th>
                                            <th class="col">Droit</th>
                                            <th class="col">Depuis le</th>
                                            <?php
                                            if ($donnees['droit'] != "lecteur") {
                                            ?>
                                                <th>Actions</th>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql1 = 'SELECT lic_compte.id as compteId, lic_autorisation.id as authId, lic_compte.mail as mail, lic_autorisation.type as droit, lic_autorisation.created_to as created_to, lic_compte.fonction as fonction
                                            FROM lic_compte
                                            INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                            WHERE lic_autorisation.id_liste = ?  AND lic_compte.deleted_to IS NULL';

                                        $response1 = $bdd->prepare($sql1);
                                        $response1->execute(array($donnees['id']));

                                        while ($donnees1 = $response1->fetch()) {
                                            $datetime = new DateTime($donnees1['created_to']);
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    echo ($donnees1['mail']);
                                                    if ($donnees1['fonction'] == 'admin') {
                                                        echo ('  <span class="badge rounded-pill bg-danger">ADMIN</span>');
                                                    }
                                                    ?>
                                                </td>
                                                <td>
                                                    <form action="" method="post">
                                                        <div class="input-group input-group-sm">
                                                            <select class=" form-select form-select-sm" name="new_droit" <?php if ($donnees1['fonction'] == 'admin' || $donnees1['droit'] == 'proprietaire') echo "disabled"; ?>>
                                                                <option disabled <?php if ($donnees1['droit'] == 'proprietaire') echo "selected"; ?> value="proprietaire">Propriétaire</option>
                                                                <option <?php if ($donnees1['droit'] == 'moderateur') echo "selected"; ?> value="moderateur">Modérateur</option>
                                                                <option <?php if ($donnees1['droit'] == 'lecteur') echo "selected"; ?> value="lecteur">Lecteur</option>
                                                            </select>

                                                            <button class="btn btn-outline-secondary" name="new_droit_auth_id" value="<?php echo $donnees1['authId'] ?>" <?php if ($donnees1['fonction'] == 'admin' || $donnees1['droit'] == 'proprietaire') echo "disabled"; ?> type="submit"><i class="fas fa-check"></i></button>
                                                        </div>
                                                    </form>
                                                </td>
                                                <td><?php echo ($datetime->format('d/m/Y H:i:s')) ?></td>
                                                <?php
                                                if ($donnees['droit'] != "lecteur") {
                                                    -$disable = false;
                                                    if ($donnees1['droit']  == 'proprietaire') {
                                                        $disable = true;
                                                    } elseif ($donnees1['compteId']  == $_SESSION['id_compte']) {
                                                        $disable = true;
                                                    }
                                                ?>
                                                    <td>
                                                        <button type="submit" name="share_delete" value="<?php echo $donnees1['authId'] ?>" class="btn btn-outline-danger" <?php if ($disable) echo ('disabled'); ?>>
                                                            <i class="far fa-trash-alt"></i>
                                                        </button>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php
                }
                $response1->closeCursor();
                ?>
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>