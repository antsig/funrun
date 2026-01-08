<header>
    <div class="container navbar">
        <a href="/" class="logo">FunRun 🏃</a>
        <nav>
            <a href="/" class="nav-link">Beranda</a>
            <a href="/registration" class="nav-link">Keranjang (<?= count(session()->get('cart') ?? []) ?>)</a>
        </nav>
    </div>
</header>
