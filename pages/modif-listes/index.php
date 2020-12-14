<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Création / Modification de liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
                <br>
                <h1 class="text-center">Création / Modification de liste</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input name="Nom de la liste" type="text" class="form-control" id="LabelNom" placeholder="Nom de la liste" required>
                                        <label for="LabelNom">Nom de la liste</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <select class="form-select" name="partage" aria-label="Paramètres de partage">
                                        <option value="prive">Privé</option>
                                        <option value="lien">Partagée par lien</option>
                                        <option value="secure">Partagée par lien (sécurisé)</option>
                                    </select>
                                    <br>
                                </div>
                                <div class="col-6 col-md-4 text-center">
                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                    <br>
                                </div>
                                <div class="col-6 col-md-4 text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <br>
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