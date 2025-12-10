<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<style>
    /* Course Management Card Styling - Square Cards with Equal Size */
    .course-item {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        height: 100%;
        display: flex;
        flex-direction: column;
        aspect-ratio: 1;
    }
    
    .course-item:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.15);
    }
    
    .course-item .card-body {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        padding: 1rem;
        height: 100%;
    }
    
    .course-item .card-title {
        color: #1f2937;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        line-height: 1.3;
    }
    
    .course-item .card-text {
        color: #6b7280;
        font-size: 0.875rem;
        margin-bottom: 0.75rem;
        flex-grow: 1;
        display: flex;
        align-items: flex-start;
        line-height: 1.4;
        overflow: hidden;
    }
    
    .course-item .btn {
        margin-top: auto;
        width: 100%;
        padding: 0.4rem 0.75rem;
        font-size: 0.85rem;
    }
    
    /* Make sure all cards in a row have the same height and are square */
    .row .col-md-3 {
        display: flex;
    }
    
    .row .col-md-3 > .card {
        width: 100%;
        aspect-ratio: 1;
    }
</style>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <?php if (($showCourses ?? false) !== true && ($showUsers ?? false) !== true && ($showCreateUser ?? false) !== true && ($showEditUser ?? false) !== true && ($showCreateCourse ?? false) !== true): ?>
                <div class="card-header bg-primary text-white">
                    <h1 class="card-title mb-0">Admin Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showUsers ?? false) === true): ?>
                        <!-- User Management List -->
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">User Management</h5>
                                    <a href="<?= base_url('admin/users/create') ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-plus-circle"></i> Create New User
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
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
                                
                                <p class="lead mb-3">Manage all users. You can create, edit, and delete any user.</p>

                                <!-- Search Bar for Users -->
                                <div class="mb-3">
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                                        <input type="text" 
                                               class="form-control" 
                                               id="userSearchInput" 
                                               placeholder="Search users by name, email, or role...">
                                    </div>
                                </div>

                                <?php if (!empty($users ?? []) && count($users) > 0): ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-hover">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Name</th>
                                                    <th>Email</th>
                                                    <th>Role</th>
                                                    <th>Status</th>
                                                    <th>Created At</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody id="userTableBody">
                                                <?php foreach ($users as $u): ?>
                                                    <tr class="user-row" data-name="<?= esc(strtolower($u['name'])) ?>" data-email="<?= esc(strtolower($u['email'])) ?>" data-role="<?= esc(strtolower($u['role'])) ?>">
                                                        <td><?= esc($u['id']) ?></td>
                                                        <td><?= esc($u['name']) ?></td>
                                                        <td><?= esc($u['email']) ?></td>
                                                        <td>
                                                            <?php
                                                            $badgeClass = 'bg-secondary';
                                                            if ($u['role'] === 'admin') {
                                                                $badgeClass = 'bg-danger';
                                                            } elseif ($u['role'] === 'teacher') {
                                                                $badgeClass = 'bg-info';
                                                            } elseif ($u['role'] === 'student') {
                                                                $badgeClass = 'bg-success';
                                                            }
                                                            ?>
                                                            <span class="badge <?= $badgeClass ?>">
                                                                <?= esc(ucfirst($u['role'])) ?>
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <?php
                                                            // Get user status, default to active if not set
                                                            $userStatus = $u['status'] ?? 'active';
                                                            if ($userStatus === 'active') {
                                                                $statusBadge = 'bg-success';
                                                                $statusText = 'Active';
                                                            } else {
                                                                $statusBadge = 'bg-secondary';
                                                                $statusText = 'Inactive';
                                                            }
                                                            ?>
                                                            <span class="badge <?= $statusBadge ?>">
                                                                <?= esc($statusText) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= esc($u['created_at'] ?? 'N/A') ?></td>
                                                        <td>
                                                            <?php 
                                                            // Get current logged-in user ID
                                                            $currentUserId = session()->get('user_id');
                                                            $isOwnAccount = ($u['id'] == $currentUserId);
                                                            $userStatus = $u['status'] ?? 'active';
                                                            ?>
                                                            <?php if ($isOwnAccount): ?>
                                                                <span class="text-muted small">Cannot edit/delete own account</span>
                                                            <?php else: ?>
                                                                <div class="btn-group" role="group">
                                                                    <a href="<?= base_url('admin/users/edit/' . $u['id']) ?>" 
                                                                       class="btn btn-sm btn-outline-warning" 
                                                                       title="Edit User"
                                                                       data-bs-toggle="tooltip">
                                                                        <i class="bi bi-pencil"></i>
                                                                    </a>
                                                                    <?php if ($userStatus === 'active'): ?>
                                                                        <a href="<?= base_url('admin/users/inactivate/' . $u['id']) ?>" 
                                                                           class="btn btn-sm btn-outline-secondary" 
                                                                           title="Inactivate User"
                                                                           data-bs-toggle="tooltip"
                                                                           onclick="return confirm('Are you sure you want to inactivate this user? They will not be able to login.');">
                                                                            <i class="bi bi-x-circle"></i>
                                                                        </a>
                                                                    <?php else: ?>
                                                                        <a href="<?= base_url('admin/users/activate/' . $u['id']) ?>" 
                                                                           class="btn btn-sm btn-outline-success" 
                                                                           title="Activate User"
                                                                           data-bs-toggle="tooltip"
                                                                           onclick="return confirm('Are you sure you want to activate this user? They will be able to login.');">
                                                                            <i class="bi bi-check-circle"></i>
                                                                        </a>
                                                                    <?php endif; ?>
                                                                <a href="<?= base_url('admin/users/delete/' . $u['id']) ?>" 
                                                                       class="btn btn-sm btn-outline-danger" 
                                                                       title="Delete User"
                                                                       data-bs-toggle="tooltip"
                                                                   onclick="return confirm('Are you sure you want to delete this user?');">
                                                                        <i class="bi bi-trash"></i>
                                                                </a>
                                                                </div>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted mt-3 mb-0">No users found.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php elseif (($showCreateUser ?? false) === true): ?>
                        <!-- Create User Form -->
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <h5 class="card-title mb-0">Create New User</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="<?= base_url('admin/users/store') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required minlength="3" maxlength="100">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required minlength="6" pattern="[A-Za-z0-9]+" title="Password can only contain letters and numbers. No special characters allowed.">
                                        <small class="form-text text-muted">Only letters and numbers allowed. No special characters.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirm" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" required minlength="6" pattern="[A-Za-z0-9]+" title="Password can only contain letters and numbers. No special characters allowed.">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="">Select Role</option>
                                            <option value="admin">Admin</option>
                                            <option value="teacher">Teacher</option>
                                            <option value="student">Student</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Create User</button>
                                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif (($showEditUser ?? false) === true): ?>
                        <!-- Edit User Form -->
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <h5 class="card-title mb-0">Edit User</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="<?= base_url('admin/users/update/' . ($editUser['id'] ?? '')) ?>" method="post">
                                    <?= csrf_field() ?>
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name</label>
                                        <input type="text" class="form-control" id="name" name="name" value="<?= esc($editUser['name'] ?? '') ?>" required minlength="3" maxlength="100">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input type="email" class="form-control" id="email" name="email" value="<?= esc($editUser['email'] ?? '') ?>" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password" class="form-label">Password <small class="text-muted">(Leave blank to keep current password)</small></label>
                                        <input type="password" class="form-control" id="password" name="password" minlength="6" pattern="[A-Za-z0-9]+" title="Password can only contain letters and numbers. No special characters allowed.">
                                        <small class="form-text text-muted">Only letters and numbers allowed. No special characters.</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="password_confirm" class="form-label">Confirm Password</label>
                                        <input type="password" class="form-control" id="password_confirm" name="password_confirm" minlength="6" pattern="[A-Za-z0-9]+" title="Password can only contain letters and numbers. No special characters allowed.">
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="role" class="form-label">Role</label>
                                        <select class="form-select" id="role" name="role" required>
                                            <option value="admin" <?= ($editUser['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                                            <option value="teacher" <?= ($editUser['role'] ?? '') === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                            <option value="student" <?= ($editUser['role'] ?? '') === 'student' ? 'selected' : '' ?>>Student</option>
                                        </select>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">Update User</button>
                                        <a href="<?= base_url('admin/users') ?>" class="btn btn-secondary">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif (($showCreateCourse ?? false) === true): ?>
                        <!-- Create Course Form -->
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <h5 class="card-title mb-0">Create New Course</h5>
                            </div>
                            <div class="card-body">
                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('error') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (session()->getFlashdata('success')): ?>
                                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                                        <?= session()->getFlashdata('success') ?>
                                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                    </div>
                                <?php endif; ?>
                                
                                <form action="<?= base_url('admin/courses/store') ?>" method="post">
                                    <?= csrf_field() ?>
                                    <!-- 1. Basic Course Information -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 1. Basic Course Information</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="title" class="form-label">Course Name <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="title" name="title" required placeholder="e.g., Introduction to Programming">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="course_code" class="form-label">Course Code <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="course_code" name="course_code" required placeholder="e.g., IT101, BSCS-202">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="description" class="form-label">Course Description <span class="text-danger">*</span></label>
                                                <textarea class="form-control" id="description" name="description" rows="4" required placeholder="Brief paragraph describing the course"></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="short_description" class="form-label">Short Description / Summary (Optional)</label>
                                                <textarea class="form-control" id="short_description" name="short_description" rows="2" placeholder="Brief summary of the course"></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 2. Academic Details -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 2. Academic Details</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="year_level" class="form-label">Year Level</label>
                                                    <select class="form-select" id="year_level" name="year_level">
                                                        <option value="">Select Year Level</option>
                                                        <option value="1st year">1st year</option>
                                                        <option value="2nd year">2nd year</option>
                                                        <option value="3rd year">3rd year</option>
                                                        <option value="4th year">4th year</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="semester" class="form-label">Semester</label>
                                                    <select class="form-select" id="semester" name="semester">
                                                        <option value="">Select Semester</option>
                                                        <option value="1st sem">1st sem</option>
                                                        <option value="2nd sem">2nd sem</option>
                                                        <option value="Summer">Summer</option>
                                                    </select>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="school_year" class="form-label">School Year / Academic Year</label>
                                                    <input type="text" class="form-control" id="school_year" name="school_year" placeholder="e.g., 2024-2025">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="department" class="form-label">Department / Program / Strand</label>
                                                    <input type="text" class="form-control" id="department" name="department" placeholder="e.g., BSIT, BSHM, STEM">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 3. Instructor / Teacher Assigned -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 3. Instructor / Teacher Assigned</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="instructor_id" class="form-label">Select Instructor (from registered teachers)</label>
                                                <select class="form-select" id="instructor_id" name="instructor_id">
                                                    <option value="">Select Instructor</option>
                                                    <?php if (!empty($teachers ?? [])): ?>
                                                        <?php foreach ($teachers as $teacher): ?>
                                                            <option value="<?= $teacher['id'] ?>" 
                                                                    data-teacher-name="<?= esc($teacher['name']) ?>" 
                                                                    data-teacher-email="<?= esc($teacher['email']) ?>">
                                                                <?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </select>
                                                <small class="form-text text-muted">Or fill in manually below if instructor is not registered</small>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="instructor_name" class="form-label">Instructor Name</label>
                                                    <input type="text" class="form-control" id="instructor_name" name="instructor_name" placeholder="Full name of instructor">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="instructor_email" class="form-label">Instructor Email / ID</label>
                                                    <input type="email" class="form-control" id="instructor_email" name="instructor_email" placeholder="instructor@example.com">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 4. Course Requirements -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 4. Course Requirements</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="prerequisite_courses" class="form-label">Prerequisite Course(s)</label>
                                                <select class="form-select" id="prerequisite_courses" name="prerequisite_courses[]" multiple size="5">
                                                    <?php if (!empty($allCourses ?? [])): ?>
                                                        <?php foreach ($allCourses as $course): ?>
                                                            <option value="<?= $course['id'] ?>">
                                                                <?= esc($course['course_code'] ?? '') ?> - <?= esc($course['title']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    <?php else: ?>
                                                        <option value="">No courses available yet</option>
                                                    <?php endif; ?>
                                                </select>
                                                <small class="form-text text-muted">Hold Ctrl (Windows) or Cmd (Mac) to select multiple courses</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="units" class="form-label">Units / Credit Hours</label>
                                                <input type="number" class="form-control" id="units" name="units" step="0.5" min="0" placeholder="e.g., 3.0">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 5. Course Structure -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 5. Course Structure</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="course_category" class="form-label">Course Category</label>
                                                    <select class="form-select" id="course_category" name="course_category">
                                                        <option value="">Select Category</option>
                                                        <option value="Major">Major</option>
                                                        <option value="Minor">Minor</option>
                                                        <option value="Elective">Elective</option>
                                                        <option value="Laboratory">Laboratory</option>
                                                        <option value="Online Course">Online Course</option>
                                                    </select>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="section" class="form-label">Section</label>
                                                    <input type="text" class="form-control" id="section" name="section" placeholder="e.g., BSCS-2A">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- 6. Additional Fields (Optional) -->
                                    <div class="card mb-4" style="border: 1px solid #dee2e6;">
                                        <div class="card-header bg-light">
                                            <h6 class="mb-0 fw-bold"> 6. Additional Fields (Optional)</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="start_date" class="form-label">Course Start Date</label>
                                                    <input type="date" class="form-control" id="start_date" name="start_date">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="end_date" class="form-label">Course End Date</label>
                                                    <input type="date" class="form-control" id="end_date" name="end_date">
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="start_time" class="form-label">Start Time <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" id="start_time" name="start_time" required>
                                                    <small class="form-text text-muted">Example: 1:00 PM (13:00 in 24-hour format)</small>
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="end_time" class="form-label">End Time <span class="text-danger">*</span></label>
                                                    <input type="time" class="form-control" id="end_time" name="end_time" required>
                                                    <small class="form-text text-muted">Example: 4:00 PM (16:00 in 24-hour format)</small>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label for="enrollment_limit" class="form-label">Enrollment Limit (Max Students)</label>
                                                    <input type="number" class="form-control" id="enrollment_limit" name="enrollment_limit" min="1" placeholder="e.g., 30">
                                                </div>
                                                
                                                <div class="col-md-6 mb-3">
                                                    <label for="grading_scheme" class="form-label">Grading Scheme Type</label>
                                                    <select class="form-select" id="grading_scheme" name="grading_scheme">
                                                        <option value="">Select Grading Scheme</option>
                                                        <option value="Percentage-based">Percentage-based</option>
                                                        <option value="Points-based">Points-based</option>
                                                        <option value="Criteria-based">Criteria-based</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-lg">Create Course</button>
                                        <a href="<?= base_url('admin/courses') ?>" class="btn btn-secondary btn-lg">Cancel</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php elseif (($showCourses ?? false) === true): ?>
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header bg-primary text-white" style="border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="card-title mb-0">Course Management</h5>
                                    <a href="<?= base_url('admin/courses/create') ?>" class="btn btn-light btn-sm">
                                        <i class="bi bi-plus-circle"></i> Create New Course
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
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
                                
                                <!-- Search Bar for Courses -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <form id="adminCourseSearchForm" class="d-flex">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="courseSearchInput" 
                                                       name="search_term" 
                                                       placeholder="Search courses...">
                                                <button type="submit" class="btn btn-outline-primary">
                                                    <i class="bi bi-search"></i> Search
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                                <!-- Courses Grid Container -->
                                <div id="adminCoursesContainer" class="row">
                                        <?php if (!empty($courses ?? []) && count($courses) > 0): ?>
                                            <?php foreach ($courses as $course): ?>
                                                <div class="col-md-3 mb-4">
                                                <div class="card course-item" 
                                                     data-title="<?= esc(strtolower(trim($course['title'] ?? 'Untitled'))) ?>" 
                                                     data-description="<?= esc(strtolower(trim($course['description'] ?? ''))) ?>"
                                                     data-code="<?= esc(strtolower(trim($course['course_code'] ?? ''))) ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title course-title-text"><?= esc($course['title'] ?? 'Untitled') ?></h5>
                                                        <p class="card-text course-desc-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                                        <?php if (!empty($course['course_code'])): ?>
                                                            <p class="text-muted"><small>Code: <?= esc($course['course_code']) ?></small></p>
                                                        <?php endif; ?>
                                                        <a href="<?= base_url('admin/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-primary">View Course</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <div class="alert alert-info">No courses available at the moment.</div>
                                        </div>
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
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // User Management Search - Filter users as you type
    $('#userSearchInput').on('keyup', function() {
        // Get the search value and convert to lowercase
        var searchValue = $(this).val().toLowerCase();
        
        // Loop through each user row in the table
        $('.user-row').each(function() {
            // Get the user's name, email, and role from data attributes
            var userName = $(this).data('name') || '';
            var userEmail = $(this).data('email') || '';
            var userRole = $(this).data('role') || '';
            
            // Check if search term matches name, email, or role
            var matches = userName.indexOf(searchValue) > -1 || 
                         userEmail.indexOf(searchValue) > -1 || 
                         userRole.indexOf(searchValue) > -1;
            
            // Show the row if it matches, hide if it doesn't
            if (matches || searchValue === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });
    
    // Course Management Search - Filter courses as you type (client-side)
    $('#courseSearchInput').on('keyup', function() {
        // Get the search value and convert to lowercase, trim whitespace
        var searchValue = $(this).val().toLowerCase().trim();
        
        // Loop through each course card
        $('.course-item').each(function() {
            // Get the course title, description, and code from data attributes
            var courseTitle = $(this).attr('data-title') || $(this).data('title') || '';
            var courseDescription = $(this).attr('data-description') || $(this).data('description') || '';
            var courseCode = $(this).attr('data-code') || $(this).data('code') || '';
            
            // Also get text from visible elements as fallback
            var visibleTitle = $(this).find('.course-title-text').text().toLowerCase() || '';
            var visibleDescription = $(this).find('.course-desc-text').text().toLowerCase() || '';
            
            // Combine all searchable text
            var allText = (courseTitle + ' ' + courseDescription + ' ' + courseCode + ' ' + visibleTitle + ' ' + visibleDescription).toLowerCase().replace(/\s+/g, ' ').trim();
            
            // Check if search term matches any part of the text
            var matches = allText.indexOf(searchValue) > -1 || searchValue === '';
            
            // Show or hide the card's parent column
            if (matches) {
                $(this).closest('.col-md-3').show();
            } else {
                $(this).closest('.col-md-3').hide();
            }
        });
    });
    
    // Prevent form submission for course search (use instant filtering instead)
    $('#adminCourseSearchForm').on('submit', function(e) {
        e.preventDefault();
        // The keyup event above handles the filtering
    });
});

// Initialize Bootstrap tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Auto-fill instructor name and email when instructor is selected
    // Get the instructor dropdown element
    var instructorDropdown = document.getElementById('instructor_id');
    
    // Only add event listener if the element exists (only on create course page)
    if (instructorDropdown) {
        // Listen for changes when user selects an instructor
        instructorDropdown.addEventListener('change', function() {
            // Get the selected option
            var selectedOption = this.options[this.selectedIndex];
            
            // Get the instructor name and email from the selected option
            var teacherName = selectedOption.getAttribute('data-teacher-name');
            var teacherEmail = selectedOption.getAttribute('data-teacher-email');
            
            // Get the input fields
            var nameField = document.getElementById('instructor_name');
            var emailField = document.getElementById('instructor_email');
            
            // Only fill if fields exist
            if (nameField && emailField) {
                // If an instructor is selected (not the default "Select Instructor")
                if (this.value !== '') {
                    // Fill in the name field
                    nameField.value = teacherName || '';
                    // Fill in the email field
                    emailField.value = teacherEmail || '';
                } else {
                    // If "Select Instructor" is selected, clear the fields
                    nameField.value = '';
                    emailField.value = '';
                }
            }
        });
    }
});
</script>

<?= $this->endSection() ?>