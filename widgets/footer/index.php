<?php
// Inclure le widget de notifications toast pour les utilisateurs connectés
if (isset($_SESSION['id_compte'])) {
    include(__DIR__ . '/../notif-toast/index.php');
}
?>

<footer class="footer mt-auto py-5 site-footer">
    <div class="container text-center">
        <nav aria-label="Navigation complémentaire">
            <span>
                <a href="https://family.matthieudevilliers.fr/pages/plan-site/">Plan du site</a>
                |
                <a href="https://family.matthieudevilliers.fr/pages/accessibilite/">Accessibilité : partiellement conforme</a>
                |
                <a href="https://family.matthieudevilliers.fr/pages/cgu/">CGU</a>
                |
                <a href="https://matthieudevilliers.fr/" target="_blank">Matthieu Devilliers</a>
                | Copyright ©2020-2025 All rights reserved |
                <a href="mailto:webmaster@matthieudevilliers.fr" target="_blank">Contactez-moi</a>
            </span>
        </nav>
    </div>
</footer>