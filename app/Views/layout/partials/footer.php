<footer class="footer">
    <div class="container">
        &copy; <?= date('Y') ?> FunRun Organizer. Hak Cipta Dilindungi.
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: <?= json_encode(session()->getFlashdata('success')) ?>,
                confirmButtonColor: '#FFD700',
                color: '#333'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: <?= json_encode(session()->getFlashdata('error')) ?>,
                confirmButtonColor: '#d33',
                color: '#333'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('toast_success')): ?>
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: 'success',
                title: <?= json_encode(session()->getFlashdata('toast_success')) ?>
            });
        <?php endif; ?>
    });
</script>

</body>
</html>
