<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\EnrollmentModel;

class Student extends Controller
{
    public function dashboard()
    {
        // Check if user is logged in and is student
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            session()->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        $data = [
            'title' => 'Student Dashboard',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ],
            'showEnrollments' => false,
        ];

        return view('student', $data);
    }

    public function enrollments()
    {
        // Check if user is logged in and is student
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            session()->setFlashdata('error', 'Access denied. Student privileges required.');
            return redirect()->to(base_url('login'));
        }

        $userId = (int) session()->get('user_id');
        $userModel = new \App\Models\UserModel();
        $student = $userModel->find($userId);
        
        // Get student's year level and semester
        $studentYearLevel = $student['year_level'] ?? null;
        $studentSemester = $student['semester'] ?? null;

        $enrollments = new EnrollmentModel();
        $enrolledCourses = $enrollments->getUserEnrollments($userId);

        // Available courses = courses not in enrolled list, Active status, and matching year/semester (unless allow_self_enrollment is true)
        $db = db_connect();
        $builder = $db->table('courses');
        
        // Only show Active courses
        $builder->where('status', 'Active');
        
        // Filter by student's year level and semester if they are set
        if (!empty($studentYearLevel)) {
            $builder->where('year_level', $studentYearLevel);
        }
        if (!empty($studentSemester)) {
            $builder->where('semester', $studentSemester);
        }
        
        // Exclude already enrolled courses
        if (!empty($enrolledCourses)) {
            $enrolledIds = array_column($enrolledCourses, 'id');
            $builder->whereNotIn('id', $enrolledIds);
        }
        
        $availableCourses = $builder->get()->getResultArray();

        $data = [
            'title' => 'My Enrollments',
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ],
            'enrolledCourses' => $enrolledCourses,
            'availableCourses' => $availableCourses,
            'studentYearLevel' => $studentYearLevel,
            'studentSemester' => $studentSemester,
            'showEnrollments' => true,
        ];

        return view('student', $data);
    }
}

