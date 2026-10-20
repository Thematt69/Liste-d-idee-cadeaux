<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
}

$alert = false;
$info = false;

if (isset($_POST['Mail'])) {
    if ($_POST['NewMDP'] == $_POST['MDP']) {
        $alert = 'Mots de passe identiques !';
    } elseif ($_POST['NewMDP'] != "") {

        $sql = 'SELECT motdepasse
                FROM lic_compte
                WHERE id = ? AND deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array($_SESSION['id_compte']));

        $donnee = $response->fetch();

        $response->closeCursor();

        if (password_verify($_POST['MDP'], $donnee['motdepasse'])) {
            $prenom = htmlentities($_POST['Prénom']);
            $nom = htmlentities($_POST['Nom']);
            $motDePasse = password_hash(htmlentities($_POST['NewMDP']), PASSWORD_DEFAULT);
            $mail = htmlentities($_POST['Mail']);

            $sql = 'UPDATE lic_compte
            SET prenom = ?, nom = ?, motdepasse = ?, mail = ?
            WHERE mail = ?';

            $response = $bdd->prepare($sql);
            $response->execute(array($prenom, $nom, $motDePasse, $mail, $mail));

            // Regenerate session ID on password change for security
            session_regenerate_id(true);

            $info = 'Vos informations ont bien été modifiées.';

            $response->closeCursor();
        } else {
            $alert = 'Mot de passe incorrect !';
        }
    } else {
        $sql = 'SELECT motdepasse
                FROM lic_compte
                WHERE id = ? AND deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array($_SESSION['id_compte']));

        $donnee = $response->fetch();

        $response->closeCursor();

        if (password_verify($_POST['MDP'], $donnee['motdepasse'])) {
            $prenom = htmlentities($_POST['Prénom']);
            $nom = htmlentities($_POST['Nom']);
            $mail = htmlentities($_POST['Mail']);

            $sql = 'UPDATE lic_compte
            SET prenom = ?, nom = ?, mail = ?
            WHERE mail = ?';

            $response = $bdd->prepare($sql);
            $response->execute(array($prenom, $nom, $mail, $mail));

            $info = 'Vos informations ont bien été modifiées.';

            $response->closeCursor();
        } else {
            $alert = 'Mot de passe incorrect !';
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Mon compte</title>

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

                    $sql = 'SELECT prenom,nom,mail
                        FROM lic_compte
                        WHERE id = ? AND deleted_to IS NULL';

                    $response = $bdd->prepare($sql);
                    $response->execute(array($_SESSION['id_compte']));

                    $donnees = $response->fetch();

                    ?>
                    <h1 class="text-center mb-4"><?php echo safe_output($donnees['prenom'] . " " . strtoupper($donnees['nom'])); ?></h1>
                    <div class="card">
                        <div class="card-body">
                            <form action="" method="post">
                                <div class="row g-3">
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating">
                                            <input name="Prénom" type="text" class="form-control" id="LabelPrénom" autocomplete="given-name" placeholder="Prénom" value="<?php echo safe_output($donnees['prenom']) ?>" required>
                                            <label for="LabelPrénom">Prénom</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating">
                                            <input name="Nom" type="text" class="form-control" id="LabelNom" autocomplete="family-name" placeholder="Nom" value="<?php echo safe_output($donnees['nom']) ?>" required>
                                            <label for="LabelNom">Nom</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating">
                                            <input name="MDP" type="password" class="form-control" id="LabelMDP" autocomplete="current-password" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                            <label for="LabelMDP">Mot de passe actuel</label>
                                        </div>
                                        <div id="DescriptionMDP" class="form-text">Votre mot de passe est enregistré dans un format haché, il est impossible pour nous de le récupérer.</div>
                                    </div>
                                    <div class="col-md-6 col-xl-4">
                                        <div class="form-floating">
                                            <input name="NewMDP" type="password" class="form-control" id="LabelNewMDP" autocomplete="new-password" placeholder="Nouveau mot de passe">
                                            <label for="LabelNewMDP">Nouveau mot de passe</label>
                                        </div>
                                    </div>
                                    <div class="col-md-12 col-xl-8">
                                        <div class="form-floating">
                                            <input name="Mail" type="email" class="form-control" id="LabelMail" autocomplete="email" aria-describedby="DescriptionMail" placeholder="Adresse mail" value="<?php echo safe_output($donnees['mail']) ?>" required>
                                            <label for="LabelMail">Adresse mail</label>
                                        </div>
                                        <div id="DescriptionMail" class="form-text">Votre adresse mail nous permet de vous transmettre toutes les informations vous concernant.</div>
                                    </div>
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                    </div>
                                </div>
                                <?php $response->closeCursor(); ?>
                            </form>
                        </div>
                    </div>
                    <h2 id="historique-connexion" class="h4 text-center mt-4">Historique de connexion</h2>
                    <p class="text-center mb-3">
                        <small>10 dernières connexions uniquement</small>
                    </p>
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table text-center align-middle" aria-labelledby="historique-connexion">
                                    <caption class="visually-hidden">Historique des dix dernières connexions</caption>
                                    <thead>
                                        <tr>
                                            <th class="col" scope="col">Date</th>
                                            <th class="col" scope="col">Adresse IP</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql1 = 'SELECT  connected_to,adresse_ip_v4
                                            FROM lic_connexion
                                            WHERE id_compte = ?
                                            ORDER BY connected_to DESC
                                            LIMIT 10';

                                        $response1 = $bdd->prepare($sql1);
                                        $response1->execute(array($_SESSION['id_compte']));

                                        while ($donnees = $response1->fetch()) {
                                            $datatime = new DateTime($donnees['connected_to']);
                                        ?>
                                            <tr>
                                                <td><?php echo safe_output($datatime->format('d/m/Y H:i:s')) ?></td>
                                                <td><?php echo safe_output($donnees['adresse_ip_v4']) ?></td>
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>