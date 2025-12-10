<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <!-- Search Form -->
            <form id="searchForm" class="d-flex">
                <div class="input-group">
                    <input type="text" 
                           class="form-control" 
                           id="searchInput" 
                           name="search_term" 
                           placeholder="Search courses...">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Courses Container -->
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
                <div class="alert alert-info">No courses available at the moment.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- jQuery Script for Search Functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.course-card').each(function() {
            const cardText = $(this).text().toLowerCase();
    
            $(this).closest('.col-md-4').toggle(cardText.indexOf(value) > -1);
        });
    });

    $('#searchForm').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#searchInput').val();
        $.get('<?= base_url('courses/search') ?>', { search_term: searchTerm }, function(data) {
            $('#coursesContainer').empty();
            
            if (data.length > 0) {
                $.each(data, function(index, course) {
                    const courseHtml = `
                        <div class="col-md-4 mb-4">
                            <div class="card course-card">
                                <div class="card-body">
                                    <h5 class="card-title">${course.title || 'Untitled Course'}</h5>
                                    <p class="card-text">${course.description || 'No description available.'}</p>
                                    ${course.course_code ? `<p class="text-muted"><small>Code: ${course.course_code}</small></p>` : ''}
                                    <a href="<?= base_url('courses/view/') ?>${course.id}" class="btn btn-primary">View Course</a>
                                </div>
                            </div>
                        </div>
                    `;
                    $('#coursesContainer').append(courseHtml);
                });
            } else {
                $('#coursesContainer').html(`
                    <div class="col-12">
                        <div class="alert alert-info">No courses found matching your search.</div>
                    </div>
                `);
            }
        }).fail(function() {
            
            $('#coursesContainer').html(`
                <div class="col-12">
                    <div class="alert alert-danger">Error loading search results. Please try again.</div>
                </div>
            `);
        });
    });
});
</script>
<?= $this->endSection() ?>

