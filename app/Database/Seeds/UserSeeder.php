<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'felicia123',
                'email' => 'admin@lms.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'hariroque123',
                'email' => 'instructor@lms.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'name' => 'isyot123',
                'email' => 'student@lms.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role' => 'user',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

       
        foreach ($data as $user) {
            $existingUser = $this->db->table('users')->where('email', $user['email'])->get()->getRow();
            
            if (!$existingUser) {
                $this->db->table('users')->insert($user);
            }
        }
    }
} 