<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToEnrollments extends Migration
{
    public function up()
    {
        // Add status field to enrollments table
        // Status can be 'active' (student can access course) or 'inactive' (student cannot access course)
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
                'after' => 'enrollment_date'
            ]
        ];
        $this->forge->addColumn('enrollments', $fields);
        
        // Set all existing enrollments to active
        $this->db->table('enrollments')->update(['status' => 'active']);
    }

    public function down()
    {
        // Remove status column if migration is rolled back
        $this->forge->dropColumn('enrollments', 'status');
    }
}

