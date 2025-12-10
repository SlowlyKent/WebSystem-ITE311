<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0"><?= esc($course['course_code'] ?? '') ?> - <?= esc($course['title'] ?? 'Course Materials') ?></h3>
                </div>
                <div class="card-body">
                    <!-- Course Overview Section -->
                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                        <div class="card-header bg-light">
                            <h5 class="mb-0 fw-bold"> Course Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <strong>Course Name:</strong> <?= esc($course['title'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <strong>Course Code:</strong> <?= esc($course['course_code'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <strong>Description:</strong><br>
                                    <p class="text-muted"><?= esc($course['description'] ?? 'No description available') ?></p>
                                </div>
                                <?php if (!empty($course['short_description'])): ?>
                                <div class="col-md-12 mb-3">
                                    <strong>Summary:</strong><br>
                                    <p class="text-muted"><?= esc($course['short_description']) ?></p>
                                </div>
                                <?php endif; ?>
                                <div class="col-md-3 mb-3">
                                    <strong>Academic Year:</strong> <?= esc($course['school_year'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Semester:</strong> <?= esc($course['semester'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Year Level:</strong> <?= esc($course['year_level'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Category:</strong> <?= esc($course['course_category'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Section:</strong> <?= esc($course['section'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Units:</strong> <?= esc($course['units'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Department:</strong> <?= esc($course['department'] ?? 'N/A') ?>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <strong>Status:</strong> 
                                    <span class="badge <?= ($course['status'] ?? '') === 'Active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= esc($course['status'] ?? 'N/A') ?>
                                    </span>
                                </div>
                                <?php if (!empty($course['instructor_name'])): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Instructor:</strong> <?= esc($course['instructor_name']) ?>
                                    <?php if (!empty($course['instructor_email'])): ?>
                                        <br><small class="text-muted"><?= esc($course['instructor_email']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($course['start_date']) || !empty($course['end_date'])): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Course Duration:</strong><br>
                                    <?php if (!empty($course['start_date'])): ?>
                                        <small>Start: <?= esc(date('M d, Y', strtotime($course['start_date']))) ?></small>
                                    <?php endif; ?>
                                    <?php if (!empty($course['end_date'])): ?>
                                        <br><small>End: <?= esc(date('M d, Y', strtotime($course['end_date']))) ?></small>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                                <?php if (!empty($course['grading_scheme'])): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Grading Scheme:</strong> <?= esc($course['grading_scheme']) ?>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3"> Course Materials</h5>
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (in_array($user['role'] ?? '', ['admin', 'teacher'])): ?>
                        <div class="mb-3">
                            <a href="<?= base_url('materials/upload/' . ($course['id'] ?? '')) ?>" class="btn btn-primary">
                                <i class="bi bi-plus-circle"></i> Upload Material
                            </a>
                        </div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>File Name</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($materials) && count($materials) > 0): ?>
                                    <?php foreach ($materials as $material): ?>
                                        <tr>
                                            <td><?= esc($material['file_name'] ?? 'Unknown') ?></td>
                                            <td><?= esc(date('Y-m-d H:i:s', strtotime($material['created_at'] ?? 'now'))) ?></td>
                                            <td>
                                                <a href="<?= base_url('materials/download/' . ($material['id'] ?? '')) ?>" 
                                                   class="btn btn-sm btn-success">
                                                    <i class="bi bi-download"></i> Download
                                                </a>
                                                <?php if (in_array($user['role'] ?? '', ['admin', 'teacher'])): ?>
                                                    <a href="<?= base_url('materials/delete/' . ($material['id'] ?? '')) ?>" 
                                                       class="btn btn-sm btn-danger"
                                                       onclick="return confirm('Are you sure you want to delete this material?');">
                                                        <i class="bi bi-trash"></i> Delete
                                                    </a>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">No materials uploaded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        <?php if (($user['role'] ?? '') === 'admin'): ?>
                            <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Courses
                            </a>
                        <?php elseif (($user['role'] ?? '') === 'teacher'): ?>
                            <a href="<?= base_url('teacher/courses') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Courses
                            </a>
                        <?php else: ?>
                            <a href="<?= base_url('student/enrollments') ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left"></i> Back to Enrollments
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>