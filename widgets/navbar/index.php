<nav class="navbar navbar-expand-md navbar-dark bg-secondary shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="https://family.matthieudevilliers.fr">
            <img src="https://family.matthieudevilliers.fr/images/logo.webp" width="30" height="30" class="d-inline-block align-top" alt="Logo du site" loading="lazy">
            Listes d’idées cadeau
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ms-auto">
                <?php
                if (isset($_SESSION['id_compte'])) {
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
                } else
                ?>
                <li class="nav-item">
                    <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/inscription/">Inscription</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://family.matthieudevilliers.fr/pages/connexion/">Connexion</a>
                </li>
                <!-- <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Dropdown
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="#">Action</a></li>
                        <li><a class="dropdown-item" href="#">Another action</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#">Something else here</a></li>
                    </ul>
                </li> -->
            </ul>
        </div>
    </div>
</nav>