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
                <?php if (($showEnrollments ?? false) !== true): ?>
                <div class="card-header bg-info text-white">
                    <h1 class="card-title mb-0">Student Dashboard</h1>
                </div>
                <?php endif; ?>
                <div class="card-body">
                    <?php if (($showEnrollments ?? false) !== true): ?>
                    <p class="lead">Welcome, <strong><?= esc($user['name']) ?></strong>!</p>
                    <?php endif; ?>

                    <?php if (($showEnrollments ?? false) === true): ?>
                        <!-- Search Bar -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <form class="d-flex">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="form-control" 
                                               id="studentCourseSearchInput" 
                                               placeholder="Search courses...">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Enrolled Courses Section -->
                        <div class="mb-4">
                            <h5 class="mb-3">Enrolled Courses</h5>
                            <div class="row" id="enrolledCoursesGrid">
                                    <?php if (!empty($enrolledCourses)): ?>
                                        <?php foreach ($enrolledCourses as $course): ?>
                                            <div class="col-md-3 mb-4">
                                            <div class="card course-item enrolled-course-item" 
                                                 data-course-id="<?= $course['id'] ?>"
                                                 data-title="<?= esc(strtolower($course['title'])) ?>"
                                                 data-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                    <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                                    <?php if (!empty($course['course_code'])): ?>
                                                        <p class="text-muted"><small>Code: <?= esc($course['course_code']) ?></small></p>
                                                    <?php endif; ?>
                                                    <a href="<?= base_url('student/courses/view/' . $course['id']) ?>" class="btn btn-primary">View Course</a>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">No enrolled courses yet.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Available Courses Section -->
                        <div class="mb-4">
                            <h5 class="mb-3">Available Courses</h5>
                            <div class="row" id="availableCoursesGrid">
                                <?php if (!empty($availableCourses)): ?>
                                    <?php $enrolledIds = !empty($enrolledCourses) ? array_column($enrolledCourses, 'id') : []; ?>
                                    <?php foreach ($availableCourses as $course): ?>
                                        <?php $already = in_array($course['id'], $enrolledIds ?? [], true); ?>
                                        <div class="col-md-3 mb-4">
                                            <div class="card course-item available-course-item" 
                                                 data-course-id="<?= $course['id'] ?>"
                                                 data-title="<?= esc(strtolower($course['title'])) ?>"
                                                 data-description="<?= esc(strtolower($course['description'] ?? '')) ?>">
                                                <div class="card-body">
                                                    <h5 class="card-title"><?= esc($course['title']) ?></h5>
                                                    <p class="card-text"><?= esc($course['description'] ?? 'No description available.') ?></p>
                                                    <?php if (!empty($course['course_code'])): ?>
                                                        <p class="text-muted"><small>Code: <?= esc($course['course_code']) ?></small></p>
                                                    <?php endif; ?>
                                                    <button class="btn <?= $already ? 'btn-secondary' : 'btn-primary' ?> enroll-btn w-100"
                                                            data-course-id="<?= $course['id'] ?>"
                                                            <?= $already ? 'disabled aria-disabled="true"' : '' ?>>
                                                        <?= $already ? 'Enrolled' : 'Enroll' ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <div class="alert alert-info">No available courses.</div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (($showEnrollments ?? false) === true): ?>
