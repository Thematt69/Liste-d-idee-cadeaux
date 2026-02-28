<?php

session_start();

include('../../scripts/verif/index.php');

if (!isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/connexion/');
    exit();
}

$alert = false;
$info = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $allergies = htmlentities($_POST['allergies'] ?? '');
    $couleur_preferee = htmlentities($_POST['couleur_preferee'] ?? '');
    $taille_vetements = htmlentities($_POST['taille_vetements'] ?? '');
    $centres_interet = htmlentities($_POST['centres_interet'] ?? '');
    $style_decoration = htmlentities($_POST['style_decoration'] ?? '');
    $objet_ou_experience = htmlentities($_POST['objet_ou_experience'] ?? '');
    $peche_mignon = htmlentities($_POST['peche_mignon'] ?? '');
    $obsession_moment = htmlentities($_POST['obsession_moment'] ?? '');
    $genres_lecture_musique_film = htmlentities($_POST['genres_lecture_musique_film'] ?? '');
    $pas_recevoir = htmlentities($_POST['pas_recevoir'] ?? '');
    $deja_trop = htmlentities($_POST['deja_trop'] ?? '');

    // Check if preferences already exist
    $sql_check = 'SELECT id FROM lic_preferences WHERE id_compte = ? AND deleted_to IS NULL';
    $response_check = $bdd->prepare($sql_check);
    $response_check->execute(array($_SESSION['id_compte']));
    $existing = $response_check->fetch();
    $response_check->closeCursor();

    if ($existing) {
        // Update existing preferences
        $sql = 'UPDATE lic_preferences 
                SET allergies = ?, couleur_preferee = ?, taille_vetements = ?,
                    centres_interet = ?, style_decoration = ?, objet_ou_experience = ?,
                    peche_mignon = ?, obsession_moment = ?, genres_lecture_musique_film = ?,
                    pas_recevoir = ?, deja_trop = ?
                WHERE id_compte = ? AND deleted_to IS NULL';
        
        $response = $bdd->prepare($sql);
        $response->execute(array(
            $allergies, $couleur_preferee, $taille_vetements,
            $centres_interet, $style_decoration, $objet_ou_experience,
            $peche_mignon, $obsession_moment, $genres_lecture_musique_film,
            $pas_recevoir, $deja_trop, $_SESSION['id_compte']
        ));
    } else {
        // Insert new preferences
        $sql = 'INSERT INTO lic_preferences 
                (id_compte, allergies, couleur_preferee, taille_vetements,
                 centres_interet, style_decoration, objet_ou_experience,
                 peche_mignon, obsession_moment, genres_lecture_musique_film,
                 pas_recevoir, deja_trop)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        
        $response = $bdd->prepare($sql);
        $response->execute(array(
            $_SESSION['id_compte'], $allergies, $couleur_preferee, $taille_vetements,
            $centres_interet, $style_decoration, $objet_ou_experience,
            $peche_mignon, $obsession_moment, $genres_lecture_musique_film,
            $pas_recevoir, $deja_trop
        ));
    }
    
    $response->closeCursor();
    $info = 'Vos préférences ont été enregistrées avec succès !';
}

