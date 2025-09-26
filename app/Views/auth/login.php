<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="auth-form">
    <h2>Welcome Back</h2>
    <p style="text-align: center; color: #6B7280; margin-bottom: 1.5rem;">Sign in to continue your educational journey</p>
    
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>
    
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>
    
    <?php if(isset($validation)): ?>
        <div class="alert alert-danger">
            <?= $validation->listErrors() ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="post">
        <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="<?= set_value('email') ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
        </div>
        
        <button type="submit" class="btn btn-primary w-100">Sign In</button>
    </form>
    
    <div class="text-center">
        <p style="margin-bottom: 0.5rem;">New to our platform?</p>
        <a href="<?= site_url('register') ?>" style="color: #2563EB; text-decoration: none; font-weight: 600;">Create your account here</a>
    </div>
</div>
<?= $this->endSection() ?>
