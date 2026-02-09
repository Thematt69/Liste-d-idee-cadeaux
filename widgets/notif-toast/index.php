<?php
// Vérifier que $bdd est disponible
if (!isset($bdd) || !($bdd instanceof PDO)) {
    return;
}

// Vérifier que la session est disponible
if (!isset($_SESSION['id_compte'])) {
    return;
}

// Récupérer les notifications non-lues
$sqlreq_toast = 'SELECT id, titre, message, created_to
                FROM lic_notif
                WHERE id_compte = ? AND etat = "non-lu" AND deleted_to IS NULL
                ORDER BY created_to DESC';

$req_toast = $bdd->prepare($sqlreq_toast);
$req_toast->execute(array($_SESSION['id_compte']));

// Récupérer toutes les notifications pour vérifier s'il y en a
$notifications = $req_toast->fetchAll();

// Ne créer le conteneur que s'il y a des notifications
if (count($notifications) > 0) {
    // Conteneur pour les toasts (position fixe en bas à droite)
    echo '<div class="toast-container position-fixed bottom-0 end-0 p-3">';

    foreach ($notifications as $notification) {
    $datetime = new DateTime($notification["created_to"]);
    $notifId = intval($notification["id"]);
    $notifTitre = safe_output($notification["titre"]);
    $notifMessage = safe_output($notification["message"]);
    $notifDate = $datetime->format("d/m/Y H:i");
?>
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true" id="toast-<?php echo $notifId; ?>" data-notif-id="<?php echo $notifId; ?>">
        <div class="toast-header bg-info text-white">
            <i class="fas fa-bell me-2"></i>
            <strong class="me-auto"><?php echo $notifTitre; ?></strong>
            <small><?php echo $notifDate; ?></small>
            <button type="button" class="btn-close btn-close-white ms-2 notif-close-btn" aria-label="Close"></button>
        </div>
        <div class="toast-body">
            <?php echo $notifMessage; ?>
        </div>
    </div>
<?php
    }

    echo '</div>';
}

$req_toast->closeCursor();
?>
