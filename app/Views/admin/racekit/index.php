<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Pengambilan Race Kit (Jersey)</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Scan / Input Order Code</h3>
                    </div>
                    <form action="/admin/racekit/search" method="get">
                        <div class="card-body">
                            <div class="form-group text-center">
                                <label for="order_code">Scan Barcode / QR Code pada Bukti Pembayaran</label>
                                <input type="text" name="order_code" id="order_code" class="form-control form-control-lg text-center" placeholder="FR2026-XXXXX" autofocus required>
                                <small class="text-muted">Pastikan kursor aktif di kolom ini saat scan.</small>
                            </div>
                        </div>
                        <div class="card-footer text-center">
                            <button type="submit" class="btn btn-primary btn-lg px-5">Cari Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                 <div class="card card-outline card-success">
                    <div class="card-header">
                        <h3 class="card-title">Riwayat Pengambilan Terakhir</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Waktu Ambil</th>
                                    <th>Kode Order</th>
                                    <th>Peserta</th>
                                    <th>Diambil Oleh</th>
                                    <th>Jersey</th>
                                    <th>Admin</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentCollections)): ?>
                                    <?php foreach ($recentCollections as $rc): ?>
                                    <tr>
                                        <td><?= date('d M H:i', strtotime($rc['collected_at'])) ?></td>
                                        <td><a href="/admin/racekit/detail/<?= $rc['order_code'] ?>"><?= esc($rc['order_code']) ?></a></td>
                                        <td><?= esc($rc['name']) ?></td>
                                        <td>
                                            <?php if (!empty($rc['taker_name'])): ?>
                                                <?= esc($rc['taker_name']) ?> <small class="text-muted">(<?= esc($rc['taker_phone']) ?>)</small>
                                            <?php else: ?>
                                                <span class="text-muted"><?= esc($rc['buyer_name']) ?> (Pemesan)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?= esc($rc['jersey_size']) ?> (<?= esc($rc['jersey_status']) ?>)</td>
                                        <td><?= esc($rc['admin_name'] ?? 'ID: ' . $rc['collected_by']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="6" class="text-center">Belum ada data pengambilan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                 </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
