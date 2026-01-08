<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"><?= isset($event) ? 'Edit Event' : 'Create Event' ?></h1>
        </div>
        <div class="col-sm-6 text-right">
             <a href="/admin/events" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Event Details</h3>
            </div>
            <form action="<?= isset($event) ? '/admin/events/update/' . $event['id'] : '/admin/events/store' ?>" method="post">
                <div class="card-body">
                    <div class="form-group">
                        <label>Event Name</label>
                        <input type="text" class="form-control" name="name" value="<?= isset($event) ? esc($event['name']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date</label>
                        <input type="date" class="form-control" name="event_date" value="<?= isset($event) ? $event['event_date'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" class="form-control" name="location" value="<?= isset($event) ? esc($event['location']) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea class="form-control" name="description" rows="5"><?= isset($event) ? esc($event['description']) : '' ?></textarea>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (isset($event)): ?>
<div class="row">
    <div class="col-md-12">
        <div class="card card-secondary">
            <div class="card-header">
                <h3 class="card-title">Ticket Categories</h3>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-4">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Quota</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td><?= esc($cat['name']) ?></td>
                            <td>Rp <?= number_format($cat['price'], 0, ',', '.') ?></td>
                            <td><?= $cat['quota'] ?></td>
                            <td>
                                <a href="/admin/events/deleteCategory/<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i> Delete</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <h4>Add Category</h4>
                <form action="/admin/events/addCategory/<?= $event['id'] ?>" method="post" class="form-inline">
                    <div class="form-group mr-2 mb-2">
                        <label class="sr-only">Name</label>
                        <input type="text" class="form-control" name="name" placeholder="Category Name (e.g. 5K)" required>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label class="sr-only">Price</label>
                        <input type="number" class="form-control" name="price" placeholder="Price" required>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label class="sr-only">Quota</label>
                        <input type="number" class="form-control" name="quota" placeholder="Quota" required>
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label class="sr-only">BIB Prefix</label>
                        <input type="text" class="form-control" name="bib_prefix" placeholder="Prefix (e.g. 5K-)" style="width: 120px;">
                    </div>
                    <div class="form-group mr-2 mb-2">
                        <label class="sr-only">Last BIB</label>
                        <input type="number" class="form-control" name="last_bib" placeholder="Start Number - 1" value="0" style="width: 120px;">
                    </div>
                    <button type="submit" class="btn btn-primary mb-2">Add</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>
