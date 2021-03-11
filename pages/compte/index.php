<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
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
    <title>Liste d'idée cadeaux - Mon compte</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
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

                $sql = 'SELECT prenom,nom,mail
                        FROM lic_compte
                        WHERE id = ? AND deleted_to IS NULL';

                $response = $bdd->prepare($sql);
                $response->execute(array($_SESSION['id_compte']));

                $donnees = $response->fetch();

                ?>
                <h1 class="text-center"><?php echo ($donnees['prenom'] . " " . strtoupper($donnees['nom'])); ?></h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating">
                                        <input name="Prénom" type="text" class="form-control" id="LabelPrénom" placeholder="Prénom" value="<?php echo $donnees['prenom'] ?>" required>
                                        <label for="LabelPrénom">Prénom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating">
                                        <input name="Nom" type="text" class="form-control" id="LabelNom" placeholder="Nom" value="<?php echo $donnees['nom'] ?>" required>
                                        <label for="LabelNom">Nom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating">
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                        <label for="LabelMDP">Mot de passe actuel</label>
                                        <small id="DescriptionMDP" class="form-text text-muted">Votre mot de passe est enregistré dans un format crypté, il est impossible pour nous de le récupérer.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6 col-xl-4">
                                    <div class="form-floating">
                                        <input name="NewMDP" type="password" class="form-control" id="LabelNewMDP" aria-describedby="DescriptionMDP" placeholder="Nouveau mot de passe">
                                        <label for="LabelNewMDP">Nouveau mot de passe</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-12 col-xl-8">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" aria-describedby="DescriptionMail" placeholder="Adresse mail" value="<?php echo $donnees['mail'] ?>" required>
                                        <label for="LabelMail">Adresse mail</label>
                                        <small id="DescriptionMail" class="form-text text-muted">Votre adresse mail nous permet de vous transmet toutes les informations vous concernant.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
                                </div>
                            </div>
                            <?php $response->closeCursor(); ?>
                        </form>
                    </div>
                </div>
                <br>
                <h3 class="text-center">Historique de connexion</h3>
                <br>
                <div class="card">
                    <div class="card-body text-center">
                        <div class="table-responsive">
                            <table class="table text-center align-middle justify-content-">
                                <thead>
                                    <tr>
                                        <th class="col">Date</th>
                                        <th class="col">Adresse IP</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php

                                    $sql1 = 'SELECT  connected_to,adresse_ip_v4
                                            FROM lic_connexion
                                            WHERE id_compte = ?
                                            ORDER BY connected_to DESC
                                            LIMIT 20';

                                    $response1 = $bdd->prepare($sql1);
                                    $response1->execute(array($_SESSION['id_compte']));

                                    while ($donnees = $response1->fetch()) {
                                        $datatime = new DateTime($donnees['connected_to']);
                                    ?>
                                        <tr>
                                            <td><?php echo ($datatime->format('d/m/Y H:i:s')) ?></td>
                                            <td><?php echo ($donnees['adresse_ip_v4']) ?></td>
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
                <br>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>