<?php

session_start();

include('../../scripts/verif/index.php');
include('../../scripts/mail/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
} elseif (!isset($_SESSION['fonction']) || $_SESSION['fonction'] != 'admin') {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
}

$alert = false;
$info = false;

// Nettoyage de l'historique de connexion
if (isset($_POST['cleanup_connexions'])) {
    try {
        cleanup_old_connexions($bdd);
        $info = 'Historique de connexion nettoyé avec succès ! Les connexions de plus d\'un an et au-delà des 10 dernières par utilisateur ont été supprimées.';
    } catch (Exception $e) {
        $alert = 'Erreur lors du nettoyage : ' . htmlspecialchars($e->getMessage());
    }
}

if (isset($_POST['Comptes'])) {
    if ($_POST['Comptes'][0] == 'tous' && count($_POST['Comptes']) > 1) {
        $alert = 'Vous ne pouvez pas sélectionner "Tous les comptes" et d\'autres comptes !';
    } else {
        $contenu = '
            <html>
                <body>
                    <h3>' . htmlspecialchars($_POST['Titre'], ENT_QUOTES, 'UTF-8') . '</h3>
                    <br>
                    <p>Bonjour,</p>
                    <p>' . nl2br(htmlspecialchars($_POST['Message'], ENT_QUOTES, 'UTF-8')) . '</p>
                    <br>
                    <p>L\'équipe de Listes d\'idées cadeaux</p>
                </body>
            </html>
        ';

        foreach ($_POST['Comptes'] as $value) {
            if ($value != 'tous') {

                $sql3 = 'INSERT INTO lic_notif (id_compte, titre, message)
                        VALUES (?, ?, ?)';

                $response3 = $bdd->prepare($sql3);
                $response3->execute(array($value, $_POST['Titre'], $_POST['Message']));
                $response3->closeCursor();

                $sql4 = 'SELECT mail
                        FROM lic_compte
                        WHERE id = ? AND deleted_to IS NULL';

                $response4 = $bdd->prepare($sql4);
                $response4->execute(array($value));
                $donnees4 = $response4->fetch();
                $response4->closeCursor();

                if ($donnees4 && $donnees4['mail']) {
                    if (!envoiMail($donnees4['mail'], "Nouvelle notification - Listes d'idées cadeau", $contenu)) {
                        $alert = 'Une erreur est survenue lors de l\'envoi d\'un ou plusieurs mails.';
                    }
                }
            } else {

                $sql1 = 'SELECT id, mail
                        FROM lic_compte
                        WHERE deleted_to IS NULL';

                $response1 = $bdd->prepare($sql1);
                $response1->execute(array());

                while ($donnees = $response1->fetch()) {
                    $sql2 = 'INSERT INTO lic_notif (id_compte, titre, message)
                        VALUES (?, ?, ?)';

                    $response2 = $bdd->prepare($sql2);
                    $response2->execute(array($donnees['id'], $_POST['Titre'], $_POST['Message']));
                    $response2->closeCursor();

                    if ($donnees['mail']) {
                        envoiMail($donnees['mail'], "Nouvelle notification - Listes d'idées cadeau", $contenu);
                    }
                }

                $response1->closeCursor();
            }
        }
        $info = 'Les notifications ont bien été envoyées.';
    }
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Panel Admin</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <?php
                    if ($alert) {
                    ?>
                        <div class="alert alert-danger fade show" role="alert">
                            <strong><?php echo $alert; ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    } elseif ($info) {
                    ?>
                        <div class="alert alert-success fade show" role="alert">
                            <?php echo $info; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    }
                    ?>
                    <h1 class="text-center mb-4">Panel Admin</h1>
                    <h2 class="h4 text-center mb-3">Notification</h2>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row g-3">
                                    <div class="col-md-5 mb-3">
                                        <div class="form-floating">
                                            <input name="Titre" type="text" class="form-control" id="LabelTitre" placeholder="Titre" required>
                                            <label for="LabelTitre">Titre</label>
                                        </div>
                                    </div>
                                    <div class="col-md-7 mb-3">
                                        <label for="Comptes" class="form-label">Destinataires</label>
                                        <select class="form-select" id="Comptes" name="Comptes[]" multiple required>
                                            <option selected value="tous">Tous les comptes</option>
                                            <?php

                                            $sql1 = 'SELECT id,prenom,nom,mail
                                                FROM lic_compte
                                                WHERE deleted_to IS NULL';

                                            $response1 = $bdd->prepare($sql1);
                                            $response1->execute(array());

                                            while ($donnees = $response1->fetch()) {
                                            ?>
                                                <option value="<?php echo ($donnees['id']) ?>"><?php echo ($donnees['prenom'] . " " . strtoupper($donnees['nom']) . " - " . $donnees['mail']); ?></option>
                                            <?php
                                            }

                                            $response1->closeCursor();

                                            ?>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <div class="form-floating">
                                            <textarea name="Message" class="form-control" id="LabelMessage" placeholder="Message" required></textarea>
                                            <label for="LabelMessage">Message</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary">Créer & Envoyer</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <h2 class="h4 text-center mt-4 mb-3">Maintenance</h2>
                    <div class="card">
                        <div class="card-body">
                            <h5>Nettoyage de l'historique de connexion</h5>
                            <p class="text-body-secondary small">
                                Supprime les connexions de plus d'un an et conserve seulement les 10 dernières par utilisateur.
                            </p>
                            <form action="" method="post" class="d-inline">
                                <button type="submit" name="cleanup_connexions" value="1" class="btn btn-warning">
                                    🧹 Nettoyer l'historique
                                </button>
                            </form>
                            <p class="text-body-secondary small mt-2">
                                ⚠️ Cette opération ne peut pas être annulée
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>