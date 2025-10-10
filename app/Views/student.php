<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h1 class="card-title mb-0">Student Dashboard</h1>
                </div>
                <div class="card-body">
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>

                    <div class="row g-4 mb-4">
                        <!-- Enrolled Courses -->
                        <div class="col-12">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0">Enrolled Courses</h5>
                                </div>
                                <div class="card-body">
                                    <ul class="list-group list-group-flush" id="enrolled-list">
                                        <?php if (!empty($enrolledCourses)): ?>
                                            <?php foreach ($enrolledCourses as $course): ?>
                                                <li class="list-group-item" data-course-id="<?= $course['id'] ?>">
                                                    <strong><?= esc($course['title']) ?></strong><br>
                                                    <small class="text-muted"><?= esc($course['description']) ?></small>
                                                </li>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <li class="list-group-item text-muted" data-placeholder="1">No enrolled courses yet.</li>
                                        <?php endif; ?>
                                    </ul>
                                </div>
                        </div>

                        
                    </div>

                    <!-- Available Courses -->
                    <div class="card border-secondary mb-4">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="card-title mb-0">Available Courses</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($availableCourses)): ?>
                                <?php $enrolledIds = !empty($enrolledCourses) ? array_column($enrolledCourses, 'id') : []; ?>
                                <ul class="list-group">
                                    <?php foreach ($availableCourses as $course): ?>
                                        <?php $already = in_array($course['id'], $enrolledIds ?? [], true); ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center" data-course-id="<?= $course['id'] ?>">
                                            <div>
                                                <strong><?= esc($course['title']) ?></strong><br>
                                                <small class="text-muted"><?= esc($course['description']) ?></small>
                                            </div>
                                            <button class="btn btn-sm <?= $already ? 'btn-secondary' : 'btn-outline-primary' ?> enroll-btn"
                                                    data-course-id="<?= $course['id'] ?>"
                                                    <?= $already ? 'disabled aria-disabled="true"' : '' ?>>
                                                <?= $already ? 'Enrolled' : 'Enroll' ?>
                                            </button>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            <?php else: ?>
                                <p class="text-muted">No available courses.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery + AJAX Enrollment -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // CSRF setup
    let csrfTokenName = '<?= csrf_token() ?>';
    let csrfHash = '<?= csrf_hash() ?>';

    $('.enroll-btn').click(function (e) {
        e.preventDefault();

        const button = $(this);
        const courseId = button.data('course-id');

        const payload = { course_id: courseId };
        payload[csrfTokenName] = csrfHash;

        // Optimistic UI: disable button and add to enrolled list immediately
        const li = button.closest('li');
        const cid = li.data('course-id');
        const wasDisabled = button.prop('disabled');
        const originalText = button.text();
        button.prop('disabled', true)
              .attr('aria-disabled', 'true')
              .removeClass('btn-outline-primary')
              .addClass('btn-secondary')
              .text('Enrolling…');

        let appendedTemp = false;
        // Remove placeholder if present
        $("#enrolled-list li[data-placeholder='1']").remove();
        if ($(`#enrolled-list li[data-course-id="${cid}"]`).length === 0) {
            $('#enrolled-list').append(`
                <li class="list-group-item" data-course-id="${cid}" data-temp="1">
                    <strong>${li.find('strong').text()}</strong><br>
                    <small class="text-muted">${li.find('small').text()}</small>
                </li>
            `);
            appendedTemp = true;
        }

        $.post('<?= site_url('course/enroll') ?>', payload, function (response) {
            const alertBox = `
                <div class="alert alert-${response.status === 'success' ? 'success' : 'danger'} alert-dismissible fade show mt-3" role="alert">
                    ${response.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;

            button.closest('.card-body').prepend(alertBox);

            const isAlready = response.status === 'error' && /Already enrolled/i.test(response.message || '');
            if (response.status === 'success' || isAlready) {
                // Finalize optimistic UI
                button.prop('disabled', true)
                      .attr('aria-disabled', 'true')
                      .removeClass('btn-outline-primary')
                      .addClass('btn-secondary')
                      .text('Enrolled');
                // Remove temp flag if present
                $("#enrolled-list li[data-course-id='" + cid + "'][data-temp='1']").removeAttr('data-temp');
            } else {
                // Unexpected non-success: revert UI
                if (appendedTemp) {
                    $("#enrolled-list li[data-course-id='" + cid + "'][data-temp='1']").remove();
                }
                button.prop('disabled', wasDisabled)
                      .attr('aria-disabled', wasDisabled ? 'true' : 'false')
                      .removeClass('btn-secondary')
                      .addClass('btn-outline-primary')
                      .text(originalText);
            }

            // Refresh CSRF from server response if provided
            if (response.csrf && response.csrf.token && response.csrf.hash) {
                csrfTokenName = response.csrf.token;
                csrfHash = response.csrf.hash;
            }
        }).fail(function (xhr) {
            const msg = xhr.responseJSON?.message || `Request failed (${xhr.status})`;
            const alertBox = `
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    ${msg}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>`;
            button.closest('.card-body').prepend(alertBox);

            // Revert optimistic UI on failure
            if (appendedTemp) {
                const cid = button.closest('li').data('course-id');
                $("#enrolled-list li[data-course-id='" + cid + "'][data-temp='1']").remove();
            }
            button.prop('disabled', wasDisabled)
                  .attr('aria-disabled', wasDisabled ? 'true' : 'false')
                  .removeClass('btn-secondary')
                  .addClass('btn-outline-primary')
                  .text(originalText);
        });
    });
});
</script>
<?= $this->endSection() ?>
