<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Idées de ma liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row  justify-content-center">
            <div class="col-sm-12">
                <br>
                <h1 class="text-center">Liste Noël 2020</h1>
                <br>

                <div class="card text-dark bg-light">
                    <div class="card-body text-center">
                        <div class="table-responsive">
                            <table class="table table-light text-center align-middle">
                                <thead>
                                    <tr>
                                        <th class="col-4">Nom</th>
                                        <th>Lien</th>
                                        <th>Image</th>
                                        <th>Déja acheté</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Casque audio</td>
                                        <td><a href="https://moncasque.fr">https://moncasque.fr</a></td>
                                        <td>Aucune</td>
                                        <td><input class="form-check-input" type="checkbox" checked disabled></td>
                                        <td><button class="btn btn-outline-secondary" type="button">Modifier</button></td>
                                    </tr>
                                    <tr>
                                        <td>Casque audio</td>
                                        <td><a href="https://moncasque.fr">https://moncasque.fr</a></td>
                                        <td>Aucune</td>
                                        <td><input class="form-check-input" type="checkbox" disabled></td>
                                        <td><button class="btn btn-outline-secondary" type="button">Modifier</button></td>
                                    </tr>
                                    <tr>
                                        <td>Casque audio</td>
                                        <td><a href="https://moncasque.fr">https://moncasque.fr</a></td>
                                        <td>Aucune</td>
                                        <td><input class="form-check-input" type="checkbox" checked disabled></td>
                                        <td><button class="btn btn-outline-secondary" type="button" disabled>Modifier</button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/">Ajouter une idée</a>
                    </div>
                </div>

                <br>
            </div>
        </div>
    </div>

</body>

</html>