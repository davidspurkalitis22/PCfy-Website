<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
require_once 'config.php';

echo "<h1>Database Connection Test</h1>";

if ($conn && $conn->ping()) {
    echo "<p style='color:green'>✓ Database connection successful!</p>";
} else {
    echo "<p style='color:red'>❌ Database connection failed: " . $conn->error . "</p>";
    exit;
}

// Test creating the repair_bookings table
echo "<h2>Testing repair_bookings table</h2>";

$createTableSQL = "CREATE TABLE IF NOT EXISTS repair_bookings (
    id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) NOT NULL,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    service_type VARCHAR(50) NOT NULL,
    preferred_date DATE NULL,
    issue TEXT NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

if ($conn->query($createTableSQL)) {
    echo "<p style='color:green'>✓ Table repair_bookings created successfully or already exists</p>";
} else {
    echo "<p style='color:red'>❌ Error creating table: " . $conn->error . "</p>";
}

// Check table structure
echo "<h2>Table Structure</h2>";
$result = $conn->query("DESCRIBE repair_bookings");

if ($result) {
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['Field']}</td>";
        echo "<td>{$row['Type']}</td>";
        echo "<td>{$row['Null']}</td>";
        echo "<td>{$row['Key']}</td>";
        echo "<td>{$row['Default']}</td>";
        echo "<td>{$row['Extra']}</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p style='color:red'>❌ Error getting table structure: " . $conn->error . "</p>";
}

// Test inserting a dummy record
echo "<h2>Testing Data Insertion</h2>";

$testName = 'Test User';
$testEmail = 'test@example.com';
$testPhone = '1234567890';
$testServiceType = 'diagnostics';
$testIssue = 'This is a test submission';

// Prepare the test query
$stmt = $conn->prepare("INSERT INTO repair_bookings 
    (name, email, phone, service_type, issue_description, status, created_at) 
    VALUES (?, ?, ?, ?, ?, 'test', NOW())");

if (!$stmt) {
    echo "<p style='color:red'>❌ Prepare failed: " . $conn->error . "</p>";
} else {
    $stmt->bind_param("sssss", 
        $testName, 
        $testEmail, 
        $testPhone, 
        $testServiceType, 
        $testIssue
    );
    
    if ($stmt->execute()) {
        echo "<p style='color:green'>✓ Test record inserted successfully</p>";
        
        // Remove test data
        $conn->query("DELETE FROM repair_bookings WHERE status = 'test'");
        echo "<p>Test data removed</p>";
    } else {
        echo "<p style='color:red'>❌ Error inserting test record: " . $stmt->error . "</p>";
    }
    
    $stmt->close();
}

$conn->close();
?> 