<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateQuizzesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'lesson_id'      => ['type' => 'INT', 'unsigned' => true],
            'question'       => ['type' => 'TEXT'],
            'choices'        => ['type' => 'JSON', 'null' => true], 
            'correct_answer' => ['type' => 'VARCHAR', 'constraint' => 255], 
            'points'         => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('lesson_id');
        $this->forge->addForeignKey('lesson_id', 'lessons', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('quizzes', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('quizzes', true);
    }
}
