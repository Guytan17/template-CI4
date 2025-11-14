<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="light">
<?= $this->include('templates/admin/head') ?>
<body>
<div class="container-fluid">
    <div class="row">
        <div class="col-12 col-lg-2 p-0">
            <?php if (isset($menus) && !empty($menus)) {
                echo view('templates/admin/sidebar',['menus' => $menus]);  }  ?>
        </div>
        <div class="col p-0">
            <div class="d-flex flex-column min-vh-100">
                <?= view('templates/admin/header'); ?>
                <?php if (isset($breadcrumb)) { echo view('templates/admin/breadcrumb');  } ?>
                <div class="body flex-grow-1">
                    <div class="container-fluid px-4">
                        <!-- Contenu de la page -->
                        <?= $this->renderSection('content') ?>
                    </div>
                </div>

                <?= $this->include('templates/admin/footer') ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>