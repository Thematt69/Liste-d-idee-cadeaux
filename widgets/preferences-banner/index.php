<?php
// Check if user has completed preferences
$sql_pref = 'SELECT id FROM lic_preferences WHERE id_compte = ? AND deleted_to IS NULL';
$response_pref = $bdd->prepare($sql_pref);
$response_pref->execute(array($_SESSION['id_compte']));
$has_preferences = $response_pref->fetch();
$response_pref->closeCursor();

// Check if banner was dismissed (stored in session)
$banner_dismissed = isset($_SESSION['preferences_banner_dismissed']) && $_SESSION['preferences_banner_dismissed'] === true;

// Show banner only if preferences not completed and not dismissed
if (!$has_preferences && !$banner_dismissed) {
?>
<div class="alert alert-info alert-dismissible fade show shadow-sm" role="alert" id="preferences-banner" style="border-left: 4px solid #0dcaf0;">
    <div class="row align-items-center">
        <div class="col-md-9">
            <h5 class="alert-heading mb-2">✨ Aidez vos proches à vous surprendre !</h5>
            <p class="mb-0">
                Complétez vos préférences pour orienter ceux qui cherchent une idée hors liste.
            </p>
        </div>
        <div class="col-md-3 text-center">
            <a href="https://family.matthieudevilliers.fr/pages/profil-cadeau/" class="btn btn-primary btn-sm me-2">Compléter mon profil</a>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="dismissBanner()">Plus tard</button>
        </div>
    </div>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" onclick="dismissBanner()"></button>
</div>

<script>
function dismissBanner() {
    // Set session variable to remember dismissal
    fetch('https://family.matthieudevilliers.fr/widgets/preferences-banner/dismiss.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        }
    });
}
</script>
<?php
}
?>
