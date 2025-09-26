<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="hero">
    <h1>Empower Your Educational Journey</h1>
    <p>Transform the way you learn and teach with our cutting-edge digital platform that connects students and educators in a seamless, innovative learning environment</p>
    
    <div class="mt-3">
        <a href="<?= base_url('about') ?>" class="btn">Discover More</a>
        <a href="<?= base_url('contact') ?>" class="btn btn-outline">Get Started</a>
    </div>
</div>
<?= $this->endSection() ?>
