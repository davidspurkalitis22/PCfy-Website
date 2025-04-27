<?php
// Enable full error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session to check session variables
session_start();

// Output header for readability
echo "<h1>Password Reset Debugging</h1>";
echo "<p>This file helps diagnose issues with the password reset process.</p>";

echo "<h2>Session Information</h2>";
echo "<pre>";
// Show if reset_email exists
if (isset($_SESSION['reset_email'])) {
    echo "reset_email: " . $_SESSION['reset_email'] . "\n";
} else {
    echo "reset_email: NOT SET\n";
}

// Show other session variables
echo "\nAll Session Variables:\n";
var_dump($_SESSION);
echo "</pre>";

echo "<h2>File System Check</h2>";
echo "<pre>";
// Check if the needed files exist
echo "forgot-password.php exists: " . (file_exists('forgot-password.php') ? 'YES' : 'NO') . "\n";
echo "forgot_password.php exists: " . (file_exists('forgot_password.php') ? 'YES' : 'NO') . "\n";
echo "reset-password.php exists: " . (file_exists('reset-password.php') ? 'YES' : 'NO') . "\n";
echo "reset_password.php exists: " . (file_exists('reset_password.php') ? 'YES' : 'NO') . "\n";
echo "</pre>";

echo "<h2>Database Check</h2>";
echo "<pre>";
// Test database connection
require_once 'config.php';
if (isset($conn) && !$conn->connect_error) {
    echo "Database connection: SUCCESS\n";
    
    // Check if reset_code column exists in users table
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_code'");
    echo "reset_code column in users table: " . ($result->num_rows > 0 ? 'EXISTS' : 'MISSING') . "\n";
    
    // Check if reset_expiry column exists in users table
    $result = $conn->query("SHOW COLUMNS FROM users LIKE 'reset_expiry'");
    echo "reset_expiry column in users table: " . ($result->num_rows > 0 ? 'EXISTS' : 'MISSING') . "\n";
    
    // Check if password_reset_tokens table exists
    $result = $conn->query("SHOW TABLES LIKE 'password_reset_tokens'");
    echo "password_reset_tokens table: " . ($result->num_rows > 0 ? 'EXISTS' : 'MISSING') . "\n";
} else {
    echo "Database connection: FAILED\n";
    if (isset($conn)) {
        echo "Error: " . $conn->connect_error . "\n";
    } else {
        echo "Error: Connection variable not set\n";
    }
}
echo "</pre>";

echo "<h2>Reset Test Links</h2>";
echo "<p><a href='forgot-password.php'>Test forgot-password.php</a></p>";
echo "<p><a href='forgot_password.php'>Test forgot_password.php</a></p>";
echo "<p><a href='reset-password.php'>Test reset-password.php directly</a></p>";

echo "<h2>Headers Check</h2>";
echo "<pre>";
// Check what headers would be sent (without actually sending them)
function check_redirect($file) {
    // Get file contents
    $contents = file_get_contents($file);
    
    // Look for header() calls with Location:
    preg_match_all('/header\s*\(\s*["\']Location:\s*([^"\']+)["\']/', $contents, $matches);
    
    if (!empty($matches[1])) {
        foreach ($matches[1] as $redirect) {
            echo "$file redirects to: $redirect\n";
        }
    } else {
        echo "$file has no redirect headers found\n";
    }
}

// Check redirects in both files
if (file_exists('forgot-password.php')) {
    check_redirect('forgot-password.php');
}

if (file_exists('forgot_password.php')) {
    check_redirect('forgot_password.php');
}
echo "</pre>";
?> 