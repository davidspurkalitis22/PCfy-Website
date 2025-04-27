<?php
// Set script timeout to a higher value for large databases
set_time_limit(300);
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');

// Database configuration for cPanel
$db_host = "localhost"; // Usually localhost for cPanel
$db_user = "vflxfuj_admin"; // Your cPanel database username
$db_pass = "Davids123?!**"; // Your cPanel database password
$db_name = "vflxfuj_pcfy"; // Your cPanel database name

// Path to your SQL file upload
$sql_file = 'pcfy_database_backup.sql';

echo "<h1>Database Import Tool</h1>";

// Check if file exists
if (!file_exists($sql_file)) {
    die("<p style='color:red'>Error: SQL file not found at $sql_file. Please upload it to this directory first.</p>");
}

// Connect to the database
$conn = new mysqli($db_host, $db_user, $db_pass);

// Check connection
if ($conn->connect_error) {
    die("<p style='color:red'>Connection failed: " . $conn->connect_error . "</p>");
}

echo "<p>Connected to database server successfully.</p>";

// Select the database
if (!$conn->select_db($db_name)) {
    echo "<p style='color:red'>Database does not exist. Attempting to create it...</p>";
    
    // Try to create the database
    if ($conn->query("CREATE DATABASE IF NOT EXISTS `$db_name`")) {
        echo "<p style='color:green'>Database created successfully!</p>";
        $conn->select_db($db_name);
    } else {
        die("<p style='color:red'>Failed to create database: " . $conn->error . "</p>");
    }
}

echo "<p>Selected database: $db_name</p>";

// Read SQL file
$sql = file_get_contents($sql_file);

// Split SQL by semicolons to get individual queries
$queries = explode(';', $sql);

echo "<p>Beginning import process...</p>";
echo "<ul>";

$success_count = 0;
$error_count = 0;

// Execute each query
foreach ($queries as $i => $query) {
    $query = trim($query);
    if (empty($query)) continue;
    
    try {
        if ($conn->query($query)) {
            $success_count++;
        } else {
            $error_count++;
            echo "<li style='color:red'>Error in query " . ($i + 1) . ": " . $conn->error . "</li>";
        }
    } catch (Exception $e) {
        $error_count++;
        echo "<li style='color:red'>Exception in query " . ($i + 1) . ": " . $e->getMessage() . "</li>";
    }
    
    // Output progress for every 50 queries
    if ($i % 50 == 0 && $i > 0) {
        echo "<li>Processed $i queries...</li>";
        ob_flush();
        flush();
    }
}

echo "</ul>";

echo "<h2>Import Summary</h2>";
echo "<p>Total queries processed: " . count($queries) . "</p>";
echo "<p>Successful queries: $success_count</p>";
echo "<p>Failed queries: $error_count</p>";

if ($error_count == 0) {
    echo "<p style='color:green; font-weight:bold;'>Database import completed successfully!</p>";
} else {
    echo "<p style='color:orange; font-weight:bold;'>Database import completed with $error_count errors.</p>";
}

// Check tables
$result = $conn->query("SHOW TABLES");
if ($result->num_rows > 0) {
    echo "<h2>Tables in the database:</h2>";
    echo "<ul>";
    while ($row = $result->fetch_row()) {
        echo "<li>" . $row[0] . "</li>";
    }
    echo "</ul>";
}

// Close connection
$conn->close();

echo "<p>You can now <a href='index.php'>go to the homepage</a> or <a href='check_db.php'>check the database connection</a>.</p>";
?> 