<?php include('script.php'); ?>
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
                <h1 class="text-center">Matthieu DEVILLIERS</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelPrénom">Prénom</label>
                                        <input name="Prénom" type="text" class="form-control" id="LabelPrénom">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelNom">Nom</label>
                                        <input name="Nom" type="text" class="form-control" id="LabelNom">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelMDP">Mot de passe</label>
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" aria-describedby="DescriptionMDP">
                                        <small id="DescriptionMDP" class="form-text text-muted">Votre mot de passe est enregistré dans un format crypté.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelConfirmationMDP">Confirmer mot de passe</label>
                                        <input name="ConfirmationMDP" type="password" class="form-control" id="LabelConfirmationMDP" aria-describedby="DescriptionMDP">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelNaissance">Date de naissance</label>
                                        <input name="Naissance" type="date" class="form-control" id="LabelNaissance" aria-describedby="DescriptionNaissance">
                                        <small id="DescriptionNaissance" class="form-text text-muted">Votre date de naissance est demandée au personne qui souhaitent accéder à vos listes partagées en lien sécurisé.</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="LabelMail">Adresse mail</label>
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" aria-describedby="DescriptionMail">
                                        <small id="DescriptionMail" class="form-text text-muted">Votre adresse mail nous permet de vous transmet toutes les informations vous concernant.</small>
                                    </div>
                                </div>
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>