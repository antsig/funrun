<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Manajemen Admin</h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="/admin/users/create" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah Admin</a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= esc($user['name']) ?></td>
                            <td><?= esc($user['email']) ?></td>
                            <td>
                                <span class="badge badge-<?= $user['role'] == 'administrator' ? 'success' : 'secondary' ?>">
                                    <?= ucfirst($user['role'] ?? 'admin') ?>
                                </span>
                            </td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
                                <a href="/admin/users/edit/<?= $user['id'] ?>" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a>
                                <?php if ($user['id'] != session()->get('admin_id')): ?>
                                <a href="/admin/users/delete/<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus user ini?')"><i class="fas fa-trash"></i></a>
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
<?= $this->endSection() ?>
