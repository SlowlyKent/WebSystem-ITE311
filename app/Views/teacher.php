<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <?php if (($showCourses ?? false) !== true && ($showCreateCourse ?? false) !== true): ?>
                <div class="card-header text-white" style="background-color: #2563EB;">
                    <h1 class="card-title mb-0">Teacher Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showCreateCourse ?? false) === true): ?>
                        <!-- Create Course Form -->
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header text-white" style="background-color: #2563EB; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <h5 class="card-title mb-0">Create New Course</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="<?= base_url('teacher/courses/store') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Course Title</label>
                                        <input type="text" class="form-control" id="title" name="title" required placeholder="Enter course title">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Course Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter course description"></textarea>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Create Course</button>
                                        <a href="<?= base_url('teacher/courses') ?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif (($showCourses ?? false) === true): ?>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="card border-primary" style="border-radius: 10px; height: 100%;">
                                    <div class="card-header text-white" style="background-color: #2563EB; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <h5 class="card-title mb-0">My Courses</h5>
                                            <a href="<?= base_url('teacher/courses/create') ?>" class="btn btn-light btn-sm">
                                                <i class="bi bi-plus-circle"></i> Create Course
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body" style="min-height: 200px; max-height: 600px; overflow-y: auto;">
                                        <?php if (session()->getFlashdata('success')): ?>
                                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                                <?= session()->getFlashdata('success') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>
                                        <?php if (session()->getFlashdata('error')): ?>
                                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                                <?= session()->getFlashdata('error') ?>
                                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($groupedCourses ?? [])): ?>
                                            <?php foreach ($groupedCourses as $schoolYear => $semesters): ?>
                                                <div class="mb-4">
                                                    <h6 class="text-primary fw-bold mb-2"> Academic Year: <?= esc($schoolYear) ?></h6>
                                                    <?php foreach ($semesters as $semester => $yearLevels): ?>
                                                        <div class="ms-3 mb-3">
                                                            <h6 class="text-info fw-bold mb-2"> Semester: <?= esc($semester) ?></h6>
                                                            <?php foreach ($yearLevels as $yearLevel => $sections): ?>
                                                                <div class="ms-3 mb-2">
                                                                    <h6 class="text-secondary fw-bold mb-2"> Year Level: <?= esc($yearLevel) ?></h6>
                                                                    <?php foreach ($sections as $section => $courses): ?>
                                                                        <div class="ms-3 mb-2">
                                                                            <h6 class="text-muted fw-bold mb-1"> Section: <?= esc($section) ?></h6>
                                                                            <ul class="list-group list-group-flush ms-3">
                                                                                <?php foreach ($courses as $course): ?>
                                                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                                                        <div>
                                                                                            <div class="fw-bold"><?= esc($course['course_code'] ?? '') ?> - <?= esc($course['title'] ?? 'Untitled') ?></div>
                                                                                            <small class="text-muted"><?= esc($course['short_description'] ?? $course['description'] ?? '') ?></small>
                                                                                        </div>
                                                                                        <a href="<?= base_url('teacher/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-sm btn-primary">Open</a>
                                                                                    </li>
                                                                                <?php endforeach; ?>
                                                                            </ul>
                                                                        </div>
                                                                    <?php endforeach; ?>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php elseif (!empty($courses ?? []) && count($courses) > 0): ?>
                                            <ul class="list-group list-group-flush">
                                                <?php foreach ($courses as $course): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <div>
                                                            <div class="fw-bold"><?= esc($course['course_code'] ?? '') ?> - <?= esc($course['title'] ?? 'Untitled') ?></div>
                                                            <small class="text-muted"><?= esc($course['description'] ?? '') ?></small>
                                                        </div>
                                                        <a href="<?= base_url('teacher/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-sm btn-primary">Open</a>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="mt-3 ms-3">No courses yet. Click "Create Course" to get started.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-primary" style="border-radius: 10px; height: 100%;">
                                    <div class="card-header text-white" style="background-color: #2563EB; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                        <h5 class="card-title mb-0">New Submissions</h5>
                                    </div>
                                    <div class="card-body" style="min-height: 200px;">
                                        <p class="ms-1 mb-0">No new submissions.</p>
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

