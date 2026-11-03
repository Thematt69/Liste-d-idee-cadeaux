<?php

include('../../scripts/session/index.php');

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
}

$alert = false;
$info = false;
$listeId = null;

// Toute mutation d'une liste existante doit viser la liste affichée et son propriétaire.
$modificationListe = isset($_POST['share_delete']) || isset($_POST['delete'])
    || (isset($_POST['Nom']) && isset($_POST['save']) && $_POST['save'] !== '')
    || isset($_POST['share']) || isset($_POST['new_droit_auth_id']);

if ($modificationListe) {
    if (!isset($_GET['liste']) || !is_string($_GET['liste']) || $_GET['liste'] === '') {
        header('Location: https://family.matthieudevilliers.fr/pages/erreur/403.php');
        exit();
    }

    $sqlAuth = 'SELECT lic_liste.id
                FROM lic_liste
                INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
                INNER JOIN lic_compte ON lic_compte.id = lic_autorisation.id_compte
                WHERE lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL
                  AND lic_compte.id = ? AND lic_compte.deleted_to IS NULL
                  AND lic_autorisation.type = "proprietaire"';
    $responseAuth = $bdd->prepare($sqlAuth);
    $responseAuth->execute(array($_GET['liste'], $_SESSION['id_compte']));
    $listeAutorisee = $responseAuth->fetch();
    $responseAuth->closeCursor();

    if (!$listeAutorisee) {
        header('Location: https://family.matthieudevilliers.fr/pages/erreur/403.php');
        exit();
    }

    $listeId = (string) $listeAutorisee['id'];
    foreach (array('share_delete', 'new_droit_auth_id') as $champAutorisation) {
        if (isset($_POST[$champAutorisation]) && (!is_string($_POST[$champAutorisation]) || !ctype_digit($_POST[$champAutorisation]))) {
            http_response_code(400);
            exit();
        }
    }
    foreach (array('delete', 'save', 'share') as $champListe) {
        if (isset($_POST[$champListe]) && (!is_scalar($_POST[$champListe]) || (string) $_POST[$champListe] !== $listeId)) {
            header('Location: https://family.matthieudevilliers.fr/pages/erreur/403.php');
            exit();
        }
    }
}

