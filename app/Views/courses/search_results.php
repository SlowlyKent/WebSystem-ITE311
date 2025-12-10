<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Search Results</h2>
            <?php if (!empty($searchTerm)): ?>
                <p>Searching for: <strong><?= esc($searchTerm) ?></strong></p>
            <?php endif; ?>
            <a href="<?= base_url('courses') ?>" class="btn btn-secondary">Back to All Courses</a>
        </div>
    </div>

    <div id="coursesContainer" class="row">
        <?php if (!empty($courses)): ?>
            <?php foreach ($courses as $course): ?>
                <div class="col-md-4 mb-4">
                    <div class="card course-card">
                        <div class="card-body">
                            <h5 class="card-title"><?= esc($course['title']) ?></h5>
                            <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                            <?php if (!empty($course['course_code'])): ?>
                                <p class="text-muted"><small>Code: <?= esc($course['course_code']) ?></small></p>
                            <?php endif; ?>
                            <a href="<?= base_url('courses/view/' . $course['id']) ?>" class="btn btn-primary">View Course</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-info">No courses found matching your search.</div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>


