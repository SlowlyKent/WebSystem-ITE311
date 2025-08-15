<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSubmissionsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'quiz_id'     => ['type' => 'INT', 'unsigned' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'answer'      => ['type' => 'TEXT'],                   
            'score'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'is_correct'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'submitted_at'=> ['type' => 'DATETIME', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['quiz_id', 'user_id']); 
        $this->forge->addForeignKey('quiz_id', 'quizzes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('submissions', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('submissions', true);
    }
}
