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
            <div class="col-sm-12 col-md-9 col-lg-6">
                <br>
                <h1 class="text-center">Liste Noël 2020</h1>
                <br>
                <div class="card text-dark bg-light">
                    <div class="card-body">
                        <!-- <form action="" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-floating">
                                        <input name="Mail" type="email" class="form-control" id="LabelMail" placeholder="name@example.com" required>
                                        <label for="LabelMail">Adresse mail</label>
                                    </div>
                                    <br>
                                    <div class="form-floating">
                                        <input name="MDP" type="password" class="form-control" id="LabelMDP" placeholder="Mot de passe" required>
                                        <label for="LabelMDP">Mot de passe</label>
                                    </div>
                                    <br>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary">Se connecter</button>
                                    </div>
                                </div>
                            </div>
                        </form> -->
                    </div>
                </div>
                <br>
            </div>
        </div>
    </div>

</body>

</html>