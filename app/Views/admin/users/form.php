<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0"><?= $title ?></h1>
            </div>
            <div class="col-sm-6 text-right">
                <a href="/admin/users" class="btn btn-secondary">Kembali</a>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card card-primary">
                    <form action="<?= isset($user) ? '/admin/users/update/' . $user['id'] : '/admin/users/store' ?>" method="post">
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

                            <div class="form-group">
                                <label>Nama Lengkap</label>
                                <input type="text" class="form-control" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="email" class="form-control" name="email" value="<?= old('email', $user['email'] ?? '') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <select name="role" class="form-control" required>
                                    <option value="admin" <?= (old('role', $user['role'] ?? '') == 'admin') ? 'selected' : '' ?>>Admin</option>
                                    <option value="administrator" <?= (old('role', $user['role'] ?? '') == 'administrator') ? 'selected' : '' ?>>Administrator</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password <?= isset($user) ? '(Kosongkan jika tidak diganti)' : '' ?></label>
                                <input type="password" class="form-control" name="password" <?= isset($user) ? '' : 'required' ?>>
                            </div>
                            <div class="form-group">
                                <label>Konfirmasi Password</label>
                                <input type="password" class="form-control" name="conf_password">
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
