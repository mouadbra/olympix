<div class="container">
    <h2>Modifier mon mot de passe</h2>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php echo form_open('/compte/modifier_profil'); ?>
        <?= csrf_field() ?>

        <!-- Champs non modifiables -->
        <div class="mb-3">
            <label for="prs_nom" class="form-label">Nom</label>
            <input type="text" class="form-control" value="<?= esc($user_data->prs_nom) ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="prs_prenom" class="form-label">Prénom</label>
            <input type="text" class="form-control" value="<?= esc($user_data->prs_prenom) ?>" readonly>
        </div>

        <?php if ($role === 'admin'): ?>
        <div class="mb-3">
            <label for="adm_solgan" class="form-label">Slogan</label>
            <input type="text" class="form-control" value="<?= esc($user_data->adm_solgan) ?>" readonly>
        </div>
        <?php endif; ?>

        <?php if ($role === 'jury'): ?>
        <div class="mb-3">
            <label for="jry_biographie" class="form-label">Biographie</label>
            <textarea class="form-control" readonly><?= esc($user_data->jry_biographie) ?></textarea>
        </div>

        <div class="mb-3">
            <label for="jry_URL" class="form-label">URL</label>
            <input type="url" class="form-control" value="<?= esc($user_data->jry_URL) ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="jry_droit" class="form-label">Droits</label>
            <input type="text" class="form-control" value="<?= esc($user_data->jry_droit) ?>" readonly>
        </div>

        <div class="mb-3">
            <label for="jry_domaine_Expertise" class="form-label">Domaine d'Expertise</label>
            <input type="text" class="form-control" value="<?= esc($user_data->jry_domaine_Expertise) ?>" readonly>
        </div>
        <?php endif; ?>

        <div class="card mb-3">
            <div class="card-header">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="togglePassword">
                    <label class="form-check-label" for="togglePassword">Modifier mon mot de passe</label>
                </div>
            </div>
            <div class="card-body password-fields" style="display: none;">
                <div class="mb-3">
                    <label for="mdp" class="form-label">Nouveau mot de passe</label>
                    <input type="password" name="mdp" id="mdp" class="form-control <?= validation_show_error('mdp') ? 'is-invalid' : '' ?>" disabled>
                    <?php if(validation_show_error('mdp')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('mdp') ?>
                        </div>
                    <?php endif; ?>
                    
                </div>
                <div class="mb-3">
                    <label for="confirm_mdp" class="form-label">Confirmer le mot de passe</label>
                    <input type="password" name="confirm_mdp" id="confirm_mdp" class="form-control <?= validation_show_error('confirm_mdp') ? 'is-invalid' : '' ?>" disabled>
                    <?php if(validation_show_error('confirm_mdp')): ?>
                        <div class="invalid-feedback">
                            <?= validation_show_error('confirm_mdp') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="mb-3">
            <button type="submit" class="btn btn-success">Valider</button>
            <a href="<?= base_url('/index.php/compte/afficher_profil') ?>" class="btn btn-secondary">Annuler</a>
        </div>
    </form>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordFields = document.querySelector('.password-fields');
    const mdpInput = document.getElementById('mdp');
    const confirmMdpInput = document.getElementById('confirm_mdp');
    const changePasswordInput = document.getElementById('change_password');
    
    togglePassword.addEventListener('change', function() {
        passwordFields.style.display = this.checked ? 'block' : 'none';
        mdpInput.disabled = !this.checked;
        confirmMdpInput.disabled = !this.checked;
        changePasswordInput.value = this.checked ? 'yes' : 'no';
        
        if (!this.checked) {
            mdpInput.value = '';
            confirmMdpInput.value = '';
        }
    });
});
</script>