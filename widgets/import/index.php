<meta charset="utf-8">

<meta name="title" content="Listes d'idées cadeau">
<meta name="author" content="Matthieu Devilliers">
<meta name="description" content="Listes d'idées cadeau pour Noël ou Anniversaire">
<meta name="keywords" content="idée,idee,cadeaux,noël,noel,anniversaire">
<meta name="owner" content="Matthieu Devilliers">
<meta name="robots" content="noindex">
<meta name="reply-to" content="webmaster@matthieudevilliers.fr">
<meta name="revisit-after" content="30">

<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="color-scheme" content="light dark">

<script>
    (() => {
        const preference = window.matchMedia('(prefers-color-scheme: dark)');
        const applyTheme = () => document.documentElement.setAttribute('data-bs-theme', preference.matches ? 'dark' : 'light');

        applyTheme();
        preference.addEventListener('change', applyTheme);
    })();
</script>

<link rel="icon" href="/images/logo.webp">

<?php
// Le ?v= change avec le fichier : le cache d'un an du .htaccess reste sans risque.
$asset = function ($path) {
    $file = __DIR__ . '/../..' . $path;
    return $path . (is_file($file) ? '?v=' . filemtime($file) : '');
};
?>
<!-- Bootstrap CSS -->
<link rel="stylesheet" href="<?php echo $asset('/css/bootstrap.min.css'); ?>">
<link rel="stylesheet" href="<?php echo $asset('/css/bootstrap-icons/bootstrap-icons.css'); ?>">

<!-- Bootstrap JS -->
<script src="<?php echo $asset('/js/bootstrap.bundle.min.js'); ?>" defer></script>

<!-- Bootstrap -->
<script src="<?php echo $asset('/js/jquery.min.js'); ?>"></script>

<!-- Notifications JS -->
<script type="module" src="<?php echo $asset('/js/notifications.js'); ?>"></script>

<!-- Style CSS -->
<link href="<?php echo $asset('/css/style.css'); ?>" rel="stylesheet">