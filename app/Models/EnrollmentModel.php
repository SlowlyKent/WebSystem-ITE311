<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table      = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];

    //  Check if user is already enrolled
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->countAllResults() > 0;
    }

    //  Enroll a user
    public function enrollUser($data)
    {
        if (!$this->isAlreadyEnrolled($data['user_id'], $data['course_id'])) {
            return $this->insert($data);
        }
        return false; // Already enrolled
    }

    //  Get all courses a user is enrolled in
    public function getUserEnrollments($user_id)
    {
        return $this->select('courses.*')
                    ->join('courses', 'courses.id = enrollments.course_id')
                    ->where('enrollments.user_id', $user_id)
                    ->findAll();
    }
}
