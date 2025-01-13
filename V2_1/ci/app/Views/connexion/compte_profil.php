<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <!-- Ajoutez le lien vers votre fichier CSS ici -->
    
</head>
<body>
    <h1>Profil de l'utilisateur</h1>

    <div>
        <h2>Informations Générales</h2>
        <p><strong>Nom : </strong><?= esc($profile_data->prs_nom) ?></p>
        <p><strong>Prénom : </strong><?= esc($profile_data->prs_prenom) ?></p>
        <p><strong>Nom d'utilisateur : </strong><?= esc($profile_data->prs_login) ?></p>

        <?php if ($user_type === 'admin'): ?>
            <h3>Informations administratives</h3>
            <p><strong>Slogan : </strong><?= esc($profile_data->adm_solgan) ?></p>
        <?php elseif ($user_type === 'jury'): ?>
            <h3>Informations du jury</h3>
            <p><strong>Biographie : </strong><?= esc($profile_data->jry_biographie) ?></p>
            <p><strong>URL : </strong><?= esc($profile_data->jry_URL) ?></p>
            <p><strong>Droits : </strong><?= esc($profile_data->jry_droit) ?></p>
            <p><strong>Domaine d'expertise : </strong><?= esc($profile_data->jry_domaine_Expertise) ?></p>
        <?php endif; ?>
    </div>

    <a href="<?= base_url('/index.php/compte/modifier_profil') ?>">Modifier mon profil</a>
</body>
</html>
