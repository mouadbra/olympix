<style>
    .active {
        background-color: #d4edda; /* Vert clair pour les profils activés */
    }
    .inactive {
        background-color: #f8d7da; /* Rouge clair pour les profils désactivés */
    }

    table {
        width: 100%; /* Permet d'utiliser tout l'espace disponible */
        border-collapse: collapse; /* Supprime les espaces entre les bordures */
    }

    th, td {
        padding: 12px; /* Ajoute de l'espace à l'intérieur des cellules */
        text-align: left; /* Aligne le texte à gauche */
        border: 1px solid #ddd; /* Ajoute des bordures aux cellules */
    }

    th {
        background-color: #007bff; /* Couleur de fond pour les en-têtes */
        color: white; /* Texte blanc pour les en-têtes */
    }

    tr:hover {
        background-color: #f1f1f1; /* Couleur de survol pour les lignes */
    }

    .btn-create {
        font-size: 16px;
        padding: 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        display: inline-block;
        margin-bottom: 20px;
        text-align: center;
    }

    .btn-create:hover {
        background-color: #0056b3; /* Assombrit le bouton au survol */
    }
</style>

<h1><?= $titre ?></h1>
<?php if(session()->getFlashdata('success')): ?>
    <div class="alert alert-success">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<!-- Bouton pour créer un nouveau compte -->
<div>
    <button class="btn-create" onclick="window.location.href='<?= base_url('/index.php/admin/creer_compte') ?>'">
        + Créer un nouveau compte
    </button>
</div>

<h2>Comptes Administrateurs</h2>
<table>
    <thead>
        <tr>
            <th style="width: 30%;">Nom</th>
            <th style="width: 30%;">Prénom</th>
            <th style="width: 30%;">Login</th>
            <th style="width: 10%;">Profil Actif</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($admins as $admin): ?>
            <tr class="<?= $admin['prs_profil_actif'] ? 'active' : 'inactive' ?>">
                <td><?= $admin['prs_nom'] ?></td>
                <td><?= $admin['prs_prenom'] ?></td>
                <td><?= $admin['prs_login'] ?></td>
                <td><?= $admin['prs_profil_actif'] ? 'Oui' : 'Non' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Comptes Jury</h2>
<table>
    <thead>
        <tr>
            <th style="width: 30%;">Nom</th>
            <th style="width: 30%;">Prénom</th>
            <th style="width: 30%;">Login</th>
            <th style="width: 10%;">Profil Actif</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($juries as $jury): ?>
            <tr class="<?= $jury['prs_profil_actif'] ? 'active' : 'inactive' ?>">
                <td><?= $jury['prs_nom'] ?></td>
                <td><?= $jury['prs_prenom'] ?></td>
                <td><?= $jury['prs_login'] ?></td>
                <td><?= $jury['prs_profil_actif'] ? 'Oui' : 'Non' ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
