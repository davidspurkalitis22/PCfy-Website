<?php
// Start session
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true || $_SESSION['is_admin'] !== 1) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized access']);
    exit();
}

// Include database connection
require_once '../config.php';

// Check if product ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Product ID is required']);
    exit();
}

$product_id = $conn->real_escape_string($_GET['id']);

// Get product details
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("s", $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
    
    // Return product details as JSON
    header('Content-Type: application/json');
    echo json_encode($product);
} else {
    // Product not found
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Product not found']);
}

$stmt->close();
$conn->close();
?> 