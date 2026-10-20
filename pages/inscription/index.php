<?php

session_start();

include('../../scripts/verif/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
}

// Retourne l'adresse IP de l'utilisateur
function getIp()
{
    // Prioritize REMOTE_ADDR as it cannot be easily spoofed
    if (!empty($_SERVER['REMOTE_ADDR'])) {
        return $_SERVER['REMOTE_ADDR'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Only use X-Forwarded-For if REMOTE_ADDR is not available
        // Take only the first IP in case of multiple proxies
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } else {
        return "Introuvable";
    }
}

$alert = false;

if (isset($_POST['Mail'])) {
    $sql = 'SELECT mail
            FROM lic_compte
            WHERE mail = ? AND deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array(htmlentities($_POST['Mail'])));

    $donnee = $response->fetch();

    if (!isset($donnee['mail'])) {

        $prenom = htmlentities($_POST['Prénom']);
        $nom = htmlentities($_POST['Nom']);
        $motdepasse = password_hash(htmlentities($_POST['MDP']), PASSWORD_DEFAULT);
        $mail = htmlentities($_POST['Mail']);

        $sql1 = 'INSERT INTO lic_compte (prenom,nom,motdepasse,mail)
                VALUES (?,?,?,?)';

        $response1 = $bdd->prepare($sql1);
        $response1->execute(array($prenom, $nom, $motdepasse, $mail));
        $response1->closeCursor();

        $sql2 = 'SELECT id
                    FROM lic_compte
                    WHERE mail = ? AND deleted_to IS NULL';

        $response2 = $bdd->prepare($sql2);
        $response2->execute(array($mail));
        $donnee2 = $response2->fetch();

        $_SESSION['id_compte'] = $donnee2['id'];

        $response2->closeCursor();

        $sql3 = 'INSERT INTO lic_connexion (id_compte, adresse_ip_v4)
                    VALUES (?, ?)';

        $response3 = $bdd->prepare($sql3);
        $response3->execute(array($_SESSION['id_compte'], getIp()));
        $response3->closeCursor();

        header('Location: https://family.matthieudevilliers.fr/pages/listes/');
        exit();
    } else {
        $alert = "L'adresse mail est déjà utilisée !";
    }

    $response->closeCursor();
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Inscription</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-12 col-lg-8">
                    <?php
                    if ($alert) {
                    ?>
                        <div class="alert alert-danger fade show" role="alert">
                            <strong><?php echo $alert; ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    }
                    ?>
                    <h1 class="text-center mb-4">Inscription</h1>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input name="Prénom" type="text" class="form-control" id="LabelPrénom"
                                                autocomplete="given-name" placeholder="Prénom" required>
                                            <label for="LabelPrénom">Prénom</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input name="Nom" type="text" class="form-control" id="LabelNom"
                                                autocomplete="family-name" placeholder="Nom" required>
                                            <label for="LabelNom">Nom</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input name="MDP" type="password" class="form-control" id="LabelMDP"
                                                autocomplete="new-password" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                            <label for="LabelMDP">Mot de passe</label>
                                        </div>
                                        <div id="DescriptionMDP" class="form-text mb-3">Votre mot de passe
                                                est enregistré dans un format haché, il est impossible pour nous de le
                                                récupérer.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-floating">
                                            <input name="Mail" type="email" class="form-control" id="LabelMail"
                                                autocomplete="email" aria-describedby="DescriptionMail" placeholder="Adresse mail" required>
                                            <label for="LabelMail">Adresse mail</label>
                                        </div>
                                        <div id="DescriptionMail" class="form-text mb-3">Votre adresse mail
                                                nous permet de vous transmet toutes les informations vous
                                                concernant.</div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-field">
                                            <input name="CGU" class="check" type="checkbox" id="LabelCGU"
                                                required>
                                            <label for="LabelCGU">
                                                J'accepte les <a
                                                    href="https://family.matthieudevilliers.fr/pages/cgu/">Conditions
                                                    Général d'Utilisation</a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <p class="small text-body-secondary">
                                            Les informations sur le traitement de vos données personnelles et vos droits sont disponibles dans les <a href="https://family.matthieudevilliers.fr/pages/mentions-legales/#donnees-personnelles">mentions légales</a>.
                                        </p>
                                        <button type="submit" class="btn btn-primary">S'inscrire</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="https://family.matthieudevilliers.fr/pages/connexion/">
                            Déjà un compte ? Je me connecte
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>