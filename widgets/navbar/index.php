<a class="skip-link" href="#main-content">Aller au contenu principal</a>
<div class="site-navbar-container">
    <nav class="navbar navbar-expand-md site-navbar shadow-sm" aria-label="Navigation principale">
        <div class="container">
            <a class="navbar-brand" href="https://family.matthieudevilliers.fr">
                <img src="/images/logo.webp" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
                Listes d'idées cadeau
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Ouvrir le menu">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="offcanvas offcanvas-end" tabindex="-1" id="navbarSupportedContent" aria-labelledby="navbarSupportedContentLabel">
                <div class="offcanvas-header">
                    <h2 class="offcanvas-title h5" id="navbarSupportedContentLabel">Menu</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Fermer le menu"></button>
                </div>
                <div class="offcanvas-body">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <?php

                        if (isset($_SESSION['id_compte'])) {

                            if (!isset($_SESSION['fonction'])) {
                                $sqlreq4 = 'SELECT fonction
                                    FROM lic_compte
                                    WHERE id = ? AND deleted_to IS NULL';

                                $req4 = $bdd->prepare($sqlreq4);
                                $req4->execute(array($_SESSION['id_compte']));
                                $donneesreq4 = $req4->fetch();

                                $_SESSION['fonction'] = $donneesreq4['fonction'];

                                $req4->closeCursor();
                            }
                            if ($_SESSION['fonction'] == 'admin') {
                        ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/admin/">Panel Admin</a>
                                </li>
                            <?php
                            }
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/listes/">Mes listes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/compte/">Mon compte</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/scripts/deconnexion/">Déconnexion</a>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/inscription/">Inscription</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/connexion/">Connexion</a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>