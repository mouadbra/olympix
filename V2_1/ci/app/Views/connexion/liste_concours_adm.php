<?php if(session()->has('success')): ?>
    <div class="alert alert-success">
        <?= session('success') ?>
    </div>
<?php endif; ?>

<?php if(session()->has('error')): ?>
    <div class="alert alert-danger">
        <?= session('error') ?>
    </div>
<?php endif; ?>
<div class="container mt-4">
<a href="<?= base_url('/index.php/admin/ajout_concours') ?>" class="btn btn-primary mb-3">
    Ajouter un nouveau concours
</a>

    <h2>Liste des Concours</h2>

    <?php if (isset($message)): ?>
        <div class="alert alert-info">
            <?= $message ?>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nom du concours</th>
                        
                        <th>Date de début</th>
                        <th>Phase actuelle</th>
                        <th>Administrateur responsable</th>
                        <th>Jury</th>
                        <th>Catégories</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($concours as $concour): ?>
                        <tr>
                            <td><?= esc($concour['con_nom_concours']) ?></td>
                            
                            <td><?= date('d/m/Y', strtotime($concour['con_date_debut'])) ?></td>
                            <td>
                                <span class="badge bg-<?= get_phase_badge_color($concour['phase_actuelle']) ?>">
                                    <?= esc($concour['phase_actuelle']) ?>
                                </span>
                            </td>
                            <td>
                                <?= esc($concour['resp_nom']) ?> <?= esc($concour['resp_prenom']) ?>
                            </td>
                            <td>
                                <?php if (empty($concour['juges'])): ?>
                                    <em class="text-muted">Aucun membre du jury</em>
                                <?php else: ?>
                                    <?= esc($concour['juges']) ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if (empty($concour['categories'])): ?>
                                    <em class="text-muted">Aucune catégorie</em>
                                <?php else: ?>
                                    <?= esc($concour['categories']) ?>
                                <?php endif; ?>
                            </td>

                            
                            <td>
                                <a 
                                   class="btn btn-sm btn-info">
                                    Détails
                                </a>
                                <?php if ($concour['phase_actuelle'] === 'à venir'): ?>
                            <form action="<?= base_url('/index.php/admin/supprimer_concours/' . $concour['con_id_concours']) ?>" method="post" onsubmit="return confirm('Voulez-vous vraiment supprimer ce concours ?');">
                             <button type="submit" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>
                            <?php endif; ?>
                            </td>


                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.badge {
    padding: 0.5em 1em;
}
</style>

<?php
// Fonction helper pour les couleurs des badges de phase
function get_phase_badge_color($phase) {
    switch($phase) {
        case 'à venir':
            return 'secondary';
        case 'inscription':
            return 'primary';
        case 'selection':
            return 'info';
        case 'finale':
            return 'warning';
        case 'terminé':
            return 'success';
        default:
            return 'secondary';
    }
}
?>