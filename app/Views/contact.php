<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card" style="max-width: 600px; margin: 0 auto;">
    <h1>Contact Us</h1>
    <div>
        <h2>Contact Details</h2>
        <p><strong>Email:</strong> lms@yahoo.com</p>
        <p><strong>Facebook Page:</strong> lms_kent@.com</p>
        <p><strong>Instagram:</strong> lms_kent@.com</p>
        <p><strong>Phone:</strong> +63 222 333 4444</p>
        <p><strong>Address:</strong><br>Lun Masla, Malapatan, Sarangani Province<br>Philippines, Mindanao, Philippines</p>
        <div class="mt-3">
            <a href="<?= base_url('home') ?>" class="btn btn-outline">Back to Home</a>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
