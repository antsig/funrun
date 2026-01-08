<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $total_events ?></h3>
                <p>Total Events</p>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
            <a href="/admin/events" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $total_orders ?></h3>
                <p>Total Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="/admin/orders" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
    <div class="col-lg-4 col-6">
        <!-- small box -->
        <div class="small-box bg-warning">
            <div class="inner">
                <h3><?= $total_participants ?></h3>
                <p>Participants</p>
            </div>
            <div class="icon">
                <i class="fas fa-users"></i>
            </div>
            <a href="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
    <!-- ./col -->
</div>

<div class="card">
    <div class="card-header border-transparent">
        <h3 class="card-title">Recent Orders</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table m-0">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Order Code</th>
                    <th>Name</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td><?= date('d M Y H:i', strtotime($order['created_at'])) ?></td>
                    <td><a href="/admin/orders/show/<?= $order['id'] ?>"><?= esc($order['order_code']) ?></a></td>
                    <td><?= esc($order['buyer_name']) ?></td>
                    <td>Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></td>
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
                        <a href="/admin/orders/show/<?= $order['id'] ?>" class="btn btn-sm btn-info">View</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_orders)): ?>
                    <tr><td colspan="6" class="text-center">No orders found</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <!-- /.table-responsive -->
    </div>
    <!-- /.card-body -->
</div>

<?= $this->endSection() ?>
