<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mt-20">
    <h1 class="text-center mb-20">Pembayaran</h1>

    <div class="grid">
        <div class="card" style="flex: 1;">
            <h3>Ringkasan</h3>
            <ul style="list-style: none; padding: 0;">
                <?php
                $total = 0;
                foreach ($cart as $item):
                    $total += $item['price'];
                    ?>
                <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #eee;">
                    <span><?= esc($item['category_name']) ?></span>
                    <span>Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                </li>
                <?php endforeach; ?>
                <li style="display: flex; justify-content: space-between; padding: 15px 0 0; font-weight: bold; font-size: 1.2em;">
                    <span>Total Pembayaran</span>
                    <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                </li>
            </ul>
        </div>

        <div style="flex: 2;">
            <form action="/checkout/process" method="post">
                <div class="card">
                    <h3>Data Pemesan</h3>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="buyer_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="buyer_email" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor HP / WA</label>
                        <input type="text" name="buyer_phone" required>
                    </div>
                </div>

                <div class="card">
                    <h3>Data Peserta</h3>
                    <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Silakan lengkapi data untuk setiap pelari.</p>

                    <?php foreach ($cart as $index => $item): ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #FFD700;">
                        <h4>Peserta <?= $index + 1 ?> - <?= esc($item['category_name']) ?></h4>
                        <input type="hidden" name="participants[<?= $index ?>][category_id]" value="<?= $item['category_id'] ?>">
                        
                        <div class="grid" style="grid-template-columns: 1fr 1fr;">
                            <div class="form-group">
                                <label>Nama Peserta</label>
                                <input type="text" name="participants[<?= $index ?>][name]" required>
                            </div>
                            <div class="form-group">
                                <label>Jenis Kelamin</label>
                                <select name="participants[<?= $index ?>][gender]" required>
                                    <option value="">Pilih...</option>
                                    <option value="L">Laki-laki</option>
                                    <option value="P">Perempuan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Lahir</label>
                                <input type="date" name="participants[<?= $index ?>][dob]" required>
                            </div>
                            <div class="form-group">
                                <label>Ukuran Jersey</label>
                                <select name="participants[<?= $index ?>][jersey_size]" required>
                                    <option value="">Pilih Ukuran</option>
                                    <option value="XS">XS</option>
                                    <option value="S">S</option>
                                    <option value="M">M</option>
                                    <option value="L">L</option>
                                    <option value="XL">XL</option>
                                    <option value="XXL">XXL</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2em;">Bayar Sekarang</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
