<?php
// Get list owner(s) IDs
$sql_owners = 'SELECT lic_compte.id
               FROM lic_compte
               INNER JOIN lic_autorisation ON lic_autorisation.id_compte = lic_compte.id
               WHERE lic_autorisation.type = "proprietaire" AND lic_autorisation.id_liste = ? AND lic_compte.deleted_to IS NULL';

$response_owners = $bdd->prepare($sql_owners);
$response_owners->execute(array($liste_id));

$owner_ids = array();
while ($owner = $response_owners->fetch()) {
    $owner_ids[] = $owner['id'];
}
$response_owners->closeCursor();

// Get preferences for all owners in a single query
$preferences_data = array();
if (count($owner_ids) > 0) {
    // Build placeholders for IN clause
    $placeholders = implode(',', array_fill(0, count($owner_ids), '?'));
    
    $sql_pref = "SELECT p.*, c.prenom, c.nom
                 FROM lic_preferences p
                 INNER JOIN lic_compte c ON p.id_compte = c.id
                 WHERE p.id_compte IN ($placeholders) AND p.deleted_to IS NULL AND c.deleted_to IS NULL";
    
    $response_pref = $bdd->prepare($sql_pref);
    $response_pref->execute($owner_ids);
    
    while ($pref = $response_pref->fetch()) {
        $preferences_data[] = $pref;
    }
    $response_pref->closeCursor();
}

// Display preferences summary and modal for each owner
foreach ($preferences_data as $pref) {
    // Check if at least one field is filled
    $has_data = false;
    $summary_items = array();
    
    if (!empty($pref['couleur_preferee'])) {
        $summary_items[] = '<strong>Couleur :</strong> ' . safe_output($pref['couleur_preferee']);
        $has_data = true;
    }
    if (!empty($pref['centres_interet'])) {
        $summary_items[] = '<strong>Loisirs :</strong> ' . safe_output($pref['centres_interet']);
        $has_data = true;
    }
    if (!empty($pref['peche_mignon'])) {
        $summary_items[] = '<strong>Gourmandise :</strong> ' . safe_output($pref['peche_mignon']);
        $has_data = true;
    }
    
    if ($has_data) {
        $owner_name = safe_output($pref['prenom'] . ' ' . $pref['nom']);
        $modal_id = 'preferencesModal' . $pref['id_compte'];
?>
        <div class="alert alert-light border shadow-sm mb-3" style="border-left: 4px solid #0d6efd;">
            <div class="row align-items-center">
                <div class="col-md-9">
                    <h6 class="mb-2">✨ Préférences de <?php echo $owner_name; ?></h6>
                    <p class="mb-0 small">
                        <?php echo implode(' • ', array_slice($summary_items, 0, 3)); ?>
                    </p>
                </div>
                <div class="col-md-3 text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#<?php echo $modal_id; ?>">
                        Voir toutes les préférences
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="<?php echo $modal_id; ?>" tabindex="-1" aria-labelledby="<?php echo $modal_id; ?>Label" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="<?php echo $modal_id; ?>Label">✨ Profil Cadeau de <?php echo $owner_name; ?></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Section 1 -->
                        <?php if (!empty($pref['allergies']) || !empty($pref['couleur_preferee']) || !empty($pref['taille_vetements'])) { ?>
                        <div class="mb-4">
                            <h6 class="text-primary border-bottom pb-2">Les Essentiels</h6>
                            <?php if (!empty($pref['allergies'])) { ?>
                            <p><strong>Allergies/régimes :</strong><br><?php echo nl2br(safe_output($pref['allergies'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['couleur_preferee'])) { ?>
                            <p><strong>Couleur préférée :</strong><br><?php echo nl2br(safe_output($pref['couleur_preferee'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['taille_vetements'])) { ?>
                            <p><strong>Tailles :</strong><br><?php echo nl2br(safe_output($pref['taille_vetements'])); ?></p>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <!-- Section 2 -->
                        <?php if (!empty($pref['centres_interet']) || !empty($pref['style_decoration']) || !empty($pref['objet_ou_experience'])) { ?>
                        <div class="mb-4">
                            <h6 class="text-success border-bottom pb-2">Univers &amp; Style de vie</h6>
                            <?php if (!empty($pref['centres_interet'])) { ?>
                            <p><strong>Centres d'intérêt :</strong><br><?php echo nl2br(safe_output($pref['centres_interet'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['style_decoration'])) { ?>
                            <p><strong>Style de décoration :</strong><br><?php echo nl2br(safe_output($pref['style_decoration'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['objet_ou_experience'])) { ?>
                            <p><strong>Objet ou expérience :</strong><br><?php echo nl2br(safe_output($pref['objet_ou_experience'])); ?></p>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <!-- Section 3 -->
                        <?php if (!empty($pref['peche_mignon']) || !empty($pref['obsession_moment']) || !empty($pref['genres_lecture_musique_film'])) { ?>
                        <div class="mb-4">
                            <h6 class="text-info border-bottom pb-2">Petits plaisirs &amp; Passions</h6>
                            <?php if (!empty($pref['peche_mignon'])) { ?>
                            <p><strong>Péché mignon :</strong><br><?php echo nl2br(safe_output($pref['peche_mignon'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['obsession_moment'])) { ?>
                            <p><strong>Obsession du moment :</strong><br><?php echo nl2br(safe_output($pref['obsession_moment'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['genres_lecture_musique_film'])) { ?>
                            <p><strong>Lecture/Musique/Films :</strong><br><?php echo nl2br(safe_output($pref['genres_lecture_musique_film'])); ?></p>
                            <?php } ?>
                        </div>
                        <?php } ?>

                        <!-- Section 4 -->
                        <?php if (!empty($pref['pas_recevoir']) || !empty($pref['deja_trop'])) { ?>
                        <div class="mb-4">
                            <h6 class="text-warning border-bottom pb-2">Guide "Anti-Déception"</h6>
                            <?php if (!empty($pref['pas_recevoir'])) { ?>
                            <p><strong>Ne pas recevoir :</strong><br><?php echo nl2br(safe_output($pref['pas_recevoir'])); ?></p>
                            <?php } ?>
                            <?php if (!empty($pref['deja_trop'])) { ?>
                            <p><strong>Déjà en trop grande quantité :</strong><br><?php echo nl2br(safe_output($pref['deja_trop'])); ?></p>
                            <?php } ?>
                        </div>
                        <?php } ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    </div>
                </div>
            </div>
        </div>
<?php
    }
}
?>
