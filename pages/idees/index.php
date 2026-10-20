<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
}

// Validate required GET parameter
if (!isset($_GET['liste']) || empty($_GET['liste'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
}

if (isset($_POST['AchatFrom'])) {
    $sql1 = 'SELECT buy_from
            FROM lic_idee
            WHERE id = ?';

    $response1 = $bdd->prepare($sql1);
    $response1->execute(array($_POST['AchatFrom']));
    $donnee1 = $response1->fetch();

    $buyFrom = $donnee1['buy_from'] == $_SESSION['id_compte'] ? NULL : $_SESSION['id_compte'];

    $response1->closeCursor();

    // Modification de la réservation
    $sql = 'UPDATE lic_idee
            SET buy_from = ?
            WHERE id = ?';

    $response = $bdd->prepare($sql);
    $response->execute(array($buyFrom, htmlentities($_POST['AchatFrom'])));

    $response->closeCursor();
}

$sql = 'SELECT id, nom, partage
        FROM lic_liste
        WHERE lien_partage = ? AND deleted_to IS NULL';

$response = $bdd->prepare($sql);
$response->execute(array($_GET['liste']));
$donnee = $response->fetch();

$sql1 = 'SELECT type
        FROM lic_autorisation
        WHERE id_liste = ? AND id_compte = ?';

$response1 = $bdd->prepare($sql1);
$response1->execute(array($donnee['id'], $_SESSION['id_compte']));
$donnee1 = $response1->fetch();

if ($donnee['partage'] == 'public' && $donnee1['type'] == null) {
    $sql2 = 'INSERT INTO lic_autorisation (id_compte, id_liste, type)
            VALUES (?, ?, "lecteur")';

    $response2 = $bdd->prepare($sql2);
    $response2->execute(array($_SESSION['id_compte'], $donnee['id']));
    $response2->closeCursor();
}

$response->closeCursor();
$response1->closeCursor();

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - <?php echo safe_output($donnee['nom'] ?? 'Idées de la liste'); ?></title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php
    include('../../widgets/navbar/index.php');
    ?>

    <main id="main-content" tabindex="-1">
        <?php

        $sql = 'SELECT lic_liste.id as id, lic_liste.nom as nom, lic_liste.partage as partage, lic_autorisation.type as droit
            FROM lic_liste
            INNER JOIN lic_autorisation ON lic_autorisation.id_liste = lic_liste.id
            WHERE lic_autorisation.id_compte = ? AND lic_liste.lien_partage = ? AND lic_liste.deleted_to IS NULL';

        $response = $bdd->prepare($sql);
        $response->execute(array($_SESSION['id_compte'], $_GET['liste']));

        $donnee = $response->fetch();

        if ($donnee == null) {
        ?>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="alert alert-danger" role="alert">
                            <p>Nous sommes désolées, mais nous n'avons pas trouvé votre liste. Merci de revenir à <a href="https://family.matthieudevilliers.fr/pages/listes/" class="alert-link">vos listes</a>.</p>
                            <p class="mb-0">Il se peut aussi que vous n'ayez pas les droits nécessaires pour y accéder.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        } else {
            $sql1 = 'SELECT lic_compte.prenom as prenom, lic_compte.id as id
                FROM lic_compte
                INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL
                ORDER BY lic_compte.prenom ASC';

            $response1 = $bdd->prepare($sql1);
            $response1->execute(array($donnee['id']));
            $proprietaires = $response1->fetchAll();
            $response1->closeCursor();

            $renderEmpty = static function () {
                echo '<span aria-hidden="true">—</span><span class="visually-hidden">Non renseigné</span>';
            };
        ?>
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-12">
                        <div class="text-center mb-4">
                            <h1 class="mb-2"><?php echo safe_output($donnee['nom']); ?></h1>
                            <p class="text-body-secondary mb-3">
                                <?php
                                switch ($donnee['partage']) {
                                    case 'public':
                                        echo '<span class="badge bg-primary-subtle text-primary-emphasis" title="Partage public"><i class="bi bi-people-fill" aria-hidden="true"></i><span class="visually-hidden">Partage public</span></span> ';
                                        break;

                                    case 'limite':
                                        echo '<span class="badge bg-primary-subtle text-primary-emphasis" title="Partage limité"><i class="bi bi-person-lock" aria-hidden="true"></i><span class="visually-hidden">Partage limité</span></span> ';
                                        break;

                                    default:
                                        echo '<span class="badge bg-primary-subtle text-primary-emphasis" title="Liste privée"><i class="bi bi-lock-fill" aria-hidden="true"></i><span class="visually-hidden">Liste privée</span></span> ';
                                        break;
                                }
                                ?>
                                <span>de
                                    <?php
                                    for ($i = 0; $i < count($proprietaires); $i++) {
                                        if ($i > 0) {
                                            echo " et ";
                                        }
                                        if ($proprietaires[$i]['id'] == $_SESSION['id_compte']) {
                                            echo "<strong>&thinsp;vous&thinsp;</strong>";
                                        } else {
                                            echo safe_output($proprietaires[$i]['prenom']);
                                        }
                                    }
                                    ?>
                                </span>
                            </p>
                            <div class="d-flex flex-wrap justify-content-center gap-2">
                                <?php if ($donnee['droit'] == "proprietaire") { ?>
                                    <a class="btn btn-outline-primary" href="https://family.matthieudevilliers.fr/pages/modif-listes/?liste=<?php echo rawurlencode($_GET['liste']); ?>">
                                        <i class="bi bi-pencil-fill me-2" aria-hidden="true"></i>Modifier la liste
                                    </a>
                                <?php } ?>
                                <a class="btn btn-outline-primary" href="https://family.matthieudevilliers.fr/pages/print-liste/?liste=<?php echo rawurlencode($_GET['liste']); ?>">
                                    <i class="bi bi-printer-fill me-2" aria-hidden="true"></i>Imprimer la liste
                                </a>
                                <?php if ($donnee['droit'] == "proprietaire" || $donnee['droit'] == "moderateur") { ?>
                                    <a class="btn btn-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?liste=<?php echo rawurlencode($_GET['liste']); ?>">
                                        <i class="bi bi-plus-lg me-2" aria-hidden="true"></i>Ajouter une idée
                                    </a>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-center ideas-table">
                                        <caption class="visually-hidden">Idées de la liste <?php echo safe_output($donnee['nom']); ?></caption>
                                        <thead>
                                            <tr>
                                                <th scope="col">Nom</th>
                                                <th scope="col">Commentaire</th>
                                                <th scope="col">Lien</th>
                                                <th scope="col">Prix</th>
                                                <th scope="col">Infos</th>
                                                <?php
                                                if ($donnee['droit'] != "lecteur") {
                                                    echo '<th scope="col">Actions</th>';
                                                } else {
                                                    echo '<th scope="col">Réservation</th>';
                                                }
                                                ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php

                                            $sql1 = 'SELECT  id, nom, commentaire, lien, is_buy, buy_from, price
                                            FROM lic_idee
                                            WHERE id_liste = ? AND deleted_to IS NULL';

                                            $response1 = $bdd->prepare($sql1);
                                            $response1->execute(array($donnee['id']));

                                            while ($donnees = $response1->fetch()) {
                                            ?>
                                                <tr<?php echo ($donnees['is_buy'] || ($donnee['droit'] != 'proprietaire' && $donnees['buy_from'])) ? ' class="idea-row-reserved"' : ''; ?>>
                                                    <th scope="row">
                                                        <?php
                                                        if ($donnees['nom'] == null || $donnees['nom'] === '') $renderEmpty();
                                                        else echo safe_output($donnees['nom']);
                                                        ?>
                                                    </th>
                                                    <td class="idea-cell-wide">
                                                        <?php
                                                        if ($donnees['commentaire'] == null || $donnees['commentaire'] === '') $renderEmpty();
                                                        else echo safe_output($donnees['commentaire']);
                                                        ?>
                                                    </td>
                                                    <td class="idea-cell-wide">
                                                        <?php
                                                        $ideaUrl = trim($donnees['lien'] ?? '');
                                                        $urlParts = $ideaUrl !== '' ? parse_url($ideaUrl) : false;
                                                        $isWebUrl = is_array($urlParts)
                                                            && isset($urlParts['scheme'], $urlParts['host'])
                                                            && in_array(strtolower($urlParts['scheme']), array('http', 'https'), true);
                                                        if ($isWebUrl) {
                                                            $domain = preg_replace('/^www\./i', '', $urlParts['host']);
                                                        ?>
                                                            <a class="idea-link" href="<?php echo safe_output($ideaUrl); ?>" title="<?php echo safe_output($ideaUrl); ?>" target="_blank" rel="noopener noreferrer">
                                                                <?php echo safe_output($domain); ?><i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i><span class="visually-hidden"> : lien externe pour <?php echo safe_output($donnees['nom'] ?: 'cette idée'); ?> (nouvelle fenêtre)</span>
                                                            </a>
                                                        <?php
                                                        } elseif ($ideaUrl !== '') {
                                                            echo safe_output(mb_substr($ideaUrl, 0, 60));
                                                            if (mb_strlen($ideaUrl) > 60) echo '…';
                                                        } else {
                                                            $renderEmpty();
                                                        }
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($donnees['price'] == null || $donnees['price'] === '') $renderEmpty();
                                                        else echo safe_output($donnees['price']);
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <?php
                                                        if ($donnees['is_buy']) {

                                                            $sql2 = 'SELECT lic_compte.prenom as prenom, lic_compte.id as id
                                                                FROM lic_compte
                                                                INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
                                                                WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL
                                                                ORDER BY lic_compte.prenom ASC';

                                                            $response2 = $bdd->prepare($sql2);
                                                            $response2->execute(array($donnee['id']));

                                                            $proprietaire = array();

                                                            while ($donnee1 = $response2->fetch()) {
                                                                array_push($proprietaire, $donnee1);
                                                            }

                                                            echo '<span class="badge bg-secondary-subtle text-secondary-emphasis">Acheté par ';

                                                            for ($i = 0; $i < count($proprietaire); $i++) {
                                                                if ($i > 0) echo " et ";
                                                                echo safe_output($proprietaire[$i]['prenom']);
                                                            }

                                                            echo '</span>';
                                                            $response2->closeCursor();
                                                        } elseif ($donnees['buy_from'] && $donnee['droit'] != "proprietaire") {

                                                            $sql2 = 'SELECT prenom
                                                            FROM lic_compte
                                                            WHERE id = ? AND deleted_to IS NULL';

                                                            $response2 = $bdd->prepare($sql2);
                                                            $response2->execute(array($donnees['buy_from']));

                                                            $donnee1 = $response2->fetch();

                                                            if ($donnee1) {
                                                                echo '<span class="badge bg-secondary-subtle text-secondary-emphasis">Réservé par ' . safe_output($donnee1['prenom']) . '</span>';
                                                            }

                                                            $response2->closeCursor();
                                                        } else {
                                                            $renderEmpty();
                                                        }
                                                        ?>
                                                    </td>
                                                    <?php
                                                    if ($donnee['droit'] != "lecteur") {
                                                    ?>
                                                        <td>
                                                            <a class="btn btn-outline-primary" href="https://family.matthieudevilliers.fr/pages/modif-idees/?idee=<?php echo (int) $donnees['id']; ?>" aria-label="Modifier l'idée <?php echo safe_output($donnees['nom'] ?: 'sans nom'); ?>">
                                                                <i class="bi bi-pencil-square me-2" aria-hidden="true"></i>Modifier
                                                            </a>
                                                        </td>
                                                        <?php
                                                    } else {
                                                        if ($donnees['is_buy'] || (isset($donnees['buy_from']) && $donnees['buy_from'] != $_SESSION['id_compte'])) {
                                                        ?>
                                                            <td>
                                                                <div class="form-check form-switch d-flex justify-content-center">
                                                                    <input class="form-check-input" id="reservation-<?php echo (int) $donnees['id']; ?>" type="checkbox" checked disabled>
                                                                    <label class="form-check-label visually-hidden" for="reservation-<?php echo (int) $donnees['id']; ?>">Réservation de <?php echo safe_output($donnees['nom'] ?: 'cette idée'); ?> : déjà achetée ou réservée</label>
                                                                </div>
                                                            </td>
                                                        <?php
                                                        } else {
                                                        ?>
                                                            <td>
                                                                <form action="" method="post" class="d-flex justify-content-center">
                                                                    <input name='AchatFrom' value="<?php echo $donnees['id']; ?>" type='hidden'>
                                                                    <div class="form-check form-switch">
                                                                        <input name="AchatFrom" value="<?php echo $donnees['id']; ?>" class="form-check-input" id="reservation-<?php echo (int) $donnees['id']; ?>" type="checkbox" onchange="this.form.submit()" <?php if ($donnees['buy_from'] == $_SESSION['id_compte']) echo 'checked'; ?>>
                                                                        <label class="form-check-label" for="reservation-<?php echo (int) $donnees['id']; ?>">Réserver<span class="visually-hidden"> <?php echo safe_output($donnees['nom']); ?></span></label>
                                                                        <noscript><button class="btn btn-sm btn-outline-primary ms-2" type="submit">Enregistrer</button></noscript>
                                                                    </div>
                                                                </form>
                                                            </td>
                                                        <?php
                                                        }
                                                        ?>

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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
        }

        $response->closeCursor();

        ?>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>