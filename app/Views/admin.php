<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <?php if (($showCourses ?? false) !== true): ?>
                <div class="card-header bg-primary text-white">
                    <h1 class="card-title mb-0">Admin Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showCourses ?? false) === true): ?>
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <a href="#adminCoursesBody" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="adminCoursesBody" class="text-white text-decoration-none d-inline-block w-100" style="background:#2563eb;border-radius:6px;padding:10px 14px;display:inline-block;">
                                    <h5 class="card-title mb-0">Course Management</h5>
                                </a>
                            </div>
                            <div id="adminCoursesBody" class="collapse show">
                                <div class="card-body">
                                    <p class="lead mb-3">Manage all courses here.</p>

                                    <?php if (!empty($courses ?? []) && count($courses) > 0): ?>
                                        <ul class="list-group list-group-flush mt-3">
                                            <?php foreach ($courses as $course): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <div class="fw-bold"><?= esc($course['title'] ?? 'Untitled') ?></div>
                                                        <small class="text-muted"><?= esc($course['description'] ?? '') ?></small>
                                                    </div>
                                                    <div>
                                                        <a href="<?= base_url('admin/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-sm btn-primary">Open</a>
                                                    </div>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted mt-3 mb-0">No courses yet.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                        
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="card border-primary">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-primary">Total Users</h5>
                                        <h2 class="display-4 text-primary">—</h2>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-success">
                                    <div class="card-body text-center">
                                        <h5 class="card-title text-success">Total Courses</h5>
                                        <h2 class="display-4 text-success">—</h2>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Recent Activity</h5>
                            </div>
                            <div class="card-body">
                                <p class="text-muted">No recent activity to show.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>