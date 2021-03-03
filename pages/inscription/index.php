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

        $sql = 'SELECT mail
            FROM lic_compte
            WHERE mail = ? AND deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array(htmlentities($_POST['Mail'])));

        $donnee = $response->fetch();

        if (!isset($donnee['mail'])) {

            $_SESSION['prenom'] = htmlentities($_POST['Prénom']);
            $_SESSION['nom'] = htmlentities($_POST['Nom']);
            $_SESSION['motdepasse'] = password_hash(htmlentities($_POST['MDP']), PASSWORD_DEFAULT);
            $date = new DateTime(htmlentities($_POST['Naissance']));
            $_SESSION['date_naissance'] = $date->format('Y-m-d');
            $_SESSION['mail'] = htmlentities($_POST['Mail']);

            $sql1 = 'INSERT INTO lic_compte (prenom,nom,motdepasse,date_naissance,mail)
                VALUES (?,?,?,?,?)';

            $response1 = $bdd->prepare($sql1);
            $response1->execute(array($_SESSION['prenom'], $_SESSION['nom'], $_SESSION['motdepasse'], $_SESSION['date_naissance'], $_SESSION['mail']));
            $response1->closeCursor();

            header('Location: https://family.matthieudevilliers.fr/pages/listes/');
        } else {
            $alert = "L'adresse mail est déja utilisé !";
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
    <title>Liste d'idée cadeaux - Inscription</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
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
                <h1 class="text-center">Inscription</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Prénom" type="text" class="form-control" id="LabelPrénom" placeholder="Prénom" required>
                                        <label for="LabelPrénom">Prénom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Nom" type="text" class="form-control" id="LabelNom" placeholder="Nom" required>
                                        <label for="LabelNom">Nom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                        <label for="LabelMDP">Mot de passe</label>
                                        <small id="DescriptionMDP" class="form-text text-muted">Votre mot de passe est enregistré dans un format crypté, il est impossible pour nous de le récupérer.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Naissance" type="date" class="form-control" id="LabelNaissance" aria-describedby="DescriptionNaissance" placeholder="Date de naissance" required>
                                        <label for="LabelNaissance">Date de naissance</label>
                                        <small id="DescriptionNaissance" class="form-text text-muted">Votre date de naissance est un moyen de sécuriser les listes que vous partagez.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" aria-describedby="DescriptionMail" placeholder="Adresse mail" required>
                                        <label for="LabelMail">Adresse mail</label>
                                        <small id="DescriptionMail" class="form-text text-muted">Votre adresse mail nous permet de vous transmet toutes les informations vous concernant.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input name="CGU" class="form-check-input" type="checkbox" id="LabelCGU" required>
                                        <label class="form-check-label" for="LabelCGU">
                                            J'accepte les <a href="https://family.matthieudevilliers.fr/pages/cgu/">Conditions Général d'Utilisation</a>
                                        </label>
                                    </div>
                                    <br>
                                    <div class="d-flex justify-content-center">
                                        <div class="g-recaptcha" data-sitekey="6Lc-ZgkaAAAAAEoCEsUwvVPygJPyhxGDtPIvkppO"></div>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">S'inscrire</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
                <div class="text-center">
                    <a href="https://family.matthieudevilliers.fr/pages/connexion/">
                        Déja un compte ? Je me connecte
                    </a>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>