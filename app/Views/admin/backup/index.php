<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Backup & Restore System</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        
        <div class="row">
            <!-- Database Section -->
            <div class="col-md-6">
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Database MySQL</h3>
                    </div>
                    <div class="card-body">
                        <h5>Backup Database</h5>
                        <p>Mengunduh seluruh struktur database dalam format <code>.sql</code>.</p>
                        <a href="/admin/backup/db-export" class="btn btn-primary mb-4">
                            <i class="fas fa-download"></i> Download SQL Backup
                        </a>

                        <hr>

                        <h5>Restore Database</h5>
                        <p class="text-danger"><strong>PERINGATAN:</strong> Tindakan ini akan menghapus dan menimpa seluruh data saat ini! Pastikan Anda memiliki backup terbaru.</p>
                        
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger">
                                <ul>
                                <?php foreach (session('errors') as $error): ?>
                                    <li><?= esc($error) ?></li>
                                <?php endforeach ?>
                                </ul>
                            </div>
                        <?php endif ?>

                        <form action="/admin/backup/db-restore" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="form-group">
                                <label>Upload File .sql</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="backup_file" id="backup_file" accept=".sql" required>
                                    <label class="custom-file-label" for="backup_file">Pilih file...</label>
                                </div>
                            </div>

                            <div class="form-group bg-light p-3 border rounded">
                                <label class="text-danger font-weight-bold">Konfirmasi Keamanan</label>
                                
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="confirm_restore" name="confirm_restore" value="1">
                                    <label class="custom-control-label text-danger" for="confirm_restore">
                                        Saya mengerti bahwa data saat ini akan ditimpa dan hilang permanen.
                                    </label>
                                </div>

                                <div class="form-group mb-0">
                                    <label>Ketik <code>RESTORE</code> untuk melanjutkan:</label>
                                    <input type="text" class="form-control" name="confirm_text" placeholder="RESTORE" autocomplete="off" required>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-upload"></i> Restore Database
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Application Section -->
            <div class="col-md-6">
                <div class="card card-success card-outline">
                    <div class="card-header">
                        <h3 class="card-title">Source Code Aplikasi</h3>
                    </div>
                    <div class="card-body">
                        <h5>Backup Source Code</h5>
                        <p>Mengunduh source code aplikasi dalam format <code>.zip</code>.</p>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> Folder <code>vendor</code>, <code>node_modules</code>, dan <code>.git</code> tidak disertakan untuk menghemat ukuran file.
                        </div>
                        <a href="/admin/backup/code-export" class="btn btn-success btn-block">
                            <i class="fas fa-file-archive"></i> Download Source Code (ZIP)
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<script>
    // Custom File Input Label
    document.querySelector('.custom-file-input').addEventListener('change', function(e){
        var fileName = document.getElementById("backup_file").files[0].name;
        var nextSibling = e.target.nextElementSibling
        nextSibling.innerText = fileName
    })
</script>
<?= $this->endSection() ?>
