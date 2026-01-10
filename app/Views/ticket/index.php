<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>
<div class="container" style="padding: 60px 20px; min-height: 60vh;">
    <div style="max-width: 600px; margin: 0 auto;">
        
        <!-- Search Form -->
        <div class="card mb-4" style="text-align: center;">
            <h2 class="mb-4 text-center">Cek Status Tiket</h2>
            <p>Masukkan Kode Pesanan Anda untuk melihat status pembayaran dan tiket.</p>
            
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <form action="/cek-tiket" method="post">
                <div style="margin-bottom: 20px;">
                    <input type="text" name="order_code" placeholder="Kode Pesanan (Contoh: ORD-12345)" required style="font-size: 1.2rem; text-align: center; letter-spacing: 2px; text-transform: uppercase;" value="<?= isset($order) ? esc($order['order_code']) : '' ?>">
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">CEK STATUS</button>
            </form>
        </div>

        <?php if (isset($order)): ?>
            <!-- Result -->
             <div class="card">
                <h3 style="border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 20px;">Detail Pesanan</h3>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>Kode Pesanan:</strong>
                    <span style="font-family: monospace; font-size: 1.2em;"><?= esc($order['order_code']) ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 10px;">
                    <strong>Tanggal:</strong>
                    <span><?= date('d F Y H:i', strtotime($order['created_at'])) ?></span>
                </div>

                <div style="display: flex; justify-content: space-between; margin-bottom: 20px;">
                    <strong>Status:</strong>
                    <?php
                    $statusColor = 'grey';
                    $statusText = $order['payment_status'];
                    if ($order['payment_status'] == 'paid') {
                        $statusColor = 'green';
                        $statusText = 'LUNAS';
                    } elseif ($order['payment_status'] == 'pending') {
                        $statusColor = 'orange';
                        $statusText = 'MENUNGGU PEMBAYARAN';
                    } elseif ($order['payment_status'] == 'expired') {
                        $statusColor = 'red';
                        $statusText = 'KADALUARSA';
                    }
                    ?>
                    <span style="font-weight: bold; color: <?= $statusColor ?>;"><?= strtoupper($statusText) ?></span>
                </div>

                <?php if ($order['payment_status'] == 'pending'): ?>
                    <div style="text-align: center; margin-bottom: 20px;">
                        <a href="/payment/<?= $order['order_code'] ?>" class="btn btn-primary">Lanjut ke Pembayaran</a>
                    </div>
                <?php endif; ?>

                <h4 class="mt-4 mb-3">Daftar Peserta</h4>
                <div style="background: #f9f9f9; border-radius: 8px; overflow: hidden;">
                    <?php foreach ($participants as $p): ?>
                        <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; align-items: center; justify-content: space-between;">
                            <div>
                                <div style="font-weight: bold;"><?= esc($p['name']) ?></div>
                                <div style="font-size: 0.9em; color: #666;"><?= esc($p['category_name']) ?></div>
                            </div>
                            <?php if (!empty($p['bib_number'])): ?>
                                <div style="text-align: right;">
                                    <small style="color: #888;">BIB Number</small><br>
                                    <span style="font-size: 1.5em; font-weight: bold; color: var(--secondary); display: block; line-height: 1;"><?= esc($p['bib_number']) ?></span>
                                    
                                    <div style="margin-top: 8px;">
                                        <?php if (!empty($p['race_kit_collected'])): ?>
                                            <span class="badge badge-success" style="background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; display: inline-block;">
                                                <i class="fas fa-check-circle"></i> Race Kit Diambil
                                            </span>
                                            <?php if (!empty($p['taker_name'])): ?>
                                                <div style="font-size: 0.75em; color: #666; margin-top: 2px;">
                                                    Oleh: <strong><?= esc($p['taker_name']) ?></strong>
                                                </div>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge badge-warning" style="background: #fff3cd; color: #856404; padding: 4px 8px; border-radius: 4px; font-size: 0.8em; display: inline-block;">
                                                Belum Diambil
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php else: ?>
                                <span class="badge badge-secondary" style="font-size: 0.8em; padding: 5px 10px; background: #ddd; color: #666; border-radius: 20px;">Belum ada BIB</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
             </div>
        <?php endif; ?>

    </div>
</div>
<?= $this->endSection() ?>
