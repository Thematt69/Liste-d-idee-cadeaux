<?php

session_start();

include('../../scripts/verif/index.php');
include('../../scripts/mail/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

if (isset($_POST['delete'])) {

    // Récupération du lien de la liste qui contient l'idée à supprimer
    $sql1 = 'SELECT lic_liste.lien_partage as lien
            FROM lic_liste
            INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
            WHERE lic_idee.id = ? AND lic_liste.deleted_to IS NULL AND lic_idee.deleted_to IS NULL';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_POST['delete']));

    $donnee1 = $response1->fetch();

    // Enregistrement de la suppresion
    $sql = 'UPDATE lic_idee
            SET deleted_to = ?
            WHERE id = ?';

    $date = new DateTime();

    $response = $bdd->prepare($sql);
    $response->execute(array($date->format('Y-m-d H:i:s'), $_POST['delete']));

    $response->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee1['lien']);

    $response1->closeCursor();
} elseif (isset($_POST['Nom']) && $_POST['save'] != "") {

    if (isset($_POST['Achat'])) {
        // Modification de l'idée par le propriétaire
        $sql = 'UPDATE lic_idee
            SET nom = ?, commentaire = ?, lien = ?, is_buy = ?, price = ?
            WHERE id = ?;';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), htmlentities($_POST['Achat']) ?? 0, htmlentities($_POST['Prix']), htmlentities($_POST['save'])));

        $response->closeCursor();

        $sql = 'SELECT lic_liste.lien_partage as lien
                FROM lic_liste
                INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
                WHERE lic_idee.nom = ? AND lic_liste.deleted_to IS NULL AND lic_idee.deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Nom'])));

        $donnee = $response->fetch();

        $sql1 = 'SELECT is_buy, buy_from
                FROM lic_idee
                WHERE nom = ? AND deleted_to IS NULL';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array(htmlentities($_POST['Nom'])));

        $donnee1 = $response1->fetch();

        if ($donnee1['is_buy'] == 1 && $donnee1['buy_from'] != null) {
            // Envoyer un mail à la personne qui avait réservé
            $contenu = '
                <html>
                    <body>
                        <h3>Annulation de votre réservation d\'idée</h3>
                        <br>
                        <p>Bonjour,</p>
                        <p>
                            Vous aviez réservé l\'idée intitulée "' . htmlentities($_POST['Nom']) . '" mais le propriétaire vient d\'indiquer qu\'il l\'a finalement acheté par lui-même.
                            Votre réservation est donc annulée, pour voir la liste d\'idée(s) concernée(s), cliquer sur le lien ci-dessous.
                        </p>
                        <p><a href="https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien'] . '">https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien'] . '</a></p>
                        <br>
                        <p>L\'équipe de Listes d\'idées cadeaux</p>
                    </body>
                </html>
            ';


            $sql3 = 'SELECT mail
                FROM lic_compte
                WHERE id = ? AND deleted_to IS NULL';

            $response3 = $bdd->prepare($sql3);
            $response3->execute(array($_SESSION['id_compte']));

            $donnee3 = $response3->fetch();

            envoiMail($donnee3['mail'], "Annulation de votre réservation d'idée - Listes d'idées cadeau", $contenu);

            $response3->closeCursor();

            // Supprimer la réservation
            $sql2 = 'UPDATE lic_idee
                    SET buy_from = NULL
                    WHERE nom = ? AND deleted_to IS NULL';

            $response2 = $bdd->prepare($sql2);
            $response2->execute(array(htmlentities($_POST['Nom'])));
            $response2->closeCursor();
        }
        $response1->closeCursor();

        header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien']);

        $response->closeCursor();
    } else {
        if (htmlentities($_POST['AchatFrom']) == "1") {
            $buyFrom = $_SESSION['id_compte'];
        }

        // Modification de l'idée par un modérateur
        $sql = 'UPDATE lic_idee
            SET nom = ?, commentaire = ?, lien = ?, buy_from = ?, price = ?
            WHERE id = ?;';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), $buyFrom, htmlentities($_POST['Prix']), htmlentities($_POST['save'])));

        $response->closeCursor();

        $sql = 'SELECT lic_liste.lien_partage as lien
            FROM lic_liste
            INNER JOIN lic_idee ON lic_idee.id_liste = lic_liste.id
            WHERE lic_idee.nom = ? AND lic_liste.deleted_to IS NULL AND lic_idee.deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Nom'])));

        $donnee = $response->fetch();

        header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $donnee['lien']);

        $response->closeCursor();
    }
} elseif (isset($_POST['Nom'])) {
    $sql2 = 'SELECT lic_autorisation.type as droit
            FROM lic_autorisation
            INNER JOIN lic_liste ON lic_autorisation.id_liste = lic_liste.id
            WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL';

    $response2 = $bdd->prepare($sql2);
    $response2->execute(array($_SESSION['id_compte'], $_GET['liste']));

    $donnee2 = $response2->fetch();

    if ($donnee2['droit'] == "proprietaire") {
        // Création de l'idée par le propriétaire
        $sql = 'INSERT INTO lic_idee (id_liste, nom, commentaire, lien, is_buy, price)
            VALUES (?,?,?,?,?,?)';

        $sql1 = 'SELECT id
            FROM lic_liste
            WHERE lien_partage = ? AND deleted_to IS NULL';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($_GET['liste']));

        $donnee1 = $response1->fetch();

        $response = $bdd->prepare($sql);
        $response->execute(array($donnee1['id'], htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), $_POST['Achat'] ?? 0, htmlentities($_POST['Prix'])));

        $response1->closeCursor();

        $response->closeCursor();

        header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
    } elseif ($donnee2['droit'] == "moderateur") {
        // Création de l'idée par un modérateur
        $sql = 'INSERT INTO lic_idee (id_liste, nom, commentaire, lien, buy_from, price)
            VALUES (?,?,?,?,?,?)';

        $sql1 = 'SELECT id
            FROM lic_liste
            WHERE lien_partage = ? AND deleted_to IS NULL';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($_GET['liste']));

        $donnee1 = $response1->fetch();

        if ($_POST['AchatFrom'] == 1) {
            $buyFrom = $_SESSION['id_compte'];
        }

        $response = $bdd->prepare($sql);
        $response->execute(array($donnee1['id'], htmlentities($_POST['Nom']), htmlentities($_POST['Commentaire']), htmlentities($_POST['Lien']), $buyFrom, htmlentities($_POST['Prix'])));

        $response1->closeCursor();

        $response->closeCursor();

        header('Location: https://family.matthieudevilliers.fr/pages/idees/?liste=' . $_GET['liste']);
    }
    $response2->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Création / Modification d'une idée</title>

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

                if ($_GET['idee']) {
                    $sql = 'SELECT lic_autorisation.type as droit, lic_liste.lien_partage as partage
                            FROM lic_autorisation
                            INNER JOIN lic_idee ON lic_idee.id_liste = lic_autorisation.id_liste
                            INNER JOIN lic_liste ON lic_liste.id = lic_autorisation.id_liste
                            WHERE lic_autorisation.id_compte = ? AND lic_idee.id = ? AND lic_idee.deleted_to IS NULL AND lic_liste.deleted_to IS NULL';
                } else {
                    $sql = 'SELECT lic_autorisation.type as droit, lic_liste.lien_partage as partage
                            FROM lic_autorisation
                            INNER JOIN lic_liste ON lic_liste.id = lic_autorisation.id_liste
                            WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL';
                }

                $response1 = $bdd->prepare($sql);
                $response1->execute(array($_SESSION['id_compte'], $_GET['idee'] ?? $_GET['liste']));

                $donnee = $response1->fetch();

                if (isset($_GET['idee']) && ($donnee == null || $donnee['droit'] == "lecteur")) {
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

                    $sql = 'SELECT id, nom, commentaire, lien, is_buy, buy_from, price
                        FROM lic_idee
                        WHERE id = ? AND deleted_to IS NULL';

                    $response = $bdd->prepare($sql);
                    $response->execute(array($_GET['idee']));

                    $donnees = $response->fetch();

                    $isModif = isset($_GET['idee']) && $donnees != null;

                    if ($isModif) {
                        echo ('<h1 class="text-center">Modification d\'une idée</h1>');
                    } else {
                        echo ('<h1 class="text-center">Création d\'une idée</h1>');
                    }
                ?>
                    <br>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-md-7">
                                        <div class="form-floating">
                                            <input name="Nom" type="text" value="<?php echo ($donnees['nom']) ?>" class="form-control" id="LabelNom" placeholder="Nom de l'idée" required>
                                            <label for="LabelNom">Nom de l'idée *</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-floating">
                                            <input name="Prix" type="text" value="<?php echo ($donnees['price']) ?>" class="form-control" id="LabelPrix" placeholder="Prix de l'idée">
                                            <label for="LabelPrix">Prix de l'idée</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input name="Commentaire" type="text" value="<?php echo ($donnees['commentaire']) ?>" class="form-control" id="LabelCommentaire" placeholder="Commentaire">
                                            <label for="LabelCommentaire">Commentaire</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-floating">
                                            <input name="Lien" type="url" value="<?php echo ($donnees['lien']) ?>" class="form-control" id="LabelLien" placeholder="Lien de l'idée">
                                            <label for="LabelLien">Lien de l'idée</label>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="col-md-5">
                                        <div class="form-check">
                                            <?php

                                            if ($donnee['droit'] == "proprietaire") {
                                            ?>
                                                <input name="Achat" value="1" class="form-check-input" type="checkbox" id="LabelAchat" <?php if ($donnees['is_buy']) echo 'checked' ?>>
                                                <label class="form-check-label" for="LabelAchat">
                                                    J'ai acheté cette idée
                                                </label>
                                                <?php
                                            } else {
                                                if ($donnees['is_buy']) {
                                                ?>
                                                    <input class="form-check-input" type="checkbox" id="LabelAchat" checked disabled>
                                                    <label class="form-check-label" for="LabelAchat">
                                                        Déjà acheté par le propriétaire
                                                    </label>
                                                <?php
                                                } elseif (isset($donnees['buy_from']) && $donnees['buy_from'] != $_SESSION['id_compte']) {
                                                ?>
                                                    <input name="AchatFrom" value="1" class="form-check-input" type="checkbox" id="LabelAchat" checked disabled>
                                                    <label class="form-check-label" for="LabelAchat">
                                                        Déjà réservé
                                                    </label>
                                                <?php
                                                } else {
                                                ?>
                                                    <input name="AchatFrom" value="1" class="form-check-input" type="checkbox" id="LabelAchat" <?php if ($donnees['buy_from'] == $_SESSION['id_compte']) echo 'checked' ?>>
                                                    <label class="form-check-label" for="LabelAchat">
                                                        Je réserve cette idée
                                                    </label>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </div>
                                        <br>
                                    </div>
                                    <div class="btn-group" role="group">
                                        <div class="col-md-8 text-center">
                                            <button type="submit" name="save" value="<?php echo $donnees['id'] ?>" class="btn btn-primary">Enregistrer</button>
                                            <a class="btn btn-secondary" href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste'] ?? $donnee['partage']; ?>">
                                                Annuler
                                            </a>
                                        </div>
                                        <?php
                                        if (isset($_GET['idee']) && $donnees != null && $donnee['droit'] == "proprietaire") {
                                        ?>
                                            <br>
                                            <br>
                                            <div class="col-md-4 text-center">
                                                <button type="submit" name="delete" value="<?php echo $donnees['id'] ?>" class="btn btn-danger">Supprimer</button>
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