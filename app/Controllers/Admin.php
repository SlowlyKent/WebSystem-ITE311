<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CourseModel;
use App\Models\UserModel;

class Admin extends Controller
{
    public function dashboard()
    {
        // Check if user is logged in and is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Admin Dashboard',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];

        return view('admin', $data);
    }

    public function courses()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $courseModel = new CourseModel();
        $courses = $courseModel->findAll();

        $data = [
            'title'   => 'Course Management',
            'user'    => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ],
            'courses' => $courses,
        ];

        // Render Course Management inside the admin dashboard view
        $data['showCourses'] = true;
        return view('admin', $data);
    }

    public function createCourse()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $courseModel = new CourseModel();
        
        // Get all teachers for the instructor dropdown
        $teachers = $userModel->where('role', 'teacher')->findAll();
        
        // Get all existing courses for prerequisites dropdown
        $allCourses = $courseModel->findAll();

        $data = [
            'title'   => 'Create Course',
            'user'    => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ],
            'teachers' => $teachers,
            'allCourses' => $allCourses
        ];

        $data['showCreateCourse'] = true;
        return view('admin', $data);
    }

    public function storeCourse()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $courseModel = new CourseModel();
        
        // Get all form data
        $title = $this->request->getPost('title');
        $courseCode = $this->request->getPost('course_code');
        $description = $this->request->getPost('description');
        $shortDescription = $this->request->getPost('short_description');
        $yearLevel = $this->request->getPost('year_level');
        $semester = $this->request->getPost('semester');
        $schoolYear = $this->request->getPost('school_year');
        $department = $this->request->getPost('department');
        $instructorId = $this->request->getPost('instructor_id');
        $instructorName = $this->request->getPost('instructor_name');
        $instructorEmail = $this->request->getPost('instructor_email');
        
        // Validate instructor email format if provided (additional security layer)
        if (!empty($instructorEmail) && !filter_var($instructorEmail, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('error', 'Invalid instructor email format.');
            return redirect()->to(base_url('admin/courses/create'));
        }
        
        $units = $this->request->getPost('units');
        $courseCategory = $this->request->getPost('course_category');
        $section = $this->request->getPost('section');
        $startDate = $this->request->getPost('start_date');
        $startTime = $this->request->getPost('start_time');
        $endTime = $this->request->getPost('end_time');
        $endDate = $this->request->getPost('end_date');
        $enrollmentLimit = $this->request->getPost('enrollment_limit');
        $gradingScheme = $this->request->getPost('grading_scheme');
        
        // Always set status to Active and allow_self_enrollment to Yes (1)
        $status = 'Active';
        $allowSelfEnrollment = 1;
        $prerequisiteCourses = $this->request->getPost('prerequisite_courses');

        // Basic validation
        if (empty($title)) {
            session()->setFlashdata('error', 'Course name is required.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        if (empty($courseCode)) {
            session()->setFlashdata('error', 'Course code is required.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        if (empty($description)) {
            session()->setFlashdata('error', 'Course description is required.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        // Validate time fields are provided
        if (empty($startTime)) {
            session()->setFlashdata('error', 'Start time is required.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        if (empty($endTime)) {
            session()->setFlashdata('error', 'End time is required.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        // Validate that end time is after start time
        if ($endTime <= $startTime) {
            session()->setFlashdata('error', 'End time must be after start time.');
            return redirect()->to(base_url('admin/courses/create'));
        }

        // Step 1: Check for time conflict
        // Rule: If course_code AND title are the same AND times overlap OR exact same = REJECT
        // Rule: If course_code is same but title is different OR times don't overlap = ALLOW
        
        // Get all existing courses with the same course code (case-insensitive comparison)
        $existingCoursesWithSameCode = $courseModel->where('course_code', $courseCode)->findAll();
        
        // Check each existing course for conflicts
        foreach ($existingCoursesWithSameCode as $existingCourse) {
            $existingTitle = trim($existingCourse['title'] ?? '');
            $existingStartTime = $existingCourse['start_time'] ?? '';
            $existingEndTime = $existingCourse['end_time'] ?? '';
            
            // Skip if existing course doesn't have time set
            if (empty($existingStartTime) || empty($existingEndTime)) {
                continue; // No time conflict if existing course has no time
            }
            
            // Check if course name (title) is the same (case-insensitive, trimmed)
            $titleTrimmed = trim($title);
            if (strtolower($existingTitle) === strtolower($titleTrimmed)) {
                // Same course code AND same name - check if times are exact same OR overlap
                
                // First check: Are times exactly the same?
                if ($startTime === $existingStartTime && $endTime === $existingEndTime) {
                    // Exact same time - this is a conflict!
                    $existingTimeDisplay = date('g:i A', strtotime($existingStartTime)) . ' - ' . date('g:i A', strtotime($existingEndTime));
                    session()->setFlashdata('error', 'Time conflict detected! Course "' . $title . '" with code "' . $courseCode . '" already exists with the exact same time: ' . $existingTimeDisplay . '. Please use a different time or course name.');
                    return redirect()->to(base_url('admin/courses/create'));
                }
                
                // Second check: Do times overlap?
                if ($this->checkTimeOverlap($startTime, $endTime, $existingStartTime, $existingEndTime)) {
                    // Times overlap - this is a conflict!
                    $existingTimeDisplay = date('g:i A', strtotime($existingStartTime)) . ' - ' . date('g:i A', strtotime($existingEndTime));
                    $newTimeDisplay = date('g:i A', strtotime($startTime)) . ' - ' . date('g:i A', strtotime($endTime));
                    session()->setFlashdata('error', 'Time conflict detected! Course "' . $title . '" with code "' . $courseCode . '" already exists with time ' . $existingTimeDisplay . '. Your time: ' . $newTimeDisplay . '. Please use a different time or course name.');
                    return redirect()->to(base_url('admin/courses/create'));
                }
            }
            // If course code is same but name is different, it's allowed (different course)
            // If course code is same, name is same, but times don't overlap, it's allowed
        }

        // Prepare data to save
        $data = [
            'title' => $title,
            'course_code' => $courseCode,
            'description' => $description,
            'short_description' => !empty($shortDescription) ? $shortDescription : null,
            'year_level' => !empty($yearLevel) ? $yearLevel : null,
            'semester' => !empty($semester) ? $semester : null,
            'school_year' => !empty($schoolYear) ? $schoolYear : null,
            'department' => !empty($department) ? $department : null,
            'instructor_id' => !empty($instructorId) ? $instructorId : null,
            'instructor_name' => !empty($instructorName) ? $instructorName : null,
            'instructor_email' => !empty($instructorEmail) ? $instructorEmail : null, // Already validated above
            'units' => !empty($units) ? $units : null,
            'course_category' => !empty($courseCategory) ? $courseCategory : null,
            'section' => !empty($section) ? $section : null,
            'start_date' => !empty($startDate) ? $startDate : null,
            'start_time' => !empty($startTime) ? $startTime : null,
            'end_time' => !empty($endTime) ? $endTime : null,
            'end_date' => !empty($endDate) ? $endDate : null,
            'enrollment_limit' => !empty($enrollmentLimit) ? (int)$enrollmentLimit : null,
            'status' => 'Active', // Always set to Active
            'allow_self_enrollment' => 1, // Always set to Yes (1)
            'grading_scheme' => !empty($gradingScheme) ? $gradingScheme : null
        ];

        // Save to database
        if ($courseModel->insert($data)) {
            $newCourseId = $courseModel->getInsertID();
            
            // Handle prerequisites (many-to-many relationship)
            if (!empty($prerequisiteCourses) && is_array($prerequisiteCourses)) {
                $db = \Config\Database::connect();
                foreach ($prerequisiteCourses as $prereqId) {
                    $prereqId = (int)$prereqId;
                    if ($prereqId > 0 && $prereqId != $newCourseId) {
                        $db->table('course_prerequisites')->insert([
                            'course_id' => $newCourseId,
                            'prerequisite_course_id' => $prereqId,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }
            
            // Create notification for all users about new course
            try {
                $notificationModel = new \App\Models\NotificationModel();
                $userName = session()->get('name') ?? 'Admin';
                $adminId = (int) session()->get('user_id');
                
                // Notify the admin who created the course
                if ($adminId > 0) {
                    $notificationModel->insert([
                        'user_id' => $adminId,
                        'message' => 'You have successfully created a new course: ' . $title . ' (' . $courseCode . ')',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
                
                // Notify all students about the new course
                $db = \Config\Database::connect();
                $students = $db->table('users')->select('id')->where('role', 'student')->get()->getResultArray();
                foreach ($students as $student) {
                    $studentId = (int)($student['id'] ?? 0);
                    if ($studentId > 0) {
                        $notificationModel->insert([
                            'user_id' => $studentId,
                            'message' => 'New course available: ' . $title . ' (' . $courseCode . ')',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                // Notify all teachers
                $teachers = $db->table('users')->select('id')->where('role', 'teacher')->get()->getResultArray();
                foreach ($teachers as $teacher) {
                    $teacherId = (int)($teacher['id'] ?? 0);
                    if ($teacherId > 0) {
                        $notificationModel->insert([
                            'user_id' => $teacherId,
                            'message' => $userName . ' created a new course: ' . $title . ' (' . $courseCode . ')',
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                // Log error but don't fail the course creation
                log_message('error', 'Failed to create course notification: ' . $e->getMessage());
            }
            
            session()->setFlashdata('success', 'Course created successfully.');
            return redirect()->to(base_url('admin/courses'));
        } else {
            $errors = $courseModel->errors();
            session()->setFlashdata('error', 'Failed to create course. ' . implode(', ', $errors));
            return redirect()->to(base_url('admin/courses/create'));
        }
    }

    public function users()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        // Get all users from the database
        $users = $userModel->findAll();

        $data = [
            'title'   => 'User Management',
            'user'    => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ],
            'users' => $users,
        ];

        // Render User Management inside the admin dashboard view
        $data['showUsers'] = true;
        return view('admin', $data);
    }

    public function createUser()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title'   => 'Create User',
            'user'    => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ],
        ];

        $data['showCreateUser'] = true;
        return view('admin', $data);
    }

    public function storeUser()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        
        // Get and validate email format using filter_var (additional security layer)
        $email = $this->request->getPost('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('error', 'Invalid email format.');
            return redirect()->to(base_url('admin/users/create'));
        }
        
        // Validate password format - only alphanumeric characters allowed
        $password = $this->request->getPost('password');
        if (!ctype_alnum($password)) {
            session()->setFlashdata('error', 'Password can only contain letters and numbers. No special characters allowed.');
            return redirect()->to(base_url('admin/users/create'));
        }
        
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $email, // Use validated email
            'password' => $password, // Use validated password
            'role' => $this->request->getPost('role'),
            'status' => 'active' // New users are active by default
        ];

        // Validate role - allow admin, teacher, or student
        if (!in_array($data['role'], ['admin', 'teacher', 'student'])) {
            session()->setFlashdata('error', 'Invalid role. Role must be admin, teacher, or student.');
            return redirect()->to(base_url('admin/users/create'));
        }

        // Validate password confirmation
        if ($data['password'] !== $this->request->getPost('password_confirm')) {
            session()->setFlashdata('error', 'Passwords do not match.');
            return redirect()->to(base_url('admin/users/create'));
        }

        // CodeIgniter uses prepared statements automatically through Model
        if ($userModel->insert($data)) {
            // Create notification for admin who created the account
            try {
                $notificationModel = new \App\Models\NotificationModel();
                $adminId = (int) session()->get('user_id');
                $adminName = session()->get('name') ?? 'Admin';
                $newUserName = $data['name'];
                $newUserRole = ucfirst($data['role']);
                
                if ($adminId > 0) {
                    $notificationModel->insert([
                        'user_id' => $adminId,
                        'message' => 'You have successfully created a new ' . $newUserRole . ' account: ' . $newUserName . ' (' . $data['email'] . ')',
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            } catch (\Throwable $e) {
                // Log error but don't fail the user creation
                log_message('error', 'Failed to create account creation notification: ' . $e->getMessage());
            }
            
            session()->setFlashdata('success', 'User created successfully.');
            return redirect()->to(base_url('admin/users'));
        } else {
            $errors = $userModel->errors();
            session()->setFlashdata('error', implode(', ', $errors));
            return redirect()->to(base_url('admin/users/create'));
        }
    }

    public function editUser($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        // Prevent editing your own account
        $currentUserId = session()->get('user_id');
        if ($id == $currentUserId) {
            session()->setFlashdata('error', 'You cannot edit your own account.');
            return redirect()->to(base_url('admin/users'));
        }

        // Allow editing all other users (admin, teacher, and student)

        $data = [
            'title'   => 'Edit User',
            'user'    => [
                'name'  => session()->get('name'),
                'email' => session()->get('email'),
                'role'  => session()->get('role')
            ],
            'editUser' => $user,
        ];

        $data['showEditUser'] = true;
        return view('admin', $data);
    }

    public function updateUser($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        // Prevent updating your own account
        $currentUserId = session()->get('user_id');
        if ($id == $currentUserId) {
            session()->setFlashdata('error', 'You cannot update your own account.');
            return redirect()->to(base_url('admin/users'));
        }

        // Allow updating all other users (admin, teacher, and student)

        // Get and validate email format using filter_var (additional security layer)
        $email = $this->request->getPost('email');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            session()->setFlashdata('error', 'Invalid email format.');
            return redirect()->to(base_url('admin/users/edit/' . $id));
        }

        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $email, // Use validated email
            'role' => $this->request->getPost('role')
        ];

        // Validate role - allow admin, teacher, or student
        if (!in_array($data['role'], ['admin', 'teacher', 'student'])) {
            session()->setFlashdata('error', 'Invalid role. Role must be admin, teacher, or student.');
            return redirect()->to(base_url('admin/users/edit/' . $id));
        }

        // Update password only if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            // Validate password format - only alphanumeric characters allowed
            if (!ctype_alnum($password)) {
                session()->setFlashdata('error', 'Password can only contain letters and numbers. No special characters allowed.');
                return redirect()->to(base_url('admin/users/edit/' . $id));
            }
            
            if ($password !== $this->request->getPost('password_confirm')) {
                session()->setFlashdata('error', 'Passwords do not match.');
                return redirect()->to(base_url('admin/users/edit/' . $id));
            }
            $data['password'] = $password;
        } else {
            // Remove password from data if not provided to avoid validation issues
            unset($data['password']);
        }

        // Get the current logged-in admin's ID
        $currentUserId = session()->get('user_id');
        
        // Add updated_by field to track who updated this user
        $data['updated_by'] = $currentUserId;
        
        // Set validation rules - password is optional for updates
        // Use permissiveRules to allow updates without password
        $validationRules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'email' => 'required|valid_email|is_unique[users.email,id,' . $id . ']',
            'role' => 'required|in_list[admin,teacher,student]'
        ];
        
        // If password is provided, add password validation
        if (isset($data['password'])) {
            $validationRules['password'] = 'min_length[6]|alpha_numeric';
        }
        
        $userModel->setValidationRules($validationRules);
        
        if ($userModel->update($id, $data)) {
            session()->setFlashdata('success', 'User updated successfully.');
            return redirect()->to(base_url('admin/users'));
        } else {
            $errors = $userModel->errors();
            session()->setFlashdata('error', implode(', ', $errors));
            return redirect()->to(base_url('admin/users/edit/' . $id));
        }
    }

    public function deleteUser($id)
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        // Prevent deleting yourself (the currently logged-in admin)
        $currentUserId = session()->get('user_id');
        if ($id == $currentUserId) {
            session()->setFlashdata('error', 'You cannot delete your own account.');
            return redirect()->to(base_url('admin/users'));
        }

        // Delete the user
        if ($userModel->delete($id)) {
            session()->setFlashdata('success', 'User deleted successfully.');
        } else {
            session()->setFlashdata('error', 'Failed to delete user.');
        }

        return redirect()->to(base_url('admin/users'));
    }

    // Activate user account
    public function activateUser($id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        // Check if user exists
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        // Prevent activating/deactivating yourself
        $currentUserId = session()->get('user_id');
        if ($id == $currentUserId) {
            session()->setFlashdata('error', 'You cannot change your own account status.');
            return redirect()->to(base_url('admin/users'));
        }

        // Update user status to active
        $data = ['status' => 'active'];
        if ($userModel->update($id, $data)) {
            session()->setFlashdata('success', 'User activated successfully. User can now login.');
        } else {
            session()->setFlashdata('error', 'Failed to activate user.');
        }

        return redirect()->to(base_url('admin/users'));
    }

    // Inactivate user account
    public function inactivateUser($id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userModel = new UserModel();
        $user = $userModel->find($id);

        // Check if user exists
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/users'));
        }

        // Prevent activating/deactivating yourself
        $currentUserId = session()->get('user_id');
        if ($id == $currentUserId) {
            session()->setFlashdata('error', 'You cannot change your own account status.');
            return redirect()->to(base_url('admin/users'));
        }

        // Update user status to inactive
        $data = ['status' => 'inactive'];
        if ($userModel->update($id, $data)) {
            session()->setFlashdata('success', 'User inactivated successfully. User cannot login now.');
        } else {
            session()->setFlashdata('error', 'Failed to inactivate user.');
        }

        return redirect()->to(base_url('admin/users'));
    }

    // View student enrollment details
    public function viewEnrollment($enrollment_id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        $userModel = new UserModel();
        $courseModel = new CourseModel();

        // Get enrollment details with student and course info
        $enrollment = $enrollmentModel->select('enrollments.*, users.name, users.email, users.role, users.status as user_status, courses.title as course_title, courses.course_code')
                                      ->join('users', 'users.id = enrollments.user_id')
                                      ->join('courses', 'courses.id = enrollments.course_id')
                                      ->where('enrollments.id', $enrollment_id)
                                      ->first();

        if (!$enrollment) {
            session()->setFlashdata('error', 'Enrollment not found.');
            return redirect()->back();
        }

        $data = [
            'title' => 'View Enrollment',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ],
            'enrollment' => $enrollment
        ];

        // Return JSON for AJAX or view for direct access
        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'status' => 'success',
                'enrollment' => $enrollment
            ]);
        }

        // For direct access, show a simple view
        return view('admin/enrollment_view', $data);
    }

    // Delete student enrollment (remove from course)
    public function deleteEnrollment($enrollment_id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        // Get enrollment details before deleting
        $enrollment = $enrollmentModel->select('enrollments.*, users.name, courses.title as course_title, courses.id as course_id')
                                      ->join('users', 'users.id = enrollments.user_id')
                                      ->join('courses', 'courses.id = enrollments.course_id')
                                      ->where('enrollments.id', $enrollment_id)
                                      ->first();

        if (!$enrollment) {
            session()->setFlashdata('error', 'Enrollment not found.');
            return redirect()->back();
        }

        $courseId = $enrollment['course_id'];
        $studentName = $enrollment['name'] ?? 'Student';
        $courseTitle = $enrollment['course_title'] ?? 'Course';

        // Delete the enrollment
        if ($enrollmentModel->delete($enrollment_id)) {
            session()->setFlashdata('success', 'Student "' . $studentName . '" has been removed from course "' . $courseTitle . '".');
            
            // Redirect back to course view
            return redirect()->to(base_url('admin/courses/view/' . $courseId));
        } else {
            session()->setFlashdata('error', 'Failed to delete enrollment.');
            return redirect()->back();
        }
    }

    // Activate student enrollment (allow access to course)
    public function activateEnrollment($enrollment_id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        // Get enrollment details
        $enrollment = $enrollmentModel->select('enrollments.*, users.name, courses.title as course_title, courses.id as course_id')
                                      ->join('users', 'users.id = enrollments.user_id')
                                      ->join('courses', 'courses.id = enrollments.course_id')
                                      ->where('enrollments.id', $enrollment_id)
                                      ->first();

        if (!$enrollment) {
            session()->setFlashdata('error', 'Enrollment not found.');
            return redirect()->back();
        }

        $courseId = $enrollment['course_id'];
        $studentName = $enrollment['name'] ?? 'Student';
        $courseTitle = $enrollment['course_title'] ?? 'Course';

        // Update enrollment status to active
        if ($enrollmentModel->update($enrollment_id, ['status' => 'active'])) {
            session()->setFlashdata('success', 'Student "' . $studentName . '" enrollment activated. Student can now access course "' . $courseTitle . '".');
            return redirect()->to(base_url('admin/courses/view/' . $courseId));
        } else {
            session()->setFlashdata('error', 'Failed to activate enrollment.');
            return redirect()->back();
        }
    }

    // Deactivate student enrollment (block access to course)
    public function deactivateEnrollment($enrollment_id)
    {
        // Check if user is admin
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'admin') {
            session()->setFlashdata('error', 'Access denied. Admin privileges required.');
            return redirect()->to(base_url('login'));
        }

        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        // Get enrollment details
        $enrollment = $enrollmentModel->select('enrollments.*, users.name, courses.title as course_title, courses.id as course_id')
                                      ->join('users', 'users.id = enrollments.user_id')
                                      ->join('courses', 'courses.id = enrollments.course_id')
                                      ->where('enrollments.id', $enrollment_id)
                                      ->first();

        if (!$enrollment) {
            session()->setFlashdata('error', 'Enrollment not found.');
            return redirect()->back();
        }

        $courseId = $enrollment['course_id'];
        $studentName = $enrollment['name'] ?? 'Student';
        $courseTitle = $enrollment['course_title'] ?? 'Course';

        // Update enrollment status to inactive
        if ($enrollmentModel->update($enrollment_id, ['status' => 'inactive'])) {
            session()->setFlashdata('success', 'Student "' . $studentName . '" enrollment deactivated. Student cannot access course "' . $courseTitle . '" now.');
            return redirect()->to(base_url('admin/courses/view/' . $courseId));
        } else {
            session()->setFlashdata('error', 'Failed to deactivate enrollment.');
            return redirect()->back();
        }
    }

    // Helper function to check if two time ranges overlap
    // This function checks if the new course time overlaps with an existing course time
    private function checkTimeOverlap($newStartTime, $newEndTime, $existingStartTime, $existingEndTime)
    {
        // Convert time strings to timestamps for easier comparison
        // We'll use a fixed date as a base, just for time comparison
        $baseDate = '2000-01-01';
        
        $newStart = strtotime($baseDate . ' ' . $newStartTime);
        $newEnd = strtotime($baseDate . ' ' . $newEndTime);
        $existingStart = strtotime($baseDate . ' ' . $existingStartTime);
        $existingEnd = strtotime($baseDate . ' ' . $existingEndTime);
        
        // If strtotime fails, return false (shouldn't happen with valid time format)
        if ($newStart === false || $newEnd === false || $existingStart === false || $existingEnd === false) {
            return false;
        }
        
        // Check if times overlap
        // Two time ranges overlap if:
        // - New start is between existing start and end (inclusive start, exclusive end), OR
        // - New end is between existing start and end (exclusive start, inclusive end), OR
        // - New time completely contains existing time, OR
        // - Existing time completely contains new time
        if (($newStart >= $existingStart && $newStart < $existingEnd) ||
            ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
            ($newStart <= $existingStart && $newEnd >= $existingEnd) ||
            ($newStart >= $existingStart && $newEnd <= $existingEnd)) {
            return true; // Times overlap
        }
        
        return false; // No overlap
    }
}

