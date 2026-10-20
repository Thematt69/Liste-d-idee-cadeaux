<?php

session_start();

include('../../scripts/verif/index.php');

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Mentions légales</title>
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">
    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" class="container py-4" tabindex="-1">
        <h1>Mentions légales</h1>

        <section aria-labelledby="editeur">
            <h2 id="editeur">Éditeur du site</h2>
            <p>Matthieu Devilliers<br>
                Courriel : <a href="mailto:webmaster@matthieudevilliers.fr">webmaster@matthieudevilliers.fr</a>
            </p>
        </section>

        <section aria-labelledby="hebergeur">
            <h2 id="hebergeur">Hébergeur</h2>
            <p>LWS (Ligne Web Services)<br>
                10, rue Penthièvre<br>
                75008 Paris, France<br>
                Téléphone : 01 77 62 30 03<br>
                Site : <a href="https://www.lws.fr/" target="_blank" rel="noopener noreferrer">www.lws.fr <span class="visually-hidden">(nouvelle fenêtre)</span><i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a>
            </p>
        </section>

        <section aria-labelledby="donnees-personnelles">
            <h2 id="donnees-personnelles">Données personnelles</h2>
            <p>Responsable du traitement : Matthieu Devilliers. Vous pouvez le contacter à <a href="mailto:webmaster@matthieudevilliers.fr">webmaster@matthieudevilliers.fr</a>.</p>
            <p>Les données suivantes sont utilisées pour le fonctionnement du compte et le partage des listes. Les données des listes sont visibles par les personnes auxquelles leur propriétaire en a donné l'accès.</p>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <caption>Données traitées par le site</caption>
                    <thead>
                        <tr>
                            <th scope="col">Donnée</th>
                            <th scope="col">Finalité</th>
                            <th scope="col">Destinataires</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">Prénom et nom</th>
                            <td>Personnalisation du compte</td>
                            <td>Vous et les personnes autorisées à consulter vos listes</td>
                        </tr>
                        <tr>
                            <th scope="row">Mot de passe</th>
                            <td>Authentification au compte</td>
                            <td>Vous uniquement ; il est stocké sous forme hachée</td>
                        </tr>
                        <tr>
                            <th scope="row">Adresse e-mail</th>
                            <td>Identification et communications relatives au compte</td>
                            <td>Vous et les personnes autorisées selon les fonctions de partage</td>
                        </tr>
                        <tr>
                            <th scope="row">Adresse IP</th>
                            <td>Historique des connexions</td>
                            <td>Administrateur du site</td>
                        </tr>
                        <tr>
                            <th scope="row">Listes et idées</th>
                            <td>Création, consultation et partage</td>
                            <td>Vous et les personnes autorisées</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <p>Les enregistrements marqués comme supprimés sont supprimés définitivement au plus tard après trois ans par le traitement de nettoyage du site.</p>
            <p>Vous pouvez demander l'accès à vos données, leur rectification ou leur effacement, vous opposer à leur traitement ou demander sa limitation en écrivant à <a href="mailto:webmaster@matthieudevilliers.fr">webmaster@matthieudevilliers.fr</a>.</p>
            <p>Vous pouvez également adresser une réclamation à la <a href="https://www.cnil.fr/fr/plaintes" target="_blank" rel="noopener noreferrer">CNIL <span class="visually-hidden">(nouvelle fenêtre)</span><i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a>.</p>
        </section>

        <section aria-labelledby="responsabilite">
            <h2 id="responsabilite">Contenus et liens</h2>
            <p>Les utilisateurs restent responsables des contenus qu'ils ajoutent et des liens externes qu'ils partagent. L'éditeur ne garantit ni leur exactitude ni leur disponibilité.</p>
        </section>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>