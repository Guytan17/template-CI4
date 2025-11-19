<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirection...</title>
    <script>
        // Rediriger vers notre page d'inscription personnalisée
        window.location.href = '<?= base_url('account/register') ?>';
    </script>
</head>
<body>
    <p>Redirection vers la page d'inscription...</p>
    <p>Si vous n'êtes pas redirigé automatiquement, <a href="<?= base_url('account/register') ?>">cliquez ici</a>.</p>
</body>
</html>
