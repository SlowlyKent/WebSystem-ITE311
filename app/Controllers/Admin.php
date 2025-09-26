<?php

namespace App\Controllers;

use CodeIgniter\Controller;

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
}

