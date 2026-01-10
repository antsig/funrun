<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Manajemen Social Media</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- Add New Social Media -->
            <div class="col-md-4">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Social Media</h3>
                    </div>
                    <form action="/admin/settings/social-media/store" method="post">
                        <div class="card-body">
                            <?php if (session()->getFlashdata('errors')): ?>
                                <div class="alert alert-danger">
                                    <ul>
                                    <?php foreach (session()->getFlashdata('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <div class="form-group">
                                <label>URL Profile</label>
                                <input type="url" class="form-control" name="url" placeholder="https://facebook.com/username" required>
                                <small class="text-muted">Platform dan Icon akan dideteksi otomatis.</small>
                            </div>
                            <div class="form-group">
                                <label>Nama Akun (Di Tampilkan)</label>
                                <input type="text" class="form-control" name="account_name" placeholder="Contoh: @funrun_gorontalo" required>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- List Social Media -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Daftar Social Media</h3>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Icon</th>
                                    <th>Platform</th>
                                    <th>Nama Akun</th>
                                    <th>URL</th>
                                    <th style="width: 40px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($social_media)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Belum ada data social media.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($social_media as $index => $sm): ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td>
                                            <i class="<?= esc($sm['icon']) ?> fa-2x"></i>
                                        </td>
                                        <td><?= esc($sm['platform']) ?></td>
                                        <td><?= esc($sm['account_name'] ?? '-') ?></td>
                                        <td><a href="<?= esc($sm['url']) ?>" target="_blank" class="btn btn-xs btn-default"><i class="fas fa-external-link-alt"></i> Buka</a></td>
                                        <td>
                                            <a href="/admin/settings/social-media/delete/<?= $sm['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Yakin ingin menghapus?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
    </div>
</div>
<?= $this->endSection() ?>
