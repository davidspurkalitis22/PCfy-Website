<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
session_start();

// Include database configuration
require_once 'config.php';

// Check connection
if (!isset($conn) || $conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

echo "<h1>Orders Table Structure</h1>";

// Get table structure
$sql = "DESCRIBE orders";
$result = $conn->query($sql);

if ($result) {
    echo "<h2>Table Structure</h2>";
    echo "<table border='1' cellpadding='5'>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>Error getting table structure: " . $conn->error . "</p>";
}

// Get sample data
echo "<h2>Sample Data</h2>";
$sql = "SELECT * FROM orders LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "<h3>Raw Data (First Row)</h3>";
    echo "<pre>";
    print_r($row);
    echo "</pre>";
} else {
    echo "<p>No data found or error: " . $conn->error . "</p>";
}

// Check for order_items table
echo "<h2>Order Items Table</h2>";

$sql = "SHOW TABLES LIKE 'order_items'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo "<p>order_items table exists.</p>";
    
    // Get structure
    $sql = "DESCRIBE order_items";
    $result = $conn->query($sql);
    
    if ($result) {
        echo "<h3>Structure</h3>";
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['Field']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Null']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Key']) . "</td>";
            echo "<td>" . htmlspecialchars($row['Default'] ?? 'NULL') . "</td>";
            echo "<td>" . htmlspecialchars($row['Extra']) . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
    }
    
    // Get sample data
    $sql = "SELECT * FROM order_items LIMIT 1";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo "<h3>Raw Data (First Row)</h3>";
        echo "<pre>";
        print_r($row);
        echo "</pre>";
    } else {
        echo "<p>No order items found.</p>";
    }
} else {
    echo "<p>order_items table does not exist.</p>";
}

// Close connection
$conn->close();
?> 