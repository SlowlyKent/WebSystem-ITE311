<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h1 class="card-title mb-0">Teacher Dashboard</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                    
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card border-info">
                                <div class="card-header bg-info text-white">
                                    <h5 class="card-title mb-0">My Courses</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">No courses yet.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">New Submissions</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">No new submissions.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>