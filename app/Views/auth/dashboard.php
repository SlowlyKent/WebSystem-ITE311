<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - ITE311</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url('dashboard') ?>">
                <i class="fas fa-tachometer-alt me-2"></i>ITE311 Dashboard
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i><?= $user['name'] ?>
                    </a>
                    <ul class="dropdown-menu">
                        <li><span class="dropdown-item-text">
                            <small class="text-muted">Role: <?= ucfirst($user['role']) ?></small>
                        </span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="<?= base_url('logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?= session()->getFlashdata('success') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5><i class="fas fa-home me-2"></i>Welcome to Your Dashboard</h5>
                    </div>
                    <div class="card-body">
                        <h4>Hello, <?= $user['name'] ?>!</h4>
                        <p class="lead">You have successfully logged into the ITE311 system.</p>
                        <hr>
                        <p>This is a protected area that only authenticated users can access. 
                           You can now explore the features available to your role.</p>
                        
                        <?php if ($user['role'] === 'admin'): ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-crown me-2"></i>
                                <strong>Admin Access:</strong> You have administrative privileges.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-secondary text-white">
                        <h6><i class="fas fa-user-circle me-2"></i>User Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td><strong>Name:</strong></td>
                                <td><?= $user['name'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Email:</strong></td>
                                <td><?= $user['email'] ?></td>
                            </tr>
                            <tr>
                                <td><strong>Role:</strong></td>
                                <td>
                                    <span class="badge bg-<?= $user['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="card mt-3">
                    <div class="card-header bg-warning text-dark">
                        <h6><i class="fas fa-cog me-2"></i>Quick Actions</h6>
                    </div>
                    <div class="card-body">
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm" disabled>
                                <i class="fas fa-edit me-2"></i>Edit Profile
                            </button>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                <i class="fas fa-key me-2"></i>Change Password
                            </button>
                            <a href="<?= base_url('logout') ?>" class="btn btn-outline-danger btn-sm">
                                <i class="fas fa-sign-out-alt me-2"></i>Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Users Table Section -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5><i class="fas fa-users me-2"></i>All Registered Users</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($allUsers)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">#</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Email</th>
                                            <th scope="col">Role</th>
                                            <th scope="col">Created At</th>
                                            <th scope="col">Updated At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($allUsers as $index => $userData): ?>
                                            <tr>
                                                <th scope="row"><?= $index + 1 ?></th>
                                                <td>
                                                    <i class="fas fa-user me-2 text-muted"></i>
                                                    <?= htmlspecialchars($userData['name']) ?>
                                                </td>
                                                <td>
                                                    <i class="fas fa-envelope me-2 text-muted"></i>
                                                    <?= htmlspecialchars($userData['email']) ?>
                                                </td>
                                                <td>
                                                    <span class="badge bg-<?= $userData['role'] === 'admin' ? 'danger' : 'primary' ?>">
                                                        <i class="fas fa-<?= $userData['role'] === 'admin' ? 'crown' : 'user' ?> me-1"></i>
                                                        <?= ucfirst(htmlspecialchars($userData['role'])) ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-plus me-1"></i>
                                                        <?= date('M d, Y H:i', strtotime($userData['created_at'])) ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="fas fa-calendar-edit me-1"></i>
                                                        <?= date('M d, Y H:i', strtotime($userData['updated_at'])) ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Total Users: <strong><?= count($allUsers) ?></strong>
                                </small>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">No Users Found</h5>
                                <p class="text-muted">There are currently no registered users in the system.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
