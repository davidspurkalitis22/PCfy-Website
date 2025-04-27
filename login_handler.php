<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

// Create a log entry
function log_login_attempt($email, $status, $details = '') {
    $log_file = 'login_attempts.log';
    $timestamp = date('Y-m-d H:i:s');
    $ip = $_SERVER['REMOTE_ADDR'];
    $log_entry = "[$timestamp] IP: $ip | Email: $email | Status: $status | Details: $details\n";
    
    // Append to log file
    file_put_contents($log_file, $log_entry, FILE_APPEND);
}

// Validate inputs
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

// Basic validation
if (empty($email) || empty($password)) {
    $_SESSION['error'] = "Please enter both email and password.";
    log_login_attempt($email, 'FAIL', 'Empty email or password');
    header("Location: login.php");
    exit();
}

// Test database connection
if ($conn->connect_error) {
    $_SESSION['error'] = "Database connection failed. Please try again later.";
    log_login_attempt($email, 'ERROR', 'Database connection failed: ' . $conn->connect_error);
    header("Location: login.php");
    exit();
}

try {
    // Prepare SQL statement to prevent SQL injection
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        throw new Exception('Failed to prepare SQL statement: ' . $conn->error);
    }
    
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Verify password (using password_verify)
        if (password_verify($password, $user['password'])) {
            // Password is correct, set session variables
            $_SESSION['loggedin'] = true;
            $_SESSION['userid'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['is_admin'] = isset($user['is_admin']) ? $user['is_admin'] : 0;
            
            log_login_attempt($email, 'SUCCESS', 'User ID: ' . $user['id']);
            
            // Check if user is admin and redirect accordingly
            if ($_SESSION['is_admin'] == 1) {
                header("Location: admin/dashboard.php");
                exit();
            }
            
            // Check if there's a redirect after login
            if (isset($_SESSION['redirect_after_login'])) {
                $redirect = $_SESSION['redirect_after_login'];
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
                exit();
            } else {
                // Redirect to profile page
                header("Location: profile.php");
                exit();
            }
        } else {
            // Password is incorrect
            $_SESSION['error'] = "Invalid email or password. Please try again.";
            log_login_attempt($email, 'FAIL', 'Password verification failed for user ID: ' . $user['id']);
            header("Location: login.php");
            exit();
        }
    } else {
        // User not found
        $_SESSION['error'] = "Invalid email or password. Please try again.";
        log_login_attempt($email, 'FAIL', 'User not found');
        header("Location: login.php");
        exit();
    }
    
    $stmt->close();
} catch (Exception $e) {
    $_SESSION['error'] = "An error occurred during login. Please try again later.";
    log_login_attempt($email, 'ERROR', 'Exception: ' . $e->getMessage());
    header("Location: login.php");
    exit();
}

$conn->close();
?> 