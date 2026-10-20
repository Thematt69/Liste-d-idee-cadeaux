<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Conditions générales d'utilisation</title>

    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" class="container py-4" tabindex="-1">
        <h1 class="text-center">Conditions générales d'utilisation</h1>

        <p>
            Les présentes conditions définissent les règles d'utilisation du site Listes d'idées cadeau.
            En créant un compte ou en utilisant le site, vous acceptez ces conditions.
        </p>

        <section aria-labelledby="compte">
            <h2 id="compte">1. Compte et sécurité</h2>
            <p>
                Vous vous engagez à fournir des informations exactes et à garder confidentiels vos identifiants.
                Toute activité réalisée depuis votre compte relève de votre responsabilité. Contactez-nous si vous
                pensez que votre compte a été utilisé sans votre autorisation.
            </p>
        </section>

        <section aria-labelledby="listes">
            <h2 id="listes">2. Listes et partage</h2>
            <p>
                Vous pouvez créer des listes d'idées et en régler le partage. Une liste publique peut être consultée
                par toute personne ayant accès au site ; une liste partagée est accessible aux personnes auxquelles
                vous en avez donné l'accès. Vérifiez ces paramètres avant d'ajouter des informations personnelles.
            </p>
        </section>

        <section aria-labelledby="reservations">
            <h2 id="reservations">3. Idées et réservations</h2>
            <p>
                Les contenus des listes et les liens associés sont ajoutés par les utilisateurs, qui en restent
                responsables. Réserver une idée signale une intention aux personnes ayant accès à la liste ; cela ne
                constitue ni une commande ni un paiement sur le site. Vérifiez les informations et les conditions du
                vendeur avant tout achat.
            </p>
        </section>

        <section aria-labelledby="utilisation">
            <h2 id="utilisation">4. Utilisation du service</h2>
            <p>
                Vous vous engagez à respecter la loi et les droits des tiers, et à ne pas publier de contenu
                illicite, trompeur ou portant atteinte à autrui. Vous êtes responsable des contenus et liens que
                vous ajoutez. Le site ne garantit ni leur exactitude, ni la disponibilité des sites externes.
            </p>
            <p>
                Le service peut évoluer, être temporairement indisponible ou être interrompu. Nous ne pouvons
                garantir un accès continu au site.
            </p>
        </section>

        <section aria-labelledby="donnees">
            <h2 id="donnees">5. Données personnelles</h2>
            <p>
                Les informations fournies sont utilisées pour faire fonctionner le compte et les fonctionnalités
                de partage. Pour toute question relative à vos données ou à l'application de ces conditions,
                <a href="mailto:webmaster@matthieudevilliers.fr">contactez-nous par e-mail</a>.
            </p>
        </section>

        <section aria-labelledby="modifications">
            <h2 id="modifications">6. Modifications</h2>
            <p>
                Ces conditions peuvent être mises à jour pour refléter l'évolution du site ou de son fonctionnement.
                La version publiée sur cette page est la version applicable.
            </p>
        </section>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>