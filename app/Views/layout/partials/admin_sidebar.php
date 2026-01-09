<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
        <span class="brand-text font-weight-light">FunRun <b>Admin</b></span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="/admin/dashboard" class="nav-link <?= strpos(current_url(), 'dashboard') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/events" class="nav-link <?= strpos(current_url(), 'events') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Events</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/orders" class="nav-link <?= strpos(current_url(), 'orders') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/admin/racekit" class="nav-link <?= current_url() == base_url('/admin/racekit') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tshirt"></i>
                        <p>Pengambilan Race Kit</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/logout" class="nav-link text-danger">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>
