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

        $enrollments = new EnrollmentModel();
        $enrolledCourses = $enrollments->getUserEnrollments($userId);

        // Available courses = courses not in enrolled list
        $db = db_connect();
        $builder = $db->table('courses');
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
            'showEnrollments' => true,
        ];

        return view('student', $data);
    }
}

