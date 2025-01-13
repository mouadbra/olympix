<h2><?= $titre ?></h2>
<?php if(session()->getFlashdata('error')): ?>
    <?php if(is_array(session()->getFlashdata('error'))): ?>
        <div class="alert alert-danger">
            <?php foreach(session()->getFlashdata('error') as $error): ?>
                <p><?= $error ?></p>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
<?php endif; ?>

<?php echo form_open('/admin/creer_compte'); ?>
<?= csrf_field() ?>

<div class="form-group">
    <label for="nom">Nom :</label>
    <input type="text" name="nom" id="nom" class="form-control" value="<?= old('nom') ?? '' ?>">
    <?php if(validation_show_error('nom')): ?>
        <div class="text-danger"><?= validation_show_error('nom') ?></div>
    <?php endif; ?>
</div>

<div class="form-group">
    <label for="prenom">Prénom :</label>
    <input type="text" name="prenom" id="prenom" class="form-control" value="<?= old('prenom') ?? '' ?>">
    <?php if(validation_show_error('prenom')): ?>
        <div class="text-danger"><?= validation_show_error('prenom') ?></div>
    <?php endif; ?>
</div>

<div class="form-group">
    <label for="pseudo">Pseudo :</label>
    <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?= old('pseudo') ?? '' ?>">
    <?php if(validation_show_error('pseudo')): ?>
        <div class="text-danger"><?= validation_show_error('pseudo') ?></div>
    <?php endif; ?>
</div>

<div class="form-group">
    <label for="mdp">Mot de passe :</label>
    <input type="password" name="mdp" id="mdp" class="form-control">
    <?php if(validation_show_error('mdp')): ?>
        <div class="text-danger"><?= validation_show_error('mdp') ?></div>
    <?php endif; ?>
</div>
<div class="form-group">
    <label for="mdp_confirmation">Confirmation du mot de passe :</label>
    <input type="password" name="mdp_confirmation" id="mdp_confirmation" class="form-control">
    <?php if(validation_show_error('mdp_confirmation')): ?>
        <div class="text-danger"><?= validation_show_error('mdp_confirmation') ?></div>
    <?php endif; ?>
</div>
<div class="form-group">
    <label for="role">Rôle :</label>
    <select name="role" id="role" class="form-control">
        <option value="" disabled selected>Choisissez un rôle</option>
        <option value="jury" <?= old('role') == 'jury' ? 'selected' : '' ?>>Jury</option>
        <option value="admin" <?= old('role') == 'admin' ? 'selected' : '' ?>>Admin</option>
    </select>
    <?php if(validation_show_error('role')): ?>
        <div class="text-danger"><?= validation_show_error('role') ?></div>
    <?php endif; ?>
</div>

<!-- Champs supplémentaires pour Admin -->
<div id="admin-fields" style="display: none;">
    <div class="form-group">
        <label for="slogan">Slogan :</label>
        <input type="text" name="slogan" id="slogan" class="form-control" value="<?= old('slogan') ?? '' ?>">
        <?php if(validation_show_error('slogan')): ?>
            <div class="text-danger"><?= validation_show_error('slogan') ?></div>
        <?php endif; ?>
    </div>
</div>

<!-- Champs supplémentaires pour Jury -->
<div id="jury-fields" style="display: none;">
    <div class="form-group">
        <label for="biographie">Biographie :</label>
        <textarea name="biographie" id="biographie" class="form-control"><?= old('biographie') ?? '' ?></textarea>
        <?php if(validation_show_error('biographie')): ?>
            <div class="text-danger"><?= validation_show_error('biographie') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="url">URL :</label>
        <input type="text" name="url" id="url" class="form-control" value="<?= old('url') ?? '' ?>">
        <?php if(validation_show_error('url')): ?>
            <div class="text-danger"><?= validation_show_error('url') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="droit">Droit :</label>
        <input type="text" name="droit" id="droit" class="form-control" value="<?= old('droit') ?? '' ?>">
        <?php if(validation_show_error('droit')): ?>
            <div class="text-danger"><?= validation_show_error('droit') ?></div>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="expertise">Domaine d'expertise :</label>
        <input type="text" name="expertise" id="expertise" class="form-control" value="<?= old('expertise') ?? '' ?>">
        <?php if(validation_show_error('expertise')): ?>
            <div class="text-danger"><?= validation_show_error('expertise') ?></div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('role').addEventListener('change', function() {
        document.getElementById('admin-fields').style.display = this.value === 'admin' ? 'block' : 'none';
        document.getElementById('jury-fields').style.display = this.value === 'jury' ? 'block' : 'none';
    });

    // Afficher/masquer les champs en fonction du rôle déjà sélectionné lors du rechargement de la page
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role');
        const currentRole = roleSelect.value;
        
        document.getElementById('admin-fields').style.display = currentRole === 'admin' ? 'block' : 'none';
        document.getElementById('jury-fields').style.display = currentRole === 'jury' ? 'block' : 'none';
    });
</script>

<button type="submit" class="btn btn-primary">Créer</button>
<?php echo form_close(); ?>