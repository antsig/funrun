<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Events Management</h1>
        </div>
        <div class="col-sm-6 text-right">
             <a href="/admin/events/create" class="btn btn-success"><i class="fas fa-plus"></i> New Event</a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body p-0">
        <table class="table table-striped table-hover">
            <thead>
                <tr>
                    <th style="width: 10px">#</th>
                    <th>Event Name</th>
                    <th>Date</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php $i = 1 + (10 * ($currentPage - 1)); ?>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($event['name']) ?></td>
                    <td><?= date('d M Y', strtotime($event['event_date'])) ?></td>
                    <td><?= esc($event['location']) ?></td>
                    <td>
                        <a href="/admin/events/tickets/<?= $event['id'] ?>" class="btn btn-sm btn-info" title="Manage Tickets"><i class="fas fa-ticket-alt"></i></a>
                        <a href="/admin/events/edit/<?= $event['id'] ?>" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i> Edit</a>
                        <a href="/admin/events/delete/<?= $event['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($events)): ?>
                    <tr><td colspan="5" class="text-center">No events found</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="card-footer clearfix">
        <?= $pager->links('default', 'default_full') ?>
    </div>
</div>

<?= $this->endSection() ?>
