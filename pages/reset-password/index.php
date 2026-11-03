<?php

include('../../scripts/session/index.php');

include('../../scripts/verif/index.php');
include('../../scripts/mail/index.php');

if (isset($_SESSION['id_compte'])) {
    header('Location: https://family.matthieudevilliers.fr/pages/listes/');
    exit();
}

header('Referrer-Policy: no-referrer');
header('Cache-Control: no-store');

$alert = false;
$info = false;
$invalide = false;
$token = isset($_GET['reset']) && is_string($_GET['reset']) ? $_GET['reset'] : '';
$tokenValide = static function ($valeur) {
    return is_string($valeur) && preg_match('/\A[a-f0-9]{64}\z/', $valeur) === 1;
};

if (isset($_POST['reset'])) {
    $tokenPost = $_POST['reset'];
    $mail = $_POST['Mail'] ?? null;
    $motDePasse = $_POST['MDP'] ?? null;
    if (!$tokenValide($tokenPost) || !$tokenValide($token) || !hash_equals($token, $tokenPost)
        || !is_string($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL)
        || !is_string($motDePasse) || $motDePasse === '' || strlen($motDePasse) > 72 || strpos($motDePasse, "\0") !== false) {
        $invalide = true;
        $alert = "Le lien ou les informations fournies ne sont pas valides.";
    } else {
        $bdd->beginTransaction();
        try {
            $sql = 'SELECT lic_compte.id FROM lic_password_reset
                    INNER JOIN lic_compte ON lic_compte.id = lic_password_reset.id_compte
                    WHERE lic_password_reset.token_hash = ? AND lic_password_reset.expires_at > UTC_TIMESTAMP()
                      AND lic_compte.mail = ? AND lic_compte.deleted_to IS NULL FOR UPDATE';
            $response = $bdd->prepare($sql);
            $response->execute(array(hash('sha256', $tokenPost), $mail));
            $compte = $response->fetch();
            $response->closeCursor();
            if (!$compte) {
                $bdd->rollBack();
                $invalide = true;
                $alert = "Le lien ou les informations fournies ne sont pas valides.";
            } else {
                $response = $bdd->prepare('UPDATE lic_compte SET motdepasse = ? WHERE id = ? AND deleted_to IS NULL');
                $response->execute(array(password_hash($motDePasse, PASSWORD_DEFAULT), $compte['id']));
                $response->closeCursor();
                $response = $bdd->prepare('DELETE FROM lic_password_reset WHERE id_compte = ?');
                $response->execute(array($compte['id']));
                $response->closeCursor();
                $response = $bdd->prepare('INSERT INTO lic_notif (id_compte, titre, message) VALUES (?, ?, ?)');
                $response->execute(array($compte['id'], 'Mot de passe modifié', "Votre mot de passe a été modifié. Si vous n'êtes pas à l'origine de ce changement, contactez le support."));
                $response->closeCursor();
                $bdd->commit();
                $info = 'Votre mot de passe a bien été modifié. Vous pouvez vous connecter.';
                $token = '';
            }
        } catch (Throwable $e) {
            if ($bdd->inTransaction()) {
                $bdd->rollBack();
            }
            throw $e;
        }
    }
} elseif (isset($_POST['Mail'])) {
    $mail = $_POST['Mail'];
    $captcha = $_POST['g-recaptcha-response'] ?? null;
    if (!is_string($mail) || !filter_var($mail, FILTER_VALIDATE_EMAIL) || !is_string($captcha) || $captcha === '') {
        $alert = 'Adresse mail ou CAPTCHA invalide.';
    } else {
        $url = 'https://www.google.com/recaptcha/api/siteverify';
    $data = array('secret' => '6Lc-ZgkaAAAAAAcf5v9GqcfTfm2o6lzOq1Y6kftU', 'response' => $captcha);
        $options = array('http' => array(
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => 5
        ));
        $result = @file_get_contents($url, false, stream_context_create($options));
        $captchaResult = $result === false ? null : json_decode($result);
        if (!$captchaResult || empty($captchaResult->success)) {
            $alert = 'CAPTCHA invalide ou indisponible.';
        } else {
            $response = $bdd->prepare('SELECT id FROM lic_compte WHERE mail = ? AND deleted_to IS NULL');
            $response->execute(array($mail));
            $compte = $response->fetch();
            $response->closeCursor();
            if ($compte) {
                $nouveauToken = bin2hex(random_bytes(32));
                $tokenHash = hash('sha256', $nouveauToken);
                $response = $bdd->prepare('INSERT INTO lic_password_reset (token_hash, id_compte, expires_at) VALUES (?, ?, DATE_ADD(UTC_TIMESTAMP(), INTERVAL 30 MINUTE))');
                $response->execute(array($tokenHash, $compte['id']));
                $response->closeCursor();
                $lien = 'https://family.matthieudevilliers.fr/pages/reset-password/?reset=' . urlencode($nouveauToken);
                $contenu = '<p>Vous avez demandé à réinitialiser votre mot de passe.</p><p><a href="' . safe_output($lien) . '">Réinitialiser le mot de passe</a></p><p>Ce lien expire dans 30 minutes et ne peut être utilisé qu’une seule fois.</p>';
                if (!envoiMail($mail, "Mot de passe oublié - Listes d'idées cadeau", $contenu, false)) {
                    $response = $bdd->prepare('DELETE FROM lic_password_reset WHERE token_hash = ?');
                    $response->execute(array($tokenHash));
                    $response->closeCursor();
                }
            }
            $info = "Si cette adresse correspond à un compte, un lien de réinitialisation sera envoyé.";
        }
    }
}

