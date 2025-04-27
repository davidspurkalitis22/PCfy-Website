<?php
// Include config file and email helper
require_once 'config.php';
require_once 'email_helper.php';

// Start output buffering
ob_start();

// For debugging, enable error display temporarily
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Initialize response array
$response = [
    'success' => false,
    'message' => 'An unknown error occurred'
];

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data
        $name = isset($_POST['name']) ? $_POST['name'] : '';
        $email = isset($_POST['email']) ? $_POST['email'] : '';
        $build_summary = isset($_POST['build_summary']) ? $_POST['build_summary'] : '';
        
        // Debug: Log form data
        error_log("Debug - Received data: name=$name, email=$email");
        error_log("Debug - Build summary: " . substr($build_summary, 0, 100) . "...");
        
        if (empty($name) || empty($email)) {
            throw new Exception("Missing required fields for confirmation email");
        }
        
        // Create email content
        $emailContent = '
        <p>Dear ' . htmlspecialchars($name) . ',</p>
        
        <p>Thank you for submitting your custom PC build request with ' . SITE_NAME . '. We have received your details and are excited to help you create your perfect PC.</p>
        
        <p>Our team will review your specifications and get back to you within 24-48 hours with a detailed quote and any recommendations we might have.</p>
        
        <p>If you have any questions in the meantime, please feel free to contact us directly at ' . SITE_EMAIL . ' or by phone at 085 863 9422.</p>
        
        <p>We appreciate your interest in ' . SITE_NAME . ' and look forward to building your custom PC.</p>';
        
        // Plain text version for email clients that don't support HTML
        $textContent = "Dear $name,

Thank you for submitting your custom PC build request with " . SITE_NAME . ". We have received your details and are excited to help you create your perfect PC.

Our team will review your specifications and get back to you within 24-48 hours with a detailed quote and any recommendations we might have.

If you have any questions in the meantime, please feel free to contact us directly at " . SITE_EMAIL . " or by phone at 085 863 9422.

We appreciate your interest in " . SITE_NAME . " and look forward to building your custom PC.

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
Galway, Ireland";

        // Generate HTML email using template
        $htmlEmail = create_email_template(
            'Custom PC Build Request Confirmation',
            $emailContent,
            'Visit Our Website',
            'https://pcfy.ie'
        );
        
        // Debug log the request
        error_log("Debug - Sending to customer email: $email");
        
        // Send confirmation email
        $emailResult = send_email(
            $email,
            $name,
            'Your ' . SITE_NAME . ' Custom PC Build Request Confirmation',
            $htmlEmail,
            $textContent,
            SITE_EMAIL,
            SITE_NAME . ' Customer Support'
        );
        
        if ($emailResult['success']) {
            $response = [
                'success' => true,
                'message' => 'Confirmation email sent successfully'
            ];
            error_log("Confirmation email sent successfully to $email");
        } else {
            throw new Exception("Failed to send confirmation email: " . $emailResult['message']);
        }

    } catch (Exception $e) {
        $response = [
            'success' => false,
            'message' => $e->getMessage()
        ];
        error_log("Error in send_confirmation_email.php: " . $e->getMessage());
    }
}

// Clear any output buffer
if (ob_get_length()) {
    ob_end_clean();
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
exit; 