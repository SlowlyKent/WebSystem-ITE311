<?php

namespace App\Controllers;

use CodeIgniter\Controller;

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
}

