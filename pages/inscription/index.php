<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Inscription</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
                <br>
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
                                        <small id="DescriptionMDP" class="form-text text-muted">Votre mot de passe est enregistré dans un format crypté.</small>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input name="Naissance" type="date" class="form-control" id="LabelNaissance" aria-describedby="DescriptionNaissance" placeholder="Date de naissance" required>
                                        <label for="LabelNaissance">Date de naissance</label>
                                        <small id="DescriptionNaissance" class="form-text text-muted">Votre date de naissance est demandée au personne qui souhaitent accéder à vos listes partagées en lien sécurisé.</small>
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
                                        <input name="CGU" class="form-check-input" type="checkbox" id="LabelCGU">
                                        <label class="form-check-label" for="LabelCGU">
                                            J'accepte les <a href="https://family.matthieudevilliers.fr/pages/cgu/">Conditions Général d'Utilisation</a>
                                        </label>
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

</html>