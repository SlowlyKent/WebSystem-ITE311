<?php
/**
 * VERY SIMPLE SCRIPT - Just add status column
 * Access this file: http://localhost/ITE311-FELICIA/add_status_simple.php
 * Then DELETE this file after running!
 */

// Database connection settings - adjust if needed
$host = 'localhost';
$username = 'root';
$password = ''; // Usually empty for XAMPP
$database = 'lms_felicia'; // Database name from your config

// Connect to database
$conn = new mysqli($host, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if column exists
$check = $conn->query("SHOW COLUMNS FROM `users` LIKE 'status'");

if ($check->num_rows > 0) {
    echo "✅ Status column already exists!<br>";
} else {
    // Add the status column
    $sql = "ALTER TABLE `users` 
            ADD COLUMN `status` ENUM('active', 'inactive') DEFAULT 'active' AFTER `role`";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Status column added successfully!<br>";
        
        // Set all existing users to active
        $updateSql = "UPDATE `users` SET `status` = 'active'";
        if ($conn->query($updateSql) === TRUE) {
            echo "✅ All existing users set to active!<br>";
        }
    } else {
        echo "❌ Error: " . $conn->error . "<br>";
    }
}

$conn->close();

echo "<br><strong>✅ Done! Please DELETE this file now for security!</strong>";

