    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #2563EB;">
        <div class="container">
            <a class="navbar-brand fw-bold" href="<?= base_url(''); ?>">
                <?php if (session()->get('isLoggedIn')): ?>
                    Welcome, <?= session()->get('name') ?>
                <?php else: ?>
                    Learning Management System
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (session()->get('isLoggedIn')): ?>
                        <?php $role = strtolower(session('role') ?? ''); ?>
                        <?php if ($role === 'admin'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Admin Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('admin/courses'); ?>">Course Management</a></li>
                            <li><a class="nav-link" href="<?= base_url('admin/users'); ?>">User Management</a></li>
                        <?php elseif ($role === 'teacher'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Teacher Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('teacher/courses'); ?>">My Courses</a></li>
                        <?php elseif ($role === 'student'): ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Student Dashboard</a></li>
                            <li><a class="nav-link" href="<?= base_url('student/enrollments'); ?>">My Enrollments</a></li>
                        <?php else: ?>
                            <li><a class="nav-link" href="<?= base_url('dashboard'); ?>">Dashboard</a></li>
                        <?php endif; ?>
                        
                        <li>
                            <a class="nav-link" href="<?= base_url('logout'); ?>">Logout</a>
                        </li>
                        
                        <!-- Notification Dropdown -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="bi bi-bell-fill"></i>
                                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notification-badge" style="display:none;">0</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="notificationDropdown" id="notification-list" style="min-width: 300px; max-height: 400px; overflow-y: auto;">
                                <li><h6 class="dropdown-header">Notifications</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="text-center py-3 text-muted" id="no-notifications">No new notifications</li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li><a class="nav-link" href="<?= base_url(''); ?>">Home</a> </li>
                        <li><a class="nav-link" href="<?= base_url('about'); ?>">About</a></li>
                        <li><a class="nav-link" href="<?= base_url('contact'); ?>">Contact</a></li>
                        <li> <a class="nav-link" href="<?= base_url('login'); ?>">Login</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>


