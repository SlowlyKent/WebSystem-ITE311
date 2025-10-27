<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'       => 'Introduction to Programming',
                'description' => 'Learn programming fundamentals with practical examples.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Web Development Basics',
                'description' => 'HTML, CSS, and JavaScript essentials for building websites.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Database Systems',
                'description' => 'Relational databases, SQL queries, and normalization.',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];

        $builder = $this->db->table('courses');

        foreach ($data as $course) {
            $exists = $builder->where('title', $course['title'])->get()->getRow();
            if (!$exists) {
                $builder->insert($course);
            }
        }
    }
}