<!-- jQuery + AJAX Enrollment -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // Student Course Search - Filter all courses as you type
    $('#studentCourseSearchInput').on('keyup', function() {
        // Get the search value and convert to lowercase
        var searchValue = $(this).val().toLowerCase().trim();
        
        // Filter enrolled courses
        $('.enrolled-course-item').each(function() {
            var courseTitle = $(this).data('title') || '';
            var courseDescription = $(this).data('description') || '';
            var matches = courseTitle.indexOf(searchValue) > -1 || 
                         courseDescription.indexOf(searchValue) > -1 || 
                         searchValue === '';
            
            if (matches) {
                $(this).closest('.col-md-3').show();
            } else {
                $(this).closest('.col-md-3').hide();
            }
        });
        
        // Filter available courses
        $('.available-course-item').each(function() {
            var courseTitle = $(this).data('title') || '';
            var courseDescription = $(this).data('description') || '';
            var matches = courseTitle.indexOf(searchValue) > -1 || 
                         courseDescription.indexOf(searchValue) > -1 || 
                         searchValue === '';
            
            if (matches) {
                $(this).closest('.col-md-3').show();
            } else {
                $(this).closest('.col-md-3').hide();
            }
        });
    });
    
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
        const card = button.closest('.course-item');
        const cid = card.data('course-id');
        const courseTitle = card.find('.card-title').text();
        const courseDescription = card.find('.card-text').text();
        const courseCode = card.find('small').text() || '';
        const wasDisabled = button.prop('disabled');
        const originalText = button.text();
        button.prop('disabled', true)
              .attr('aria-disabled', 'true')
              .removeClass('btn-primary')
              .addClass('btn-secondary')
              .text('Enrolling…');

        let appendedTemp = false;
        // Check if course is already in enrolled list
        if ($(`#enrolledCoursesGrid .enrolled-course-item[data-course-id="${cid}"]`).length === 0) {
            const newCard = `
                <div class="col-md-3 mb-4">
                    <div class="card course-item enrolled-course-item" 
                         data-course-id="${cid}"
                         data-title="${courseTitle.toLowerCase()}"
                         data-description="${courseDescription.toLowerCase()}"
                         data-temp="1">
                        <div class="card-body">
                            <h5 class="card-title">${courseTitle}</h5>
                            <p class="card-text">${courseDescription}</p>
                            ${courseCode ? `<p class="text-muted"><small>${courseCode}</small></p>` : ''}
                            <a href="<?= base_url('student/courses/view/') ?>${cid}" class="btn btn-primary">View Course</a>
                        </div>
                    </div>
                </div>
            `;
            $('#enrolledCoursesGrid').append(newCard);
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
                // Remove course from Available Courses grid
                card.closest('.col-md-3').fadeOut(300, function() {
                    $(this).remove();
                });
                
                // Remove temp item if exists and add proper one
                $("#enrolledCoursesGrid .enrolled-course-item[data-course-id='" + cid + "'][data-temp='1']").closest('.col-md-3').remove();
                
                // Add course to enrolled courses grid with proper card
                const enrolledCard = `
                    <div class="col-md-3 mb-4">
                        <div class="card course-item enrolled-course-item" 
                             data-course-id="${cid}"
                             data-title="${courseTitle.toLowerCase()}"
                             data-description="${courseDescription.toLowerCase()}">
                            <div class="card-body">
                                <h5 class="card-title">${courseTitle}</h5>
                                <p class="card-text">${courseDescription}</p>
                                ${courseCode ? `<p class="text-muted"><small>${courseCode}</small></p>` : ''}
                                <a href="<?= base_url('student/courses/view/') ?>${cid}" class="btn btn-primary">View Course</a>
                            </div>
                        </div>
                    </div>
                `;
                $('#enrolledCoursesGrid').append(enrolledCard);
                
                // Reload notifications to show new enrollment notification
                // Wait a bit for server to create notification, then refresh
                setTimeout(function() {
                    // Try to call loadNotifications function (from template.php)
                    if (typeof loadNotifications === 'function') {
                        loadNotifications();
                    } else {
                        // Fallback: directly update notifications via AJAX
                        $.get('<?= base_url('notifications') ?>', function(response) {
                            if (response.success) {
                                const unreadCount = parseInt(response.unread) || 0;
                                const notifications = response.notifications || [];
                                
                                // Update badge
                                const badgeElement = $('#notification-badge');
                                if (unreadCount > 0) {
                                    badgeElement.text(unreadCount).show();
                                } else {
                                    badgeElement.hide();
                                    badgeElement.text('0');
                                }
                                
                                // Update notification list
                                const notificationList = $('#notification-list');
                                notificationList.find('li:not(:first):not(:nth-child(2))').remove();
                                
                                if (notifications.length === 0) {
                                    notificationList.append('<li class="text-center py-3 text-muted" id="no-notifications">No notifications</li>');
                                } else {
                                    notifications.forEach(function(notif) {
                                        const isUnread = notif.is_read == 0;
                                        const alertClass = isUnread ? 'alert alert-info' : 'alert alert-secondary';
                                        const boldClass = isUnread ? 'fw-bold' : '';
                                        const buttonHtml = isUnread 
                                            ? `<button class="btn btn-sm btn-primary mark-read-btn ms-2" data-id="${notif.id}" style="white-space: nowrap;">Mark as Read</button>`
                                            : `<span class="badge bg-secondary ms-2">Read</span>`;
                                        
                                        const notifItem = `
                                            <li class="dropdown-item p-0" data-id="${notif.id}">
                                                <div class="${alertClass} mb-2 mx-2" style="border-radius: 6px;">
                                                    <div class="d-flex justify-content-between align-items-start">
                                                        <div class="flex-grow-1">
                                                            <p class="mb-1 ${boldClass}" style="font-size: 0.9rem; margin: 0;">${notif.message}</p>
                                                            <small class="text-muted">${new Date(notif.created_at).toLocaleString()}</small>
                                                        </div>
                                                        ${buttonHtml}
                                                    </div>
                                                </div>
                                            </li>
                                        `;
                                        notificationList.append(notifItem);
                                    });
                                }
                            }
                        });
                    }
                }, 800); // Wait 800ms for server to create notification
            } else {
                // Unexpected non-success: revert UI
                if (appendedTemp) {
                    $("#enrolledCoursesGrid .enrolled-course-item[data-course-id='" + cid + "'][data-temp='1']").closest('.col-md-3').remove();
                }
                button.prop('disabled', wasDisabled)
                      .attr('aria-disabled', wasDisabled ? 'true' : 'false')
                      .removeClass('btn-secondary')
                      .addClass('btn-primary')
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
                const cid = button.closest('.course-item').data('course-id');
                $("#enrolledCoursesGrid .enrolled-course-item[data-course-id='" + cid + "'][data-temp='1']").closest('.col-md-3').remove();
            }
            button.prop('disabled', wasDisabled)
                  .attr('aria-disabled', wasDisabled ? 'true' : 'false')
                  .removeClass('btn-secondary')
                  .addClass('btn-primary')
                  .text(originalText);
        });
    });
});
</script>
<?php endif; ?>
<?= $this->endSection() ?>
