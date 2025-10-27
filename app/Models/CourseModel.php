<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table         = 'courses';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['title', 'description', 'created_at', 'updated_at'];

    // If your courses table has created_at/updated_at columns and CI manages them
    protected $useTimestamps = false; // change to true if you want CI to auto-manage
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}