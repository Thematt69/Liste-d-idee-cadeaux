<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

$alert = false;
$info = false;

if (isset($_POST['Mail'])) {

    if (($_POST['NewMDP'] != $_POST['MDP'])) {

        $sql = 'SELECT motdepasse
                FROM lic_compte
                WHERE id = ? AND deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array($_SESSION['id_compte']));

        $donnee = $response->fetch();

        $response->closeCursor();

        if (password_verify($_POST['MDP'], $donnee['motdepasse'])) {
            $mail = $mail;
            $prenom = htmlentities($_POST['Prénom']);
            $nom = htmlentities($_POST['Nom']);
            $motDePasse = password_hash(htmlentities($_POST['NewMDP']), PASSWORD_DEFAULT);
            $date = new DateTime(htmlentities($_POST['Naissance']));
            $dateNaissance = $date->format('Y-m-d');
            $mail = htmlentities($_POST['Mail']);

            $sql = 'UPDATE lic_compte
            SET prenom = ?, nom = ?, motdepasse = ?, date_naissance = ?, mail = ?
            WHERE mail = ?';

            $response = $bdd->prepare($sql);
            $response->execute(array($prenom, $nom, $motDePasse, $dateNaissance, $mail, $mail));

            $info = 'Vos informations ont bien été modifiées.';

            $response->closeCursor();
        } else {
            $alert = 'Mot de passe incorrect !';
        }
    } else {
        $alert = 'Mot de passe identique !';
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Mon compte</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

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
                } else if ($info) {
                ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $info; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                }
                ?>
                <h1 class="text-center">Matthieu DEVILLIERS</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <?php

                            $sql = 'SELECT prenom,nom,date_naissance,mail
                                    FROM lic_compte
                                    WHERE id = ? AND deleted_to IS NULL';

                            $response = $bdd->prepare($sql);
                            $response->execute(array(1));

                            $donnees = $response->fetch();

                            ?>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Prénom" type="text" class="form-control" id="LabelPrénom" placeholder="Prénom" value="<?php echo $donnees['prenom'] ?>" required>
                                        <label for="LabelPrénom">Prénom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Nom" type="text" class="form-control" id="LabelNom" placeholder="Nom" value="<?php echo $donnees['nom'] ?>" required>
                                        <label for="LabelNom">Nom</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" aria-describedby="DescriptionMDP" placeholder="Mot de passe" required>
                                        <label for="LabelMDP">Mot de passe actuel</label>
                                        <small id="DescriptionMDP" class="form-text text-muted">Votre mot de passe est enregistré dans un format crypté, il est impossible pour nous de le récupérer.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="NewMDP" type="password" class="form-control" id="LabelNewMDP" aria-describedby="DescriptionMDP" placeholder="Confirmer mot de passe" required>
                                        <label for="LabelNewMDP">Nouveau mot de passe</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Naissance" type="date" class="form-control" id="LabelNaissance" aria-describedby="DescriptionNaissance" placeholder="Date de naissance" value="<?php echo $donnees['date_naissance'] ?>" required>
                                        <label for="LabelNaissance">Date de naissance</label>
                                        <small id="DescriptionNaissance" class="form-text text-muted">Votre date de naissance est un moyen de sécuriser les listes que vous partagez.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" aria-describedby="DescriptionMail" placeholder="Adresse mail" value="<?php echo $donnees['mail'] ?>" required>
                                        <label for="LabelMail">Adresse mail</label>
                                        <small id="DescriptionMail" class="form-text text-muted">Votre adresse mail nous permet de vous transmet toutes les informations vous concernant.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                            <?php $response->closeCursor(); ?>
                        </form>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>