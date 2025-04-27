<?php
// Include the database configuration
require_once 'config.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Log debug information
error_log("Starting table creation process");
error_log("Database connection status: " . ($conn ? "Connected" : "Failed"));

// First, check if users table exists
$userTable = $conn->query("SHOW TABLES LIKE 'users'");
if ($userTable->num_rows == 0) {
    echo "Error: The users table does not exist in the database. Please run setup_database.php first.";
    echo "<br><br><a href='setup_database.php'>Run Setup Database</a>";
    exit;
}

// SQL to create the repair_bookings table
$sql = "CREATE TABLE IF NOT EXISTS `repair_bookings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_ref` varchar(15) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(30) NOT NULL,
  `service_type` varchar(50) NOT NULL,
  `preferred_date` date DEFAULT NULL,
  `issue` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pending',
  `created_at` datetime NOT NULL,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_ref` (`booking_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

// Execute the SQL
try {
    if ($conn->query($sql) === TRUE) {
        error_log("Table repair_bookings created successfully");
        echo "Table repair_bookings created successfully<br>";
        
        // Now try to add the foreign key
        $alterSql = "ALTER TABLE repair_bookings 
                     ADD CONSTRAINT repair_bookings_ibfk_1 
                     FOREIGN KEY (user_id) REFERENCES users (id) 
                     ON DELETE SET NULL ON UPDATE CASCADE";
        
        if ($conn->query($alterSql) === TRUE) {
            echo "Foreign key constraint added successfully";
        } else {
            echo "Warning: Could not add foreign key constraint. Error: " . $conn->error;
        }
    } else {
        error_log("Error creating table: " . $conn->error);
        echo "Error creating table: " . $conn->error;
    }
} catch (Exception $e) {
    error_log("Exception occurred: " . $e->getMessage());
    echo "Exception occurred: " . $e->getMessage();
}

echo "<br><br><a href='repair-services.php'>Go back to Repair Services</a>";

// Close the connection
$conn->close();
?> 