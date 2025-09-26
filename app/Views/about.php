<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="card grid-2-260">
    <div>
        <h1>Welcome to Our Learning Platform</h1>
        <p>We craft intuitive digital solutions that empower educators to deliver exceptional teaching experiences while enabling students to achieve their academic goals efficiently.</p>
        
        <h2>What We Stand For</h2>
        <p>Our vision is to create an innovative educational ecosystem that bridges the gap between traditional learning and modern technology, making quality education accessible to everyone.</p>
        
        <h2>Core Principles</h2>
        <ul>
            <li><strong>Innovation</strong> — cutting-edge technology meets educational excellence</li>
            <li><strong>Accessibility</strong> — learning opportunities for all, regardless of location or background</li>
            <li><strong>Excellence</strong> — commitment to delivering outstanding educational experiences</li>
        </ul>
        
        <div class="mt-3">
            <a href="<?= base_url('contact') ?>" class="btn">Get in Touch</a>
            <a href="<?= base_url('home') ?>" class="btn btn-outline">Back to Home</a>
        </div>
    </div>
    <div>
        <div class="stack-gap">
            <div class="tile">
                <h2>Student Success</h2>
                <p>Comprehensive tools for monitoring academic progress, accessing resources, and achieving learning milestones.</p>
            </div>
            <div class="tile">
                <h2>Educator Empowerment</h2>
                <p>Advanced features for designing engaging lessons, managing assessments, and fostering student engagement.</p>
            </div>
            <div class="tile">
                <h2>Collaborative Learning</h2>
                <p>Build meaningful connections between students and teachers through interactive discussions and shared learning experiences.</p>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
