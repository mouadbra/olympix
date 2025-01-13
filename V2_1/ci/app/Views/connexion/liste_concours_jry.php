<table class="table table-striped">
    <thead>
        <tr>
            <th>Nom du Concours</th>
            <th>Administrateur</th>
            <th>Catégories</th>
            <th>Jury</th>
            <th>Date de Début</th>
            <th>Dates du Concours</th>
            <th>Phase Actuelle</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($concours)): ?>
            <?php foreach ($concours as $conc): ?>
                <tr>
                    <td><?= esc($conc['con_nom_concours']) ?></td>
                    <td><?= esc($conc['resp_nom'] . ' ' . $conc['resp_prenom']) ?></td>
                    <td>
                        <?php if (empty($conc['categories'])): ?>
                            <em class="text-muted">Pas de catégories</em>
                        <?php else: ?>
                            <?= esc($conc['categories']) ?>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (empty($conc['juges'])): ?>
                            <em class="text-muted">Pas de jury</em>
                        <?php else: ?>
                            <?= esc($conc['juges']) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= esc(date('d-m-Y', strtotime($conc['con_date_debut']))) ?></td>
                    <td>
                        <?php 
                        $dates = explode("\n", $conc['dates']);
                        foreach ($dates as $date):
                        ?>
                            <div><?= esc($date) ?></div>
                        <?php endforeach; ?>
                    </td>
                    <td><?= esc($conc['phase_actuelle']) ?></td>
                    <td>
                        <!-- Affichage des icônes en fonction de la phase actuelle -->
                        <?php if ($conc['phase_actuelle'] === 'à venir'): ?>
                            <i class="fa-solid fa-magnifying-glass" style="color: #000000;" title="À venir"></i>
                        <?php elseif ($conc['phase_actuelle'] === 'inscription'): ?>
                            <i class="fa-solid fa-magnifying-glass" style="color: #000000;" title="Consulter"></i>
                            <i class="fa-regular fa-pen-to-square" title="S'inscrire"></i>
                        <?php elseif ($conc['phase_actuelle'] === 'selection'): ?>
                            <i class="fa-solid fa-magnifying-glass" style="color: #000000;" title="Consulter"></i>
                        <?php elseif ($conc['phase_actuelle'] === 'finale'): ?>
                            <i class="fa-solid fa-magnifying-glass" style="color: #000000;" title="Consulter"></i>
                            <a href="<?= base_url('/index.php/jury/galerie/' . $conc['con_id_concours']) ?>" class="btn btn-link">
                                <i class="fa-solid fa-user-check" style="color: #000000;" title="Voir la galerie"></i>
                            </a>
                            <i class="fa-solid fa-user-check" style="color: #000000;" title="Juger"></i>
                        <?php else: ?>
                            <i class="fa-solid fa-magnifying-glass" style="color: #000000;" title="Consulter"></i>
                            <a href="<?= base_url('/index.php/jury/galerie/' . $conc['con_id_concours']) ?>" class="btn btn-link">
                                <i class="fa-solid fa-user-check" style="color: #000000;" title="Voir la galerie"></i>
                            </a>
                            <i class="fa-solid fa-trophy" style="color: #000000;" title="Terminé"></i>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="8" class="text-center">Aucun concours trouvé.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>