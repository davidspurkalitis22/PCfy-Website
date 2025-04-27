<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if there's an order in the session
if (isset($_SESSION['order'])) {
    // Unset the order from the session
    unset($_SESSION['order']);
    
    // Set a response status
    $response = array('success' => true, 'message' => 'Order cleared from session');
} else {
    // No order to clear
    $response = array('success' => false, 'message' => 'No order found in session');
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 