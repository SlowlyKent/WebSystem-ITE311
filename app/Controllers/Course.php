<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    public function enroll()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response
                ->setStatusCode(401)
                ->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        if (!$this->request->is('post')) {
            return $this->response
                ->setStatusCode(405)
                ->setJSON(['status' => 'error', 'message' => 'Method not allowed']);
        }

        $courseId = (int) $this->request->getPost('course_id');
        if ($courseId <= 0) {
            return $this->response
                ->setStatusCode(400)
                ->setJSON(['status' => 'error', 'message' => 'Invalid course ID']);
        }

        $db = \Config\Database::connect();
        $course = $db->table('courses')
                     ->select('id, title, description')
                     ->where('id', $courseId)
                     ->get()
                     ->getRowArray();

        if (!$course) {
            return $this->response
                ->setStatusCode(404)
                ->setJSON(['status' => 'error', 'message' => 'Course not found']);
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
