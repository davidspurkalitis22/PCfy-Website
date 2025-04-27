<?php
// Start session
session_start();

// Include database configuration
require_once 'config.php';

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // User is not logged in, redirect to login page
    header("Location: login.php");
    exit();
}

// Get user ID from session
$userid = $_SESSION['userid'];

// Get form data
$firstname = $_POST['firstname'];
$lastname = $_POST['lastname'];
$email = $_POST['email'];
$phone = $_POST['phone'];
$address = $_POST['address'];

// Update user information
$sql = "UPDATE users SET firstname = ?, lastname = ?, email = ?, phone = ?, address = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssi", $firstname, $lastname, $email, $phone, $address, $userid);

if ($stmt->execute()) {
    // Update successful, update session variables
    $_SESSION['firstname'] = $firstname;
    $_SESSION['lastname'] = $lastname;
    $_SESSION['email'] = $email;
    
    $_SESSION['success'] = "Profile updated successfully!";
} else {
    $_SESSION['error'] = "Error updating profile: " . $conn->error;
}

// Redirect back to profile page
header("Location: profile.php");
exit();

$conn->close();
?> 