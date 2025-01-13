<div class="border-end bg-white" id="sidebar-wrapper">
    <div class="sidebar-heading border-bottom bg-light">Menu Administrateur</div>
    <div class="list-group list-group-flush">
       <!--  <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Dashboard</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Shortcuts</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Overview</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Events</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Profile</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="#!">Status</a>
        Rubriques ajoutées -->
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?= base_url('/index.php/compte/afficher_profil'); ?>">Mon profil</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?= base_url('/index.php/admin/concours'); ?>">Concours</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?= base_url('/index.php/admin/creer_compte'); ?>">Créer un compte</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?= base_url('/index.php/admin/lister'); ?>">Liste des comptes</a>
        <a class="list-group-item list-group-item-action list-group-item-light p-3" href="<?= base_url('/index.php/compte/deconnecter'); ?>">Déconnexion</a>
    </div>
</div>
