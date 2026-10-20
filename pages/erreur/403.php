<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Erreur 403</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h1 class="text-center mb-4">Erreur 403</h1>
                    <div class="text-center">
                        <h2 class="h4 mb-3">Vous n'êtes pas autorisé à accéder à ce fichier.</h2>
                        <a class="btn btn-outline-primary" href="https://family.matthieudevilliers.fr">
                            Revenir au site
                        </a>
                        <video class="error-video mt-3" autoplay loop muted>
                            <source src="https://spa.matthieudevilliers.fr/images/maintenance-resize.webm" type="video/webm">
                            <source src="https://spa.matthieudevilliers.fr/images/maintenance-resize.mp4" type="video/mp4">
                            <p>Votre navigateur ne prend pas en charge les vidéos HTML5.</p>
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>