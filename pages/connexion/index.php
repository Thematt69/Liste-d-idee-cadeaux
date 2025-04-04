<?php

session_start();

include ('../../scripts/verif/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
}

// Retourne l'adresse IP de l'utilisateur
function getIp()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } else {
        return "Introuvable";
    }
}

$alert = false;

if (isset($_POST['Mail'])) {
    $sql = 'SELECT id,motdepasse
            FROM lic_compte
            WHERE mail = ? AND deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array($_POST['Mail']));

    $donnee = $response->fetch();

    if (password_verify($_POST['MDP'], $donnee['motdepasse'])) {
        // Le mot de passe correspond
        $_SESSION['id_compte'] = $donnee['id'];

        $sql1 = 'INSERT INTO lic_connexion (id_compte, adresse_ip_v4)
                    VALUES (?, ?)';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($_SESSION['id_compte'], getIp()));
        $response1->closeCursor();

        header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    } else {
        $alert = 'Adresse mail ou mot de passe incorrect !';
    }

    $response->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

    <head>
        <title>Listes d'idées cadeau - Connexion</title>

        <!-- Import -->
        <?php include ('../../widgets/import/index.php'); ?>
    </head>

    <body class="d-flex flex-column h-100">

        <?php include ('../../widgets/navbar/index.php'); ?>

        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-12 col-md-9 col-lg-6">
                    <br>
                    <?php
                    if ($alert) {
                        ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong><?php echo $alert; ?></strong>
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
                                            <input name="Mail" type="email" class="form-control" id="LabelMail"
                                                placeholder="name@example.com" required>
                                            <label for="LabelMail">Adresse mail</label>
                                        </div>
                                        <br>
                                        <div class="form-floating">
                                            <input name="MDP" type="password" class="form-control" id="LabelMDP"
                                                aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                            <label for="LabelMDP">Mot de passe</label>
                                            <small id="DescriptionMDP" class="form-text text-muted"><a
                                                    href="https://family.matthieudevilliers.fr/pages/reset-password/">Mot
                                                    de passe oublié</a></small>
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

    <?php include ('../../widgets/footer/index.php'); ?>

</html>