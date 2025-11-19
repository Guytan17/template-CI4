<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="light">
<?= $this->include('templates/front/head') ?>

<body>
<div class="container-fluid p-0">
    <?= $this->include('templates/front/navbar') ?>

</div>
<div class="body flex-grow-1">
<main class="front-main">
    <div class="container">
        <?= $this->renderSection('content') ?>
    </div>
</main>

<?= $this->include('templates/front/footer') ?>
</body>
</html>