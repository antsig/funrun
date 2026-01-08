<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success',
                text: <?= json_encode(session()->getFlashdata('success')) ?>,
                confirmButtonColor: '#FFD700',
                confirmButtonText: 'OK'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: <?= json_encode(session()->getFlashdata('error')) ?>,
                confirmButtonColor: '#e74c3c'
            });
        <?php endif; ?>
    });
</script>
</body>
</html>
