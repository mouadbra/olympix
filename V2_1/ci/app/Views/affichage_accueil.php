<style>
    .table-custom {
        background-color: #f8f9fa; /* Gris clair pour l'arrière-plan du tableau */
        color: #333333; /* Texte en gris foncé */
        border: 2px solid #007bff; /* Bordure bleue autour du tableau */
    }
    .table-custom thead {
        background-color: #007bff; /* Bleu pour l'en-tête */
        color: #ffffff; /* Texte blanc */
    }
    .table-custom tbody tr:nth-child(odd) {
        background-color: #e9ecef; /* Gris clair pour les lignes impaires */
    }
    .table-custom tbody tr:nth-child(even) {
        background-color: #ffffff; /* Blanc pour les lignes paires */
    }
    .table-custom td, .table-custom th {
        border: 1px solid #dee2e6; /* Bordures internes */
    }
</style>

<div class="container mt-4">
    <h1>Photographie Microcosmique</h1>
    <h2><?= isset($actualite) ? 'Détail de l\'actualité' : 'Liste des Actualités'; ?></h2>

    <?php if (isset($actualite)) : ?>
        <table class="table table-custom">
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Date</th>
                    <th scope="col">Description</th>
                    <th scope="col">Organisateur</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($actualite->act_titre); ?></td>
                    <td><?= htmlspecialchars($actualite->act_date); ?></td>
                    <td><?= htmlspecialchars($actualite->act_description); ?></td>
                    <td><?= htmlspecialchars($actualite->prs_nom); ?></td>
                </tr>
            </tbody>
        </table>
    <?php elseif (!empty($actualites)) : ?>
        <table class="table table-custom">
            <thead>
                <tr>
                    <th scope="col">Titre</th>
                    <th scope="col">Date</th>
                    <th scope="col">Description</th>
                    <th scope="col">Organisateur</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($actualites as $act) : ?>
                    <tr>
                    <td>
    <a href="<?= base_url('index.php/actualite/afficher/' . $act['act_id_actu']); ?>">
        <?= htmlspecialchars($act['act_titre']); ?>
    </a>
</td>

                        <td><?= htmlspecialchars($act['act_date']); ?></td>
                        <td><?= htmlspecialchars($act['act_description']); ?></td>
                        <td><?= htmlspecialchars($act['prs_nom']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else : ?>
        <p>Pas d'actualités disponibles pour le moment.</p>
    <?php endif; ?>

    <?php if (isset($message)) : ?>
        <p class="text-danger"><?= htmlspecialchars($message); ?></p>
    <?php endif; ?>
</div>

