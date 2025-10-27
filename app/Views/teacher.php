<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <?php if (($showCourses ?? false) !== true): ?>
                <div class="card-header bg-success text-white">
                    <h1 class="card-title mb-0">Teacher Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showCourses ?? false) === true): ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-info" style="border-radius: 10px;">
                                    <div class="card-header bg-info text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                        <a href="#myCoursesBody" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="myCoursesBody"
                                           class="text-white text-decoration-none d-inline-block w-100"
                                           style="background: #06b6d4; border-radius: 6px; padding: 10px 14px; display: inline-block;">
                                            <h5 class="card-title mb-0">My Courses</h5>
                                        </a>
                                    </div>
                                    <div id="myCoursesBody" class="collapse show">
                                        <div class="card-body">
                                            <?php if (!empty($courses ?? []) && count($courses) > 0): ?>
                                                <ul class="list-group list-group-flush">
                                                    <?php foreach ($courses as $course): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <div>
                                                                <div class="fw-bold"><?= esc($course['title'] ?? 'Untitled') ?></div>
                                                                <small class="text-muted"><?= esc($course['description'] ?? '') ?></small>
                                                            </div>
                                                            <a href="<?= base_url('teacher/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-sm btn-primary">Open</a>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="mt-3 ms-3">No courses yet.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-warning" style="border-radius: 10px;">
                                    <div class="card-header bg-warning text-dark" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                        <a href="#newSubmissionsBody" data-bs-toggle="collapse" role="button" aria-expanded="true" aria-controls="newSubmissionsBody"
                                           class="text-dark text-decoration-none d-inline-block w-100"
                                           style="background: #fbbf24; border-radius: 6px; padding: 10px 14px; display: inline-block;">
                                            <h5 class="card-title mb-0">New Submissions</h5>
                                        </a>
                                    </div>
                                    <div id="newSubmissionsBody" class="collapse show">
                                        <div class="card-body">
                                            <p class="ms-1 mb-0">No new submissions.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>

                        <!-- Dashboard cards removed as requested. Use the header nav 'My Courses' link instead. -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>