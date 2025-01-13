<div class="container mt-4">
    <h2>Rechercher une Candidature</h2>
    
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <form action="<?= base_url('/index.php/candidature/rechercher') ?>" method="post">
        <div class="mb-3">
            <label for="cnd_CODECAND" class="form-label">Code Candidature</label>
            <input type="text" class="form-control" id="cnd_CODECAND" name="cnd_CODECAND" required>
        </div>
        <div class="mb-3">
            <label for="cnd_CODEINSCRIT" class="form-label">Code Inscription</label>
            <input type="text" class="form-control" id="cnd_CODEINSCRIT" name="cnd_CODEINSCRIT" required>
        </div>
        <button type="submit" class="btn btn-primary">Rechercher</button>
    </form>
</div>