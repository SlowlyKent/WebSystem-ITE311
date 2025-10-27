<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title mb-0">Web System and Development - Materials</h3>
                </div>
                <div class="card-body">
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