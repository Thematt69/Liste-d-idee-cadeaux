<?php
// Inclure le widget de notifications toast pour les utilisateurs connectés
if (isset($_SESSION['id_compte'])) {
    include(__DIR__ . '/../notif-toast/index.php');
}
?>

<footer class="footer mt-auto py-5 site-footer">
    <div class="container text-center">
        <span>
            <a href="https://family.matthieudevilliers.fr/pages/accessibilite/">Accessibilité : partiellement conforme</a>
            |
            <a href="https://family.matthieudevilliers.fr/pages/cgu/">CGU</a>
            |
            <a href="https://matthieudevilliers.fr/" target="_blank">Matthieu Devilliers</a>
            | Copyright ©2020-2025 All rights reserved |
            <a href="mailto:devilliers.matthieu@gmail.com" target="_blank">Contactez-moi</a>
        </span>
    </div>
</footer>