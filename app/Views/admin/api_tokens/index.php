<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<?php if (session()->getFlashdata('new_token')): ?>
<div class="alert alert-success alert-dismissible">
    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <h5><i class="icon fas fa-check"></i> Token Generated!</h5>
    <p>Please copy this token now. You will not be able to see it again.</p>
    <div class="input-group">
        <input type="text" class="form-control" value="<?= session()->getFlashdata('new_token') ?>" readonly id="newToken">
        <span class="input-group-append">
            <button type="button" class="btn btn-default btn-flat" onclick="copyToken()">Copy</button>
        </span>
    </div>
</div>
<script>
function copyToken() {
  var copyText = document.getElementById("newToken");
  copyText.select();
  document.execCommand("copy");
  alert("Copied to clipboard");
}
</script>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage API Tokens</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#modal-create">
                <i class="fas fa-plus"></i> Generate New Token
            </button>
        </div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Scopes</th>
                    <th>Created At</th>
                    <th>Last Used</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tokens as $t): ?>
                    <tr>
                        <td><?= $t['id'] ?></td>
                        <td><?= esc($t['name']) ?></td>
                        <td><?= esc($t['scopes'] ?? 'all') ?></td>
                        <td><?= $t['created_at'] ?></td>
                        <td><?= $t['last_used_at'] ?? '-' ?></td>
                        <td>
                            <?php if ($t['revoked_at']): ?>
                                <span class="badge badge-danger">Revoked</span>
                            <?php else: ?>
                                <span class="badge badge-success">Active</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!$t['revoked_at']): ?>
                                <a href="/admin/api-tokens/revoke/<?= $t['id'] ?>" class="btn btn-xs btn-danger" onclick="return confirm('Revoke this token?')">Revoke</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Create -->
<div class="modal fade" id="modal-create">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="/admin/api-tokens/create" method="post">
                <div class="modal-header">
                    <h4 class="modal-title">Generate API Token</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Token Name / Description</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Mobile App" required>
                    </div>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Generate</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
