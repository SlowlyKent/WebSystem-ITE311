<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use CodeIgniter\Exceptions\PageNotFoundException;

class Material extends BaseController
{
    protected $materialModel;
    protected $uploadPath = WRITEPATH . 'uploads/materials/';

    public function __construct()
    {
        $this->materialModel = new MaterialModel();
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0777, true);
        }
    }

    /**
     * Display upload form and handle file upload
     */
    public function upload($courseId)
    {
        // Check if user is logged in and has permission
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        // Handle file upload
        if ($this->request->getMethod() === 'post') {
            $file = $this->request->getFile('material_file');
            
            if ($file->isValid() && !$file->hasMoved()) {
                $newName = $file->getRandomName();
                $file->move($this->uploadPath, $newName);

                $this->materialModel->insertMaterial([
                    'course_id' => $courseId,
                    'file_name' => $file->getClientName(),
                    'file_path' => 'materials/' . $newName
                ]);

                return redirect()->back()->with('success', 'File uploaded successfully');
            }

            return redirect()->back()->with('error', $file->getErrorString());
        }

        // Display upload form
        return view('materials/upload', ['courseId' => $courseId]);
    }

    /**
     * Delete a material and its associated file
     */
    public function delete($materialId)
    {
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $material = $this->materialModel->find($materialId);
        
        if (!$material) {
            throw new PageNotFoundException('Material not found');
        }

        // Delete file
        $filePath = WRITEPATH . 'uploads/' . $material['file_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete record
        $this->materialModel->delete($materialId);

        return redirect()->back()->with('success', 'Material deleted successfully');
    }

    /**
     * Download a material file
     */
    public function download($materialId)
    {
        $material = $this->materialModel->find($materialId);
        
        if (!$material) {
            throw new PageNotFoundException('Material not found');
        }

        $filePath = WRITEPATH . 'uploads/' . $material['file_path'];
        
        if (!file_exists($filePath)) {
            throw new PageNotFoundException('File not found');
        }

        // Check if user is enrolled in the course (you'll need to implement this check)
        // if (!$this->isEnrolled($material['course_id'])) {
        //     return redirect()->back()->with('error', 'You are not enrolled in this course');
        // }

        return $this->response->download($filePath, null)->setFileName($material['file_name']);
    }
}