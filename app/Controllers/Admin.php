<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\CourseModel;

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
}

