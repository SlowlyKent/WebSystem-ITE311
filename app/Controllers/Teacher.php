<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CourseModel;

class Teacher extends Controller
{
    public function dashboard()
    {
        // Check if user is logged in and is teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Teacher Dashboard',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];

        return view('teacher', $data);
    }

    public function courses()
    {
        // Check if user is logged in and is teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        $courseModel = new CourseModel();
        $userId = session()->get('user_id');
        
        // Get courses created by this teacher
        $allCourses = $courseModel->where('instructor_id', $userId)->orderBy('school_year', 'DESC')->orderBy('semester', 'ASC')->orderBy('year_level', 'ASC')->orderBy('section', 'ASC')->findAll();

        // Group courses by Academic Year, Semester, Year Level, and Section
        $groupedCourses = [];
        foreach ($allCourses as $course) {
            $schoolYear = $course['school_year'] ?? 'No AY';
            $semester = $course['semester'] ?? 'No Semester';
            $yearLevel = $course['year_level'] ?? 'No Year';
            $section = $course['section'] ?? 'No Section';
            
            if (!isset($groupedCourses[$schoolYear])) {
                $groupedCourses[$schoolYear] = [];
            }
            if (!isset($groupedCourses[$schoolYear][$semester])) {
                $groupedCourses[$schoolYear][$semester] = [];
            }
            if (!isset($groupedCourses[$schoolYear][$semester][$yearLevel])) {
                $groupedCourses[$schoolYear][$semester][$yearLevel] = [];
            }
            if (!isset($groupedCourses[$schoolYear][$semester][$yearLevel][$section])) {
                $groupedCourses[$schoolYear][$semester][$yearLevel][$section] = [];
            }
            
            $groupedCourses[$schoolYear][$semester][$yearLevel][$section][] = $course;
        }

        $data = [
            'title' => 'My Courses',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ],
            'courses' => $allCourses,
            'groupedCourses' => $groupedCourses,
        ];

        // Render My Courses inside the teacher dashboard view
        $data['showCourses'] = true;
        return view('teacher', $data);
    }

    public function createCourse()
    {
        // Check if user is logged in and is teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Create Course',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ],
        ];

        $data['showCreateCourse'] = true;
        return view('teacher', $data);
    }

    public function storeCourse()
    {
        // Check if user is logged in and is teacher
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'teacher') {
            session()->setFlashdata('error', 'Access denied. Teacher privileges required.');
            return redirect()->to(base_url('login'));
        }

        $courseModel = new CourseModel();
        
        // Get form data
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $userId = session()->get('user_id');

        // Simple validation
        if (empty($title)) {
            session()->setFlashdata('error', 'Course title is required.');
            return redirect()->to(base_url('teacher/courses/create'));
        }

        // Check if course name (title) already exists in database
        // This prevents duplicate course names
        $existingCourseByName = $courseModel->where('title', $title)->first();
        if ($existingCourseByName) {
            session()->setFlashdata('error', 'Course name "' . $title . '" already exists. Please use a different course name.');
            return redirect()->to(base_url('teacher/courses/create'));
        }

        // Prepare data to save
        $data = [
            'title' => $title,
            'description' => $description,
            'instructor_id' => $userId,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // Save to database
        if ($courseModel->insert($data)) {
            // Create notification for all users about new course
            try {
                $notificationModel = new \App\Models\NotificationModel();
                $userName = session()->get('name') ?? 'Teacher';
                
                // Notify all students about the new course
                $db = \Config\Database::connect();
                $students = $db->table('users')->select('id')->where('role', 'student')->get()->getResultArray();
                foreach ($students as $student) {
                    $studentId = (int)($student['id'] ?? 0);
                    if ($studentId > 0) {
                        $notificationModel->insert([
                            'user_id' => $studentId,
                            'message' => 'New course available: ' . $title,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                // Notify all admins
                $admins = $db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
                foreach ($admins as $admin) {
                    $adminId = (int)($admin['id'] ?? 0);
                    if ($adminId > 0) {
                        $notificationModel->insert([
                            'user_id' => $adminId,
                            'message' => $userName . ' created a new course: ' . $title,
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
            return redirect()->to(base_url('teacher/courses'));
        } else {
            $errors = $courseModel->errors();
            session()->setFlashdata('error', 'Failed to create course. ' . implode(', ', $errors));
            return redirect()->to(base_url('teacher/courses/create'));
        }
    }
}

