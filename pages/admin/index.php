<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
} elseif (!isset($_SESSION['fonction']) || $_SESSION['fonction'] != 'admin') {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
}

$alert = false;
$info = false;

if (isset($_POST['Comptes'])) {
    if ($_POST['Comptes'][0] == 'tous' && count($_POST['Comptes']) > 1) {
        $alert = 'Vous ne pouvez pas sélectionez "Tous les comptes" et d\'autres comptes !';
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
        $info = 'Les notifications ont bien été envoyés.';
    }
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeaux - Connexion</title>

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
                                        <input name="Message" type="text" class="form-control" id="LabelMessage" aria-describedby="DescriptionMessage" placeholder="Message" required>
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
            </div>
            <br>
        </div>
    </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>