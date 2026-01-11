<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="/admin/dashboard" class="nav-link">Home</a>
        </li>
        <?php if (getenv('CI_ENVIRONMENT') === 'production'): ?>
        <li class="nav-item d-none d-md-inline-block">
            <a href="#" class="nav-link text-danger font-weight-bold" style="border: 2px solid red; border-radius: 5px; padding: 5px 10px; margin-top: 2px;">
                <i class="fas fa-exclamation-triangle"></i> PRODUCTION MODE
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item">
            <a class="nav-link" href="/" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
        </li>
    </ul>
</nav>
