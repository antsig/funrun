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
                <p><i class="fas fa-calendar-alt"></i> <?= date('d F Y', strtotime($event['event_date'])) ?> &nbsp; <i class="fas fa-clock"></i> <?= date('H:i', strtotime($event['event_date'])) ?> WITA &bull; <i class="fas fa-map-marker-alt"></i> <?= esc($event['location']) ?></p>
                
                <?php
                $deadline = $event['registration_deadline'] ?? $event['event_date'];
                ?>
                <p style="color: rgba(255,255,255,0.9); margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px;">
                    <i class="fas fa-hourglass-end"></i> Batas Pendaftaran: <?= date('d F Y • H:i', strtotime($deadline)) ?> WITA
                </p>

                <!-- Countdown Timer -->
                <div id="countdown" class="mb-4">
                    <div class="countdown-item">
                        <span id="days">00</span>
                        <small>Hari</small>
                    </div>
                    <div class="countdown-item">
                        <span id="hours">00</span>
                        <small>Jam</small>
                    </div>
                    <div class="countdown-item">
                        <span id="minutes">00</span>
                        <small>Menit</small>
                    </div>
                    <div class="countdown-item">
                        <span id="seconds">00</span>
                        <small>Detik</small>
                    </div>
                </div>
                <br>

                <?php
                $isClosed = date('Y-m-d H:i:s') > $deadline;
                ?>

                <?php if ($isClosed): ?>
                    <button class="btn btn-secondary" disabled style="background: #95a5a6; cursor: not-allowed;">PENDAFTARAN DITUTUP</button>
                    <p class="mt-2 text-white"><small>Batas pendaftaran: <?= date('d F Y H:i', strtotime($deadline)) ?></small></p>
                <?php else: ?>
                    <a href="#register" class="btn">DAFTAR SEKARANG</a>
                <?php endif; ?>
            </div>
            <!-- Optional: Valid Sport Image here if available -->
             <div class="hero-image-placeholder">
                <?php if ($banner = get_setting('home_banner')): ?>
                    <img src="/<?= $banner ?>" alt="Event Banner" style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
                <?php else: ?>
                    <div style="width: 40%; height: 300px; background: rgba(0,0,0,0.1); border: 2px dashed rgba(255,255,255,0.3); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                        Runner Image
                    </div>
                <?php endif; ?>
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
    if (!empty($event)):
        // Use registration_deadline if set, otherwise event_date
        $targetDate = $event['registration_deadline'] ?? $event['event_date'];

        // Ensure we have a string
        if (!$targetDate) {
            $timestamp = 0;  // Invalid
        } else {
            $timestamp = (int) strtotime($targetDate) * 1000;
        }
    else:
        $timestamp = 0;
    endif;
    ?>
    
    // Server Timestamp: <?= $timestamp ?> 
    const targetDate = <?= $timestamp ?>;
    
    // Safety check for DOM elements
    const elDays = document.getElementById("days");
    const elHours = document.getElementById("hours");
    const elMinutes = document.getElementById("minutes");
    const elSeconds = document.getElementById("seconds");

    function updateCountdown() {
        const now = new Date().getTime();
        
        // If targetDate is 0 or invalid, consider closed/past
        if (!targetDate || targetDate <= 0) {
            handleExpired();
            return;
        }

        const distance = targetDate - now;

        if (distance < 0) {
            handleExpired();
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        // Update DOM safely
        if(elDays) elDays.innerText = days.toString().padStart(2, '0');
        if(elHours) elHours.innerText = hours.toString().padStart(2, '0');
        if(elMinutes) elMinutes.innerText = minutes.toString().padStart(2, '0');
        if(elSeconds) elSeconds.innerText = seconds.toString().padStart(2, '0');
    }

    function handleExpired() {
        const countdownEl = document.getElementById("countdown");
        if (countdownEl) {
             // Only update if not already updated to avoid flickering/looping
             if (!countdownEl.innerHTML.includes("PENDAFTARAN DITUTUP")) {
                 countdownEl.innerHTML = "<div class='time-box' style='width:100%; min-width:auto; padding: 10px 20px; background: rgba(255, 0, 0, 0.6);'><span style='font-size:1.5rem'>PENDAFTARAN DITUTUP</span></div>";
             }
        }
        
        const btn = document.querySelector('a[href="#register"]');
        if(btn) btn.style.display = 'none';
    }

    // Run interval
    setInterval(updateCountdown, 1000);
    updateCountdown(); // Run immediately
});

// Qty Function (Global)
function updateQty(btn, change) {
    const wrapper = btn.closest('div');
    const input = wrapper.querySelector('input');
    
    if(!input) return;
    
    let newVal = parseInt(input.value) + change;
    if (newVal < 1) newVal = 1;
    if (newVal > 10) newVal = 10;
    
    input.value = newVal;
    
    // Update display if needed (wrapper sibling)
    const displaySpan = wrapper.querySelector('span'); 
    if(displaySpan) displaySpan.innerText = newVal;
}
</script>

<?= $this->endSection() ?>
