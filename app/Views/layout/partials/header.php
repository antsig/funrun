<header>
    <div class="container navbar">
        <a href="/" class="logo">
            <?php if ($logo = get_setting('site_logo')): ?>
                <img src="/<?= $logo ?>" alt="Logo" style="height: 40px; vertical-align: middle; margin-right: 5px;">
                <span class="logo-text"><?= get_setting('app_name') ?></span>
            <?php else: ?>
                <?= get_setting('site_title', 'FunRun 🏃') ?>
            <?php endif; ?>
        </a>
        <nav>
            <a href="/" class="nav-link">Beranda</a>
            <a href="/cek-tiket" class="nav-link">Cek Tiket</a>
            <a href="/registration" class="nav-link">Keranjang (<?= count(session()->get('cart') ?? []) ?>)</a>
        </nav>
    </div>
</header>
