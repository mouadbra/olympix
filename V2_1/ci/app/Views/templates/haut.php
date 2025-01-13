<!-- app/Views/haut.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title>Olympix - MB</title>
  <meta name="description" content="">
  <meta name="keywords" content="">
  <!--icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900&family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Cardo:ital,wght@0,400;0,700;1,400;1,700&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="<?php echo base_url();?>bootstrap/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?php echo base_url();?>bootstrap/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="<?php echo base_url();?>bootstrap/assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="<?php echo base_url();?>bootstrap/assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="<?php echo base_url();?>bootstrap/assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="<?php echo base_url();?>bootstrap/assets/css/main.css" rel="stylesheet">







<!-- Dans haut.php -->
<style>
    body {
        background-image: url('<?php echo base_url();?>bootstrap/assets/img/micro.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        background-attachment: fixed;
        min-height: 100vh;
        margin: 0; /* Supprime les marges par défaut */
        display: flex;
        flex-direction: column;
    }

    .container {
      background-color: rgba(0, 0, 0, 0.9);
        padding: 20px;
        border-radius: 10px;
        margin-top: 20px;
    }





    .footer {
          /* Couleur du texte */
    font-size: 0.85rem;    /* Réduction de la taille du texte */
    padding: 10px 0;       /* Réduction de l'espacement vertical */
    line-height: 1.5;      /* Hauteur des lignes plus compacte */
}

.footer .social-links a {
    font-size: 0.9rem; /* Réduction de la taille des icônes */
    margin: 0 5px;     /* Espacement réduit entre les icônes */
}






</style>
</head>








</head>

<body>
<div class="d-flex flex-column min-vh-100"> <!-- Conteneur principal -->
<?php echo view('menu_visiteur'); ?> <!-- Inclusion du menu de navigation -->
</body>
</html>
