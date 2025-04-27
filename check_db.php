<?php
// Show basic info about database setup
echo "<h2>Database Settings Check</h2>";

// Your current settings
$host = "localhost";
$user = "vflxfuj_admin";
$pass = "Davids123?!**";
$dbname = "vflxfuj_pcfy";

echo "<p>Attempting to connect to database server...</p>";

// Try connecting to server without specifying database
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    echo "<p style='color:red'>Failed to connect to MySQL server: " . $conn->connect_error . "</p>";
} else {
    echo "<p style='color:green'>Successfully connected to MySQL server!</p>";
    
    // Get list of databases this user can access
    echo "<p>Databases accessible to this user:</p>";
    echo "<ul>";
    $result = $conn->query("SHOW DATABASES");
    $found = false;
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            echo "<li>" . $row['Database'] . "</li>";
            if ($row['Database'] == $dbname) {
                $found = true;
            }
        }
    }
    echo "</ul>";
    
    if (!$found) {
        echo "<p style='color:red'>Your database '$dbname' is not in the list of accessible databases.</p>";
        echo "<p>Please check that:</p>";
        echo "<ol>";
        echo "<li>The database name is spelled correctly</li>";
        echo "<li>User '$user' has been added to the database with proper permissions</li>";
        echo "</ol>";
    }
    
    // Try to select the database
    echo "<p>Attempting to select database '$dbname'...</p>";
    if ($conn->select_db($dbname)) {
        echo "<p style='color:green'>Successfully selected database!</p>";
        
        // Try to list tables
        $result = $conn->query("SHOW TABLES");
        if ($result) {
            echo "<p>Tables in this database:</p>";
            echo "<ul>";
            while ($row = $result->fetch_row()) {
                echo "<li>" . $row[0] . "</li>";
            }
            echo "</ul>";
        }
    } else {
        echo "<p style='color:red'>Could not select database: " . $conn->error . "</p>";
    }
    
    $conn->close();
}
?> 