<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Laporan Peserta</h1>
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
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Kategori Lari</label>
                                <select name="category_id" class="form-control">
                                    <option value="">Semua Kategori</option>
                                    <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>><?= esc($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gender</label>
                                <select name="gender" class="form-control">
                                    <option value="">Semua</option>
                                    <option value="L" <?= ($filters['gender'] == 'L') ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= ($filters['gender'] == 'P') ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4 align-self-end">
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
                <h3 class="card-title">Data Peserta</h3>
                <div class="card-tools">
                    <a href="/admin/reports/export_participants?<?= http_build_query($filters) ?>" class="btn btn-success btn-sm d-print-none">
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
                            <th>BIB</th>
                            <th>Nama Peserta</th>
                            <th>Kategori</th>
                            <th>L/P</th>
                            <th>Jersey</th>
                            <th>Kode Pesanan</th>
                            <th>Status Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($participants)): ?>
                            <tr><td colspan="8" class="text-center">Tidak ada data ditemukan.</td></tr>
                        <?php else: ?>
                            <?php foreach ($participants as $key => $p): ?>
                            <tr>
                                <td><?= $key + 1 ?></td>
                                <td>
                                    <?php if ($p['bib_number']): ?>
                                        <span class="badge badge-info"><?= esc($p['bib_number']) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= esc($p['name']) ?></td>
                                <td><?= esc($p['category_name']) ?></td>
                                <td><?= $p['gender'] ?></td>
                                <td><?= $p['jersey_size'] ?></td>
                                <td><?= esc($p['order_code']) ?></td>
                                <td>
                                    <span class="badge <?= ($p['payment_status'] == 'paid') ? 'badge-success' : 'badge-warning' ?>">
                                        <?= ucfirst($p['payment_status']) ?>
                                    </span>
                                </td>
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
