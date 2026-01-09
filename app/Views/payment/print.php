<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bukti Pendaftaran - <?= esc($order['order_code']) ?></title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            border: 2px solid #333;
            padding: 40px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .header p {
            margin: 5px 0 0;
            font-size: 14px;
        }
        .meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .meta-group h3 {
            margin: 0 0 5px;
            font-size: 12px;
            text-transform: uppercase;
            color: #666;
        }
        .meta-group p {
            margin: 0;
            font-weight: bold;
            font-size: 18px;
        }
        .participants {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .participants th, .participants td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: left;
        }
        .participants th {
            background: #eee;
            text-transform: uppercase;
            font-size: 12px;
        }
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 12px;
            border-top: 1px dashed #ccc;
            padding-top: 20px;
        }
        .qrcode {
            text-align: center;
            margin: 20px 0;
        }
        @media print {
            body {
                padding: 0;
            }
            .container {
                border: none;
                max-width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
        .btn-print {
            display: inline-block;
            padding: 10px 20px;
            background: #333;
            color: #fff;
            text-align: center;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">CETAK HALAMAN INI</button>
        <a href="/" style="display: inline-block; margin-top: 10px; color: #333; text-decoration: none;">&larr; Kembali ke Home</a>
    </div>

    <div class="container">
        <div class="header">
            <h1><?= esc($eventName) ?></h1>
            <p>Bukti Pengambilan Jersey & Paket Lomba</p>
        </div>

        <div class="meta">
            <div class="meta-group">
                <h3>Kode Order</h3>
                <p><?= esc($order['order_code']) ?></p>
            </div>
            <div class="meta-group" style="text-align: right;">
                <h3>Status Pembayaran</h3>
                <p style="color: #27ae60;">PAID (LUNAS)</p>
            </div>
        </div>

        <div class="meta">
            <div class="meta-group">
                <h3>Nama Pemesan</h3>
                <p><?= esc($order['buyer_name']) ?></p>
            </div>
            <div class="meta-group" style="text-align: right;">
                <h3>Tanggal Order</h3>
                <p><?= date('d F Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <table class="participants">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Nama Peserta</th>
                    <th>Kategori</th>
                    <th>Ukuran Jersey</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1;
                foreach ($participants as $p): ?>
                <tr>
                    <td width="5%" style="text-align: center;"><?= $i++ ?></td>
                    <td><?= esc($p['name']) ?></td>
                    <td><?= esc($p['category_name']) ?></td>
                    <td><strong><?= esc($p['jersey_size']) ?></strong></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="footer">
            <p>Harap membawa bukti ini saat pengambilan Race Pack.</p>
            <p><strong>Fun Run Organizer</strong> - support@funrun.com</p>
        </div>
    </div>

</body>
</html>
