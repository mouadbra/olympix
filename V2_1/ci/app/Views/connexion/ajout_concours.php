<div class="container mt-4">
    <h2>Ajouter un nouveau concours</h2>

    <?php if (session()->getFlashdata('error')) : ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?= form_open(base_url('/index.php/admin/ajout_concours'), ['method' => 'post']) ?>
        <div class="mb-3">
            <label for="nom_concours" class="form-label">Nom du concours</label>
            <input type="text" class="form-control <?= validation_show_error('nom_concours') ? 'is-invalid' : '' ?>" 
                   id="nom_concours" name="nom_concours" 
                   value="<?= old('nom_concours') ?>" 
                   placeholder="Entrez le nom du concours">
            <?php if (validation_show_error('nom_concours')): ?>
                <div class="invalid-feedback">
                    <?= validation_show_error('nom_concours') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="date_debut" class="form-label">Date de début</label>
            <input type="date" class="form-control <?= validation_show_error('date_debut') ? 'is-invalid' : '' ?>" 
                   id="date_debut" name="date_debut" 
                   value="<?= old('date_debut') ?>">
            <?php if (validation_show_error('date_debut')): ?>
                <div class="invalid-feedback">
                    <?= validation_show_error('date_debut') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="temps_candidature" class="form-label">Temps de candidature (en jours)</label>
            <input type="number" class="form-control <?= validation_show_error('temps_candidature') ? 'is-invalid' : '' ?>" 
                   id="temps_candidature" name="temps_candidature" 
                   value="<?= old('temps_candidature') ?>" 
                   placeholder="Nombre de jours pour la période de candidature">
            <?php if (validation_show_error('temps_candidature')): ?>
                <div class="invalid-feedback">
                    <?= validation_show_error('temps_candidature') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="temps_pre_select" class="form-label">Temps de présélection (en jours)</label>
            <input type="number" class="form-control <?= validation_show_error('temps_pre_select') ? 'is-invalid' : '' ?>" 
                   id="temps_pre_select" name="temps_pre_select" 
                   value="<?= old('temps_pre_select') ?>" 
                   placeholder="Nombre de jours pour la période de présélection">
            <?php if (validation_show_error('temps_pre_select')): ?>
                <div class="invalid-feedback">
                    <?= validation_show_error('temps_pre_select') ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="temps_select" class="form-label">Temps de sélection finale (en jours)</label>
            <input type="number" class="form-control <?= validation_show_error('temps_select') ? 'is-invalid' : '' ?>" 
                   id="temps_select" name="temps_select" 
                   value="<?= old('temps_select') ?>" 
                   placeholder="Nombre de jours pour la période de sélection finale">
            <?php if (validation_show_error('temps_select')): ?>
                <div class="invalid-feedback">
                    <?= validation_show_error('temps_select') ?>
                </div>
            <?php endif; ?>
        </div>


        <div class="mb-3">
    <label for="discipline" class="form-label">Discipline</label>
    <input type="text" class="form-control <?= validation_show_error('discipline') ? 'is-invalid' : '' ?>" 
           id="discipline" name="discipline" 
           value="<?= old('discipline') ?>" 
           placeholder="Entrez la discipline du concours">
    <?php if (validation_show_error('discipline')): ?>
        <div class="invalid-feedback">
            <?= validation_show_error('discipline') ?>
        </div>
    <?php endif; ?>
</div>

        <button type="submit" class="btn btn-primary">Ajouter le concours</button>
    <?= form_close() ?>
</div>