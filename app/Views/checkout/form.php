<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<div class="container mt-20">
    <h1 class="text-center mb-20">Pembayaran</h1>

    <form action="/checkout/process" method="post">
        <div class="grid">
            <!-- Left Column: Participants -->
            <div style="flex: 2;">
                <div class="card">
                    <h3>Data Peserta</h3>
                    <p style="color: #666; font-size: 0.9em; margin-bottom: 20px;">Silakan lengkapi data untuk setiap pelari.</p>

                    <?php foreach ($cart as $index => $item): ?>
                    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #FFD700;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                            <h4 style="margin: 0;">Peserta <?= $index + 1 ?> - <?= esc($item['category_name']) ?></h4>
                            <a href="/registration/remove/<?= $index ?>?redirect=checkout" onclick="return confirm('Hapus peserta ini?')" style="color: #dc3545; text-decoration: none; font-size: 0.9em;">
                                <i class="fas fa-trash"></i> Hapus
                            </a>
                        </div>
                        <input type="hidden" name="participants[<?= $index ?>][category_id]" value="<?= $item['category_id'] ?>">
                        
                        <div class="grid" style="grid-template-columns: 2fr 1fr; gap: 15px;">
                            <!-- Row 1 -->
                            <!-- Row 1 -->
                            <div class="form-group">
                                <label style="font-size: 0.9em; color: #555;">Nama Peserta</label>
                                <input type="text" name="participants[<?= $index ?>][name]" value="<?= old('participants.' . $index . '.name', $item['name'] ?? '') ?>" required style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                            </div>
                            <div class="form-group">
                                <label style="font-size: 0.9em; color: #555;">Ukuran Jersey</label>
                                <select name="participants[<?= $index ?>][jersey_size]" required style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                                    <option value="">Pilih</option>
                                    <?php $pSize = old('participants.' . $index . '.jersey_size', $item['jersey_size'] ?? ''); ?>
                                    <option value="XS" <?= $pSize == 'XS' ? 'selected' : '' ?>>XS</option>
                                    <option value="S" <?= $pSize == 'S' ? 'selected' : '' ?>>S</option>
                                    <option value="M" <?= $pSize == 'M' ? 'selected' : '' ?>>M</option>
                                    <option value="L" <?= $pSize == 'L' ? 'selected' : '' ?>>L</option>
                                    <option value="XL" <?= $pSize == 'XL' ? 'selected' : '' ?>>XL</option>
                                    <option value="XXL" <?= $pSize == 'XXL' ? 'selected' : '' ?>>XXL</option>
                                </select>
                            </div>
                            
                            <?php if (isset($bibAllowed) && $bibAllowed): ?>
                            <div class="form-group" style="grid-column: span 2;">
                                <label style="font-size: 0.9em; color: #555;">Request Nomor BIB (Custom)</label>
                                <input type="number" name="participants[<?= $index ?>][bib_number]" 
                                       value="<?= old('participants.' . $index . '.bib_number') ?>" 
                                       class="form-control" 
                                       placeholder="Isi <?= $bibLength ?> digit angka (Opsional)"
                                       minlength="<?= $bibLength ?>" maxlength="<?= $bibLength ?>"
                                       style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                                <small class="text-muted" style="font-size: 0.8em;">Biarkan kosong jika ingin sistem mengacak nomor.</small>
                            </div>
                            <?php endif; ?>

                            <!-- Row 2 -->
                            <div class="form-group">
                                <label style="font-size: 0.9em; color: #555;">Tanggal Lahir</label>
                                <input type="hidden" name="participants[<?= $index ?>][dob]" id="dob_<?= $index ?>" value="<?= old('participants.' . $index . '.dob', $item['dob'] ?? '') ?>" required>
                                
                                <?php
                                $savedDob = $item['dob'] ?? '';
                                // Use individual old inputs if available, otherwise fall back to saved DOB components
                                $dVal = old('participants.' . $index . '.dob_day', $savedDob ? date('d', strtotime($savedDob)) : '');
                                $mVal = old('participants.' . $index . '.dob_month', $savedDob ? date('m', strtotime($savedDob)) : '');
                                $yVal = old('participants.' . $index . '.dob_year', $savedDob ? date('Y', strtotime($savedDob)) : '');
                                ?>
                                
                                <div style="display: flex; gap: 5px;">
                                    <select name="participants[<?= $index ?>][dob_day]" class="dob-day" data-target="dob_<?= $index ?>" style="flex: 1; padding: 10px 5px; border: 1px solid #ddd; border-radius: 4px;" required>
                                        <option value="">Tgl</option>
                                        <?php for ($d = 1; $d <= 31; $d++): ?>
                                            <option value="<?= str_pad($d, 2, '0', STR_PAD_LEFT) ?>" <?= $dVal == str_pad($d, 2, '0', STR_PAD_LEFT) ? 'selected' : '' ?>><?= $d ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <select name="participants[<?= $index ?>][dob_month]" class="dob-month" data-target="dob_<?= $index ?>" style="flex: 1; padding: 10px 5px; border: 1px solid #ddd; border-radius: 4px;" required>
                                        <option value="">Bln</option>
                                        <?php
                                        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                                        foreach ($months as $k => $m):
                                            $mNum = str_pad($k + 1, 2, '0', STR_PAD_LEFT);
                                            ?>
                                            <option value="<?= $mNum ?>" <?= $mVal == $mNum ? 'selected' : '' ?>><?= $m ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <select name="participants[<?= $index ?>][dob_year]" class="dob-year" data-target="dob_<?= $index ?>" style="flex: 1; padding: 10px 5px; border: 1px solid #ddd; border-radius: 4px;" required>
                                        <option value="">Thn</option>
                                        <?php
                                        $currentYear = date('Y');
                                        for ($y = $currentYear; $y >= 1940; $y--):
                                            ?>
                                            <option value="<?= $y ?>" <?= $yVal == $y ? 'selected' : '' ?>><?= $y ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                             <div class="form-group">
                                <label style="font-size: 0.9em; color: #555;">Jenis Kelamin</label>
                                <select name="participants[<?= $index ?>][gender]" required style="padding: 10px; border: 1px solid #ddd; border-radius: 4px; width: 100%;">
                                    <option value="">Pilih...</option>
                                    <?php $pGender = old('participants.' . $index . '.gender', $item['gender'] ?? ''); ?>
                                    <option value="L" <?= $pGender == 'L' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="P" <?= $pGender == 'P' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Right Column: Buyer + Summary + Button -->
            <div style="flex: 1;">
                
                <!-- Data Pemesan (Moved Here) -->
                <div class="card">
                    <h3>Data Pemesan</h3>
                    <div class="form-group">
                        <label>Nama Lengkap</label>
                        <input type="text" name="buyer_name" value="<?= old('buyer_name', session()->get('buyer_name') ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="buyer_email" value="<?= old('buyer_email', session()->get('buyer_email') ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Nomor HP / WA</label>
                        <input type="text" name="buyer_phone" value="<?= old('buyer_phone', session()->get('buyer_phone') ?? '') ?>" required>
                    </div>
                </div>

                <!-- Summary (Moved inside loop to maintain flow if needed, but visually distinct) -->
                <div class="card">
                    <h3>Ringkasan</h3>
                    <ul style="list-style: none; padding: 0;">
                        <?php
                        $total = 0;
                        foreach ($cart as $item):
                            $total += $item['price'];
                            ?>
                        <li style="display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #eee;">
                            <span><?= esc($item['category_name']) ?></span>
                            <span>Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                        </li>
                        <?php endforeach; ?>
                        <li style="display: flex; justify-content: space-between; padding: 15px 0 0; font-weight: bold; font-size: 1.2em;">
                            <span>Total Pembayaran</span>
                            <span>Rp <?= number_format($total, 0, ',', '.') ?></span>
                        </li>
                    </ul>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 15px; font-size: 1.2em; margin-top: 10px;">Bayar Sekarang</button>
            </div>
        </div>
    </form>
</div>

<?= $this->endSection() ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: 'Perhatian',
            text: <?= json_encode(session()->getFlashdata('error')) ?>,
            confirmButtonColor: '#d33'
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
        Swal.fire({
            icon: 'info',
            title: 'Info',
            text: <?= json_encode(session()->getFlashdata('info')) ?>,
            confirmButtonColor: '#3085d6'
        });
    <?php endif; ?>

    function updateDob(targetId) {
        const row = document.querySelector(`select[data-target="${targetId}"]`).parentNode;
        const d = row.querySelector('.dob-day').value;
        const m = row.querySelector('.dob-month').value;
        const y = row.querySelector('.dob-year').value;
        
        const input = document.getElementById(targetId);
        if (d && m && y) {
            input.value = `${y}-${m}-${d}`;
        } else {
            input.value = '';
        }
    }

    document.querySelectorAll('.dob-day, .dob-month, .dob-year').forEach(el => {
        el.addEventListener('change', function() {
            updateDob(this.getAttribute('data-target'));
        });
    });
});
</script>
