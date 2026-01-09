<?= $this->extend('layout/admin') ?>

<?= $this->section('content') ?>

<div class="content-header">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Manage Tickets: <?= esc($event['name']) ?></h1>
        </div>
        <div class="col-sm-6 text-right">
             <a href="/admin/events" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to Events</a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card card-info">
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
                            <th>Status</th>
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
                                <span class="badge badge-<?= $cat['is_active'] ? 'success' : 'secondary' ?>">
                                    <?= $cat['is_active'] ? 'Active' : 'Inactive' ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#editCategoryModal<?= $cat['id'] ?>">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <a href="/admin/events/deleteCategory/<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this category?')"><i class="fas fa-trash"></i> Delete</a>
                            
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editCategoryModal<?= $cat['id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel<?= $cat['id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="editModalLabel<?= $cat['id'] ?>">Edit Category</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="/admin/events/updateCategory/<?= $cat['id'] ?>" method="post">
                                                    <div class="form-group">
                                                        <label>Name</label>
                                                        <input type="text" class="form-control" name="name" value="<?= esc($cat['name']) ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Price</label>
                                                        <input type="number" class="form-control" name="price" value="<?= $cat['price'] ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>Quota</label>
                                                        <input type="number" class="form-control" name="quota" value="<?= $cat['quota'] ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label>BIB Prefix</label>
                                                        <input type="text" class="form-control" name="bib_prefix" value="<?= esc($cat['bib_prefix']) ?>">
                                                    </div>
                                                     <div class="form-group">
                                                        <label>Last BIB</label>
                                                        <input type="number" class="form-control" name="last_bib" value="<?= $cat['last_bib'] ?>">
                                                    </div>
                                                    <div class="form-check">
                                                        <input type="checkbox" class="form-check-input" name="is_active" id="isActive<?= $cat['id'] ?>" <?= $cat['is_active'] ? 'checked' : '' ?>>
                                                        <label class="form-check-label" for="isActive<?= $cat['id'] ?>">Active</label>
                                                    </div>
                                                    <div class="modal-footer px-0 pb-0">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            
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

<?= $this->endSection() ?>
