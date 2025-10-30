<?php

session_start();

include('../../scripts/verif/index.php');
include('../../scripts/mail/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
}

$alert = false;
$info = false;

$invalide = false;

if ($_POST['reset']) {
    $sql2 = 'UPDATE lic_compte
            SET motdepasse = ?
            WHERE mail = ? AND motdepasse = ?';

    $response2 = $bdd->prepare($sql2);
    $response2->execute(array(password_hash(htmlentities($_POST['MDP']), PASSWORD_DEFAULT), htmlentities($_POST['Mail']), htmlentities($_POST['reset'])));
    $response2->closeCursor();

    $info = "Votre mot de passe a bien été modifié.";

    $sql3 = 'INSERT INTO lic_notif (id_compte, titre, message)
            VALUES ((SELECT id FROM lic_compte WHERE mail = ?),?,?)';

    $msg =
        "Votre mot de passe vient d'être modifié, si vous n'êtes pas à l'origine de ce changement, nous vous recommandons de le modifier directement dans l'onglet \"Mon compte\" ou en contactant le support.";

    $response3 = $bdd->prepare($sql3);
    $response3->execute(array(htmlentities($_POST['Mail']), 'Mot de passe modifié', $msg));
    $response3->closeCursor();

    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
} elseif (isset($_POST['Mail'])) {

    $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => '6Lc-ZgkaAAAAAAcf5v9GqcfTfm2o6lzOq1Y6kftU', 'response' => $_POST['g-recaptcha-response']);

    $options = array(
        'http' => array(
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result !== FALSE) {
        $response = json_decode($result);
    }

    if ($response->success) {

        $sql = 'SELECT id
                FROM lic_compte
                WHERE mail = ? AND deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Mail'])));

        $donnee = $response->fetch();

        if ($donnee != null) {
            // Un compte existe

            $sql1 = 'UPDATE lic_compte
                    SET motdepasse = ?
                    WHERE mail = ?';

            // Lien random de partage
            $rand = bin2hex(random_bytes(32));
            $hashedRand = password_hash(htmlentities($rand), PASSWORD_DEFAULT);

            $response1 = $bdd->prepare($sql1);
            $response1->execute(array(htmlentities($hashedRand), htmlentities($_POST['Mail'])));
            $response1->closeCursor();

            // Contenu du mail
            $contenu = '
                <html>
                    <body>
                        <h3>Mot de passe oublié</h3>
                        <br>
                        <p>Bonjour,</p>
                        <p>Vous avez demandé à réinitialiser votre mot de passe, pour continuer, cliquer sur le lien ci-dessous.</p>
                        <p><a href="https://family.matthieudevilliers.fr/pages/reset-password/?reset=' . htmlentities($rand) . '">https://family.matthieudevilliers.fr/pages/reset-password/?reset=' . htmlentities($rand) . '</a></p>
                        <br>
                        <p>L\'équipe de Listes d\'idées cadeaux</p>
                    </body>
                </html>
            ';
            if (envoiMail(htmlentities($_POST['Mail']), "Mot de passe oublié - Listes d'idées cadeau", $contenu)) {
                // Le mail a bien été envoyé
                $info = "Un mail va prochainement vous être envoyé à l'adresse fournie.";
            } else {
                // Le mail n'a pas été envoyé
                $alert = "Une erreur est survenue lors de l'envoi du mail.";
            }
        } else {
            // Aucun compte existant
            $alert = "Cette adresse mail n'est pas inscrite.";
        }

        $response->closeCursor();
    } else {
        $alert = 'CAPTCHA invalide ou manquant !';
    }
} else {
    $alert = 'La réinitialisation d\'un mot de passe est définitive, vous ne pourrez plus revenir en arrière.';
}

if ($_GET['reset']) {
    $sql = 'SELECT motdepasse
            FROM lic_compte
            WHERE motdepasse = ? AND deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_GET['reset'])));

    $donnee = $response->fetch();

    if (password_verify($_GET['reset'], $donnee['motdepasse'])) {
        $invalide = true;
        // Le lien n'est plus valide
        $alert = "Le lien n'est plus valide !";
    }

    $response->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Réinitialiser votre mot de passe</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

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
                } elseif ($info) {
                ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $info; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                }
                ?>
                <h1 class="text-center">Mot de passe oublié</h1>
                <br>
                <?php
                if (isset($_GET['reset'])) {
                ?>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input name="Mail" type="email" class="form-control" id="LabelMail" placeholder="name@example.com" required <?php if ($invalide) echo 'disabled aria-disabled' ?>>
                                            <label for="LabelMail">Adresse mail</label>
                                        </div>
                                        <br>
                                        <div class="form-floating">
                                            <input name="MDP" type="password" class="form-control" id="LabelMDP" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required <?php if ($invalide) echo 'disabled aria-disabled' ?>>
                                            <label for="LabelMDP">Nouveau mot de passe</label>
                                        </div>
                                        <br>
                                        <div class=" d-flex justify-content-center">
                                            <div class="g-recaptcha" data-sitekey="6Lc-ZgkaAAAAAEoCEsUwvVPygJPyhxGDtPIvkppO"></div>
                                        </div>
                                        <br>
                                        <div class="text-center">
                                            <button type="submit" name="reset" value="<?php echo $_GET['reset'] ?>" class="btn btn-primary" <?php if ($invalide) echo 'disabled aria-disabled' ?>>Réinitialiser le mot de passe</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php
                } else {
                ?>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-floating">
                                            <input name="Mail" type="email" class="form-control" id="LabelMail" placeholder="name@example.com" required <?php if ($invalide) echo 'disabled aria-disabled' ?>>
                                            <label for="LabelMail">Adresse mail</label>
                                        </div>
                                        <br>
                                        <div class=" d-flex justify-content-center">
                                            <div class="g-recaptcha" data-sitekey="6Lc-ZgkaAAAAAEoCEsUwvVPygJPyhxGDtPIvkppO"></div>
                                        </div>
                                        <br>
                                        <div class="text-center">
                                            <button type="submit" class="btn btn-primary" <?php if ($invalide) echo 'disabled aria-disabled' ?>>Demander la réinitialisation</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                <?php
                }
                ?>
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>