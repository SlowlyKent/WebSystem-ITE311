<?php

namespace App\Controllers;

use App\Models\MaterialsModel;
use App\Models\CourseModel;

class Material extends BaseController
{
    protected $materialsModel;
    protected $courseModel;
    protected $enrollmentModel;

    public function __construct()
    {
        helper(['form', 'url']);
        // Use fully-qualified class names to avoid autoload/name resolution issues
        $this->materialsModel = new \App\Models\MaterialsModel();
        $this->courseModel = new \App\Models\CourseModel();
        $this->enrollmentModel = new \App\Models\EnrollmentModel();
    }

    public function index($course_id)
    {
        $data = [
            'course' => $this->courseModel->find($course_id),
            'materials' => $this->materialsModel->getMaterialsByCourse($course_id),
            'user' => [
                'name' => session()->get('name') ?? session()->get('username'),
                'role' => session()->get('role')
            ]
        ];

        return view('materials/list', $data);
    }

    public function student()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'student') {
            return redirect()->to('/login');
        }

        $user_id = session()->get('user_id');
        $enrolledCourses = $this->enrollmentModel->getUserEnrollments($user_id);
        
        $data = [
            'title' => 'My Course Materials',
            'user' => [
                'name' => session()->get('name') ?? session()->get('username'),
                'role' => session()->get('role')
            ],
            'enrolledCourses' => [],
            'availableCourses' => []
        ];

        foreach ($enrolledCourses as $course) {
            $materials = $this->materialsModel->getMaterialsByCourse($course['id']);
            $data['enrolledCourses'][] = [
                'course' => [
                    'id' => $course['id'],
                    'title' => $course['title'],
                    'description' => $course['description'] ?? ''
                ],
                'materials' => $materials ?? []
            ];
        }

        $allCourses = $this->courseModel->findAll();
        $data['availableCourses'] = array_values(array_filter($allCourses, function($course) use ($user_id) {
            return !$this->enrollmentModel->isAlreadyEnrolled($user_id, $course['id']);
        }));

        // Build a simple courses list for the existing materials/list view
        $courseList = array_map(function ($item) {
            return $item['course'];
        }, $data['enrolledCourses']);

        $viewData = [
            'courses' => $courseList,
            'user' => $data['user'],
        ];

        return view('materials/list', $viewData);
    }

    public function download($id)
    {
        $material = $this->materialsModel->getMaterialById($id);
        if (!$material) {
            return redirect()->back()->with('error', 'File not found.');
        }

        // Enrollment/permission check: allow admin/teacher, or enrolled student
        $userRole = session()->get('role');
        // normalize session key for user id (support both 'user_id' and legacy 'userID')
        $userId = (int) (session()->get('user_id') ?? session()->get('userID') ?? 0);
        $courseId = (int) ($material['course_id'] ?? 0);

        $allowed = in_array($userRole, ['admin','teacher']);
        if ($userRole === 'student') {
            $allowed = $this->enrollmentModel->isAlreadyEnrolled($userId, $courseId);
        }
        if (!$allowed) {
            return redirect()->back()->with('error', 'You must be enrolled in this course to download materials.');
        }

        $path = FCPATH . ($material['file_path'] ?? '');
        if (!is_file($path)) {
            return redirect()->back()->with('error', 'File not found on disk.');
        }

        // Send notification when student downloads material
        if ($userRole === 'student') {
            try {
                $nm = new \App\Models\NotificationModel();
                $studentName = session()->get('name') ?? 'A student';
                $fileName = $material['file_name'] ?? 'a material';
                $courseTitle = $material['course_title'] ?? 'a course';
                
                // Notify course instructor
                $course = $this->courseModel->find($courseId);
                $instructorId = (int)($course['instructor_id'] ?? 0);
                if ($instructorId > 0 && $instructorId !== $userId) {
                    $nm->insert([
                        'user_id' => $instructorId,
                        'message' => $studentName . ' downloaded "' . $fileName . '" from ' . $courseTitle,
                        'is_read' => 0,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                }
                
                // Notify admins
                $admins = $this->enrollmentModel->db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
                foreach ($admins as $a) {
                    $adminId = (int)($a['id'] ?? 0);
                    if ($adminId > 0) {
                        $nm->insert([
                            'user_id' => $adminId,
                            'message' => $studentName . ' downloaded material from ' . $courseTitle,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Failed to send download notification: ' . $e->getMessage());
            }
        }

        return $this->response->download($path, null)->setFileName($material['file_name']);
    }

    public function delete($id)
    {
        if (!in_array(session()->get('role'), ['admin', 'teacher'])) {
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $material = $this->materialsModel->find($id);
        if ($material) {
            $courseId = (int)($material['course_id'] ?? 0);
            $fileName = $material['file_name'] ?? 'a material';
            
            // Delete the file
            $path = FCPATH . ($material['file_path'] ?? '');
            if (is_file($path)) {
                @unlink($path);
            }
            $this->materialsModel->deleteMaterial($id);
            
            // Send notifications about deletion
            try {
                $course = $this->courseModel->find($courseId);
                $courseTitle = $course['title'] ?? ('Course #' . $courseId);
                $nm = new \App\Models\NotificationModel();
                $actorRole = session()->get('role');
                $actorName = session()->get('name') ?? 'An instructor';
                
                // Notify enrolled students
                $userIds = $this->enrollmentModel
                    ->select('user_id')
                    ->where('course_id', $courseId)
                    ->findColumn('user_id');
                    
                if (!empty($userIds)) {
                    foreach ($userIds as $uid) {
                        $nm->insert([
                            'user_id' => (int)$uid,
                            'message' => 'A material "' . $fileName . '" was removed from ' . $courseTitle,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
                
                // Notify admins if teacher deleted, or notify teacher if admin deleted
                if ($actorRole === 'teacher') {
                    $admins = $this->enrollmentModel->db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
                    foreach ($admins as $a) {
                        $nm->insert([
                            'user_id' => (int)($a['id'] ?? 0),
                            'message' => 'Teacher deleted material "' . $fileName . '" from ' . $courseTitle,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                } elseif ($actorRole === 'admin') {
                    $instructorId = (int)($course['instructor_id'] ?? 0);
                    if ($instructorId > 0) {
                        $nm->insert([
                            'user_id' => $instructorId,
                            'message' => 'Admin deleted material "' . $fileName . '" from your course: ' . $courseTitle,
                            'is_read' => 0,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', 'Failed to send deletion notifications: ' . $e->getMessage());
            }
            
            return redirect()->back()->with('success', 'Material deleted successfully.');
        }

        return redirect()->back()->with('error', 'Material not found.');
    }

    public function upload()
    {
        $file = $this->request->getFile('file');
        $course_id = $this->request->getPost('course_id');

        if (!$file || !$file->isValid() || $file->hasMoved()) {
            return redirect()->back()->with('error', 'No valid file uploaded.');
        }

        // Step 1: Get the file extension (the part after the dot, like "pdf" or "ppt")
        $fileExtension = strtolower($file->getClientExtension());
        
        // Step 2: List of allowed file types (only PPT and PDF)
        $allowedExtensions = ['pdf', 'ppt', 'pptx'];
        
        // Step 3: Check if the uploaded file extension is in our allowed list
        if (!in_array($fileExtension, $allowedExtensions)) {
            return redirect()->back()->with('error', 'Invalid file type! Only PPT (PowerPoint) and PDF files are allowed. Your file type: ' . strtoupper($fileExtension));
        }

        // Step 4: Also check the MIME type for extra security
        // MIME type is like "application/pdf" or "application/vnd.ms-powerpoint"
        $allowedMimeTypes = [
            'application/pdf',                                    // PDF files
            'application/vnd.ms-powerpoint',                     // Old PPT format
            'application/vnd.openxmlformats-officedocument.presentationml.presentation'  // New PPTX format
        ];
        
        $fileMimeType = $file->getClientMimeType();
        if (!in_array($fileMimeType, $allowedMimeTypes)) {
            return redirect()->back()->with('error', 'Invalid file format! Only PPT (PowerPoint) and PDF files are allowed.');
        }

        $newName = $file->getRandomName();
        $targetDir = FCPATH . 'uploads/materials';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }

        if ($file->move($targetDir, $newName)) {
            $this->materialsModel->insertMaterial([
                'course_id' => $course_id,
                'file_name' => $file->getClientName(),
                'file_path' => 'uploads/materials/' . $newName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            // Notifications: inform enrolled students of new material
            try {
                $course = $this->courseModel->find((int)$course_id);
                $courseTitle = $course['title'] ?? ('Course #' . (int)$course_id);

                // Get all enrolled user IDs for this course
                $userIds = $this->enrollmentModel
                    ->select('user_id')
                    ->where('course_id', (int)$course_id)
                    ->findColumn('user_id');

                if (!empty($userIds)) {
                    $nm = new \App\Models\NotificationModel();
                    // Notify all enrolled students
                    foreach ($userIds as $uid) {
                        $nm->insert([
                            'user_id' => (int)$uid,
                            'message' => 'New material uploaded in ' . $courseTitle,
                            'is_read' => 0,
                        ]);
                    }

                    // Additionally notify based on actor role
                    $actorRole = session()->get('role');
                    if ($actorRole === 'admin') {
                        // Admin uploaded: notify the course instructor (if any)
                        $instructorId = (int)($course['instructor_id'] ?? 0);
                        if ($instructorId > 0) {
                            $nm->insert([
                                'user_id' => $instructorId,
                                'message' => 'Admin uploaded new material to your course: ' . $courseTitle,
                                'is_read' => 0,
                            ]);
                        }
                    } elseif ($actorRole === 'teacher') {
                        // Teacher uploaded: notify all admins
                        $admins = $this->enrollmentModel->db->table('users')->select('id')->where('role', 'admin')->get()->getResultArray();
                        foreach ($admins as $a) {
                            $aid = (int)($a['id'] ?? 0);
                            if ($aid > 0) {
                                $nm->insert([
                                    'user_id' => $aid,
                                    'message' => 'Teacher uploaded new material in ' . $courseTitle,
                                    'is_read' => 0,
                                ]);
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                // Silent fail; uploading should still succeed
            }

            return redirect()->to("/materials/course/{$course_id}")
                ->with('success', 'Material uploaded successfully!');
        }

        return redirect()->back()->with('error', 'Failed to upload file.');
    }

    public function uploadForm($course_id)
    {
        $data = [
            'course_id' => $course_id,
            'user' => [
                'name' => session()->get('name') ?? session()->get('username'),
                'role' => session()->get('role')
            ]
        ];

        return view('materials/upload', $data);
    }
}