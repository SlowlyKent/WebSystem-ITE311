<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table      = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date', 'status'];

    //  Check if user is already enrolled (only check active enrollments)
    public function isAlreadyEnrolled($user_id, $course_id)
    {
        return $this->where('user_id', $user_id)
                    ->where('course_id', $course_id)
                    ->where('status', 'active')
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

    // Get all students enrolled in a specific course
    public function getCourseEnrollments($course_id)
    {
        return $this->select('enrollments.*, users.id as user_id, users.name, users.email, users.role, users.status as user_status')
                    ->join('users', 'users.id = enrollments.user_id')
                    ->where('enrollments.course_id', $course_id)
                    ->orderBy('enrollments.enrollment_date', 'DESC')
                    ->findAll();
    }
}
