<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>
<div class="content-header">
    <div class="container-fluid">
        <h1 class="m-0">Pengaturan Website</h1>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-12">
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="custom-tabs-one-general-tab" data-toggle="pill" href="#custom-tabs-one-general" role="tab" aria-controls="custom-tabs-one-general" aria-selected="true">Umum & Tampilan</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-bib-tab" data-toggle="pill" href="#custom-tabs-one-bib" role="tab" aria-controls="custom-tabs-one-bib" aria-selected="false">Event & BIB</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-email-tab" data-toggle="pill" href="#custom-tabs-one-email" role="tab" aria-controls="custom-tabs-one-email" aria-selected="false">Email (SMTP)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <form action="/admin/settings/save" method="post" enctype="multipart/form-data">
                            <div class="tab-content" id="custom-tabs-one-tabContent">
                                
                                <!-- General & Appearance Tab -->
                                <div class="tab-pane fade show active" id="custom-tabs-one-general" role="tabpanel" aria-labelledby="custom-tabs-one-general-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Informasi Umum</h4>
                                            <div class="form-group">
                                                <label>Nama Website (Title)</label>
                                                <input type="text" class="form-control" name="site_title" value="<?= esc($settings['general']['site_title'] ?? '') ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Nama Aplikasi (Disamping Logo)</label>
                                                <input type="text" class="form-control" name="app_name" value="<?= esc($settings['general']['app_name'] ?? '') ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>Email Kontak</label>
                                                <input type="email" class="form-control" name="contact_email" value="<?= esc($settings['contact']['contact_email'] ?? '') ?>">
                                            </div>
                                            <div class="form-group">
                                                <label>No. Telepon Kontak</label>
                                                <input type="text" class="form-control" name="contact_phone" value="<?= esc($settings['contact']['contact_phone'] ?? '') ?>">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <h4>Tampilan (Gambar)</h4>
                                            <div class="form-group">
                                                <label>Logo Website</label><br>
                                                <?php if (!empty($settings['general']['site_logo'])): ?>
                                                    <img src="/<?= $settings['general']['site_logo'] ?>" height="50" class="mb-2"><br>
                                                <?php endif; ?>
                                                <input type="file" class="form-control-file" name="site_logo">
                                            </div>
                                            <div class="form-group">
                                                <label>Favicon</label><br>
                                                <?php if (!empty($settings['general']['site_favicon'])): ?>
                                                    <img src="/<?= $settings['general']['site_favicon'] ?>" height="32" class="mb-2"><br>
                                                <?php endif; ?>
                                                <input type="file" class="form-control-file" name="site_favicon">
                                            </div>
                                            <div class="form-group">
                                                <label>Banner Halaman Depan</label><br>
                                                <?php if (!empty($settings['general']['home_banner'])): ?>
                                                    <img src="/<?= $settings['general']['home_banner'] ?>" height="100" class="mb-2"><br>
                                                <?php endif; ?>
                                                <input type="file" class="form-control-file" name="home_banner">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Tab -->
                                <div class="tab-pane fade" id="custom-tabs-one-email" role="tabpanel" aria-labelledby="custom-tabs-one-email-tab">
                                    <div class="alert alert-info">
                                        Pengaturan ini digunakan untuk mengirim email notifikasi (Invoice, Lupa Password, dll).
                                    </div>
                                    <div class="form-group">
                                        <label>SMTP Host</label>
                                        <input type="text" class="form-control" name="smtp_host" value="<?= esc($settings['email']['smtp_host'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>SMTP User (Email)</label>
                                        <input type="text" class="form-control" name="smtp_user" value="<?= esc($settings['email']['smtp_user'] ?? '') ?>">
                                    </div>
                                    <div class="form-group">
                                        <label>SMTP Password</label>
                                        <input type="password" class="form-control" name="smtp_pass" value="<?= esc($settings['email']['smtp_pass'] ?? '') ?>">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>SMTP Port</label>
                                                <input type="number" class="form-control" name="smtp_port" id="smtp_port" value="<?= esc($settings['email']['smtp_port'] ?? '465') ?>" placeholder="465">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label>SMTP Crypto (Protocol)</label>
                                                <input type="text" class="form-control" name="smtp_crypto" id="smtp_crypto" value="<?= esc($settings['email']['smtp_crypto'] ?? 'ssl') ?>" placeholder="ssl">
                                                <small class="text-muted">Otomatis terdeteksi berdasarkan port (465=ssl, 587=tls).</small>
                                            </div>
                                        </div>
                                    </div>

                                    <script>
                                        document.getElementById('smtp_port').addEventListener('input', function() {
                                            var port = this.value;
                                            var cryptoInput = document.getElementById('smtp_crypto');
                                            
                                            if (port == 465) {
                                                cryptoInput.value = 'ssl';
                                            } else if (port == 587) {
                                                cryptoInput.value = 'tls';
                                            } else if (port == 25) {
                                                cryptoInput.value = '';
                                            }
                                        });
                                    </script>

                                    <a href="/admin/settings/test-email" class="btn btn-warning mt-2">Kirim Email Test</a>
                                </div>

                                <!-- Event & BIB Tab -->
                                <div class="tab-pane fade" id="custom-tabs-one-bib" role="tabpanel" aria-labelledby="custom-tabs-one-bib-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h4>Konfigurasi BIB</h4>
                                            <div class="form-group">
                                                <label>Panjang Karakter BIB</label>
                                                <input type="number" class="form-control" name="bib_config_length" value="<?= esc($settings['event']['bib_config_length'] ?? '5') ?>" min="3" max="10">
                                                <small class="text-muted">Jumlah karakter/digit untuk nomor BIB.</small>
                                            </div>
                                            <div class="form-group">
                                                <label>Izinkan Peserta Custom BIB?</label>
                                                <select class="form-control" name="bib_config_custom_allowed">
                                                    <option value="1" <?= ($settings['event']['bib_config_custom_allowed'] ?? '0') == '1' ? 'selected' : '' ?>>Ya, Izinkan</option>
                                                    <option value="0" <?= ($settings['event']['bib_config_custom_allowed'] ?? '0') == '0' ? 'selected' : '' ?>>Tidak</option>
                                                </select>
                                                <small class="text-muted">Jika diizinkan, peserta dapat memilih nomor BIB sendiri saat checkout.</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <hr>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
