<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h1 class="card-title mb-0">Student Dashboard</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                    
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Enrolled Courses</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item">No enrolled courses yet.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-warning">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="card-title mb-0">Upcoming Deadlines</h5>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted">No deadlines.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-success">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">Recent Grades</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted">No grades available.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>