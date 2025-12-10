<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddStatusToUsers extends Migration
{
    public function up()
    {
        // Add status field to users table
        // active = user can login and use account
        // inactive = user cannot login
        $fields = [
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default' => 'active',
                'after' => 'role'
            ]
        ];
        
        $this->forge->addColumn('users', $fields);
        
        // Set all existing users to active by default
        $this->db->table('users')->update(['status' => 'active']);
    }

    public function down()
    {
        // Remove status field if migration is rolled back
        $this->forge->dropColumn('users', 'status');
    }
}

