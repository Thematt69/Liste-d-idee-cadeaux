<nav class="navbar navbar-expand-md navbar-dark bg-secondary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="https://family.matthieudevilliers.fr">
            <img src="https://family.matthieudevilliers.fr/images/logo.webp" width="30" height="30" class="d-inline-block align-top" alt="Logo du site" loading="lazy">
            Listes d'idées cadeau
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <?php

                if (isset($_SESSION['id_compte'])) {

                    // NOTE - Notification
                    include('../../widgets/notif/index.php');

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
                    $req2->closeCursor();
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
</nav>