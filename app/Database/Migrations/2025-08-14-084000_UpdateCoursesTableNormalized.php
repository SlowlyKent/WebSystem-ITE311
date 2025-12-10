<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateCoursesTableNormalized extends Migration
{
    public function up(): void
    {
        // First, create course_prerequisites table for many-to-many relationship
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true
            ],
            'course_id' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'prerequisite_course_id' => [
                'type' => 'INT',
                'unsigned' => true
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true
            ]
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['course_id', 'prerequisite_course_id']);
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('prerequisite_course_id', 'courses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('course_prerequisites', true, ['ENGINE' => 'InnoDB']);

        // Now update the courses table with all new fields
        $fields = [
            'course_code' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'title'
            ],
            'short_description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'description'
            ],
            'year_level' => [
                'type' => 'ENUM',
                'constraint' => ['1st year', '2nd year', '3rd year', '4th year', '5th year'],
                'null' => true,
                'after' => 'short_description'
            ],
            'semester' => [
                'type' => 'ENUM',
                'constraint' => ['1st sem', '2nd sem', 'Summer'],
                'null' => true,
                'after' => 'year_level'
            ],
            'school_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => true,
                'after' => 'semester'
            ],
            'department' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'school_year'
            ],
            'instructor_name' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'instructor_id'
            ],
            'instructor_email' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
                'after' => 'instructor_name'
            ],
            'units' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
                'default' => 0,
                'after' => 'instructor_email'
            ],
            'course_category' => [
                'type' => 'ENUM',
                'constraint' => ['Major', 'Minor', 'Elective', 'Laboratory', 'Online Course'],
                'null' => true,
                'after' => 'units'
            ],
            'section' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'course_category'
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'section'
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'start_date'
            ],
            'enrollment_limit' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => true,
                'after' => 'end_date'
            ],
            'status' => [
                'type' => 'ENUM',
                'constraint' => ['Active', 'Inactive'],
                'default' => 'Active',
                'after' => 'enrollment_limit'
            ],
            'allow_self_enrollment' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'status'
            ],
            'grading_scheme' => [
                'type' => 'ENUM',
                'constraint' => ['Percentage-based', 'Points-based', 'Criteria-based'],
                'null' => true,
                'after' => 'allow_self_enrollment'
            ]
        ];

        $this->forge->addColumn('courses', $fields);
    }

    public function down(): void
    {
        // Drop course_prerequisites table
        $this->forge->dropTable('course_prerequisites', true);

        // Remove columns from courses table
        $this->forge->dropColumn('courses', [
            'course_code',
            'short_description',
            'year_level',
            'semester',
            'school_year',
            'department',
            'instructor_name',
            'instructor_email',
            'units',
            'course_category',
            'section',
            'start_date',
            'end_date',
            'enrollment_limit',
            'status',
            'allow_self_enrollment',
            'grading_scheme'
        ]);
    }
}

