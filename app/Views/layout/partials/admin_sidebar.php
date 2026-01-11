<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="/admin/dashboard" class="brand-link">
        <?php if ($logo = get_setting('site_logo')): ?>
            <img src="/<?= $logo ?>" alt="Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
            <span class="brand-text font-weight-light"><?= get_setting('app_name', 'FunRun') ?></span>
        <?php else: ?>
            <span class="brand-text font-weight-light"><?= get_setting('site_title', 'FunRun Admin') ?></span>
        <?php endif; ?>
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
                
                <?php if (session()->get('role') === 'administrator'): ?>
                <li class="nav-item">
                    <a href="/admin/events" class="nav-link <?= strpos(current_url(), 'events') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Events</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="/admin/orders" class="nav-link <?= strpos(current_url(), 'orders') ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>Orders</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/admin/racekit" class="nav-link <?= strpos(uri_string(), 'racekit') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-tshirt"></i>
                        <p>Race Kit</p>
                    </a>
                </li>                

                <li class="nav-item">
                    <a href="/admin/reports" class="nav-link <?= strpos(uri_string(), 'reports') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Laporan</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="/admin/profile" class="nav-link <?= strpos(uri_string(), 'profile') !== false ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-user-circle"></i>
                        <p>Profil Saya</p>
                    </a>
                </li>
                
                <?php if (session()->get('role') === 'administrator'): ?>
                <li class="nav-item <?= (strpos(uri_string(), 'users') !== false || strpos(uri_string(), 'settings') !== false) ? 'menu-open' : '' ?>">
                    <a href="#" class="nav-link <?= (strpos(uri_string(), 'users') !== false || strpos(uri_string(), 'settings') !== false) ? 'active' : '' ?>">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>
                            Pengaturan
                            <i class="right fas fa-angle-left"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/admin/settings" class="nav-link <?= (strpos(uri_string(), 'settings') !== false && strpos(uri_string(), 'social-media') === false) ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                            <p>Website & Email</p>
                            </a>
                        </li>                        
                        <li class="nav-item">
                            <a href="/admin/settings/social-media" class="nav-link <?= strpos(uri_string(), 'social-media') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Social Media</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/users" class="nav-link <?= strpos(uri_string(), 'users') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Kelola User</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/admin/backup" class="nav-link <?= strpos(uri_string(), 'backup') !== false ? 'active' : '' ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Backup & Restore</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
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
