 <?php
    if (isset($_POST['notif'])) {
        if ($_POST['notif'] == "tous") {
            $sqlreq6 = 'UPDATE lic_notif 
                                    SET etat = "lu"
                                    WHERE id_compte = ? AND etat != "lu"';

            $req6 = $bdd->prepare($sqlreq6);
            $req6->execute(array($_SESSION['id_compte']));

            $req6->closeCursor();
        } else {
            $sqlreq6 = 'UPDATE lic_notif 
                                    SET etat = "lu"
                                    WHERE id_compte = ? AND etat != "lu" AND id = ?';

            $req6 = $bdd->prepare($sqlreq6);
            $req6->execute(array($_SESSION['id_compte'], $_POST['notif']));

            $req6->closeCursor();
        }
        unset($_POST['notif']);
    }

    $sqlreq2 = 'SELECT count(id) as num_alert
                                        FROM lic_notif
                                        WHERE id_compte = ? AND etat = "non-lu"';

    $req2 = $bdd->prepare($sqlreq2);
    $req2->execute(array($_SESSION['id_compte']));
    $donneesreq2 = $req2->fetch();

    // NOTE - Si il y a des alertes non-lus
    if ($donneesreq2['num_alert'] > 0) {
    ?>
     <li class="nav-item dropdown">
         <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
             <i class="far fa-bell"></i>
         </a>
         <ul class="dropdown-menu text-center" aria-labelledby="navbarDropdown">
             <li>
                 <form action="" method="post">
                     <small>
                         <button name="notif" value="tous" class="btn btn-link btn-sm" type="submit">
                             Marquer tout comme lu
                         </button>
                     </small>
                 </form>
             </li>
             <li>
                 <hr class="dropdown-divider">
             </li>
             <?php

                $sqlreq3 = 'SELECT id,titre,message,etat,created_to
                                            FROM lic_notif
                                            WHERE id_compte = ? AND etat = "non-lu"';

                $req3 = $bdd->prepare($sqlreq3);
                $req3->execute(array($_SESSION['id_compte']));

                while ($donneesreq3 = $req3->fetch()) {
                    $datetime = new DateTime($donneesreq3["created_to"]);
                ?>
                 <li>
                     <form action="" method="post">
                         <strong>
                             <?php echo $donneesreq3["titre"] ?>
                         </strong>
                         <br>
                         <?php echo $donneesreq3["message"] ?>
                         <br>
                         <small>
                             Le <?php echo $datetime->format("d/m/Y H:i") ?>
                         </small>
                         <small>
                             <button style="vertical-align: initial;" name="notif" value="<?php echo $donneesreq3["id"] ?>" class="btn btn-link btn-sm" type="submit">
                                 Marquer comme lu
                             </button>
                         </small>
                     </form>
                 </li>
             <?php
                }

                $req3->closeCursor();
                ?>

         </ul>
     </li>
 <?php
    }
    ?>