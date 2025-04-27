<?php
// Start session
session_start();

// Start output buffering to prevent partial outputs
ob_start();

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    // Include database connection
    require_once 'config.php';
    require_once 'email_helper.php';

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Get form data and sanitize
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $phone = htmlspecialchars(trim($_POST['phone']));
        $serviceType = htmlspecialchars(trim($_POST['service-type']));
        $preferredDate = !empty($_POST['preferred-date']) ? htmlspecialchars(trim($_POST['preferred-date'])) : null;
        $issue = htmlspecialchars(trim($_POST['issue']));

        // Validate required fields
        if (empty($name) || empty($email) || empty($phone) || empty($serviceType) || empty($issue)) {
            throw new Exception("All required fields must be completed");
        }

        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        // Insert into database using direct query for simplicity
        $sql = "INSERT INTO repair_bookings 
                (name, email, phone, service_type, issue_description, preferred_date, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, 'pending', NOW())";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("ssssss", 
            $name, 
            $email, 
            $phone, 
            $serviceType, 
            $issue, 
            $preferredDate
        );
        
        if (!$stmt->execute()) {
            throw new Exception("Database insert failed: " . $stmt->error);
        }
        
        // Get the inserted ID to use as booking reference
        $bookingId = $conn->insert_id;
        $bookingRef = 'PCF-' . $bookingId;
        
        // Set success message
        $_SESSION['booking_success'] = "Your repair request has been submitted successfully. We'll contact you shortly to confirm your booking.";
        
        // Map service type to human-readable name
        $serviceTypeNames = [
            'hardware' => 'Hardware Repair',
            'software' => 'Software Solution',
            'diagnostics' => 'Diagnostics',
            'other' => 'Other Service'
        ];
        
        $serviceTypeName = isset($serviceTypeNames[$serviceType]) ? $serviceTypeNames[$serviceType] : 'Service';
        
        // Format preferred date
        $formattedDate = !empty($preferredDate) ? date('l, F j, Y', strtotime($preferredDate)) : 'Not specified';
        
        // Try to send confirmation email - don't stop process if email fails
        try {
            $emailContent = '
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            
            <p>Thank you for booking a repair service with PCFY. We have received your request and are processing it.</p>
            
            <p><strong>Booking Reference:</strong> ' . $bookingRef . '</p>
            
            <p><strong>Service Details:</strong></p>
            <ul>
                <li><strong>Service Type:</strong> ' . htmlspecialchars($serviceTypeName) . '</li>
                <li><strong>Preferred Date:</strong> ' . htmlspecialchars($formattedDate) . '</li>
            </ul>
            
            <p><strong>Your Issue Description:</strong><br>
            ' . nl2br(htmlspecialchars($issue)) . '</p>
            
            <p>One of our technicians will contact you within 24 hours to confirm your appointment and discuss any additional details.</p>
            
            <p>If you need to make any changes to your booking or have any questions, please contact us at ' . SITE_EMAIL . ' or by phone at 085 842 4769.</p>
            
            <p>We appreciate your business and look forward to helping you resolve your computer issues.</p>';
            
            // Plain text version
            $textContent = "Dear $name,

Thank you for booking a repair service with PCFY. We have received your request and are processing it.

Booking Reference: $bookingRef

Service Details:
- Service Type: $serviceTypeName
- Preferred Date: $formattedDate

Your Issue Description:
$issue

One of our technicians will contact you within 24 hours to confirm your appointment and discuss any additional details.

If you need to make any changes to your booking or have any questions, please contact us at " . SITE_EMAIL . " or by phone at 085 842 4769.

We appreciate your business and look forward to helping you resolve your computer issues.

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
Galway, Ireland";

            // Generate HTML email using template
            $htmlEmail = create_email_template(
                'Repair Service Booking Confirmation',
                $emailContent,
                'Visit Our Website',
                'https://pcfy.ie'
            );
            
            // Send confirmation email
            send_email(
                $email,
                $name,
                'Your PCFY Repair Service Booking Confirmation',
                $htmlEmail,
                $textContent,
                SITE_EMAIL,
                SITE_NAME . ' Service Center'
            );
            
        } catch (Exception $emailEx) {
            // Log email error but continue with form submission
            error_log("Error sending repair booking confirmation email: " . $emailEx->getMessage());
        }
        
        // Clean output buffer
        if (ob_get_length()) ob_end_clean();
        
        // Redirect to success page
        header("Location: repair-services.php#book-repair");
        exit;
    } else {
        // Not a POST request
        if (ob_get_length()) ob_end_clean();
        header("Location: repair-services.php");
        exit;
    }
} catch (Exception $e) {
    // Log the error
    error_log("Error in process_repair_booking.php: " . $e->getMessage());
    
    // Set error message
    $_SESSION['booking_error'] = "Sorry, we couldn't process your booking. Please try again or contact us directly.";
    
    // Clean output buffer
    if (ob_get_length()) ob_end_clean();
    
    // Redirect back to form
    header("Location: repair-services.php#book-repair");
    exit;
}
?> 