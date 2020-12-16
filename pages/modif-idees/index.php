<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Création / Modification d'une idée</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-md-12 col-lg-8">
                <br>
                <h1 class="text-center">Création / Modification d'une idée</h1>
                <br>
                <div class="card">
                    <div class="card-body">
                        <form action="" method="post" enctype="multipart/form-data">
                            <?php

                            $sql = 'SELECT nom,lien,image,is_buy
                                    FROM lic_idee
                                    WHERE id = ?';

                            $response = $bdd->prepare($sql);
                            $response->execute(array($_GET['idee']));

                            $donnees = $response->fetch();

                            ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-floating">
                                        <input name="Nom de l'idée" type="text" value="<?php echo ($donnees['nom']) ?>" class="form-control" id="LabelNom" placeholder="Nom de l'idée" required>
                                        <label for="LabelNom">Nom de l'idée</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input name="Lien de l'idée" type="url" value="<?php echo ($donnees['lien']) ?>" class="form-control" id="LabelLien" placeholder="Lien de l'idée">
                                        <label for="LabelLien">Lien de l'idée</label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-md-8">
                                    <input name="image" class="form-control" type="file" accept="image/*">
                                    <br>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input name="Achat" class="form-check-input" type="checkbox" id="LabelAchat" <?php if ($donnees['is_buy']) echo 'checked' ?>>
                                        <label class="form-check-label" for="LabelAchat">
                                            Déjà acheté l'idée
                                        </label>
                                    </div>
                                    <br>
                                </div>
                                <div class="col-6 col-md-4 text-center">
                                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                                    <br>
                                </div>
                                <?php
                                if (isset($_GET['idee']) && $donnees != null) {
                                ?>
                                    <div class="col-6 col-md-4 text-center">
                                        <button type="button" class="btn btn-danger">Supprimer</button>
                                        <br>
                                    </div>
                                <?php
                                }
                                ?>
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