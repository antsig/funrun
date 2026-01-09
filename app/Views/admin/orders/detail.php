<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Order #<?= esc($order['order_code']) ?></h1>
        </div>
        <div class="col-sm-6 text-right">
             <a href="/admin/orders" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title">Order Details</h3>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tr><td width="200"><strong>Buyer Name</strong></td><td><?= esc($order['buyer_name']) ?></td></tr>
                    <tr><td><strong>Email</strong></td><td><?= esc($order['buyer_email']) ?></td></tr>
                    <tr><td><strong>Phone</strong></td><td><?= esc($order['buyer_phone']) ?></td></tr>
                    <tr><td><strong>Date</strong></td><td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td></tr>
                    <tr><td><strong>Total Amount</strong></td><td><span class="text-success font-weight-bold" style="font-size: 1.2em;">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></span></td></tr>
                    <tr><td><strong>Payment Method</strong></td><td><?= !empty($order['payment_method']) ? esc(ucfirst($order['payment_method'])) : '<span class="text-muted">-</span>' ?></td></tr>
                    <tr><td><strong>Status</strong></td><td><span class="badge badge-<?= $order['payment_status'] ?>"><?= esc($order['payment_status']) ?></span></td></tr>
                </table>

                <div class="mt-4 pt-3 border-top">
                    <h4>Update Status</h4>
                    <form action="/admin/orders/updateStatus/<?= $order['id'] ?>" method="post" class="form-inline">
                        <div class="form-group mr-2">
                            <select name="status" class="form-control">
                                <option value="pending" <?= $order['payment_status'] == 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="paid" <?= $order['payment_status'] == 'paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="expired" <?= $order['payment_status'] == 'expired' ? 'selected' : '' ?>>Expired</option>
                                <option value="failed" <?= $order['payment_status'] == 'failed' ? 'selected' : '' ?>>Failed</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Update</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Registered Participants</h3>
            </div>
            <div class="card-body p-0">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Bib #</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Jersey</th>
                            <th>Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($participants as $p): ?>
                        <tr>
                            <td><?= esc($p['bib_number'] ?? '-') ?></td>
                            <td><?= esc($p['name']) ?></td>
                            <td><?= esc($p['gender']) ?></td>
                            <td><?= esc($p['jersey_size']) ?> (<?= esc($p['jersey_status']) ?>)</td>
                            <td><?= esc($p['category_name']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <?php if (!empty($order['payment_method']) || !empty($order['proof_file'])): ?>
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Payment Info</h3>
            </div>
            <div class="card-body">
                <p><strong>Gateway:</strong> <?= esc($order['payment_method'] ?? '-') ?></p>
                <p><strong>Ref Code:</strong> <?= esc($order['payment_ref'] ?? '-') ?></p>
                <p><strong>Status:</strong> <?= esc($order['payment_status']) ?></p>
                
                <?php if (!empty($order['proof_file'])): ?>
                    <div class="mt-3">
                        <strong>Bukti Pembayaran:</strong><br>
                        <?php if (strtolower(pathinfo($order['proof_file'], PATHINFO_EXTENSION)) === 'pdf'): ?>
                             <div class="mt-2 text-center" style="background: #f4f4f4; padding: 20px; border-radius: 5px;">
                                <i class="fas fa-file-pdf fa-3x text-danger mb-2"></i><br>
                                <a href="/uploads/payments/<?= esc($order['proof_file']) ?>" target="_blank" class="font-weight-bold">View PDF</a>
                             </div>
                        <?php else: ?>
                            <a href="/uploads/payments/<?= esc($order['proof_file']) ?>" target="_blank">
                                <img src="/uploads/payments/<?= esc($order['proof_file']) ?>" alt="Bukti Transfer" class="img-fluid img-thumbnail" style="max-height: 200px;">
                            </a>
                        <?php endif; ?>
                        
                        <div class="mt-2">
                            <a href="/uploads/payments/<?= esc($order['proof_file']) ?>" download class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-download"></i> Download Original
                            </a>
                        </div>
                    </div>
                <?php endif; ?>

                <?php if ($order['payment_status'] == 'pending'): ?>
                    <div class="mt-3 border-top pt-3">
                        <a href="/admin/orders/approvePayment/<?= $order['id'] ?>" class="btn btn-success btn-block" onclick="return confirm('Approve this payment?')">Approve Payment</a>
                        <a href="/admin/orders/rejectPayment/<?= $order['id'] ?>" class="btn btn-danger btn-block" onclick="return confirm('Reject this payment?')">Reject Payment</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php else: ?>
        <div class="card card-secondary">
             <div class="card-header">
                <h3 class="card-title">Payment Info</h3>
            </div>
            <div class="card-body">
                <p class="text-muted font-italic">No payment record found</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>
