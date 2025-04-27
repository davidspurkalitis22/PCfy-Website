<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// List of files to remove for security
$files_to_remove = [
    'create_admin.php',          // Admin creation tool - security risk
    'check_admin.php',           // Lists all admin accounts - security risk
    'setup_database.php',        // Database setup information - security risk
    'verify_login.php',          // Login verification details - security risk
    'login_debug.php',           // Login debugging information - security risk
    'test_api_key.php',          // API key testing - security risk
    'test_email.php',            // Email testing - development only
    'login_attempts.log',        // Login attempt logs - security risk
    'php_errors.log',            // Error logs - security risk
    'php_error.log',             // Error logs - security risk
    'composer-setup.php',        // Composer setup file - no longer needed
    'pcfy.sql',                  // SQL file - no longer needed
    'repair_bookings.sql',       // SQL file - no longer needed
    'update_db_for_reset.php',   // DB reset script - no longer needed
    'stripe-readme.md',          // Documentation - no longer needed in production
    'update_all_pages.php',      // Update script - development only
    'cleanup.php'                // This file will delete itself last
];

// Counter for successfully removed files
$removed_count = 0;
$failed_count = 0;
$already_missing = 0;

echo "<h1>Security Cleanup</h1>";
echo "<p>Removing unnecessary and security-sensitive files...</p>";
echo "<ul>";

// Process each file
foreach ($files_to_remove as $file) {
    if ($file === 'cleanup.php') {
        // Skip the cleanup file for now, we'll handle it at the end
        continue;
    }
    
    if (file_exists($file)) {
        // Try to remove the file
        if (unlink($file)) {
            echo "<li style='color: green;'>✓ Successfully removed: $file</li>";
            $removed_count++;
        } else {
            echo "<li style='color: red;'>✗ Failed to remove: $file</li>";
            $failed_count++;
        }
    } else {
        echo "<li style='color: blue;'>○ File already missing: $file</li>";
        $already_missing++;
    }
}

echo "</ul>";

// Summary
echo "<h2>Cleanup Summary</h2>";
echo "<p>Successfully removed: $removed_count files</p>";
echo "<p>Failed to remove: $failed_count files</p>";
echo "<p>Already missing: $already_missing files</p>";

// Add self-destruct script
echo "<script>
    setTimeout(function() {
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'cleanup_self.php', true);
        xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
        xhr.send('file=cleanup.php');
    }, 5000); // Wait 5 seconds
</script>";

// Create a temporary file to delete this one
$self_destruct = <<<EOT
<?php
// This script is created by cleanup.php to remove itself
if (isset(\$_POST['file']) && \$_POST['file'] === 'cleanup.php') {
    if (file_exists('cleanup.php')) {
        unlink('cleanup.php');
    }
    // Delete this file too
    unlink(__FILE__);
}
?>
EOT;

// Write the self-destruct file
file_put_contents('cleanup_self.php', $self_destruct);

echo "<p>This page will attempt to remove itself in 5 seconds.</p>";
echo "<p><a href='index.php'>Return to homepage</a></p>";
?> 