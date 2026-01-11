<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="row mb-2">
    <div class="col-sm-6">
        <h1 class="m-0">Dashboard</h1>
    </div>
</div>

<div class="row">
    <!-- Event Card -->
    <div class="col-lg-6 col-12">
        <div class="small-box bg-info">
            <div class="inner">
                <h3><?= $total_events ?></h3>
                <p>Total Events</p>
                <div style="font-size: 1rem; margin-top: 5px;">
                     <i class="fas fa-tags"></i> <?= $total_categories ?> Kategori Tiket
                </div>
            </div>
            <div class="icon">
                <i class="fas fa-calendar"></i>
            </div>
            <a href="/admin/events" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Revenue Card -->
    <div class="col-lg-6 col-12">
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>Rp <?= number_format($total_revenue / 1000000, 1) ?> Juta</h3>
                <p>Total Pendapatan</p>
                <div style="font-size: 1rem; margin-top: 5px;">
                    <i class="fas fa-money-bill"></i> Real: Rp <?= number_format($total_revenue, 0, ',', '.') ?>
                </div>
            </div>
            <div class="icon">
                <i class="fas fa-money-bill-wave"></i>
            </div>
            <a href="/admin/reports/orders" class="small-box-footer">Laporan Pesanan <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Order Card -->
    <div class="col-lg-6 col-12">
        <div class="small-box bg-success">
            <div class="inner">
                <h3><?= $total_orders ?></h3>
                <p>Total Pesanan</p>
                <div style="font-size: 0.9em; margin-top: 5px;">
                    <span class="mr-2" title="Paid"><i class="fas fa-check-circle"></i> <?= $orders_paid ?> Lunas</span>
                    <span class="mr-2" title="Pending"><i class="fas fa-clock"></i> <?= $orders_pending ?> Pending</span>
                    <span title="Failed/Expired"><i class="fas fa-times-circle"></i> <?= $orders_failed ?> Gagal</span>
                </div>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="/admin/orders" class="small-box-footer">Selengkapnya <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Participant Card -->
    <div class="col-lg-6 col-12">
         <div class="small-box bg-warning">
             <div class="inner">
                 <h3><?= $total_participants ?></h3>
                 <p>Total Peserta</p>
                 <div style="font-size: 0.9em; margin-top: 5px;">
                     <span class="mr-2" title="Confirmed"><i class="fas fa-user-check"></i> <?= $participants_paid ?> Terkonfirmasi</span>
                     <span title="Unpaid"><i class="fas fa-user-clock"></i> <?= $participants_unpaid ?> Belum Lunas</span>
                 </div>
             </div>
             <div class="icon">
                 <i class="fas fa-users"></i>
             </div>
             <a href="/admin/reports/participants" class="small-box-footer">Laporan Peserta <i class="fas fa-arrow-circle-right"></i></a>
         </div>
     </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header border-transparent">
                <h3 class="card-title">Statistik Penjualan Tiket</h3>
            </div>
            <div class="card-body">
                <div class="chart">
                    <canvas id="ticketChart" style="min-height: 250px; height: 350px; max-height: 350px; max-width: 100%;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ChartJS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var ctx = document.getElementById('ticketChart').getContext('2d');
        
        var ticketData = <?= json_encode($ticket_stats) ?>;
        var labels = ticketData.map(item => item.name);
        var soldData = ticketData.map(item => item.sold);
        var pendingData = ticketData.map(item => item.pending);
        var remainingData = ticketData.map(item => item.remaining);

        var chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Terjual (Lunas)',
                        backgroundColor: '#28a745',
                        data: soldData
                    },
                    {
                        label: 'Booking (Pending)',
                        backgroundColor: '#ffc107',
                        data: pendingData
                    },
                    {
                        label: 'Sisa Kuota',
                        backgroundColor: '#dc3545',
                        data: remainingData
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true
                    }
                },
                plugins: {
                    tooltip: {
                        mode: 'index',
                        intersect: false
                    }
                }
            }
        });
    });
</script>

<div class="card">
    <div class="card-header border-transparent">
        <h3 class="card-title">Pesanan Terbaru</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                <i class="fas fa-minus"></i>
            </button>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table m-0">
                <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kode Order</th>
                    <th>Nama</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td><?= !empty($order['created_at']) && $order['created_at'] != '0000-00-00 00:00:00' ? date('d/m/Y H:i', strtotime($order['created_at'])) : '-' ?></td>
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
                        <span class="badge <?= $badgeClass ?>"><?= ucfirst(esc($order['payment_status'])) ?></span>
                    </td>
                    <td>
                        <a href="/admin/orders/show/<?= $order['id'] ?>" class="btn btn-sm btn-info">Lihat</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($recent_orders)): ?>
                    <tr><td colspan="6" class="text-center">Belum ada pesanan.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
