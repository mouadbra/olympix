<h1><?php echo $titre; ?></h1>
<div class="container mt-4">
    <?php if (isset($actualite)) : ?>
        <h2><?= htmlspecialchars($actualite->act_titre); ?></h2>
        <p><strong>Date :</strong> <?= htmlspecialchars($actualite->act_date); ?></p>
        <p><strong>Description :</strong> <?= htmlspecialchars($actualite->act_description); ?></p>
        <p><strong>Organisateur :</strong> <?= isset($actualite->prs_nom) ? htmlspecialchars($actualite->prs_nom) : 'Non spécifié'; ?></p>
    <?php else : ?>
        <p>Actualité non trouvée.</p>
    <?php endif; ?>
</div>
