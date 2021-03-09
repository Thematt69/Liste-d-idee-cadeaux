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

                $sqlreq3 = 'SELECT count(id)
                            FROM lic_notif
                            WHERE id_compte = ? AND etat = "non-lu"';

                $req3 = $bdd->prepare($sqlreq3);
                $req3->execute(array($_SESSION['id_compte']));
                $donneesreq3 = $req3->fetch();

                ?>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php
                        if ($donneesreq3['count(id)'] > 0) echo ('<i class="fas fa-bell" style="color: Tomato;"></i>');
                        else echo ('<i class="far fa-bell"></i>');
                        ?>
                    </a>
                    <ul class="dropdown-menu text-center" aria-labelledby="navbarDropdown">
                        <?php

                        $sqlreq2 = 'SELECT titre,message,etat,created_to
                                        FROM lic_notif
                                        WHERE id_compte = ?';

                        $req2 = $bdd->prepare($sqlreq2);
                        $req2->execute(array($_SESSION['id_compte']));


                        while ($donneesreq2 = $req2->fetch()) {
                            $datetime = new DateTime($donneesreq2["created_to"]);
                        ?>
                            <li>
                                <strong><?php echo $donneesreq2["titre"] ?></strong>
                                <br>
                                <?php echo $donneesreq2["message"] ?>
                                <br>
                                <small>Le <?php echo $datetime->format("d/m/Y H:m") ?></small>
                            </li>
                        <?php
                        }

                        $req2->closeCursor();

                        $sqlreq1 = 'SELECT count(id)
                                    FROM lic_notif
                                    WHERE id_compte = ?';

                        $req1 = $bdd->prepare($sqlreq1);
                        $req1->execute(array($_SESSION['id_compte']));
                        $donneesreq1 = $req1->fetch();

                        if ($donneesreq1['count(id)'] == 0) echo ('<li>Aucune alerte</li>');

                        $req1->closeCursor();
                        ?>
                    </ul>
                </li>
                <?php

                $req3->closeCursor();
                ?>
            </ul>
        </div>
    </div>
</nav>