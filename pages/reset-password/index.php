<?php

session_start();

include('../../scripts/verif/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
}

$alert = false;

if (isset($_POST['Mail'])) {

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
        $response->execute(array($_POST['Mail']));

        $donnee = $response->fetch();

        if ($donnee != null) {
            // Un compte existe

            // TODO - remplacer le mot de passe par des chiffres random

            // TODO - Envoyer un mail avec $_GET['reset'] = chiffres random
        } else {
            // Aucun compte existant
            $alert = "Cette adresse mail n'est pas inscrite.";
        }

        $response->closeCursor();
    } else {
        $alert = 'CAPTCHA invalide !';
    }
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Liste d'idée cadeaux - Réinitialiser votre mot de passe</title>

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
                }
                ?>
                <h1 class="text-center">Réinitialiser votre mot de passe</h1>
                <br>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Cette page n'est pas encore disponible</strong>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <!-- <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" placeholder="name@example.com" required>
                                        <label for="LabelMail">Adresse mail</label>
                                    </div>
                                    <br>
                                    <div class=" d-flex justify-content-center">
                                        <div class="g-recaptcha" data-sitekey="6Lc-ZgkaAAAAAEoCEsUwvVPygJPyhxGDtPIvkppO"></div>
                                    </div>
                                    <br>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Réinitialiser</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div> -->
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>