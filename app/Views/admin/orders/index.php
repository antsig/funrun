<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Orders Management</h1>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="get" class="form-inline">
            <div class="form-group mr-2">
                <select name="category_id" class="form-control">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            <?= esc($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group mr-2">
                <select name="status" class="form-control">
                    <option value="">All Statuses</option>
                    <option value="pending" <?= ($filters['status'] == 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="paid" <?= ($filters['status'] == 'paid') ? 'selected' : '' ?>>Paid</option>
                    <option value="expired" <?= ($filters['status'] == 'expired') ? 'selected' : '' ?>>Expired</option>
                    <option value="failed" <?= ($filters['status'] == 'failed') ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="/admin/orders" class="btn btn-default ml-2">Reset</a>
        </form>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Order Code</th>
                        <th>Buyer</th>
                        <th>Amount</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                        <td><?= esc($order['order_code']) ?></td>
                        <td>
                            <div><?= esc($order['buyer_name']) ?></div>
                            <small class="text-muted"><?= esc($order['buyer_email']) ?></small>
                        </td>
                        <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
                        <td><?= esc($order['payment_gateway']) ?? '-' ?></td>
                        <td>
                             <?php
    $badgeClass = 'badge-secondary';
    if ($order['payment_status'] == 'paid')
        $badgeClass = 'badge-success';
    elseif ($order['payment_status'] == 'pending')
        $badgeClass = 'badge-warning';
    elseif ($order['payment_status'] == 'expired')
        $badgeClass = 'badge-danger';
    ?>
                            <span class="badge <?= $badgeClass ?>"><?= esc($order['payment_status']) ?></span>
                        </td>
                        <td>
                            <a href="/admin/orders/show/<?= $order['id'] ?>" class="btn btn-sm btn-primary">View</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="7" class="text-center">No orders found</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
