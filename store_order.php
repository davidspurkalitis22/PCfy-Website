<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Get the raw POST data
$jsonData = file_get_contents('php://input');

// Decode the JSON data
$data = json_decode($jsonData, true);

// Set default response
$response = ['success' => false];

// Check if data is valid
if ($data && isset($data['orderNumber']) && isset($data['orderTotal']) && isset($data['items'])) {
    // Store order information in session
    $_SESSION['order'] = [
        'orderNumber' => $data['orderNumber'],
        'orderDate' => date('Y-m-d H:i:s'),
        'orderTotal' => $data['orderTotal'],
        'items' => $data['items']
    ];
    
    $response['success'] = true;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
?> 