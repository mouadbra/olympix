<!-- app/Views/concours.php -->
<div class="container mt-4">
<h2 class="mb-4">Liste des Concours</h2>
<?php if (!empty($concours)) : ?>
<table class="table table-striped">
<thead>
<tr>
<th scope="col">Phase actuelle</th>
<th scope="col">Nom du concours</th>
<th scope="col">Date de début</th>
<th scope="col">Organisateur responsable</th>
<th scope="col">Catégories</th>
<th scope="col">Juges</th>
<th scope="col">Dates</th>
<th> </th>
</tr>
</thead>
<tbody>
<?php foreach ($concours as $index => $con) : ?>
<tr>
<td><?= htmlspecialchars($con['phase_actuelle']); ?></td>
<td><?= htmlspecialchars($con['con_nom_concours']); ?></td>
<td><?= htmlspecialchars($con['con_date_debut']); ?></td>
<td><?= htmlspecialchars($con['resp_nom'] . ' ' . $con['resp_prenom']); ?></td>
<td>
<?php if (empty($con['categories'])): ?>
<em class="text-muted">Pas de catégories pour le moment</em>
<?php else: ?>
<?= htmlspecialchars($con['categories']); ?>
<?php endif; ?>
</td>
<td>
<?php if (empty($con['juges'])): ?>
<em class="text-muted">Pas de jury pour le moment</em>
<?php else: ?>
<?= htmlspecialchars($con['juges']); ?>
<?php endif; ?>
</td>
<td>
<?php
// Récupérer les dates sous forme de tableau
$dates = explode("\n", $con['dates']);
?>
<ul>
<?php foreach ($dates as $date) : ?>
<li><?= htmlspecialchars($date); ?></li>
<?php endforeach; ?>
</ul>
</td>
<td> <?php if ($con['phase_actuelle'] === 'à venir') : ?>
<i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
<?php elseif ($con['phase_actuelle'] === 'inscription') : ?>
<i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
<i class="fa-regular fa-pen-to-square"></i>
<?php elseif ($con['phase_actuelle'] === 'selection') : ?>
<i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
<?php elseif ($con['phase_actuelle'] === 'finale') : ?>
<i class="fa-solid fa-magnifying-glass" style="color: #000000;"></i>
<a href="<?= base_url('/index.php/candidature/galerie/' . $con['con_id_concours']) ?>" class="btn btn-link">
<i class="fa-solid fa-user-check" style="color: #000000;"></i> Voir la galerie
</a>
<?php else : ?>
<a href="<?= base_url('/index.php/candidature/galerie/' . $con['con_id_concours']) ?>" class="btn btn-link">
<i class="fa-solid fa-user-check" style="color: #000000;"></i> Voir la galerie
</a>
<i class="fa-solid fa-trophy"></i>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else : ?>
<p>Aucun concours trouvé.</p>
<?php endif; ?>
</div>