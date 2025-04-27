<?php
// Start session
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config.php';

// Function to safely redirect
function redirect($location, $message = '', $type = 'error') {
    global $conn;
    if (isset($conn)) {
        $conn->close();
    }
    $_SESSION[$type] = $message;
    header("Location: $location");
    exit();
}

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get user input from form
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = trim($_POST['phone']);
    $address = trim($_POST['address']);
    
    // Validate input
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($confirm_password) || empty($phone) || empty($address)) {
        redirect('register.php', 'Please fill in all fields.');
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect('register.php', 'Please enter a valid email address.');
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        redirect('register.php', 'Passwords do not match.');
    }
    
    try {
        // Check if email already exists
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $result = $check_stmt->get_result();
        
        if ($result->num_rows > 0) {
            redirect('register.php', 'Email already in use. Please use a different email or login.');
        }
        
        // Hash password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // Prepare SQL statement for insertion
        $sql = "INSERT INTO users (firstname, lastname, email, password, phone, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $firstname, $lastname, $email, $hashed_password, $phone, $address);
        
        if ($stmt->execute()) {
            redirect('login.php', 'Registration successful! You can now login.', 'success');
        } else {
            throw new Exception("Registration failed: " . $stmt->error);
        }
    } catch (Exception $e) {
        redirect('register.php', 'Registration error: ' . $e->getMessage());
    }
} else {
    redirect('register.php');
} 