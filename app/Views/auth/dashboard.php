<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    
    <!-- Welcome Message -->


    <?php
      // Wrapper: load role-specific partials
      $role = $user['role'] ?? session('role');

      switch ($role) {
        case 'admin':
          echo view('admin', ['user' => $user]);
          break;
        case 'teacher':
          echo view('teacher', ['user' => $user]);
          break;
        case 'student':
          echo view('student', ['user' => $user]);
          break;
        default:
          echo '<div class="alert alert-warning mt-3">Role not recognized.</div>';
          break;
      }
    ?>
</div>
<?= $this->endSection() ?>