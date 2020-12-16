<?php

session_start();

include('../../scripts/verif/index.php');

$alert = false;

if ($_POST['Mail']) {

    $sql = 'SELECT id,motdepasse
            FROM lic_compte
            WHERE mail = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array($_POST['Mail']));

    $donnee = $response->fetch();

    if (password_verify($_POST['MDP'], $donnee['motdepasse'])) {
        // Le mot de passe correspond
        $_SESSION['id_compte'] = $donnee['id'];
        header('Location: https://family.matthieudevilliers.fr/pages/mes-listes/');
    } else {
        // Le mot de passe ne correspond pas
        $alert = true;
    }

    $response->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Connexion</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-sm-12 col-md-9 col-lg-6">
                <br>
                <?php
                if ($alert) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Mot de passe incorrect !</strong>
                        Votre mail ou votre mot de passe est incorrect, merci de réessayer.
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                }
                ?>
                <h1 class="text-center">Connexion</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" placeholder="name@example.com" required>
                                        <label for="LabelMail">Adresse mail</label>
                                    </div>
                                    <br>
                                    <div class="form-floating">
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" placeholder="Mot de passe" required>
                                        <label for="LabelMDP">Mot de passe</label>
                                    </div>
                                    <br>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Se connecter</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <a href="https://family.matthieudevilliers.fr/pages/inscription/">
                        Pas de compte ? Je m'inscris
                    </a>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>