<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - FunRun</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap');
        body { font-family: 'Outfit', sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .payment-container { background: white; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; width: 100%; }
        h1 { color: #1a1a1a; text-align: center; margin-bottom: 30px; }
        .order-details { background: #fffdf0; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #FFD700; }
        .order-details p { margin: 8px 0; }
        .amount { font-size: 1.5em; font-weight: bold; color: #27ae60; }
        .status { padding: 4px 8px; border-radius: 4px; font-weight: bold; text-transform: uppercase; font-size: 0.8em; }
        .status.pending { background: #ffeeba; color: #856404; }
        .status.paid { background: #d4edda; color: #155724; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; }
        input[type="text"] { width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box; }
        button { width: 100%; padding: 12px; background: #FFD700; color: #1a1a1a; border: none; border-radius: 6px; font-size: 16px; font-weight: bold; cursor: pointer; transition: background 0.3s; }
        button:hover { background: #e6c200; }
        .alert { padding: 10px; border-radius: 6px; margin-bottom: 20px; text-align: center; }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .back-link { display: block; text-align: center; margin-top: 20px; color: #7f8c8d; text-decoration: none; }
        .back-link:hover { color: #333; }
        /* SweetAlert override if needed */
        .swal2-styled.swal2-confirm { background-color: #FFD700 !important; color: #333 !important; }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="payment-container">
    <h1>Pembayaran</h1>

    <div class="order-details">
        <p><strong>Kode Order:</strong> <?= esc($order['order_code']) ?></p>
        <p><strong>Nama:</strong> <?= esc($order['buyer_name']) ?></p>
        <p><strong>Total:</strong> <span class="amount">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span></p>
        <p><strong>Status:</strong> <span class="status <?= esc($order['payment_status']) ?>"><?= esc($order['payment_status']) ?></span></p>
    </div>

    <?php if ($order['payment_status'] == 'pending'): ?>
        
        <!-- Tabs Nav -->
        <div style="display: flex; border-bottom: 2px solid #eee; margin-bottom: 20px;">
            <div class="tab-nav active" onclick="switchTab('instant')" style="padding: 10px 20px; cursor: pointer; border-bottom: 2px solid #FFD700; font-weight: bold;">Instant Payment</div>
            <div class="tab-nav" onclick="switchTab('manual')" style="padding: 10px 20px; cursor: pointer; color: #7f8c8d;">Manual Transfer</div>
        </div>

        <!-- Tab Content: Instant -->
        <div id="tab-instant" class="tab-content">
            <p>Bayar otomatis dengan QRIS, Virtual Account, atau E-Wallet via Midtrans.</p>
            <?php if (!empty($order['snap_token'])): ?>
                <button id="pay-button" style="background: #0063d1; color: white;">Bayar Sekarang (Midtrans)</button>
            <?php else: ?>
                <div class="alert alert-danger">Token pembayaran tidak valid.</div>
            <?php endif; ?>
        </div>

        <!-- Tab Content: Manual -->
        <div id="tab-manual" class="tab-content" style="display: none;">
            
            <?php if (!empty($order['proof_file'])): ?>
                <div style="background: #e3f2fd; border: 2px solid #2196f3; padding: 20px; border-radius: 8px; margin-bottom: 25px; text-align: center;">
                    <i class="fas fa-check-circle" style="color: #2196f3; font-size: 2em; margin-bottom: 10px;"></i><br>
                    <strong style="color: #0d47a1; font-size: 1.2em;">Bukti Pembayaran Telah Diupload</strong><br>
                    <span style="font-size: 0.9em; color: #555;">Filename: <?= esc($order['proof_file']) ?></span><br>
                    <small style="color: #666; display: block; margin-top: 5px;">Status: Menunggu verifikasi admin.</small>
                    
                    <div style="margin-top: 15px;">
                        <?php if (strtolower(pathinfo($order['proof_file'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                             <a href="/uploads/payments/<?= esc($order['proof_file']) ?>" target="_blank" style="display:inline-block; padding: 10px 20px; background: #fff; border: 1px solid #2196f3; border-radius: 4px; text-decoration: none; color: #0d47a1; font-weight: bold;">
                                <i class="fas fa-file-pdf" style="color: #d32f2f;"></i> Lihat Bukti Saya
                             </a>
                        <?php else: ?>
                            <img src="/uploads/payments/<?= esc($order['proof_file']) ?>" alt="Bukti" style="max-height: 150px; border-radius: 4px; border: 1px solid #ddd; padding: 4px; background: white;">
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <p>Silakan transfer total pembayaran ke:</p>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; text-align: center; border: 1px solid #eee;">
                <div style="font-weight: bold; margin-bottom: 5px;">Bank BCA</div>
                <div style="font-size: 1.4em; letter-spacing: 2px; color: #1a1a1a; font-weight: bold;">123 456 7890</div>
                <div style="font-size: 0.9em; color: #7f8c8d;">a.n. FunRun Organizer</div>
            </div>

            <form action="/payment/manual-confirm/<?= esc($order['order_code']) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="ref_number">Nama Pengirim / Catatan</label>
                    <input type="text" id="ref_number" name="ref_number" placeholder="Contoh: Budi Santoso" value="<?= isset($order['payment_ref']) ? esc($order['payment_ref']) : '' ?>" required>
                </div>
                <div class="form-group">
                    <label for="proof_file"><?= !empty($order['proof_file']) ? 'Upload Ulang Bukti' : 'Upload Bukti Transfer' ?> (Max 5MB, PDF/Image)</label>
                    <input type="file" id="proof_file" name="proof_file" accept="image/*,application/pdf" required style="padding: 10px; border: 1px solid #ddd; width: 100%; border-radius: 6px;">
                </div>
                <button type="submit">Kirim Bukti Pembayaran</button>
            </form>
        </div>

        <script>
            function switchTab(tab) {
                // Hide all contents
                document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
                // Show selected
                document.getElementById('tab-' + tab).style.display = 'block';
                
                // Reset nav styles
                document.querySelectorAll('.tab-nav').forEach(el => {
                    el.style.borderBottom = 'none';
                    el.style.color = '#7f8c8d';
                });
                // Active nav style
                const activeNav = document.querySelector(`.tab-nav[onclick="switchTab('${tab}')"]`);
                if (activeNav) {
                    activeNav.style.borderBottom = '2px solid #FFD700';
                    activeNav.style.color = '#1a1a1a';
                }
                
                // Save to localStorage
                localStorage.setItem('activePaymentTab', tab);
            }

            // Restore tab on load
            document.addEventListener('DOMContentLoaded', function() {
                const savedTab = localStorage.getItem('activePaymentTab') || 'instant';
                switchTab(savedTab);
            });
        </script>
    <?php elseif ($order['payment_status'] == 'paid' || $order['payment_status'] == 'settlement'): ?>
        <div style="text-align: center; color: #27ae60; margin: 30px 0;">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            <p><strong>Pembayaran Berhasil</strong></p>
            <p>Terima kasih atas partisipasi Anda!</p>

            <a href="/payment/print/<?= esc($order['order_code']) ?>" target="_blank" class="btn btn-primary" style="background: #333; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; display: inline-block;">
                <i class="fas fa-print"></i> Cetak Bukti Pengambilan Jersey
            </a>
        </div>
    <?php else: ?>
        <!-- Handle Expired/Failed if not redirected -->
        <div class="alert alert-danger" style="margin-top: 20px;">
            <h4>Status: <?= esc(strtoupper($order['payment_status'])) ?></h4>
            <p>Maaf, pesanan Anda tidak dapat diproses (Kadaluarsa atau Dibatalkan).</p> 
            <a href="/checkout" class="btn btn-primary" style="margin-top: 10px;">Buat Pesanan Baru</a>
        </div>
    <?php endif; ?>

    <a href="/" class="back-link">Kembali ke Beranda</a>
</div>

<!-- Snap.js -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="<?= $clientKey ?? '' ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil',
                text: <?= json_encode(session()->getFlashdata('success')) ?>,
                confirmButtonColor: '#FFD700',
                color: '#333'
            });
        <?php endif; ?>

        <?php if (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: <?= json_encode(session()->getFlashdata('error')) ?>,
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>

        // Midtrans Pay Button
        const payButton = document.getElementById('pay-button');
        const snapToken = "<?= $order['snap_token'] ?? '' ?>";

        if (payButton && snapToken) {
            payButton.addEventListener('click', function () {
                snap.pay(snapToken, {
                    onSuccess: function(result) {
                        /* You may add your own implementation here */
                        // alert("payment success!"); 
                        Swal.fire('Pembayaran Berhasil!', 'Terima kasih', 'success').then(() => {
                           window.location.reload();
                        });
                        console.log(result);
                    },
                    onPending: function(result) {
                        /* You may add your own implementation here */
                        Swal.fire('Menunggu Pembayaran!', 'Silakan selesaikan pembayaran.', 'info');
                        console.log(result);
                    },
                    onError: function(result) {
                        /* You may add your own implementation here */
                        Swal.fire('Pembayaran Gagal!', 'Terjadi kesalahan.', 'error');
                        console.log(result);
                    },
                    onClose: function() {
                        /* You may add your own implementation here */
                        // alert('you closed the popup without finishing the payment');
                    }
                });
            });
        }
    });
</script>

</body>
</html>
