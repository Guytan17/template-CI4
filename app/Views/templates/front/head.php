<!DOCTYPE html>
<html lang="fr-FR" data-bs-theme="light">
<head>
    <base href="<?= base_url('./') ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="keyword" content="">
    <title><?= $title ?? "" ?></title>
    <!-- Favicons-->
    <link rel="icon" type="image/png" href="<?= base_url('/assets/favicon/favicon-96x96.png') ?>" sizes="96x96" />
    <link rel="icon" type="image/svg+xml" href="<?= base_url('/assets/favicon/favicon.svg') ?>" />
    <link rel="shortcut icon" href="<?= base_url('/assets/favicon/favicon.ico') ?>"/>
    <link rel="apple-touch-icon" sizes="180x180" href="<?= base_url('/assets/favicon/apple-touch-icon.png') ?>"/>
    <meta name="apple-mobile-web-app-title" content="Ins3gram" />
    <link rel="manifest" href="<?= base_url('/assets/favicon/site.webmanifest') ?>"/>
    <meta name="theme-color" content="#ffffff">

    <!-- BOOTSTRAP BUNDLE -->
    <link href="<?= base_url('/assets/css/bootstrap.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('/assets/js/bootstrap.bundle.min.js') ?>"></script>

    <!-- CSS-->
    <link href="<?= base_url('/assets/css/style.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('/assets/css/toastr.min.css') ?>">
    <link href="<?= base_url('/assets/css/custom-front.css') ?>" rel="stylesheet">

    <!-- Javascript -->
    <script src="<?= base_url('/assets/js/jquery-3.7.1.min.js') ?>"></script>
    <script>
        var base_url = '<?= base_url(); ?>';
        let csrfName = '<?= csrf_token() ?>';
        let csrfHash = '<?= csrf_hash() ?>';
    </script>
    <script src="<?= base_url('/assets/js/main.js') ?>"></script>
    <script src="<?= base_url('/assets/js/front.js') ?>"></script>

    <!-- Library -->
    <script src="<?= base_url('/assets/js/toastr.min.js') ?>"></script>
    <script src="<?= base_url('/assets/js/tinymce/tinymce.min.js') ?>"></script>

    <!-- Javascript CDN -->
    <script src="https://code.jquery.com/ui/1.14.1/jquery-ui.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>

    <!-- FontAwesome CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/js/all.min.js" integrity="sha512-6BTOlkauINO65nLhXhthZMtepgJSghyimIalb+crKRPhvhmsCdnIuGcVbR5/aQY2A+260iC1OPy1oCdB6pSSwQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <!-- Datatable CDN -->
    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.0/b-3.0.0/b-html5-3.0.0/fh-4.0.0/sp-2.3.0/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.0.0/b-3.0.0/b-html5-3.0.0/fh-4.0.0/sp-2.3.0/datatables.min.js"></script>

    <!-- SWEETALERT 2  -->
    <link href="<?= base_url('/assets/css/sweetalert2.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('/assets/js/sweetalert2.all.min.js') ?>"></script>

    <!-- Lightbox -->
    <link href="<?= base_url('/assets/css/lightbox.min.css') ?>" rel="stylesheet">
    <script src="<?= base_url('/assets/js/lightbox.min.js') ?>"></script>

    <!-- SELECT 2 -->
    <link href="<?=base_url('/assets/css/select2.min.css'); ?>" rel="stylesheet"></link>
    <link href="<?=base_url('/assets/css/select2-bootstrap-5-theme.min.css'); ?>" rel="stylesheet"></link>
    <script src="<?= base_url('/assets/js/select2.min.js') ?>"></script>
</head>