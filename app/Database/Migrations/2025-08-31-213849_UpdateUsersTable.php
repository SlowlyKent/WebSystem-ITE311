<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateUsersTable extends Migration
{
    public function up()
    {
         //  Change id constraint from 11 → 5
    $this->forge->modifyColumn('users', [
        'id' => [
            'type'           => 'INT',
            'constraint'     => 5,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
    ]);

        //  Rename 'username' → 'name'
        $this->forge->modifyColumn('users', [
            'username' => [
                'name'       => 'name',
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        //  Drop first_name and last_name
        $this->forge->dropColumn('users', ['first_name', 'last_name']);

        //  Modify 'role' enum values
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['admin', 'user'],
                'default'    => 'user',
            ],
        ]);

        //  Drop 'status'
        $this->forge->dropColumn('users', 'status');
    }

    public function down()
    {
        // Rollback changes
          //  Restore id constraint back to 11
    $this->forge->modifyColumn('users', [
        'id' => [
            'type'           => 'INT',
            'constraint'     => 11,
            'unsigned'       => true,
            'auto_increment' => true,
        ],
    ]);

        // Rename back to 'username'
        $this->forge->modifyColumn('users', [
            'name' => [
                'name'       => 'username',
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
        ]);

        // Re-add first_name and last_name
        $this->forge->addColumn('users', [
            'first_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'last_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);

        // Restore old role
        $this->forge->modifyColumn('users', [
            'role' => [
                'type'       => 'ENUM',
                'constraint' => ['student', 'instructor', 'admin'],
                'default'    => 'student',
            ],
        ]);

        // Re-add status
        $this->forge->addColumn('users', [
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
        ]);
    }
}
