<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStudentYearSemesterToUsers extends Migration
{
    public function up(): void
    {
        $fields = [
            'year_level' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'role'
            ],
            'semester' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'year_level'
            ]
        ];

        $this->forge->addColumn('users', $fields);
    }

    public function down(): void
    {
        $this->forge->dropColumn('users', ['year_level', 'semester']);
    }
}

