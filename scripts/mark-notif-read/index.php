<?php
session_start();

include('../verif/index.php');

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_compte'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Non autorisé']);
    exit();
}

// Vérifier que c'est une requête POST avec un ID de notification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    $notif_id = intval($_POST['notif_id']);
    
    // Mettre à jour la notification pour la marquer comme lue
    $sqlreq = 'UPDATE lic_notif 
                SET etat = "lu"
                WHERE id = ? AND id_compte = ? AND etat = "non-lu"';
    
    $req = $bdd->prepare($sqlreq);
    $req->execute(array($notif_id, $_SESSION['id_compte']));
    
    $affected_rows = $req->rowCount();
    $req->closeCursor();
    
    if ($affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Notification non trouvée ou déjà lue']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Requête invalide']);
}
?>
