<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeaux - Idées de ma liste</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php
    include('../../widgets/navbar/index.php');

    $sql = 'SELECT lic_liste.id as id, lic_liste.nom as nom, lic_liste.partage as partage, lic_autorisation.type as droit
            FROM lic_liste
            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
            WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL AND lic_autorisation.deleted_to IS NULL';

    $response = $bdd->prepare($sql);
    $response->execute(array($_SESSION['id_compte'], $_GET['liste']));

    $donnee = $response->fetch();

    if ($donnee == null) {
    ?>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <br>
                    <div class="alert alert-danger" role="alert">
                        <strong>Liste introuvable !</strong> Nous sommes désolées, mais nous n'avons pas trouvé votre liste. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.
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
                <div class="col-12">
                    <br>
                    <h1 class="text-center">
                        <?php
                        switch ($donnee['partage']) {
                            case 'public':
                                echo ('<i class="fas fa-users fa-2x" style="font-size: 1.5rem;"></i>');
                                break;

                            case 'limite':
                                echo ('<i class="fas fa-user-lock fa-2x" style="font-size: 1.5rem;"></i>');
                                break;

                            default:
                                echo ('<i class="fas fa-lock fa-2x" style="font-size: 1.5rem;"></i>');
                                break;
                        }
                        ?>
                        <?php echo $donnee['nom'] ?>
                        <?php
                        if ($donnee['droit'] == "proprietaire") {
                        ?>
                            <a href="https://family.matthieudevilliers.fr/pages/modif-listes/?liste=<?php echo ($_GET['liste']) ?>" class="link-dark">
                                <i class="fas fa-pen fa-2x" style="font-size: 1.5rem;"></i>
                            </a>
                        <?php
                        }
                        ?>
                    </h1>
                    <br>

                    <div class="card text-dark bg-light">
                        <div class="card-body text-center">
                            <div class="table-responsive">
                                <table class="table table-light text-center align-middle justify-content-">
                                    <thead>
                                        <tr>
                                            <th>Nom</th>
                                            <th style="max-width: 30rem;">Commentaire</th>
                                            <th style="max-width: 40rem;">Lien</th>
                                            <!-- <th class="col image">Image</th> -->
                                            <th>Déja acheté</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php

                                        $sql1 = 'SELECT  id, nom, commentaire, lien, image, is_buy
                                            FROM lic_idee
                                            WHERE id_liste = ? AND deleted_to IS NULL';

                                        $response1 = $bdd->prepare($sql1);
                                        $response1->execute(array($donnee['id']));

                                        while ($donnees = $response1->fetch()) {
                                        ?>
                                            <tr>
                                                <td>
                                                    <?php
                                                    if ($donnees['nom'] == null) echo ('Aucun nom');
                                                    else echo ($donnees['nom']);
                                                    ?>
                                                </td>
                                                <td style="max-width:25%">
                                                    <?php
                                                    if ($donnees['commentaire'] == null) echo ('Aucun commentaire');
                                                    else echo ($donnees['commentaire']);
                                                    ?>
                                                </td>
                                                <td style="max-width: 40rem;">
                                                    <?php
                                                    if ($donnees['lien'] == null)
                                                        echo ('Aucun');
                                                    else
                                                    ?>
                                                    <a href="<?php echo ($donnees['lien']) ?>">
                                                        <?php
                                                        // echo $donnees['lien'];
                                                        if ($donnees['lien'] != '') echo (substr($donnees['lien'], 0, 60));
                                                        if (strlen($donnees['lien']) > 60) echo '...';
                                                        ?>
                                                    </a>
                                                </td>
                                                <!-- <td class="image">
                                                    <?php
                                                    // if ($donnees['image'] == null) {
                                                    //     echo ('Aucune');
                                                    // } else {
                                                    ?>
                                                        <a href="https://family.matthieudevilliers.fr/images/<?php /* echo ($donnees['image']) */ ?>">
                                                            <?php
                                                            // if ($donnees['image'] != '') echo (substr($donnees['image'], 0, 20));
                                                            // if (strlen($donnees['image']) > 20) echo '...';
                                                            ?>
                                                        </a>
                                                    <?php
                                                    // }
                                                    ?>
                                                </td> -->
                                                <td><input class="form-check-input" type="checkbox" <?php if ($donnees['is_buy']) echo ('checked') ?> disabled></td>
                                                <?php
                                                if ($donnee['droit'] != "lecteur") {
                                                ?>
                                                    <td>
                                                        <a class="btn btn-outline-secondary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?idee=<?php echo $donnees['id']; ?>">
                                                            Modifier
                                                        </a>
                                                    </td>
                                                <?php
                                                }
                                                ?>
                                            </tr>
                                        <?php
                                        }

                                        $response1->closeCursor();
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php
                            if ($donnee['droit'] == "proprietaire" || $donnee['droit'] == "moderateur") {
                            ?>
                                <br>
                                <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?liste=<?php echo $_GET['liste']; ?>">
                                    Ajouter une idée
                                </a>
                            <?php
                            }
                            ?>
                            <br>
                            <?php
                            if ($donnee['partage'] == "limite" || $donnee['partage'] == "public") {
                            ?>
                                <br>
                                <div class="input-group mb-3">
                                    <span class="input-group-text" id="copy">Partager ce lien :</span>
                                    <input type="text" class="form-control" value="https://family.matthieudevilliers.fr/pages/idees/?liste=<?php echo $_GET['liste']; ?>" disabled aria-describedby="copy">
                                </div>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    <?php
    }

    $response->closeCursor();

    ?>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>