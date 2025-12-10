<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    
    public function enroll()
    {
        // Security 1: Authorization Bypass Protection
        // Check if user is logged in and is a student
        if (!session()->get('isLoggedIn')) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized. Please login first.']);
        }
        
        // Get user ID from session only (not from POST to prevent tampering)
        $userId = (int) session()->get('user_id');
        if (!$userId || $userId <= 0) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized. Invalid user session.']);
        }
        
        // Security 4: Data Tampering Protection
        // Ensure user can only enroll themselves (prevent user_id override)
        $postUserId = $this->request->getPost('user_id');
        if ($postUserId && (int)$postUserId !== $userId) {
            // Someone tried to enroll another user - block it
            return $this->response
                ->setStatusCode(403)
                ->setJSON(['status' => 'error', 'message' => 'Forbidden. You can only enroll yourself.']);
        }

        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        // Security 2: SQL Injection Protection
        // Validate course_id - must be positive integer only
        $courseIdInput = $this->request->getPost('course_id');
        if (empty($courseIdInput)) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Course ID is required']);
        }
        
        // Convert to integer and validate (prevents SQL injection)
        $courseId = (int) $courseIdInput;
        if ($courseId <= 0 || !is_numeric($courseIdInput) || (string)$courseId !== (string)$courseIdInput) {
            // Invalid input - not a pure integer
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course ID. Must be a valid number.']);
        }

        $db = \Config\Database::connect();
        $course = $db->table('courses')
                     ->select('id, title, description, status, enrollment_limit, allow_self_enrollment')
                     ->where('id', $courseId)
                     ->get()
                     ->getRowArray();

        if (!$course) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Course not found']);
        }

        // Check if course is Active
        if ($course['status'] !== 'Active') {
            return $this->response
                ->setJSON(['status' => 'error', 'message' => 'Course is not active. Enrollment is not allowed.']);
        }

        // Check enrollment limit
        if (!empty($course['enrollment_limit'])) {
            $currentEnrollments = $db->table('enrollments')
                                    ->where('course_id', $courseId)
                                    ->countAllResults();
            if ($currentEnrollments >= (int)$course['enrollment_limit']) {
                return $this->response
                    ->setJSON(['status' => 'error', 'message' => 'Course enrollment limit reached.']);
            }
        }

        // Check prerequisites
        $prerequisites = $db->table('course_prerequisites')
                           ->where('course_id', $courseId)
                           ->get()
                           ->getResultArray();
        
        if (!empty($prerequisites)) {
            $enrollments = new EnrollmentModel();
            foreach ($prerequisites as $prereq) {
                $prereqCourseId = $prereq['prerequisite_course_id'];
                if (!$enrollments->isAlreadyEnrolled($userId, $prereqCourseId)) {
                    $prereqCourse = $db->table('courses')->where('id', $prereqCourseId)->get()->getRowArray();
                    $prereqTitle = $prereqCourse['title'] ?? 'prerequisite course';
                    return $this->response
                        ->setJSON(['status' => 'error', 'message' => 'You must complete the prerequisite course: ' . $prereqTitle]);
                }
            }
        }

        $enrollments = new EnrollmentModel();
        if ($enrollments->isAlreadyEnrolled($userId, $courseId)) {
            return $this->response
                ->setJSON(['status' => 'error', 'message' => 'Already enrolled']);
        }

        $data = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s')
        ];

        try {
            $success = $enrollments->enrollUser($data);
        } catch (\Throwable $e) {
            log_message('error', 'Enrollment error: ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Enrollment failed']);
        }

        if (!$success) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON(['status' => 'error', 'message' => 'Failed to enroll']);
        }

        // Create notifications for enrollment
        try {
            $notificationModel = new \App\Models\NotificationModel();
            $studentName = session()->get('name') ?? 'A student';
            $courseTitle = $course['title'] ?? 'a course';
            
            // Notify the student
            $notificationModel->insert([
                'user_id' => $userId,
                'message' => 'You have been enrolled in ' . $courseTitle,
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ]);
            
            // Notify all teachers
            $enrollmentModel = new \App\Models\EnrollmentModel();
            $teachers = $enrollmentModel->db->table('users')->select('id')->where('role', 'teacher')->get()->getResultArray();
            foreach ($teachers as $teacher) {
                $teacherId = (int)($teacher['id'] ?? 0);
                if ($teacherId > 0) {
                    $notificationModel->insert([
                        'user_id' => $teacherId,
                        'message' => $studentName . ' enrolled in ' . $courseTitle,
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
            
            // Notify all admins
            $admins = $enrollmentModel->db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
            foreach ($admins as $admin) {
                $adminId = (int)($admin['id'] ?? 0);
                if ($adminId > 0) {
                    $notificationModel->insert([
                        'user_id' => $adminId,
                        'message' => $studentName . ' enrolled in ' . $courseTitle,
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
            }
        } catch (\Throwable $e) {
            // Log error but don't fail the enrollment
            log_message('error', 'Failed to create enrollment notification: ' . $e->getMessage());
        }

        $security = service('security');
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Enrolled successfully',
            'course' => [
                'id' => (int) $course['id'],
                'title' => $course['title'] ?? '',
                'description' => $course['description'] ?? ''
            ],
            'csrf' => [
                'token' => $security->getTokenName(),
                'hash' => $security->getHash()
            ]
        ]);
    }
}
