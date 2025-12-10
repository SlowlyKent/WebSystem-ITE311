<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => [
                'type' => 'INT', 
                'unsigned' => true, 
                'auto_increment' => true
            ],
            'title'         => [
                'type' => 'VARCHAR', 
                'constraint' => 150
            ],
            'description'   => [
                'type' => 'TEXT', 
                'null' => true
            ],
            'instructor_id' => [
                'type' => 'INT', 
                'unsigned' => true, 
                'null' => true
            ], 
            'created_at'    => [
                'type' => 'DATETIME', 
                'null' => true
            ],
            'updated_at'    => [
                'type' => 'DATETIME', 
                'null' => true
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('instructor_id');
        $this->forge->addForeignKey('instructor_id', 'users', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('courses', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('courses', true);
    }
}
