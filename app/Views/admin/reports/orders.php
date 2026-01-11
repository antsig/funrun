<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Laporan Pesanan</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <!-- Filter Card -->
        <div class="card card-outline card-primary d-print-none">
            <div class="card-header">
                <h3 class="card-title">Filter Laporan</h3>
            </div>
            <div class="card-body">
                <form method="get" action="">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status Pembayaran</label>
                                <select name="status" class="form-control">
                                    <option value="">Semua Status</option>
                                    <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                                    <option value="paid" <?= ($filters['status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                                    <option value="expired" <?= ($filters['status'] == 'expired') ? 'selected' : '' ?>>Expired</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Dari Tanggal</label>
                                <input type="date" name="start_date" class="form-control" value="<?= $filters['start_date'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Sampai Tanggal</label>
                                <input type="date" name="end_date" class="form-control" value="<?= $filters['end_date'] ?>">
                            </div>
                        </div>
                        <div class="col-md-3 align-self-end">
                            <div class="form-group">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-filter"></i> Terapkan Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Data Table -->
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Pesanan</h3>
                <div class="card-tools">
                    <a href="/admin/reports/export_orders?<?= http_build_query($filters) ?>" class="btn btn-success btn-sm d-print-none">
                        <i class="fas fa-file-excel"></i> Export Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-default btn-sm d-print-none">
                        <i class="fas fa-print"></i> Cetak PDF
                    </button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kode Pesanan</th>
                            <th>Nama Pemesan</th>
                            <th>Email</th>
                            <th>Total Bayar</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr><td colspan="7" class="text-center">Tidak ada data ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $key => $order): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td><?= esc($order['order_code']) ?></td>
                                <td><?= esc($order['buyer_name']) ?></td>
                                <td><?= esc($order['buyer_email']) ?></td>
                                <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                                <td>
                                    <?php
                                    $badges = [
                                        'pending' => 'badge-warning',
                                        'paid' => 'badge-success',
                                        'expired' => 'badge-danger'
                                    ];
                                    $badge = $badges[$order['payment_status']] ?? 'badge-secondary';
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= ucfirst($order['payment_status']) ?></span>
                                </td>
                                <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
<?= $this->endSection() ?>
