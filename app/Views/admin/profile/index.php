<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Profil Saya</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <!-- Display Errors/Success -->
        <?php if (session()->has('errors')): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
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

        <div class="row">
            <!-- Left Column: Profile Card -->
            <div class="col-md-3">
                <div class="card card-primary card-outline">
                    <div class="card-body box-profile">
                        <div class="text-center">
                            <img class="profile-user-img img-fluid img-circle"
                                 src="https://ui-avatars.com/api/?name=<?= urlencode($user['name']) ?>&background=random"
                                 alt="User profile picture">
                        </div>

                        <h3 class="profile-username text-center"><?= esc($user['name']) ?></h3>
                        <p class="text-muted text-center"><?= ucfirst($user['role']) ?></p>

                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <b>Email</b> <a class="float-right"><?= esc($user['email']) ?></a>
                            </li>
                            <li class="list-group-item">
                                <b>Terdaftar</b> <a class="float-right"><?= date('d M Y', strtotime($user['created_at'] ?? 'now')) ?></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right Column: Settings Form -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header p-2">
                        <ul class="nav nav-pills">
                            <li class="nav-item"><a class="nav-link active" href="#settings" data-toggle="tab">Pengaturan Akun</a></li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content">
                            <div class="active tab-pane" id="settings">
                                <form class="form-horizontal" action="/admin/profile/update" method="post">
                                    <div class="form-group row">
                                        <label for="inputName" class="col-sm-3 col-form-label">Nama Lengkap</label>
                                        <div class="col-sm-9">
                                            <input type="text" class="form-control" id="inputName" name="name" value="<?= old('name', $user['name'] ?? '') ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <label for="inputEmail" class="col-sm-3 col-form-label">Email</label>
                                        <div class="col-sm-9">
                                            <input type="email" class="form-control" id="inputEmail" value="<?= esc($user['email']) ?>" disabled>
                                            <small class="text-muted">Email tidak dapat diubah.</small>
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputPassword" class="col-sm-3 col-form-label">Password Baru</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="inputPassword" name="password" placeholder="Kosongkan jika tidak ingin mengubah">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <label for="inputConfPassword" class="col-sm-3 col-form-label">Konfirmasi Password</label>
                                        <div class="col-sm-9">
                                            <input type="password" class="form-control" id="inputConfPassword" name="conf_password" placeholder="Ulangi password baru">
                                        </div>
                                    </div>
                                    
                                    <div class="form-group row">
                                        <div class="offset-sm-3 col-sm-9">
                                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
