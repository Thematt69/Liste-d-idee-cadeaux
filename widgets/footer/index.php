<?php
// Inclure le widget de notifications toast pour les utilisateurs connectés
if (isset($_SESSION['id_compte'])) {
    include(__DIR__ . '/../notif-toast/index.php');
}
?>

<footer class="site-footer mt-auto" id="footer">
    <div class="container py-4">
        <div class="row g-4">
            <nav class="col-12 col-md-4" aria-label="Navigation complémentaire">
                <h2 class="h6">Navigation</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="https://family.matthieudevilliers.fr/pages/plan-site/">Plan du site</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/accessibilite/">Accessibilité : partiellement conforme</a></li>
                </ul>
            </nav>
            <nav class="col-12 col-md-4" aria-label="Informations légales">
                <h2 class="h6">Informations légales</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="https://family.matthieudevilliers.fr/pages/cgu/">Conditions générales d'utilisation</a></li>
                    <li><a href="https://family.matthieudevilliers.fr/pages/mentions-legales/">Mentions légales</a></li>
                </ul>
            </nav>
            <section class="col-12 col-md-4" aria-labelledby="footer-contact-title">
                <h2 class="h6" id="footer-contact-title">Contact</h2>
                <ul class="list-unstyled mb-0">
                    <li><a href="mailto:webmaster@matthieudevilliers.fr">webmaster@matthieudevilliers.fr</a></li>
                    <li><a href="https://matthieudevilliers.fr/" target="_blank" rel="noopener noreferrer">Matthieu Devilliers <span class="visually-hidden">(nouvelle fenêtre)</span><i class="bi bi-box-arrow-up-right ms-1" aria-hidden="true"></i></a></li>
                </ul>
            </section>
        </div>
    </div>
    <div class="site-footer-meta py-3">
        <div class="container">
            <small>Copyright © 2020-<?php echo date('Y'); ?> Matthieu Devilliers. Tous droits réservés.</small>
        </div>
    </div>
</footer>