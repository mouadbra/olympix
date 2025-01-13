<div class="container mt-4">
    <h2 class="mb-4">Candidats Pré-sélectionnés - <?= htmlspecialchars($concours->con_nom_concours) ?></h2>
    
    <?php if (empty($candidats)) : ?>
        <div class="alert alert-info">
            Aucun candidat n'a été pré-sélectionné pour ce concours.
        </div>
    <?php else : ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($candidats as $candidat) : ?>
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">
                                <?= htmlspecialchars($candidat->cnd_Nom) ?> 
                                <?= htmlspecialchars($candidat->cnd_Prenom) ?>
                            </h5>
                            <p class="card-text">
                                <strong>Email :</strong> <?= htmlspecialchars($candidat->cnd_MAIL) ?><br>
                                <strong>Présentation :</strong> <?= htmlspecialchars($candidat->cnd_Presentation) ?><br>
                                <strong>Catégorie :</strong> <?= htmlspecialchars($candidat->categorie) ?><br>
                                <strong>Concours :</strong> <?= htmlspecialchars($candidat->concours) ?>
                            </p>
                            <?php if (!empty($candidat->documents)) : ?>
    <div class="mt-3">
        <h6 class="text-dark">Documents associés :</h6>
        <div class="document-container">
            <?php foreach ($candidat->documents as $key => $document) : ?>
                <div class="document-item mb-2">
                    <a href="#" 
                       class="document-link" 
                       data-bs-toggle="modal" 
                       data-bs-target="#documentModal_<?= $key ?>">
                        <?= htmlspecialchars($document['doc_descriptionDocument']); ?>
                    </a>

                    <!-- Modal pour l'image -->
                    <div class="modal fade" 
                         id="documentModal_<?= $key ?>" 
                         tabindex="-1" 
                         aria-labelledby="documentModalLabel" 
                         aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="documentModalLabel">
                                        <?= htmlspecialchars($document['doc_descriptionDocument']); ?>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                </div>
                                <div class="modal-body text-center">
                                    <img src="<?= base_url('documents/' . htmlspecialchars($document['doc_nomDocument'])); ?>" 
                                         alt="<?= htmlspecialchars($document['doc_nomDocument']); ?>" 
                                         class="img-fluid"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php else : ?>
    <p class="text-muted">Aucun document associé.</p>
<?php endif; ?>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>