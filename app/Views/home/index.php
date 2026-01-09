<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php if ($event): ?>
    <div class="hero">
        <div class="container">
            <div class="hero-content">
                <div style="background: var(--secondary); color: white; display: inline-block; padding: 5px 15px; font-weight: bold; margin-bottom: 10px; transform: skewX(-10deg);">
                    <span style="display: block; transform: skewX(10deg);">EVENT TERDEKAT</span>
                </div>
                <h1><?= esc($event['name']) ?></h1>
                <p><?= date('d F Y', strtotime($event['event_date'])) ?> &bull; <?= esc($event['location']) ?></p>
                <a href="#register" class="btn">DAFTAR SEKARANG</a>
            </div>
            <!-- Optional: Valid Sport Image here if available -->
             <div class="hero-image-placeholder" style="width: 40%; height: 300px; background: rgba(0,0,0,0.1); border: 2px dashed rgba(255,255,255,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                Runner Image
            </div>
        </div>
    </div>

    <div class="container">
        <div class="card">
            <h2>Tentang Acara</h2>
            <p><?= nl2br(esc($event['description'])) ?></p>
        </div>

        <h2 class="mb-20" id="register">Pilih Kategori</h2>
        <div class="grid">
            <?php foreach ($categories as $category): ?>
            <div class="card">
                <h3><?= esc($category['name']) ?></h3>
                <div style="font-size: 1.5em; font-weight: bold; color: #27ae60; margin: 10px 0;">
                    Rp <?= number_format($category['price'], 0, ',', '.') ?>
                </div>
                <p>Kuota: <?= $category['quota'] ?> slot</p>
                
                <?php
                $cart = session('cart') ?? [];
                $qty = 0;
                foreach ($cart as $item) {
                    if (isset($item['category_id']) && $item['category_id'] == $category['id'])
                        $qty++;
                }
                ?>

                <div style="margin-top: 15px;">
                    <?php if ($qty == 0): ?>
                        <form action="/registration/add" method="post">
                            <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                            <input type="hidden" name="category_name" value="<?= esc($category['name']) ?>">
                            <input type="hidden" name="price" value="<?= $category['price'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary" style="width: 100%; font-weight: bold;">DAFTAR</button>
                        </form>
                    <?php else: ?>
                        <div style="display: flex; align-items: center; justify-content: center; gap: 10px;">
                            <a href="/registration/decrease/<?= $category['id'] ?>" class="btn-qty" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ddd; background: white; font-weight: bold; color: #333; display: flex; align-items: center; justify-content: center; text-decoration: none; font-size: 1.2em;">-</a>
                            
                            <span style="font-size: 1.5em; font-weight: bold; width: 40px; text-align: center;"><?= $qty ?></span>
                            
                            <form action="/registration/add" method="post" style="display: inline;">
                                <input type="hidden" name="category_id" value="<?= $category['id'] ?>">
                                <input type="hidden" name="category_name" value="<?= esc($category['name']) ?>">
                                <input type="hidden" name="price" value="<?= $category['price'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" class="btn-qty" style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #ddd; background: white; font-weight: bold; color: #333; cursor: pointer; font-size: 1.2em;">+</button>
                            </form>
                        </div>
                        <div style="text-align: center; margin-top: 5px; font-size: 0.9em; color: #27ae60;">
                            Sudah di keranjang
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($categories)): ?>
                <div class="card">Belum ada kategori tiket.</div>
            <?php endif; ?>
        </div>

        <?php
        $cart = session('cart') ?? [];
        $cartTotal = count($cart);
        ?>
        <?php if ($cartTotal > 0): ?>
            <div style="margin-top: 40px; text-align: center; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05);">
                <h3 style="margin-bottom: 10px; color: #333;">Keranjang Belanja</h3>
                <p style="color: #666; margin-bottom: 20px;">Anda memiliki <strong><?= $cartTotal ?> tiket</strong> yang belum diselesaikan.</p>
                <a href="/checkout" class="btn btn-success" style="padding: 12px 30px; font-size: 1.1em; border-radius: 50px;">
                    Lanjut Isi Data Peserta <i class="fas fa-arrow-right" style="margin-left: 5px;"></i>
                </a>
            </div>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="container" style="padding: 100px 20px; text-align: center;">
        <div style="max-width: 600px; margin: 0 auto;">
            <i class="fas fa-running" style="font-size: 5em; color: #e0e0e0; margin-bottom: 30px;"></i>
            <h2 style="color: #333; margin-bottom: 15px; font-weight: bold;">Belum Ada Event Aktif</h2>
            <p style="color: #7f8c8d; font-size: 1.2em; line-height: 1.6;">
                Saat ini kami sedang mempersiapkan event lari seru berikutnya untuk Anda.<br>
                Mohon kembali lagi nanti atau pantau update terbaru dari kami.
            </p>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<script>
function updateQty(btn, change) {
    const wrapper = btn.closest('div');
    const input = wrapper.querySelector('input');
    const btnMinus = wrapper.querySelector('.btn-minus');
    const btnPlus = wrapper.querySelector('.btn-plus');
    
    let newVal = parseInt(input.value) + change;
    if (newVal < 1) newVal = 1;
    if (newVal > 10) newVal = 10;
    
    input.value = newVal;

    // Update Button States
    if (newVal <= 1) {
        btnMinus.disabled = true;
        btnMinus.style.color = '#aaa';
    } else {
        btnMinus.disabled = false;
        btnMinus.style.color = '#333';
    }

    if (newVal >= 10) {
        btnPlus.disabled = true;
        btnPlus.style.color = '#aaa';
    } else {
        btnPlus.disabled = false;
        btnPlus.style.color = '#333';
    }
}
</script>
