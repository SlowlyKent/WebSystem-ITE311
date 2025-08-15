<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLessonsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'course_id'  => ['type' => 'INT', 'unsigned' => true],
            'title'      => ['type' => 'VARCHAR', 'constraint' => 150],
            'content'    => ['type' => 'TEXT', 'null' => true],
            'sort_order' => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('course_id');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('lessons', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('lessons', true);
    }
}
