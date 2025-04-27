<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Database Connection Test</h1>";

// Test array of possible database configurations
$configs = [
    [
        'host' => 'localhost',
        'user' => 'vflxfuj',
        'pass' => 'Davids123?!**',
        'name' => 'vflxfuj_pcfy',
        'desc' => 'Default configuration: Main account username'
    ],
    [
        'host' => 'localhost',
        'user' => 'vflxfuj_pcfy',
        'pass' => 'Davids123?!**',
        'name' => 'vflxfuj_pcfy',
        'desc' => 'Configuration with database prefix in username'
    ],
    [
        'host' => 'localhost', 
        'user' => 'vflxfuj_vflxfuj',
        'pass' => 'Davids123?!**',
        'name' => 'vflxfuj_pcfy',
        'desc' => 'Configuration with double prefix (sometimes used by cPanel)'
    ],
    [
        'host' => 'localhost',
        'user' => 'vflxfuj',
        'pass' => '',
        'name' => 'vflxfuj_pcfy',
        'desc' => 'Main account with empty password'
    ],
    [
        'host' => '127.0.0.1',
        'user' => 'vflxfuj',
        'pass' => 'Davids123?!**',
        'name' => 'vflxfuj_pcfy',
        'desc' => 'Using IP instead of localhost'
    ]
];

echo "<h2>Testing MySQL Connections:</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Description</th><th>Host</th><th>User</th><th>Database</th><th>Result</th></tr>";

foreach ($configs as $config) {
    echo "<tr>";
    echo "<td>" . $config['desc'] . "</td>";
    echo "<td>" . $config['host'] . "</td>";
    echo "<td>" . $config['user'] . "</td>";
    echo "<td>" . $config['name'] . "</td>";
    
    // Test connection
    try {
        $link = mysqli_connect($config['host'], $config['user'], $config['pass']);
        
        if ($link) {
            // Test database selection
            if (mysqli_select_db($link, $config['name'])) {
                echo "<td style='background-color: #d4edda;'>SUCCESS: Connected to database!</td>";
            } else {
                echo "<td style='background-color: #fff3cd;'>PARTIAL: Connected to MySQL but database selection failed: " . mysqli_error($link) . "</td>";
            }
            mysqli_close($link);
        } else {
            echo "<td style='background-color: #f8d7da;'>FAILED: " . mysqli_connect_error() . "</td>";
        }
    } catch (Exception $e) {
        echo "<td style='background-color: #f8d7da;'>EXCEPTION: " . $e->getMessage() . "</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

// Show PHP and MySQL information
echo "<h2>PHP MySQL Configuration:</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "MySQL Functions Available: " . (function_exists('mysqli_connect') ? 'Yes' : 'No') . "\n";
echo "PDO Available: " . (class_exists('PDO') ? 'Yes' : 'No') . "\n";
echo "PDO MySQL Driver Available: " . (in_array('mysql', PDO::getAvailableDrivers()) ? 'Yes' : 'No') . "\n";
echo "</pre>";

echo "<h2>Server Information:</h2>";
echo "<pre>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "\n";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "\n";
echo "Script Filename: " . $_SERVER['SCRIPT_FILENAME'] . "\n";
echo "</pre>";

echo "<p>Take note of which configuration works and update your config.php file with those settings.</p>";
?> 