<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<style>
    /* Course Card Styling - Square Cards with Equal Size */
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
                <?php if (($showCourses ?? false) !== true && ($showCreateCourse ?? false) !== true): ?>
                <div class="card-header text-white" style="background-color: #2563EB;">
                    <h1 class="card-title mb-0">Teacher Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showCourses ?? false) === true): ?>
                        <div class="card border-primary" style="border-radius: 10px;">
                            <div class="card-header text-white" style="background-color: #2563EB; border-top-left-radius: 10px; border-top-right-radius: 10px;">
                                <h5 class="card-title mb-0">My Courses</h5>
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
                                
                                <!-- Search Bar for Teacher Courses -->
                                <div class="row mb-4">
                                    <div class="col-md-6">
                                        <form id="teacherCourseSearchForm" class="d-flex">
                                            <div class="input-group">
                                                <input type="text" 
                                                       class="form-control" 
                                                       id="teacherCourseSearchInput" 
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
                                <div class="row" id="teacherCoursesGrid">
                                        <?php if (!empty($courses ?? []) && count($courses) > 0): ?>
                                            <?php foreach ($courses as $course): ?>
                                                <div class="col-md-3 mb-4">
                                                <div class="card course-item teacher-course-item"
                                                     data-title="<?= esc(strtolower(trim($course['title'] ?? 'Untitled'))) ?>"
                                                     data-code="<?= esc(strtolower(trim($course['course_code'] ?? ''))) ?>"
                                                     data-description="<?= esc(strtolower(trim($course['short_description'] ?? $course['description'] ?? ''))) ?>">
                                                    <div class="card-body">
                                                        <h5 class="card-title"><?= esc($course['title'] ?? 'Untitled') ?></h5>
                                                        <p class="card-text"><?= esc($course['short_description'] ?? $course['description'] ?? 'No description available.') ?></p>
                                                        <?php if (!empty($course['course_code'])): ?>
                                                            <p class="text-muted"><small>Code: <?= esc($course['course_code']) ?></small></p>
                                                        <?php endif; ?>
                                                        <a href="<?= base_url('teacher/courses/view/' . ($course['id'] ?? '')) ?>" class="btn btn-primary">View Course</a>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                            <?php else: ?>
                                                <div class="col-12">
                                                    <div class="alert alert-info">No courses assigned yet.</div>
                                                </div>
                                            <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>

                        <!-- Dashboard cards removed as requested. Use the header nav 'My Courses' link instead. -->
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Script for Teacher Course Search -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Teacher Course Search - Filter courses as you type
    $('#teacherCourseSearchInput').on('keyup', function() {
        // Get the search value and convert to lowercase
        var searchValue = $(this).val().toLowerCase().trim();
        
        // Loop through each course card
        $('.teacher-course-item').each(function() {
            // Get the course title, code, and description from data attributes
            var courseTitle = $(this).data('title') || '';
            var courseCode = $(this).data('code') || '';
            var courseDescription = $(this).data('description') || '';
            
            // Check if search term matches title, code, or description
            var matches = courseTitle.indexOf(searchValue) > -1 || 
                         courseCode.indexOf(searchValue) > -1 || 
                         courseDescription.indexOf(searchValue) > -1 ||
                         searchValue === '';
            
            // Show or hide the card's parent column
            if (matches) {
                $(this).closest('.col-md-3').show();
            } else {
                $(this).closest('.col-md-3').hide();
            }
        });
    });
    
    // Prevent form submission for teacher course search
    $('#teacherCourseSearchForm').on('submit', function(e) {
        e.preventDefault();
        // The keyup event above handles the filtering
    });
});
</script>
<?= $this->endSection() ?>

