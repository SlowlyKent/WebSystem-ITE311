<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'    => ['type' => 'INT', 'unsigned' => true],     
            'course_id'  => ['type' => 'INT', 'unsigned' => true],
            'status'     => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'enrolled'],
            'enrolled_at'=> ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['user_id', 'course_id']);
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('enrollments', true, ['ENGINE' => 'InnoDB']);
    }

    public function down(): void
    {
        $this->forge->dropTable('enrollments', true);
    }
}
