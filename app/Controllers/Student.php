<?php

namespace App\Controllers;

use CodeIgniter\Controller;

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
            ]
        ];

        return view('student', $data);
    }
}

