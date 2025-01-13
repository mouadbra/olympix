</div> <!-- Fin du container-fluid pour le contenu de la page -->
        </div> <!-- Fin du page-content-wrapper -->
    </div> <!-- Fin du wrapper -->

    <!-- Bootstrap core JS-->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Core theme JS-->
    <script src="<?= base_url('bootstrap2/js/scripts.js'); ?>"></script>



<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalTriggers = document.querySelectorAll('[data-bs-toggle="modal"]');
    modalTriggers.forEach(function(trigger) {
        trigger.addEventListener('click', function(e) {
            console.log('Modal trigger clicked');
            var targetId = this.getAttribute('data-bs-target') || this.getAttribute('href');
            var modal = document.querySelector(targetId);
            if (modal) {
                console.log('Modal found:', targetId);
                var bsModal = new bootstrap.Modal(modal);
                bsModal.show();
            } else {
                console.error('Modal not found:', targetId);
            }
        });
    });
});
</script>

</body>
</html>
