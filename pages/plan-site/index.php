<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Plan du site</title>

    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" tabindex="-1">
        <div class="container py-4">
            <h1>Plan du site</h1>

            <nav aria-label="Pages publiques">
                <h2>Pages publiques</h2>
                <ul>
                    <li><a href="https://family.matthieudevilliers.fr/">Accueil</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/plan-site/">Plan du site</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/connexion/">Connexion</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/inscription/">Inscription</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/reset-password/">Réinitialiser le mot de passe</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/accessibilite/">Déclaration d'accessibilité</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/cgu/">Conditions générales d'utilisation</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/mentions-legales/">Mentions légales</a></li>
                </ul>
            </nav>

            <?php if (isset($_SESSION['id_compte'])) { ?>
                <nav aria-label="Pages de votre compte">
                    <h2>Pages de votre compte</h2>
                    <ul>
                        <li><a href="https://family.matthieudevilliers.fr/pages/listes/">Mes listes</a></li>
                        <li><a href="https://family.matthieudevilliers.fr/pages/compte/">Mon compte</a></li>
                        <li><a href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=solo">Créer une liste</a></li>
                        <li><a href="https://family.matthieudevilliers.fr/pages/modif-listes/?type=duo">Créer une liste en duo</a></li>
                    </ul>
                    <p>La gestion des listes partagées et des idées est accessible depuis vos listes.</p>
                </nav>

                <?php if (isset($_SESSION['fonction']) && $_SESSION['fonction'] === 'admin') { ?>
                    <nav aria-label="Pages d'administration">
                        <h2>Administration</h2>
                        <ul>
                            <li><a href="https://family.matthieudevilliers.fr/pages/admin/">Panel d'administration</a></li>
                        </ul>
                    </nav>
                <?php } ?>
            <?php } ?>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>