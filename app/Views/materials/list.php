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
                                <?php if (!empty($course['start_time']) || !empty($course['end_time'])): ?>
                                <div class="col-md-6 mb-3">
                                    <strong>Class Time:</strong><br>
                                    <?php if (!empty($course['start_time']) && !empty($course['end_time'])): ?>
                                        <small><?= esc(date('g:i A', strtotime($course['start_time']))) ?> - <?= esc(date('g:i A', strtotime($course['end_time']))) ?></small>
                                    <?php elseif (!empty($course['start_time'])): ?>
                                        <small>Start: <?= esc(date('g:i A', strtotime($course['start_time']))) ?></small>
                                    <?php elseif (!empty($course['end_time'])): ?>
                                        <small>End: <?= esc(date('g:i A', strtotime($course['end_time']))) ?></small>
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

                    <?php if (($user['role'] ?? '') === 'admin'): ?>
                        <hr class="my-4">
                        
                        <h5 class="mb-3"> Enrolled Students</h5>
                        <p class="text-muted">Manage students enrolled in this course. You can view details, remove wrong enrollments, or activate/deactivate student access.</p>
                        
                        <?php if (!empty($enrolledStudents ?? []) && count($enrolledStudents) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Email</th>
                                            <th>Enrollment Date</th>
                                            <th>Enrollment Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($enrolledStudents as $enrollment): ?>
                                            <tr>
                                                <td><?= esc($enrollment['name'] ?? 'Unknown') ?></td>
                                                <td><?= esc($enrollment['email'] ?? 'N/A') ?></td>
                                                <td><?= esc($enrollment['enrollment_date'] ? date('M d, Y h:i A', strtotime($enrollment['enrollment_date'])) : 'N/A') ?></td>
                                                <td>
                                                    <?php 
                                                    $enrollmentStatus = $enrollment['status'] ?? 'active';
                                                    $badgeClass = ($enrollmentStatus === 'active') ? 'bg-success' : 'bg-secondary';
                                                    ?>
                                                    <span class="badge <?= $badgeClass ?>">
                                                        <?= ucfirst($enrollmentStatus) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="btn-group btn-group-sm" role="group">
                                                        <!-- View Button -->
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#viewStudentModal<?= $enrollment['id'] ?>"
                                                                title="View Student Details">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        
                                                        <?php if ($enrollmentStatus === 'active'): ?>
                                                            <!-- Deactivate Button -->
                                                            <a href="<?= base_url('admin/enrollments/deactivate/' . $enrollment['id']) ?>" 
                                                               class="btn btn-warning btn-sm"
                                                               onclick="return confirm('Are you sure you want to deactivate this student\\'s enrollment? They will not be able to access this course.');"
                                                               title="Deactivate Enrollment">
                                                                <i class="bi bi-pause-circle"></i>
                                                            </a>
                                                        <?php else: ?>
                                                            <!-- Activate Button -->
                                                            <a href="<?= base_url('admin/enrollments/activate/' . $enrollment['id']) ?>" 
                                                               class="btn btn-success btn-sm"
                                                               onclick="return confirm('Are you sure you want to activate this student\\'s enrollment? They will be able to access this course.');"
                                                               title="Activate Enrollment">
                                                                <i class="bi bi-play-circle"></i>
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <!-- Delete Button -->
                                                        <a href="<?= base_url('admin/enrollments/delete/' . $enrollment['id']) ?>" 
                                                           class="btn btn-danger btn-sm"
                                                           onclick="return confirm('Are you sure you want to remove this student from this course? This action cannot be undone.');"
                                                           title="Remove Student from Course">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                    
                                                    <!-- View Student Modal -->
                                                    <div class="modal fade" id="viewStudentModal<?= $enrollment['id'] ?>" tabindex="-1" aria-labelledby="viewStudentModalLabel<?= $enrollment['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-info text-white">
                                                                    <h5 class="modal-title" id="viewStudentModalLabel<?= $enrollment['id'] ?>">
                                                                        <i class="bi bi-person-circle"></i> Student Details
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="mb-3">
                                                                        <strong>Name:</strong> <?= esc($enrollment['name'] ?? 'N/A') ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Email:</strong> <?= esc($enrollment['email'] ?? 'N/A') ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Role:</strong> 
                                                                        <span class="badge bg-primary"><?= esc(ucfirst($enrollment['role'] ?? 'N/A')) ?></span>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>User Status:</strong> 
                                                                        <?php 
                                                                        $userStatus = $enrollment['user_status'] ?? 'active';
                                                                        $userBadgeClass = ($userStatus === 'active') ? 'bg-success' : 'bg-secondary';
                                                                        ?>
                                                                        <span class="badge <?= $userBadgeClass ?>">
                                                                            <?= ucfirst($userStatus) ?>
                                                                        </span>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Course:</strong> <?= esc($course['title'] ?? 'N/A') ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Course Code:</strong> <?= esc($course['course_code'] ?? 'N/A') ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Enrollment Date:</strong> 
                                                                        <?= esc($enrollment['enrollment_date'] ? date('F d, Y h:i A', strtotime($enrollment['enrollment_date'])) : 'N/A') ?>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <strong>Enrollment Status:</strong> 
                                                                        <?php 
                                                                        $enrollmentStatus = $enrollment['status'] ?? 'active';
                                                                        $badgeClass = ($enrollmentStatus === 'active') ? 'bg-success' : 'bg-secondary';
                                                                        ?>
                                                                        <span class="badge <?= $badgeClass ?>">
                                                                            <?= ucfirst($enrollmentStatus) ?>
                                                                        </span>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle"></i> No students enrolled in this course yet.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

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