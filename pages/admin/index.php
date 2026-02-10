<?php

session_start();

include('../../scripts/verif/index.php');

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
        foreach ($_POST['Comptes'] as $value) {
            if ($value != 'tous') {

                $sql3 = 'INSERT INTO lic_notif (id_compte, titre, message)
                        VALUES (?, ?, ?)';

                $response3 = $bdd->prepare($sql3);
                $response3->execute(array($value, $_POST['Titre'], $_POST['Message']));
                $response3->closeCursor();
            } else {

                $sql1 = 'SELECT id
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
    <title>Listes d'idées cadeau - Connexion</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row">
            <div class="col-md-12">
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
                ?>
                <h1 class="text-center">Panel Admin</h1>
                <br>
                <h3 class="text-center">Notification</h3>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="form-floating">
                                        <input name="Titre" type="text" class="form-control" id="LabelTitre" placeholder="Titre" required>
                                        <label for="LabelTitre">Titre</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-7">
                                    <select class="form-select" name="Comptes[]" multiple required>
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
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <textarea name="Message" class="form-control" id="LabelMessage" aria-describedby="DescriptionMessage" placeholder="Message" required></textarea>
                                        <label for="LabelMessage">Message</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-12">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Créer & Envoyer</button>
                                    </div>
                                </div>
                            </div>
                    </div>
                    </form>
                </div>
                <br>
                <h3 class="text-center">Maintenance</h3>
                <br>
                <div class="card">
                    <div class="card-body">
                        <h5>Nettoyage de l'historique de connexion</h5>
                        <p style="color: #666; font-size: 14px;">
                            Supprime les connexions de plus d'un an et conserve seulement les 10 dernières par utilisateur.
                        </p>
                        <form action="" method="post" style="display: inline;">
                            <button type="submit" name="cleanup_connexions" value="1" class="btn btn-warning">
                                🧹 Nettoyer l'historique
                            </button>
                        </form>
                        <p style="color: #999; font-size: 12px; margin-top: 10px;">
                            ⚠️ Cette opération ne peut pas être annulée
                        </p>
                    </div>
                </div>
            </div>
            <br>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>