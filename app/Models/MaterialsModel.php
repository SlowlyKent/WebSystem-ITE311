<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialsModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;

    public function getMaterialsByCourse($course_id)
    {
        return $this->select('materials.*, courses.title as course_title')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->where('materials.course_id', $course_id)
                    ->orderBy('materials.created_at', 'DESC')
                    ->findAll();
    }

    public function getMaterialsForStudent($user_id)
    {
        $enrollmentModel = new EnrollmentModel();
        $enrolledCourses = $enrollmentModel->getUserEnrollments($user_id);
        
        $results = [];
        foreach ($enrolledCourses as $course) {
            $materials = $this->select('materials.*, courses.title as course_title')
                             ->join('courses', 'courses.id = materials.course_id')
                             ->where('materials.course_id', $course['id'])
                             ->orderBy('materials.created_at', 'DESC')
                             ->findAll();
            
            $results[] = [
                'course' => $course,
                'materials' => $materials ?? []
            ];
        }
        
        return $results;
    }

    public function getMaterialById($id)
    {
        return $this->select('materials.*, courses.title as course_title, courses.id as course_id')
                    ->join('courses', 'courses.id = materials.course_id')
                    ->where('materials.id', $id)
                    ->first();
    }

    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    public function deleteMaterial($id)
    {
        return $this->delete($id);
    }

    public function canAccessMaterial($material_id, $user_id)
    {
        // Admin and teachers can always access materials
        if (session()->get('role') === 'admin' || session()->get('role') === 'teacher') {
            return true;
        }

        // For students, check enrollment
        if (session()->get('role') === 'student') {
            $material = $this->getMaterialById($material_id);
            if (!$material) {
                return false;
            }

            $enrollmentModel = new \App\Models\EnrollmentModel();
            return $enrollmentModel->isAlreadyEnrolled($user_id, $material['course_id']);
        }

        return false;
    }
}