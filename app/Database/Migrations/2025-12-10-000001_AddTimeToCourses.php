<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTimeToCourses extends Migration
{
    public function up()
    {
        // Add start_time and end_time fields to courses table
        // These fields will store the class time (e.g., "1:00 PM" or "13:00")
        $fields = [
            'start_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'start_date'
            ],
            'end_time' => [
                'type' => 'TIME',
                'null' => true,
                'after' => 'start_time'
            ]
        ];
        $this->forge->addColumn('courses', $fields);
    }

    public function down()
    {
        // Remove time columns if migration is rolled back
        $this->forge->dropColumn('courses', ['start_time', 'end_time']);
    }
}

