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
        <?php if ($payment): ?>
        <div class="card card-info">
            <div class="card-header">
                <h3 class="card-title">Payment Info</h3>
            </div>
            <div class="card-body">
                <p><strong>Gateway:</strong> <?= esc($payment['gateway']) ?></p>
                <p><strong>Ref Code:</strong> <?= esc($payment['gateway_ref']) ?></p>
                <p><strong>Status:</strong> <?= esc($payment['status']) ?></p>
                <p><strong>Date:</strong> <?= date('d M Y H:i', strtotime($payment['created_at'])) ?></p>
                
                <?php if ($payment['payload']): ?>
                <div class="mt-2 p-2 bg-light rounded" style="font-size: 0.9em; overflow-x: auto;">
                    <strong>Payload:</strong><br>
                    <code style="word-break: break-all;"><?= esc($payment['payload']) ?></code>
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
