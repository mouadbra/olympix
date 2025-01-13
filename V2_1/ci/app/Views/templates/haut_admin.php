<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="" />
    <meta name="author" content="" />
    <title><?= isset($titre) ? esc($titre) : 'Panel Utilisateur'; ?></title>
    <!-- Favicon-->
    <link rel="icon" type="image/x-icon" href="<?= base_url('assets/favicon.ico'); ?>" />
    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="<?= base_url('bootstrap2/css/styles.css'); ?>" rel="stylesheet" />
    <!--icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Menu dynamique basé sur le rôle -->
        <?php
log_message('info', 'Rôle utilisateur reçu dans la vue : ' . (isset($user_type) ? $user_type : 'non défini'));

if (isset($user_type)) {
    $menuFile = 'menus/menu_' . $user_type . '.php';
    if (file_exists(APPPATH . 'Views/' . $menuFile)) {
        echo view($menuFile);
    } else {
        echo "<div class='alert alert-danger'>Menu non défini pour le rôle : " . esc($user_type) . "</div>";
    }
} else {
    echo "<div class='alert alert-warning'>Aucun rôle utilisateur défini</div>";
}
?>


        <!-- Page content wrapper-->
        <div id="page-content-wrapper">
            <!-- Top navigation-->
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="sidebarToggle">Toggle Menu</button>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarSupportedContent">
                        <ul class="navbar-nav ms-auto mt-2 mt-lg-0">
                            
                        </ul>
                    </div>
                </div>
            </nav>
            <!-- Page content-->
            <div class="container-fluid">
