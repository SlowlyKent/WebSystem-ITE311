<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        // Get a teacher user ID if available (for instructor_id)
        $teacherId = null;
        
        // Try to get a teacher user, but don't fail if table doesn't exist
        try {
            $teacher = $this->db->table('users')->where('role', 'teacher')->get()->getRow();
            if ($teacher) {
                $teacherId = $teacher->id;
            }
        } catch (\CodeIgniter\Database\Exceptions\DatabaseException $e) {
            // If users table doesn't exist, continue without instructor_id
            // Courses will be created with instructor_name and instructor_email instead
        } catch (\Exception $e) {
            // Catch any other exceptions and continue
        }

        $data = [
            [
                'title'                => 'Introduction to Programming',
                'course_code'          => 'IT101',
                'description'          => 'Learn programming fundamentals with practical examples. This course covers basic programming concepts, data types, control structures, and problem-solving techniques.',
                'short_description'    => 'Basic programming concepts and problem-solving',
                'year_level'           => '1st year',
                'semester'             => '1st sem',
                'school_year'          => '2024-2025',
                'department'           => 'BSIT',
                'instructor_id'        => $teacherId,
                'instructor_name'      => 'Dr. John Smith',
                'instructor_email'     => 'john.smith@university.edu',
                'units'                => 3.0,
                'course_category'        => 'Major',
                'section'              => 'BSIT-1A',
                'start_date'           => '2024-08-15',
                'end_date'             => '2024-12-15',
                'enrollment_limit'     => 30,
                'status'               => 'Active',
                'allow_self_enrollment' => 1,
                'grading_scheme'       => 'Percentage-based',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'title'                => 'Web Development Basics',
                'course_code'          => 'IT102',
                'description'          => 'HTML, CSS, and JavaScript essentials for building websites. Students will learn to create responsive web pages and interactive user interfaces.',
                'short_description'    => 'HTML, CSS, and JavaScript fundamentals',
                'year_level'           => '1st year',
                'semester'             => '2nd sem',
                'school_year'          => '2024-2025',
                'department'           => 'BSIT',
                'instructor_id'        => $teacherId,
                'instructor_name'      => 'Prof. Maria Garcia',
                'instructor_email'     => 'maria.garcia@university.edu',
                'units'                => 3.0,
                'course_category'      => 'Major',
                'section'              => 'BSIT-1B',
                'start_date'           => '2025-01-15',
                'end_date'             => '2025-05-15',
                'enrollment_limit'     => 25,
                'status'               => 'Active',
                'allow_self_enrollment' => 1,
                'grading_scheme'       => 'Points-based',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'title'                => 'Database Systems',
                'course_code'          => 'IT201',
                'description'          => 'Relational databases, SQL queries, and normalization. This course covers database design, implementation, and management using modern database systems.',
                'short_description'    => 'Database design and SQL fundamentals',
                'year_level'           => '2nd year',
                'semester'             => '1st sem',
                'school_year'          => '2024-2025',
                'department'           => 'BSIT',
                'instructor_id'        => $teacherId,
                'instructor_name'      => 'Dr. Robert Chen',
                'instructor_email'     => 'robert.chen@university.edu',
                'units'                => 3.0,
                'course_category'      => 'Major',
                'section'              => 'BSIT-2A',
                'start_date'           => '2024-08-15',
                'end_date'             => '2024-12-15',
                'enrollment_limit'     => 35,
                'status'               => 'Active',
                'allow_self_enrollment' => 0,
                'grading_scheme'       => 'Criteria-based',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'title'                => 'Data Structures and Algorithms',
                'course_code'          => 'IT202',
                'description'          => 'Study of fundamental data structures (arrays, linked lists, stacks, queues, trees) and algorithms (sorting, searching, graph algorithms).',
                'short_description'    => 'Data structures and algorithm analysis',
                'year_level'           => '2nd year',
                'semester'             => '2nd sem',
                'school_year'          => '2024-2025',
                'department'           => 'BSIT',
                'instructor_id'        => $teacherId,
                'instructor_name'      => 'Prof. Sarah Johnson',
                'instructor_email'     => 'sarah.johnson@university.edu',
                'units'                => 3.0,
                'course_category'      => 'Major',
                'section'              => 'BSIT-2B',
                'start_date'           => '2025-01-15',
                'end_date'             => '2025-05-15',
                'enrollment_limit'     => 30,
                'status'               => 'Active',
                'allow_self_enrollment' => 1,
                'grading_scheme'       => 'Percentage-based',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
            [
                'title'                => 'Software Engineering',
                'course_code'          => 'IT301',
                'description'          => 'Software development lifecycle, requirements analysis, system design, testing, and project management methodologies.',
                'short_description'    => 'Software development process and methodologies',
                'year_level'           => '3rd year',
                'semester'             => '1st sem',
                'school_year'          => '2024-2025',
                'department'           => 'BSIT',
                'instructor_id'        => $teacherId,
                'instructor_name'      => 'Dr. Michael Brown',
                'instructor_email'     => 'michael.brown@university.edu',
                'units'                => 3.0,
                'course_category'      => 'Major',
                'section'              => 'BSIT-3A',
                'start_date'           => '2024-08-15',
                'end_date'             => '2024-12-15',
                'enrollment_limit'     => 28,
                'status'               => 'Active',
                'allow_self_enrollment' => 0,
                'grading_scheme'       => 'Criteria-based',
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s'),
            ],
        ];

        // Check if courses table exists
        try {
            $tables = $this->db->listTables();
            if (!in_array('courses', $tables)) {
                echo "Error: 'courses' table does not exist. Please run migrations first:\n";
                echo "php spark migrate\n";
                return;
            }
        } catch (\Exception $e) {
            echo "Error checking database tables: " . $e->getMessage() . "\n";
            echo "Please ensure migrations have been run: php spark migrate\n";
            return;
        }

        $builder = $this->db->table('courses');

        foreach ($data as $course) {
            $exists = $builder->where('course_code', $course['course_code'])->get()->getRow();
            if (!$exists) {
                $builder->insert($course);
                echo "Course seeded: " . $course['course_code'] . " - " . $course['title'] . "\n";
            } else {
                echo "Course already exists: " . $course['course_code'] . " - " . $course['title'] . "\n";
            }
        }
        
        echo "\nCourse seeding completed!\n";
    }
}