<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

class Notification extends BaseController
{
    public function get(): ResponseInterface
    {
        // Debug logging
        log_message('debug', 'Notification get called. IsLoggedIn: ' . (session()->get('isLoggedIn') ? 'yes' : 'no'));
        
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $userId = (int) (session()->get('user_id') ?? session()->get('userID') ?? 0);
        log_message('debug', 'User ID: ' . $userId);
        if ($userId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Missing user id']);
        }

        $nm = new \App\Models\NotificationModel();
        $unread = $nm->getUnreadCount($userId);

        // Optional limit parameter (1..50), default 10
        $limit = (int) ($this->request->getGet('limit') ?? 10);
        if ($limit < 1) { $limit = 10; }
        if ($limit > 50) { $limit = 50; }

        $recent = $nm->getNotificationsForUser($userId, $limit);

        return $this->response->setJSON([
            'success' => true,
            'unread' => (int) $unread,
            'notifications' => $recent,
        ]);
    }

    public function mark_as_read($id = null): ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }

        $notificationId = (int) $id;
        if ($notificationId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid notification id']);
        }

        $userId = (int) (session()->get('user_id') ?? session()->get('userID') ?? 0);
        $nm = new \App\Models\NotificationModel();

        // Ensure the notification belongs to the current user
        $row = $nm->where('id', $notificationId)->where('user_id', $userId)->first();
        if (!$row) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Notification not found']);
        }

        $ok = $nm->markAsRead($notificationId);
        if (!$ok) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Failed to update']);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function mark_all(): ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $userId = (int) (session()->get('user_id') ?? session()->get('userID') ?? 0);
        if ($userId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Missing user id']);
        }
        $nm = new \App\Models\NotificationModel();
        $nm->where('user_id', $userId)->set('is_read', 1)->update();
        return $this->response->setJSON(['success' => true]);
    }

    // Assignment created: notify students (enrolled in course) and instructor
    public function assignment_created(): ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $courseId = (int) ($this->request->getPost('course_id') ?? 0);
        $message  = (string) ($this->request->getPost('message') ?? 'New assignment posted');
        if ($courseId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid course id']);
        }
        try {
            $course = (new \App\Models\CourseModel())->find($courseId);
            $courseTitle = $course['title'] ?? ('Course #' . $courseId);
            $nm = new \App\Models\NotificationModel();
            $enroll = new \App\Models\EnrollmentModel();
            $studentIds = $enroll->select('user_id')->where('course_id', $courseId)->findColumn('user_id') ?? [];
            foreach ($studentIds as $sid) {
                $nm->insert(['user_id' => (int)$sid, 'message' => $message . ' in ' . $courseTitle, 'is_read' => 0]);
            }
            $instructorId = (int)($course['instructor_id'] ?? 0);
            if ($instructorId > 0) {
                $nm->insert(['user_id' => $instructorId, 'message' => 'Assignment created in ' . $courseTitle, 'is_read' => 0]);
            }
            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false]);
        }
    }

    // Assignment submitted: notify instructor (and optionally admins)
    public function assignment_submitted(): ResponseInterface
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['success' => false, 'message' => 'Unauthorized']);
        }
        $courseId = (int) ($this->request->getPost('course_id') ?? 0);
        $student  = (string) ($this->request->getPost('student_name') ?? 'A student');
        if ($courseId <= 0) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid course id']);
        }
        try {
            $course = (new \App\Models\CourseModel())->find($courseId);
            $courseTitle = $course['title'] ?? ('Course #' . $courseId);
            $nm = new \App\Models\NotificationModel();
            $instructorId = (int)($course['instructor_id'] ?? 0);
            if ($instructorId > 0) {
                $nm->insert(['user_id' => $instructorId, 'message' => $student . ' submitted an assignment in ' . $courseTitle, 'is_read' => 0]);
            }
            // Notify admins as well
            $db = \Config\Database::connect();
            $adminIds = $db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
            foreach ($adminIds as $a) {
                $aid = (int)($a['id'] ?? 0);
                if ($aid > 0) {
                    $nm->insert(['user_id' => $aid, 'message' => 'Assignment submitted in ' . $courseTitle, 'is_read' => 0]);
                }
            }
            return $this->response->setJSON(['success' => true]);
        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON(['success' => false]);
        }
    }
}