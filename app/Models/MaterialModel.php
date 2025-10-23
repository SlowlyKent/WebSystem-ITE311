<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];
    protected $useTimestamps = false;
    protected $createdField = 'created_at';

    /**
     * Insert a new material record
     *
     * @param array $data Material data including file_name, file_path, and course_id
     * @return int|bool Insert ID on success, false on failure
     */
    public function insertMaterial($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->insert($data);
    }

    /**
     * Get all materials for a specific course
     *
     * @param int $courseId The ID of the course
     * @return array Array of material records
     */
    public function getMaterialsByCourse($courseId)
    {
        return $this->where('course_id', $courseId)
                   ->orderBy('created_at', 'DESC')
                   ->findAll();
    }
}