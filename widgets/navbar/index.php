<a class="skip-link visually-hidden-focusable position-fixed top-0 start-0 p-3" href="#main-content">Aller au contenu principal</a>
<header class="site-header">
    <nav class="navbar navbar-expand-md site-navbar bg-body-tertiary shadow-sm" aria-label="Navigation principale">
        <div class="container">
            <a class="navbar-brand" href="https://family.matthieudevilliers.fr">
                <img src="/images/logo.webp" width="30" height="30" class="d-inline-block align-top" alt="" loading="lazy">
                Listes d'idées cadeau
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Ouvrir le menu">
                <i class="bi bi-list fs-4" aria-hidden="true"></i>
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
                        $currentPath = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
                        $isCurrent = static function ($path) use ($currentPath) {
                            return $currentPath === $path || strpos($currentPath, rtrim($path, '/') . '/') === 0;
                        };

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
                                    <a class="nav-link<?php echo $isCurrent('/pages/admin/') ? ' active' : ''; ?>" href="https://family.matthieudevilliers.fr/pages/admin/"<?php echo $isCurrent('/pages/admin/') ? ' aria-current="page"' : ''; ?>>Panel Admin</a>
                                </li>
                            <?php
                            }
                            ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $isCurrent('/pages/listes/') ? ' active' : ''; ?>" href="https://family.matthieudevilliers.fr/pages/listes/"<?php echo $isCurrent('/pages/listes/') ? ' aria-current="page"' : ''; ?>>Mes listes</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $isCurrent('/pages/compte/') ? ' active' : ''; ?>" href="https://family.matthieudevilliers.fr/pages/compte/"<?php echo $isCurrent('/pages/compte/') ? ' aria-current="page"' : ''; ?>>Mon compte</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://family.matthieudevilliers.fr/scripts/deconnexion/">Déconnexion</a>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $isCurrent('/pages/inscription/') ? ' active' : ''; ?>" href="https://family.matthieudevilliers.fr/pages/inscription/"<?php echo $isCurrent('/pages/inscription/') ? ' aria-current="page"' : ''; ?>>Inscription</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link<?php echo $isCurrent('/pages/connexion/') ? ' active' : ''; ?>" href="https://family.matthieudevilliers.fr/pages/connexion/"<?php echo $isCurrent('/pages/connexion/') ? ' aria-current="page"' : ''; ?>>Connexion</a>
                            </li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</header>