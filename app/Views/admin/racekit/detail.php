<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Detail Pengambilan Race Kit</h1>
            </div>
            <div class="col-sm-6 text-right">
                 <a href="/admin/racekit" class="btn btn-secondary"><i class="fas fa-search"></i> Scan Lainnya</a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <?php if ($order['payment_status'] !== 'paid'): ?>
        <div class="alert alert-danger">
            <h5><i class="icon fas fa-ban"></i> Peringatan!</h5>
            Order ini belum lunas. Status saat ini: <strong><?= strtoupper($order['payment_status']) ?></strong>.
            Tidak dapat melakukan pengambilan Race Kit.
        </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-4">
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Info Order</h3>
                    </div>
                    <div class="card-body">
                        <strong>Order Code:</strong>
                        <p class="text-primary text-xl"><?= esc($order['order_code']) ?></p>
                        
                        <strong>Nama Pemesan:</strong>
                        <p><?= esc($order['buyer_name']) ?></p>

                        <strong>Status Pembayaran:</strong>
                        <p><span class="badge badge-<?= $order['payment_status'] == 'paid' ? 'success' : 'danger' ?>"><?= strtoupper($order['payment_status']) ?></span></p>
                    </div>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Peserta & Status Pengambilan</h3>
                        <?php if ($order['payment_status'] == 'paid'): ?>
                        <div class="card-tools">
                             <a href="/admin/racekit/mark-all/<?= $order['id'] ?>" class="btn btn-success btn-sm" onclick="return confirm('Tandai SEMUA peserta sudah mengambil?')"><i class="fas fa-check-double"></i> Ambil Semua</a>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>BIB</th>
                                    <th>Nama Peserta</th>
                                    <th>Kategori (Jersey)</th>
                                    <th>Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($participants as $p): ?>
                                <tr>
                                    <td><span class="badge badge-info text-md"><?= esc($p['bib_number'] ?? 'BELUM ADA') ?></span></td>
                                    <td><?= esc($p['name']) ?></td>
                                    <td>
                                        <?= esc($p['category_name']) ?><br>
                                        <small>Size: <strong><?= esc($p['jersey_size']) ?></strong></small>
                                    </td>
                                    <td>
                                        <?php if ($p['is_collected']): ?>
                                            <span class="badge badge-success"><i class="fas fa-check"></i> SUDAH DIAMBIL</span><br>
                                            <small class="text-muted"><?= date('d M H:i', strtotime($p['collected_at'])) ?></small>
                                        <?php else: ?>
                                            <span class="badge badge-warning">BELUM DIAMBIL</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!$p['is_collected'] && $order['payment_status'] == 'paid'): ?>
                                            <button type="button" class="btn btn-primary btn-sm btn-collect" data-id="<?= $p['id'] ?>" data-name="<?= esc($p['name']) ?>">
                                                <i class="fas fa-hand-holding"></i> Ambil
                                            </button>
                                        <?php elseif ($p['is_collected']): ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Selesai</button>
                                        <?php else: ?>
                                            <button class="btn btn-secondary btn-sm" disabled>Locked</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Collect -->
<div class="modal fade" id="modalCollect" tabindex="-1" role="dialog" aria-labelledby="modalCollectLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCollectLabel">Konfirmasi Pengambilan</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" method="post" id="formCollect">
                <div class="modal-body">
                    <p>Peserta: <strong id="participantName"></strong></p>
                    
                    <div class="form-group">
                        <label for="taker_name">Nama Pengambil</label>
                        <input type="text" class="form-control" name="taker_name" id="taker_name" required placeholder="Masukkan nama yang mengambil">
                    </div>
                    
                    <div class="form-group">
                        <label for="taker_phone">No. HP Pengambil</label>
                        <input type="text" class="form-control" name="taker_phone" id="taker_phone" required placeholder="Masukkan nomor HP">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan & Tandai Diambil</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $('.btn-collect').click(function() {
            var id = $(this).data('id');
            var name = $(this).data('name');
            
            $('#participantName').text(name);
            $('#formCollect').attr('action', '/admin/racekit/mark/' + id);
            
            $('#modalCollect').modal('show');
        });
    });
</script>
<?= $this->endSection() ?>

<?= $this->endSection() ?>
