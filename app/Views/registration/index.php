<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mt-20">
    <h1>Keranjang Anda</h1>

    <?php if (empty($cart)): ?>
        <div class="card text-center" style="padding: 40px;">
            <p style="font-size: 1.2em; color: #7f8c8d;">Keranjang Anda kosong.</p>
            <a href="/" class="btn btn-primary">Lihat Tiket</a>
        </div>
    <?php else: ?>
        <div class="card">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 2px solid #ddd;">
                        <th style="padding: 10px; text-align: left;">Kategori</th>
                        <th style="padding: 10px; text-align: right;">Harga</th>
                        <th style="padding: 10px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total = 0;
                    foreach ($cart as $index => $item):
                        $total += $item['price'];
                        ?>
                    <tr>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #eee;">
                            <strong><?= esc($item['category_name']) ?></strong>
                        </td>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: right;">
                            Rp <?= number_format($item['price'], 0, ',', '.') ?>
                        </td>
                        <td style="padding: 15px 10px; border-bottom: 1px solid #eee; text-align: center;">
                            <a href="/registration/remove/<?= $index ?>" class="btn btn-danger" style="padding: 5px 10px; font-size: 0.8em;">Hapus</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td style="padding: 20px 10px; font-weight: bold; font-size: 1.2em;">Total</td>
                        <td style="padding: 20px 10px; font-weight: bold; font-size: 1.2em; text-align: right;">Rp <?= number_format($total, 0, ',', '.') ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div style="text-align: right;">
            <a href="/#register" class="btn" style="color: #7f8c8d; margin-right: 10px;">Tambah Lagi</a>
            <a href="/checkout" class="btn btn-success">Lanjut Pembayaran &rarr;</a>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
