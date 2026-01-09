<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Profil Saya</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <form action="/admin/profile/update" method="post">
                        <div class="card-body">
                            
                            <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= $error ?></li>
                                <?php endforeach; ?>
                                </ul>
                            </div>
                            <?php endif; ?>

                            <?php if (session()->has('success')): ?>
                            <div class="alert alert-success">
                                <?= session('success') ?>
                            </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email (Locked)</label>
                                <input type="email" class="form-control" value="<?= esc($user['email']) ?>" disabled>
                                <small class="text-muted">Email hanya dapat diubah oleh Administrator.</small>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <input type="text" class="form-control" value="<?= ucfirst($user['role']) ?>" disabled>
                            </div>
                            <hr>
                            <div class="form-group">
                                <label>Password Baru <small class="text-muted">(Kosongkan jika tidak ingin mengubah)</small></label>
                                <input type="password" class="form-control" name="password">
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password Baru</label>
                                <input type="password" class="form-control" name="conf_password">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