if (isset($_POST['cancel'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
    exit();
} elseif (isset($_POST['share_delete'])) {
    // Suppresion de l'autorisation
    $sql1 = 'DELETE FROM lic_autorisation
            WHERE id = ? AND id_liste = ? AND type != "proprietaire" AND id_compte != ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_POST['share_delete'], $listeId, $_SESSION['id_compte']));

    $response1->closeCursor();
} elseif (isset($_POST['delete'])) {

    // Suppresion des autorisations
    $sql1 = 'DELETE FROM lic_autorisation
            WHERE id_liste = ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_POST['delete']));

    $response1->closeCursor();

    // Suppresion de la liste
    $sql = 'UPDATE lic_liste
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:i:s'), $_POST['delete']));

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
} elseif (isset($_POST['Nom']) && isset($_POST['save']) && $_POST['save'] != "") {

    if (!isset($_POST['Partage']) || !in_array($_POST['Partage'], array('prive', 'limite', 'public'), true)) {
        http_response_code(400);
        exit();
    }

    $sql = 'SELECT partage
            FROM lic_liste
            WHERE id = ? AND deleted_to IS NULL';

    $response1 = $bdd->prepare($sql);
    $response1->execute(array($_POST['save']));

    $donnees = $response1->fetch();

    if (!$donnees) {
        $response1->closeCursor();
        header('Location: https://family.matthieudevilliers.fr/pages/erreur/403.php');
        exit();
    }

    // Modification des autorisations en fonction du partage
    switch ($_POST['Partage']) {
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
                    $response2->execute(array($_POST['save']));
                    $response2->closeCursor();
                    break;
            }
            break;
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
            break;
        case 'public':
            switch ($donnees['partage']) {
                case 'prive': # Privé -> Public : Nothing to do
                    // Ajout des autorisations pour les admins
                    $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
                            VALUES ((SELECT id FROM `lic_compte` WHERE fonction = "admin"), ?, "moderateur")';

                    $response2 = $bdd->prepare($sql2);
                    $response2->execute(array($_POST['save']));
                    $response2->closeCursor();
                    break;
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
    $response->execute(array($_POST['Nom'], $_POST['Partage'], $_POST['save']));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
    exit();
} elseif (isset($_POST['Nom'])) {

    if (!isset($_POST['Partage']) || !in_array($_POST['Partage'], array('prive', 'limite', 'public'), true)) {
        http_response_code(400);
        exit();
    }

    $coProprietaire = $_POST['Co-propriétaire'] ?? null;
    if ($coProprietaire !== null && (!is_string($coProprietaire) || !ctype_digit($coProprietaire))) {
        http_response_code(400);
        exit();
    }

    // Création de la liste
    $sql = 'INSERT INTO lic_liste (nom, partage, lien_partage)
            VALUES (?,?,?)';

    // 128 bits aléatoires, encodés sur 32 caractères pour rester compatibles avec les anciens liens.
    $rand = bin2hex(random_bytes(16));

    $bdd->beginTransaction();
    try {
        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Nom']), $_POST['Partage'], $rand));
        $listeCreeeId = $bdd->lastInsertId();
        $response->closeCursor();

        if ($listeCreeeId === '0' || $listeCreeeId === '') {
            throw new RuntimeException('Impossible de récupérer l’identifiant de la liste créée.');
        }

        // Création de l'autorisation propriétaire pour le compte courant.
        $sql1 = 'INSERT INTO lic_autorisation (id_compte, id_liste, `type`)
                VALUES (?, ?, "proprietaire")';
        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($_SESSION['id_compte'], $listeCreeeId));
        $response1->closeCursor();

        if ($coProprietaire !== null) {
            $sql1 = 'INSERT INTO lic_autorisation (id_compte, id_liste, `type`)
                    SELECT id, ?, "proprietaire"
                    FROM lic_compte
                    WHERE id = ? AND id != ? AND deleted_to IS NULL';
            $response1 = $bdd->prepare($sql1);
            $response1->execute(array($listeCreeeId, $coProprietaire, $_SESSION['id_compte']));
            if ($response1->rowCount() !== 1) {
                $response1->closeCursor();
                throw new RuntimeException('Le co-propriétaire sélectionné n’est pas un compte actif valide.');
            }
            $response1->closeCursor();
        }

        if ($_POST['Partage'] !== 'prive') {
            $sql1 = 'SELECT id
                    FROM lic_compte
                    WHERE fonction = "admin" AND deleted_to IS NULL AND id != ?';
            $response1 = $bdd->prepare($sql1);
            $response1->execute(array($_SESSION['id_compte']));

            $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
                    VALUES (?, ?, "moderateur")';
            $response2 = $bdd->prepare($sql2);
            while ($donnee1 = $response1->fetch()) {
                $response2->execute(array($donnee1['id'], $listeCreeeId));
            }
            $response2->closeCursor();
            $response1->closeCursor();
        }

        $bdd->commit();
    } catch (Throwable $e) {
        if ($bdd->inTransaction()) {
            $bdd->rollBack();
        }
        throw $e;
    }

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $rand);
    exit();
} elseif (isset($_POST['share'])) {
    if (!isset($_POST['share_email']) || !is_string($_POST['share_email']) || !filter_var($_POST['share_email'], FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        exit();
    }
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
    if (!isset($_POST['new_droit']) || !in_array($_POST['new_droit'], array('lecteur', 'moderateur'), true)) {
        http_response_code(400);
        exit();
    }
    // Modification de l'autorisation
    $sql = 'UPDATE lic_autorisation
            INNER JOIN lic_compte ON lic_compte.id = lic_autorisation.id_compte
            SET lic_autorisation.type = ?
            WHERE lic_autorisation.id = ? AND lic_autorisation.id_liste = ?
              AND lic_autorisation.type != "proprietaire" AND COALESCE(lic_compte.fonction, "") != "admin"';

    $response = $bdd->prepare($sql);
    $response->execute(array($_POST['new_droit'], $_POST['new_droit_auth_id'], $listeId));

    $response->closeCursor();
    header('Location: https://family.matthieudevilliers.fr/pages/modif-listes/?liste=' . $_GET['liste']);
    exit();
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

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-10">
                    <?php
                    if ($alert) {
                    ?>
                        <div class="alert alert-danger fade show" role="alert">
                            <strong><?php echo safe_output($alert); ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    } elseif ($info) {
                    ?>
                        <div class="alert alert-success fade show" role="alert">
                            <?php echo safe_output($info); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
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
                                    <div class="alert alert-danger" role="alert">
                                        <strong>Vous n'avez pas les droits !</strong> Nous sommes désolées, mais il semblerait que vous n'ayez pas les droits pour cette idée. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    } else {
                        if (isset($_GET['liste'])) {
                            echo ('<h1 class="text-center mb-4">Modification de liste</h1>');
                        } else {
                            echo ('<h1 class="text-center mb-4">Création de liste</h1>');
                        }
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post">
                                    <?php csrf_field(); ?>
                                    <div class="row g-3">
                                        <div class="col-md-8">
                                            <div class="form-floating">
                                                <input name="Nom" value="<?php echo safe_output($donnees['nom']) ?>" type="text" class="form-control" id="LabelNom" placeholder="Nom de la liste" required>
                                                <label for="LabelNom">Nom de la liste</label>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-floating">
                                                <select class="form-select" id="Partage" name="Partage">
                                                    <option <?php if ($donnees['partage'] == 'prive') echo ('selected') ?> value="prive">Privé</option>
                                                    <option <?php if ($donnees['partage'] == 'limite') echo ('selected') ?> value="limite">Limité</option>
                                                    <option <?php if ($donnees['partage'] == 'public') echo ('selected') ?> value="public">Public</option>
                                                </select>
                                                <label for="Partage">Paramètres de partage</label>
                                            </div>
                                        </div>
                                        <?php if (isset($_GET['type']) && $_GET['type'] == 'duo') { ?>
                                            <div class="col-md-6">
                                                <label for="Co-proprietaire" class="form-label">Co-propriétaire</label>
                                                <select class="form-select" id="Co-proprietaire" name="Co-propriétaire">
                                                    <?php

                                                    $sql1 = 'SELECT id,prenom,nom,mail
                                                        FROM lic_compte
                                                        WHERE id != ? AND deleted_to IS NULL';

                                                    $response1 = $bdd->prepare($sql1);
                                                    $response1->execute(array($_SESSION['id_compte']));

                                                    while ($compte = $response1->fetch()) {
                                                    ?>
                                                        <option value="<?php echo (int) $compte['id'] ?>"><?php echo safe_output($compte['prenom'] . " " . strtoupper($compte['nom']) . " - " . $compte['mail']); ?></option>
                                                    <?php
                                                    }

                                                    $response1->closeCursor();

                                                    ?>
                                                </select>
                                            </div>
                                        <?php } ?>
                                        <div class="col-12">
                                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                                <button type="submit" name="save" value="<?php echo (int) $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                                <button type="submit" name="cancel" value="<?php echo (int) $donnees['id'] ?>" class="btn btn-secondary">Annuler</button>
                                                <?php if (isset($_GET['liste']) && $donnees != null) { ?>
                                                    <button type="submit" name="delete" value="<?php echo (int) $donnees['id'] ?>" class="btn btn-danger">Supprimer</button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                    <div class="card mt-4">
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
                    <?php
                    if ($donnees['droit'] == "proprietaire" && $donnees['partage'] != 'prive') {
                    ?>
                        <div class="card">
                            <div class="card-body text-center">
                                <h2 id="personnes-partage-liste" class="h4 text-center mb-3">Les personnes à qui vous partagez votre liste</h2>
                                <?php
                                if ($donnees['partage'] == 'limite') {
                                ?>
                                    <form action="" method="post">
                                        <?php csrf_field(); ?>
                                        <div class="input-group">
                                            <input type="email" class="form-control" name="share_email" placeholder="Adresse mail" autocomplete="email" aria-label="Adresse e-mail de la personne à inviter" required>
                                            <button type="submit" name="share" value="<?php echo (int) $donnees['id'] ?>" class="btn btn-outline-primary">Ajouter</button>
                                        </div>
                                    </form>
                                <?php
                                }
                                ?>
                                <div class="table-responsive">
                                    <table class="table text-center align-middle" aria-labelledby="personnes-partage-liste">
                                        <caption class="visually-hidden">Personnes autorisées à consulter cette liste</caption>
                                        <thead>
                                            <tr>
                                                <th class="col" scope="col">Adresse mail</th>
                                                <th class="col" scope="col">Droit</th>
                                                <th class="col" scope="col">Depuis le</th>
                                                <?php
                                                if ($donnees['droit'] != "lecteur") {
                                                ?>
                                                    <th scope="col">Actions</th>
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
                                                    <th scope="row">
                                                        <?php
                                                        echo safe_output($donnees1['mail']);
                                                        if ($donnees1['fonction'] == 'admin') {
                                                            echo ('  <span class="badge rounded-pill bg-danger">ADMIN</span>');
                                                        }
                                                        ?>
                                                    </th>
                                                    <td>
                                                        <form action="" method="post">
                                                            <?php csrf_field(); ?>
                                                            <div class="input-group input-group-sm">
                                                                <select class="form-select" name="new_droit" aria-label="Droit d'accès de <?php echo safe_output($donnees1['mail']); ?>" <?php if ($donnees1['fonction'] == 'admin' || $donnees1['droit'] == 'proprietaire') echo "disabled"; ?>>
                                                                    <option disabled <?php if ($donnees1['droit'] == 'proprietaire') echo "selected"; ?> value="proprietaire">Propriétaire</option>
                                                                    <option <?php if ($donnees1['droit'] == 'moderateur') echo "selected"; ?> value="moderateur">Modérateur</option>
                                                                    <option <?php if ($donnees1['droit'] == 'lecteur') echo "selected"; ?> value="lecteur">Lecteur</option>
                                                                </select>

                                                                <button class="btn btn-outline-secondary" name="new_droit_auth_id" value="<?php echo (int) $donnees1['authId'] ?>" aria-label="Enregistrer le rôle de <?php echo safe_output($donnees1['mail']); ?>" <?php if ($donnees1['fonction'] == 'admin' || $donnees1['droit'] == 'proprietaire') echo "disabled"; ?> type="submit"><i class="bi bi-check-lg" aria-hidden="true"></i></button>
                                                            </div>
                                                        </form>
                                                    </td>
                                                    <td><?php echo ($datetime->format('d/m/Y H:i:s')) ?></td>
                                                    <?php
                                                    if ($donnees['droit'] != "lecteur") {
                                                        $disable = false;
                                                        if ($donnees1['droit']  == 'proprietaire') {
                                                            $disable = true;
                                                        } elseif ($donnees1['compteId']  == $_SESSION['id_compte']) {
                                                            $disable = true;
                                                        }
                                                    ?>
                                                        <td>
                                                            <form action="" method="post">
                                                                <?php csrf_field(); ?>
                                                                <button type="submit" name="share_delete" value="<?php echo (int) $donnees1['authId'] ?>" class="btn btn-outline-danger" aria-label="Supprimer l'accès de <?php echo safe_output($donnees1['mail']); ?>" <?php if ($disable) echo ('disabled'); ?>>
                                                                    <i class="bi bi-trash" aria-hidden="true"></i>
                                                                </button>
                                                            </form>
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
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>