// Fetch existing preferences
$sql = 'SELECT * FROM lic_preferences WHERE id_compte = ? AND deleted_to IS NULL';
$response = $bdd->prepare($sql);
$response->execute(array($_SESSION['id_compte']));
$preferences = $response->fetch();
$response->closeCursor();

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Mon Profil Cadeau</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-12 col-lg-10">
                <br>
                <?php
                if ($alert) {
                ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong><?php echo $alert; ?></strong>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                } elseif ($info) {
                ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $info; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php
                }
                ?>
                <h1 class="text-center">Mon Profil Cadeau ✨</h1>
                <p class="text-center text-muted">
                    <em>"Envie d'être surpris ?"</em><br>
                    En complément de votre liste, partagez ici vos goûts, vos passions et vos petites habitudes. 
                    Ces informations (totalement optionnelles) aideront vos proches à vous trouver le cadeau parfait, 
                    même s'il n'est pas encore dans votre liste !
                </p>
                <br>
                
                <form action="" method="post">
                    <!-- Section 1 : Les Essentiels -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h4 class="mb-0">Section 1 : Les Essentiels (Profil &amp; Sécurité)</h4>
                            <small>C'est la base pour éviter les erreurs et donner une direction immédiate.</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="allergies" class="form-label">Allergies ou régimes spécifiques ?</label>
                                <textarea name="allergies" class="form-control" id="allergies" rows="2" 
                                    placeholder="Ex : Sans gluten, végétarien, pas d'alcool, allergie aux arachides..."><?php echo isset($preferences['allergies']) ? safe_output($preferences['allergies']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="couleur_preferee" class="form-label">Quelle est votre couleur préférée ou dominante ?</label>
                                <textarea name="couleur_preferee" class="form-control" id="couleur_preferee" rows="2" 
                                    placeholder="Ex : Bleu pétrole, terracotta, tons pastel, noir et blanc..."><?php echo isset($preferences['couleur_preferee']) ? safe_output($preferences['couleur_preferee']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="taille_vetements" class="form-label">Quelle est votre taille de vêtements ou accessoires ?</label>
                                <textarea name="taille_vetements" class="form-control" id="taille_vetements" rows="2" 
                                    placeholder="Ex : Taille M, pointure 39, tour de doigt 52..."><?php echo isset($preferences['taille_vetements']) ? safe_output($preferences['taille_vetements']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2 : Univers & Style de vie -->
                    <div class="card mb-4">
                        <div class="card-header bg-success text-white">
                            <h4 class="mb-0">Section 2 : Univers &amp; Style de vie</h4>
                            <small>Pour aider à choisir l'ambiance du cadeau.</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="centres_interet" class="form-label">Quels sont vos centres d'intérêt ou loisirs ?</label>
                                <textarea name="centres_interet" class="form-control" id="centres_interet" rows="2" 
                                    placeholder="Ex : Cuisine italienne, randonnée, yoga, jeux de société..."><?php echo isset($preferences['centres_interet']) ? safe_output($preferences['centres_interet']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="style_decoration" class="form-label">Comment décrivez-vous votre style de décoration ?</label>
                                <textarea name="style_decoration" class="form-control" id="style_decoration" rows="2" 
                                    placeholder="Ex : Minimaliste, industriel, bohème, jungle urbaine..."><?php echo isset($preferences['style_decoration']) ? safe_output($preferences['style_decoration']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="objet_ou_experience" class="form-label">Plutôt objet physique ou expérience à vivre ?</label>
                                <textarea name="objet_ou_experience" class="form-control" id="objet_ou_experience" rows="2" 
                                    placeholder="Ex : J'aime garder un souvenir / Je préfère une sortie ou une activité."><?php echo isset($preferences['objet_ou_experience']) ? safe_output($preferences['objet_ou_experience']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 3 : Petits plaisirs & Passions -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white">
                            <h4 class="mb-0">Section 3 : Petits plaisirs &amp; Passions</h4>
                            <small>Pour les attentions qui touchent en plein cœur.</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="peche_mignon" class="form-label">Votre péché mignon ou gourmandise favorite ?</label>
                                <textarea name="peche_mignon" class="form-control" id="peche_mignon" rows="2" 
                                    placeholder="Ex : Le chocolat noir intense, le bon café en grains, les thés fumés..."><?php echo isset($preferences['peche_mignon']) ? safe_output($preferences['peche_mignon']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="obsession_moment" class="form-label">Votre obsession ou thématique du moment ?</label>
                                <textarea name="obsession_moment" class="form-control" id="obsession_moment" rows="2" 
                                    placeholder="Ex : J'apprends la céramique, je me passionne pour l'espace..."><?php echo isset($preferences['obsession_moment']) ? safe_output($preferences['obsession_moment']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="genres_lecture_musique_film" class="form-label">Quels genres de lecture, musique ou films aimez-vous ?</label>
                                <textarea name="genres_lecture_musique_film" class="form-control" id="genres_lecture_musique_film" rows="2" 
                                    placeholder="Ex : Romans policiers, jazz, science-fiction, documentaires..."><?php echo isset($preferences['genres_lecture_musique_film']) ? safe_output($preferences['genres_lecture_musique_film']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Section 4 : Le guide "Anti-Déception" -->
                    <div class="card mb-4">
                        <div class="card-header bg-warning text-dark">
                            <h4 class="mb-0">Section 4 : Le guide "Anti-Déception"</h4>
                            <small>Pour être sûr de ne pas offrir quelque chose qui finira au fond d'un placard.</small>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="pas_recevoir" class="form-label">Ce que vous ne souhaitez surtout PAS recevoir ?</label>
                                <textarea name="pas_recevoir" class="form-control" id="pas_recevoir" rows="2" 
                                    placeholder="Ex : Pas de gadgets inutiles, pas de produits de beauté, pas de plastique..."><?php echo isset($preferences['pas_recevoir']) ? safe_output($preferences['pas_recevoir']) : ''; ?></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="deja_trop" class="form-label">Ce que vous avez déjà en (trop) grande quantité ?</label>
                                <textarea name="deja_trop" class="form-control" id="deja_trop" rows="2" 
                                    placeholder="Ex : J'ai déjà trop de mugs, de bougies ou de carnets..."><?php echo isset($preferences['deja_trop']) ? safe_output($preferences['deja_trop']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mb-4">
                        <button type="submit" class="btn btn-primary btn-lg">Enregistrer mes préférences</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>

<?php include('../../widgets/footer/index.php'); ?>

</html>
