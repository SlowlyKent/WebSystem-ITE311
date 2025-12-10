<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'title',
        'course_code',
        'description',
        'short_description',
        'year_level',
        'semester',
        'school_year',
        'department',
        'instructor_id',
        'instructor_name',
        'instructor_email',
        'units',
        'course_category',
        'section',
        'start_date',
        'end_date',
        'enrollment_limit',
        'status',
        'allow_self_enrollment',
        'grading_scheme',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true; 
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}