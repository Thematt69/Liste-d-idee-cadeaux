<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <title>Liste d'idée cadeaux - Idées de ma liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body>

    <?php
    include('../../widgets/navbar/index.php');

    $sql = 'SELECT id,nom,partage
            FROM lic_liste
            WHERE lien_partage = ? AND deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array($_GET['liste']));

    $donnee = $response->fetch();

    if ($donnee == null) {
    ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <strong>Liste introuvable !</strong> Nous sommes désolées, mais nous n'avons pas trouvé votre liste. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/mes-listes/" class="alert-link">vos listes</a>.
                    </div>
                    <br>
                </div>
            </div>
        </div>

    <?php
    } else {
    ?>
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-sm-12">
                    <br>
                    <h1 class="text-center">
                        <?php
                        switch ($donnee['partage']) {
                            case 'lien':
                                echo ('<i class="fas fa-users fa-2x" style="font-size: 1.5rem;"></i>');
                                break;

                            case 'secure':
                                echo ('<i class="fas fa-user-lock fa-2x" style="font-size: 1.5rem;"></i>');
                                break;

                            default:
                                echo ('<i class="fas fa-lock fa-2x" style="font-size: 1.5rem;"></i>');
                                break;
                        }
                        ?>
                        <?php echo $donnee['nom'] ?>
                        <a href="https://family.matthieudevilliers.fr/pages/modif-listes/?liste=<?php echo ($_GET['liste']) ?>" class="link-dark">
                            <i class="fas fa-pen fa-2x" style="font-size: 1.5rem;"></i>
                        </a>
                    </h1>
                    <br>

                    <div class="card text-dark bg-light">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table table-light text-center align-middle">
                                    <thead>
                                        <tr>
                                            <th class="col">Nom</th>
                                            <th class="col lien">Lien</th>
                                            <th class="col image">Image</th>
                                            <th class="col">Déja acheté</th>
                                            <th class="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql1 = 'SELECT id,nom,lien,image,is_buy
                                            FROM lic_idee
                                            WHERE id_liste = ? AND deleted_to IS NULL';

                                        $response1 = $bdd->prepare($sql1);
                                        $response1->execute(array($donnee['id']));

                                        while ($donnees = $response1->fetch()) {
                                        ?>
                                            <tr>
                                                <td><?php echo ($donnees['nom']) ?></td>
                                                <td class="lien">
                                                    <a href="<?php echo ($donnees['lien']) ?>">
                                                        <?php
                                                        if ($donnees['lien'] != '') echo (substr($donnees['lien'], 0, 40));
                                                        if (strlen($donnees['lien']) > 40) echo '...';
                                                        ?>
                                                    </a>
                                                </td>
                                                <td class="image">
                                                    <?php
                                                    if ($donnees['image'] == null) {
                                                        echo ('Aucune');
                                                    } else {
                                                    ?>
                                                        <a href="https://family.matthieudevilliers.fr/images/<?php echo ($donnees['image']) ?>">
                                                            <?php
                                                            if ($donnees['image'] != '') echo (substr($donnees['image'], 0, 20));
                                                            if (strlen($donnees['image']) > 20) echo '...';
                                                            ?>
                                                        </a>
                                                    <?php
                                                    }
                                                    ?>
                                                </td>
                                                <td><input class="form-check-input" type="checkbox" <?php if ($donnees['is_buy']) echo ('checked') ?> disabled></td>
                                                <td><a class="btn btn-outline-secondary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?idee=<?php echo $donnees['id']; ?>">Modifier</a></td>
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?liste=<?php echo $_GET['liste']; ?>">Ajouter une idée</a>
                            <?php
                            if ($donnee['partage'] != 'prive') {
                            ?>
                                <p>Lien de partage :
                                    <a class="link-primary" target="_blank" href="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste']; ?>">
                                        https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste']; ?>
                                    </a>
                                </p>
                            <?php
                            }
                            ?>
                        </div>
                    </div> <br>
                </div>
            </div>
        </div>
    <?php
    }

    $response->closeCursor();

    ?>

</body>

</html>