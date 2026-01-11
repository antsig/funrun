<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="row mb-3">
    <div class="col-md-4">
        <div class="info-box bg-warning">
            <span class="info-box-icon"><i class="fas fa-clock"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Pending</span>
                <span class="info-box-number"><?= $stats['pending'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-success">
            <span class="info-box-icon"><i class="fas fa-check"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Sent</span>
                <span class="info-box-number"><?= $stats['sent'] ?></span>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="info-box bg-danger">
            <span class="info-box-icon"><i class="fas fa-times"></i></span>
            <div class="info-box-content">
                <span class="info-box-text">Failed</span>
                <span class="info-box-number"><?= $stats['failed'] ?></span>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Antrian Email</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>To</th>
                    <th>Subject</th>
                    <th>Status</th>
                    <th>Attempts</th>
                    <th>Created At</th>
                    <th>Last Error</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($emails as $email): ?>
                    <tr>
                        <td><?= $email['id'] ?></td>
                        <td><?= esc($email['to_email']) ?></td>
                        <td><?= esc($email['subject']) ?></td>
                        <td>
                            <?php if ($email['status'] == 'sent'): ?>
                                <span class="badge badge-success">Sent</span>
                            <?php elseif ($email['status'] == 'failed'): ?>
                                <span class="badge badge-danger">Failed</span>
                            <?php else: ?>
                                <span class="badge badge-warning"><?= ucfirst($email['status']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $email['attempts'] ?></td>
                        <td><?= $email['created_at'] ?></td>
                        <td title="<?= esc($email['error_message']) ?>">
                            <?= esc(substr($email['error_message'] ?? '', 0, 30)) ?>...
                        </td>
                        <td>
                            <?php if ($email['status'] == 'failed'): ?>
                                <a href="/admin/queue/retry/<?= $email['id'] ?>" class="btn btn-xs btn-primary">Retry</a>
                            <?php endif; ?>
                            <a href="/admin/queue/delete/<?= $email['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Hapus antrian ini?')">Hapus</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($emails)): ?>
                    <tr>
                        <td colspan="8" class="text-center">Antrian kosong.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <?= $pager->links() ?>
    </div>
</div>
<?= $this->endSection() ?>
