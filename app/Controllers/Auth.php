<?php
namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;
use App\Models\EnrollmentModel;

class Auth extends Controller
{
    public function register()
    {
        helper(['form']);
        $session = session();
        $model = new UserModel();
        
        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => 'required|min_length[3]|max_length[100]',
                'email' => 'required|valid_email|is_unique[users.email]',
                'password' => 'required|min_length[6]|alpha_numeric',
                'password_confirm' => 'matches[password]'
            ];
            
            if ($this->validate($rules)) {
                // Get and validate email format using filter_var (additional security layer)
                $email = $this->request->getPost('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $session->setFlashdata('error', 'Invalid email format.');
                    return redirect()->to('/register');
                }
                
                // Validate password format - only alphanumeric characters allowed
                $password = $this->request->getPost('password');
                if (!ctype_alnum($password)) {
                    $session->setFlashdata('error', 'Password can only contain letters and numbers. No special characters allowed.');
                    return redirect()->to('/register');
                }
                
                $data = [
                    'name' => $this->request->getPost('name'),
                    'email' => $email, // Use validated email
                    'password' => password_hash($password, PASSWORD_DEFAULT), // Use validated password
                    'role' => 'student'
                ];
                
                // Save user to database (CodeIgniter uses prepared statements automatically)
                if ($model->insert($data)) {
                    $session->setFlashdata('success', 'Registration successful. Please login.');
                    return redirect()->to('/login');
                } else {
                    // Get the last error for debugging
                    $errors = $model->errors();
                    $errorMessage = 'Registration failed. ';
                    if (!empty($errors)) {
                        $errorMessage .= implode(', ', $errors);
                    } else {
                        $errorMessage .= 'Please try again.';
                    }
                    $session->setFlashdata('error', $errorMessage);
                }
            }
        }
        
        echo view('auth/register', [
            'validation' => $this->validator
        ]);
    }

    public function login()
    {
        // Redirect if already logged in
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        
        helper(['form']);
        $session = session();
        $model = new UserModel();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'email' => 'required|valid_email',
                'password' => 'required'
            ];
            if ($this->validate($rules)) {
                // Get and validate email format using filter_var (additional security layer)
                $email = $this->request->getPost('email');
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $session->setFlashdata('error', 'Invalid email format.');
                    return redirect()->to('/login');
                }
                
                // Validate password format - only alphanumeric characters allowed
                $password = $this->request->getPost('password');
                if (!ctype_alnum($password)) {
                    $session->setFlashdata('error', 'Password can only contain letters and numbers. No special characters allowed.');
                    return redirect()->to('/login');
                }
                
                // CodeIgniter uses prepared statements automatically through Query Builder
                $user = $model->where('email', $email)->first();
                
                // Check if user exists and password is correct
                if ($user && password_verify($password, $user['password'])) {
                    // Check if user account is active
                    $userStatus = $user['status'] ?? 'active'; // Default to active if status not set
                    if ($userStatus !== 'active') {
                        $session->setFlashdata('error', 'Your account is inactive. Please contact administrator.');
                        return redirect()->to('/login');
                    }
                    $session->set([
                        'user_id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role'],
                        'isLoggedIn' => true
                    ]);
                    $session->setFlashdata('success', 'Welcome, ' . $user['name'] . '!');
                    
                    // Redirect everyone to unified dashboard (as per teacher requirements)
                    return redirect()->to(base_url('dashboard'));
                } else {
                    $session->setFlashdata('error', 'Invalid login credentials.');
                }
            }
        }
        echo view('auth/login', [
            'validation' => $this->validator
        ]);
    }

    public function logout()
    {
        // Destroy the current session
        session()->destroy();
        
        // Set logout message and redirect
        session()->setFlashdata('success', 'You have been logged out successfully.');
        return redirect()->to(base_url('login'));
    }

    public function dashboard()
    {
        // Check if user is logged in
        if (!session()->get('isLoggedIn')) {
            session()->setFlashdata('error', 'Please login to access the dashboard.');
            return redirect()->to(base_url('login'));
        }

        // User is logged in, show dashboard
        $data = [
            'user' => [
                'name' => session()->get('name'),
                'email' => session()->get('email'),
                'role' => session()->get('role')
            ]
        ];

        // If student, prepare enrolled and available courses
        if (session()->get('role') === 'student') {
            $userId = (int) session()->get('user_id');
            $userModel = new \App\Models\UserModel();
            $student = $userModel->find($userId);
            
            // Get student's year level and semester
            $studentYearLevel = $student['year_level'] ?? null;
            $studentSemester = $student['semester'] ?? null;
            
            $enrollments = new EnrollmentModel();
            $enrolledCourses = $enrollments->getUserEnrollments($userId);

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

            $data['enrolledCourses'] = $enrolledCourses;
            $data['availableCourses'] = $availableCourses;
        }

        return view('auth/dashboard', $data);
    }
}
