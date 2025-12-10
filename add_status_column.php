<?php
/**
 * Simple script to add status column to users table
 * Run this file once by accessing: http://localhost/ITE311-FELICIA/add_status_column.php
 * Then delete this file for security
 */

// Load CodeIgniter
require_once __DIR__ . '/vendor/autoload.php';

// Or if autoload doesn't work, use this:
$pathsConfig = FCPATH . '../app/Config/Paths.php';
require realpath($pathsConfig) ?: $pathsConfig;

$paths = new Config\Paths();
require rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . 'bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

// Get database connection
$db = \Config\Database::connect();

// Check if column already exists
$query = $db->query("SHOW COLUMNS FROM `users` LIKE 'status'");
$columnExists = $query->getNumRows() > 0;

if ($columnExists) {
    echo "Status column already exists!<br>";
} else {
    // Add status column
    $sql = "ALTER TABLE `users` 
            ADD COLUMN `status` ENUM('active', 'inactive') DEFAULT 'active' AFTER `role`";
    
    if ($db->query($sql)) {
        echo "Status column added successfully!<br>";
        
        // Set all existing users to active
        $db->table('users')->update(['status' => 'active']);
        echo "All existing users set to active!<br>";
    } else {
        echo "Error adding column: " . $db->error()['message'] . "<br>";
    }
}

echo "<br>Done! You can now delete this file.";

