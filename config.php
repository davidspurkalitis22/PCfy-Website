<?php
// Database configuration - Using production credentials only
$db_host = "localhost"; 
$db_user = "vflxfuj_admin"; 
$db_pass = "Davids123?!**"; 
$db_name = "vflxfuj_pcfy"; 

/*
IMPORTANT: 
1. These are production database credentials
2. The site will connect to the production database regardless of environment
*/

// Create mysqli connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection - throw exception instead of die()
if ($conn->connect_error) {
    throw new Exception("Database connection failed: " . $conn->connect_error);
}

// Set charset to utf8mb4
$conn->set_charset("utf8mb4");

// Create PDO connection for checkout page
try {
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);
} catch (PDOException $e) {
    throw new Exception("PDO Connection failed: " . $e->getMessage());
}

// Configuration settings for the website

// SendGrid API key - THIS KEY IS INVALID OR EXPIRED
// Follow these steps to get a new API key:
// 1. Login to your SendGrid account at sendgrid.com
// 2. Go to Settings > API Keys
// 3. Create a new API key with Mail Send permissions
// 4. Copy the key and replace the value below
// 5. Make sure to verify your sender email at Settings > Sender Authentication
define('SENDGRID_API_KEY', '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');

// Email settings
define('SITE_EMAIL', 'pcfygalway@gmail.com');
define('SITE_NAME', 'PCFY');

// Other configuration settings can be added here
?>