if ($token !== '' && !isset($_POST['reset'])) {
    if (!$tokenValide($token)) {
        $invalide = true;
    } else {
        $response = $bdd->prepare('SELECT lic_compte.id FROM lic_password_reset INNER JOIN lic_compte ON lic_compte.id = lic_password_reset.id_compte WHERE lic_password_reset.token_hash = ? AND lic_password_reset.expires_at > UTC_TIMESTAMP() AND lic_compte.deleted_to IS NULL');
        $response->execute(array(hash('sha256', $token)));
        $invalide = !$response->fetch();
        $response->closeCursor();
    }
    if ($invalide) {
        $alert = "Le lien n'est plus valide !";
    }
}

?>
<!DOCTYPE html>
<html lang="fr" class="h-100">

<head>
    <title>Listes d'idées cadeau - Réinitialiser votre mot de passe</title>

    <!-- Import -->
    <?php include('../../widgets/import/index.php'); ?>

    <?php if (!isset($_GET['reset'])) { ?>
        <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php } ?>
</head>

<body class="d-flex flex-column h-100">

    <?php include('../../widgets/navbar/index.php'); ?>

    <main id="main-content" tabindex="-1">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-9 col-lg-6">
                    <?php
                    if ($alert) {
                    ?>
                        <div class="alert alert-danger fade show" role="alert">
                            <strong><?php echo safe_output($alert); ?></strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    } elseif ($info) {
                    ?>
                        <div class="alert alert-success fade show" role="alert">
                            <?php echo safe_output($info); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fermer"></button>
                        </div>
                    <?php
                    }
                    ?>
                    <h1 class="text-center mb-4">Mot de passe oublié</h1>
                    <?php
                    if ($token !== '') {
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post">
                                    <?php csrf_field(); ?>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input name="Mail" type="email" class="form-control" id="LabelMail" autocomplete="email" placeholder="name@example.com" required <?php if ($invalide) echo 'disabled aria-disabled="true"' ?>>
                                                <label for="LabelMail">Adresse mail</label>
                                            </div>
                                            <div class="form-floating">
                                                <input name="MDP" type="password" class="form-control" id="LabelMDP" autocomplete="new-password" placeholder="Mot de passe" required <?php if ($invalide) echo 'disabled aria-disabled="true"' ?>>
                                                <label for="LabelMDP">Nouveau mot de passe</label>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" name="reset" value="<?php echo safe_output($token); ?>" class="btn btn-primary" <?php echo $invalide ? 'disabled aria-disabled="true"' : ''; ?>>Réinitialiser le mot de passe</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="card">
                            <div class="card-body">
                                <form action="" method="post">
                                    <?php csrf_field(); ?>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <div class="form-floating">
                                                <input name="Mail" type="email" class="form-control" id="LabelMail" autocomplete="email" placeholder="name@example.com" required <?php if ($invalide) echo 'disabled aria-disabled="true"' ?>>
                                                <label for="LabelMail">Adresse mail</label>
                                            </div>
                                            <div class="d-flex justify-content-center mb-3">
                                                <div class="g-recaptcha" data-sitekey="6Lc-ZgkaAAAAAEoCEsUwvVPygJPyhxGDtPIvkppO"></div>
                                            </div>
                                            <div class="text-center">
                                                <button type="submit" class="btn btn-primary" <?php echo $invalide ? 'disabled aria-disabled="true"' : ''; ?>>Demander la réinitialisation</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </main>

    <?php include('../../widgets/footer/index.php'); ?>
</body>

</html>
