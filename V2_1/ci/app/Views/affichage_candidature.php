<div class="container mt-4">
    <?php if (isset($error_message)) : ?>
        <!-- Message d'erreur -->
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($error_message); ?>
        </div>
    <?php else : ?>
        <!-- Détails de la candidature -->
        <?php if (isset($candidature->cnd_CODEINSCRIT) && !empty($candidature->cnd_CODEINSCRIT)) : ?>
            <h2>Détails de la Candidature</h2>
            
            <p><strong>Nom : </strong><?= htmlspecialchars($candidature->cnd_Nom); ?></p>
            <p><strong>Prénom : </strong><?= htmlspecialchars($candidature->cnd_Prenom); ?></p>
            <p><strong>Concours : </strong><?= htmlspecialchars($candidature->concours); ?></p>
            <p><strong>Catégorie : </strong><?= htmlspecialchars($candidature->categorie); ?></p>
            <p><strong>Présentation : </strong><?= htmlspecialchars($candidature->cnd_Presentation); ?></p>
            <p><strong>Statut : </strong><?= htmlspecialchars($candidature->cnd_etat); ?></p>
            <p><strong>Code Candidat : </strong><?= htmlspecialchars($candidature->cnd_CODECAND); ?></p>
            <p><strong>Code Inscription : </strong><?= htmlspecialchars($candidature->cnd_CODEINSCRIT); ?></p>

            
            <h3>Documents Associés</h3>
            <?php if (!empty($candidature->documents)) : ?>
                <div class="documents">
                    <?php foreach ($candidature->documents as $doc) : ?>
                        <div class="document-item">
                            <?= htmlspecialchars($doc['doc_descriptionDocument']); ?>
                            <img src="<?= base_url('documents/' . htmlspecialchars($doc['doc_nomDocument'])); ?>" 
                                 alt="<?= htmlspecialchars($doc['doc_nomDocument']); ?>" 
                                 style="max-width: 300px; max-height: 300px;"/>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <p>Aucun document associé.</p>
            <?php endif; ?>
            
        <?php endif; ?>
    <?php endif; ?>
</div>

<div class="mt-4">
    <form action="<?= base_url('index.php/candidature/supprimer'); ?>" method="POST" 
          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer votre candidature ?');">
        <input type="hidden" name="cnd_idCANDIDATURE" value="<?= $candidature->cnd_idCANDIDATURE; ?>">
        <input type="hidden" name="cnd_CODECAND" value="<?= $candidature->cnd_CODECAND; ?>">
        <input type="hidden" name="cnd_CODEINSCRIT" value="<?= $candidature->cnd_CODEINSCRIT; ?>">
        <button type="submit" class="btn btn-danger">
            Supprimer ma candidature
        </button>
    </form>
</div>