<?php
// Start session
session_start();

// Start output buffering to prevent partial outputs
ob_start();

// Include config file and email helper
require_once 'config.php';
require_once 'email_helper.php';

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Store form data in session in case of error
        $_SESSION['form_data'] = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'subject' => $_POST['subject'] ?? '',
            'message' => $_POST['message'] ?? ''
        ];
        
        // Get form data and sanitize
        $name = htmlspecialchars(trim($_POST['name']));
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $subject = htmlspecialchars(trim($_POST['subject']));
        $message = htmlspecialchars(trim($_POST['message']));
        
        // Validate required fields
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            throw new Exception("All fields are required");
        }
        
        // Validate email format
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }
        
        // Create contact_messages table if it doesn't exist
        $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
            id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL,
            subject VARCHAR(255) NOT NULL,
            message TEXT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            is_read TINYINT(1) DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
        
        // Insert the message into the database
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param('ssss', $name, $email, $subject, $message);
        
        if (!$stmt->execute()) {
            throw new Exception("Failed to save message: " . $stmt->error);
        }
        
        $messageId = $stmt->insert_id;
        $stmt->close();
        
        try {
            // Send confirmation to user
            $userEmailContent = '
            <p>Dear ' . htmlspecialchars($name) . ',</p>
            
            <p>Thank you for contacting ' . SITE_NAME . '. This is to confirm that we have received your message regarding "<strong>' . htmlspecialchars($subject) . '</strong>".</p>
            
            <p>Our team will review your message and get back to you as soon as possible, usually within 24-48 hours.</p>
            
            <p>If your inquiry is urgent, please feel free to call us directly at 085 842 4769 during our business hours (Monday-Friday, 9am-6pm).</p>
            
            <p>We appreciate your interest in ' . SITE_NAME . ' and look forward to assisting you.</p>';

            // Plain text version
            $userTextContent = "Dear $name,

Thank you for contacting " . SITE_NAME . ". This is to confirm that we have received your message regarding \"$subject\".

Our team will review your message and get back to you as soon as possible, usually within 24-48 hours.

If your inquiry is urgent, please feel free to call us directly at 085 842 4769 during our business hours (Monday-Friday, 9am-6pm).

We appreciate your interest in " . SITE_NAME . " and look forward to assisting you.

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
Galway, Ireland";

            // Generate HTML email using template
            $userHtmlEmail = create_email_template(
                'Message Received',
                $userEmailContent,
                'Visit Our Website',
                'https://pcfy.ie'
            );

            // Send emails
            send_email(
                $email,
                $name,
                'Thank you for contacting ' . SITE_NAME,
                $userHtmlEmail,
                $userTextContent,
                SITE_EMAIL,
                SITE_NAME . ' Support'
            );
            
            // Send admin notification
            $adminEmailContent = '
            <div class="detail">
                <p><strong>New message from website contact form:</strong></p>
                
                <p><strong>Name:</strong> ' . htmlspecialchars($name) . '</p>
                <p><strong>Email:</strong> <a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></p>
                <p><strong>Subject:</strong> ' . htmlspecialchars($subject) . '</p>
                <p><strong>Message:</strong></p>
                <div style="background-color: #f5f5f5; padding: 15px; margin: 10px 0; border-left: 4px solid #2a3990;">
                    ' . nl2br(htmlspecialchars($message)) . '
                </div>
            </div>';
            
            send_email(
                SITE_EMAIL,
                SITE_NAME . ' Admin',
                'New Contact Form Submission - ' . $subject,
                $adminEmailContent,
                "New message from $name ($email):\n\n$message",
                $email,
                $name
            );
            
        } catch (Exception $emailEx) {
            // Log email error but continue with form submission
            error_log("Error sending contact form emails: " . $emailEx->getMessage());
        }
        
        // Clear form data from session on success
        unset($_SESSION['form_data']);
        
        // Set success message
        $_SESSION['contact_success'] = "Your message has been sent successfully. We'll get back to you soon!";
        
    } else {
        throw new Exception("Invalid request method");
    }
    
    // Clean output buffer
    if (ob_get_length()) ob_end_clean();
    
    // Redirect back to contact page
    header("Location: contact.php#contact-form");
    exit;
    
} catch (Exception $e) {
    // Log the error
    error_log("Contact form error: " . $e->getMessage());
    
    // Set error message
    $_SESSION['contact_error'] = "Sorry, we couldn't send your message. Please try again or contact us directly.";
    
    // Keep the form data in session to repopulate the form
    // (already stored at the beginning of the try block)
    
    // Clean output buffer
    if (ob_get_length()) ob_end_clean();
    
    // Redirect back to contact page
    header("Location: contact.php#contact-form");
    exit;
}
?> 