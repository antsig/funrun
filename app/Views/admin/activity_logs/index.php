<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Activity Logs</h3>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Target ID</th>
                    <th>IP Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= $log['created_at'] ?></td>
                        <td><?= esc($log['user_name'] ?? 'System/Guest') ?></td>
                        <td><span class="badge badge-info"><?= esc($log['action']) ?></span></td>
                        <td><?= esc($log['target_id']) ?></td>
                        <td><?= esc($log['ip_address']) ?></td>
                        <td>
                            <?php
                            $details = $log['details'];
                            if (is_string($details) && (str_starts_with($details, '{') || str_starts_with($details, '['))) {
                                echo '<pre style="font-size: 0.8em; margin:0;">' . esc($details) . '</pre>';
                            } else {
                                echo esc($details);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada log aktivitas.</td>
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
