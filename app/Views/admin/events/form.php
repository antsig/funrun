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
                        <label>Date & Time</label>
                        <input type="datetime-local" class="form-control" name="event_date" value="<?= isset($event) ? date('Y-m-d\TH:i', strtotime($event['event_date'])) : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Registration Deadline</label>
                        <input type="datetime-local" class="form-control" name="registration_deadline" value="<?= isset($event) && $event['registration_deadline'] ? date('Y-m-d\TH:i', strtotime($event['registration_deadline'])) : '' ?>">
                        <small class="text-muted">Empty = No deadline (Event Date)</small>
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



<?= $this->endSection() ?>
