<!-- app/Views/menu_visiteur.php -->
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('/'); ?>">
          <!--  <img src="<?= base_url('/assets/logo.png'); ?>" alt="Olympix Logo" height="30" class="d-inline-block align-top">  -->
            Olympix
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= base_url('/'); ?>">
                        <i class="bi bi-house-door"></i> Accueil
                    </a>
                </li>
                <li class="nav-item">
    <a class="nav-link active" href="<?= base_url('/index.php/candidature/rechercher'); ?>">
        <i class="bi bi-newspaper"></i> Candidature
    </a>
</li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/index.php/concours/afficher'); ?>">
                        <i class="bi bi-trophy"></i> Concours
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= base_url('/index.php/compte/connecter'); ?>">
                        <i class="bi bi-people"></i> Connexion
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Style personnalisé -->
<style>
    /* Rendre le fond de la navbar transparent */
    .custom-navbar {
        background-color: transparent !important; /* Fond transparent */
        box-shadow: none; /* Retirer la bordure ou l'ombre si nécessaire */
    }

    /* Modifier la couleur du texte de la navbar pour le rendre lisible */
    .custom-navbar .navbar-nav .nav-link {
        color: #ffffff; /* Texte en blanc */
    }

    /* Changer la couleur du texte au survol */
    .custom-navbar .navbar-nav .nav-link:hover {
        color: #ffcc00; /* Exemple de couleur au survol */
    }
</style>
