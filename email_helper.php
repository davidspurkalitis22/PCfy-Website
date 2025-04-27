<?php
/**
 * Email Helper Functions
 * 
 * This file contains reusable functions for sending emails via SendGrid
 */

// Include configuration
require_once 'config.php';

/**
 * Send an email using SendGrid API
 * 
 * @param string $to_email Recipient email address
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $html_content HTML version of email content
 * @param string $text_content Plain text version of email content
 * @param string $from_email (Optional) Sender email, defaults to SITE_EMAIL
 * @param string $from_name (Optional) Sender name, defaults to SITE_NAME
 * @param string $reply_to_email (Optional) Reply-to email, defaults to from_email
 * @param string $reply_to_name (Optional) Reply-to name, defaults to from_name
 * @return array Associative array with 'success' (boolean) and 'message' (string)
 */
function send_email($to_email, $to_name, $subject, $html_content, $text_content, 
                    $from_email = null, $from_name = null, 
                    $reply_to_email = null, $reply_to_name = null) {
    
    // Set default values if not provided
    $from_email = $from_email ?: SITE_EMAIL;
    $from_name = $from_name ?: SITE_NAME;
    $reply_to_email = $reply_to_email ?: $from_email;
    $reply_to_name = $reply_to_name ?: $from_name;
    
    // Debug log
    error_log("Preparing to send email to: $to_email ($to_name), Subject: $subject");
    
    try {
        // Configure SendGrid request
        $url = 'https://api.sendgrid.com/v3/mail/send';
        $data = [
            'personalizations' => [
                [
                    'to' => [
                        ['email' => $to_email, 'name' => $to_name]
                    ],
                    'subject' => $subject
                ]
            ],
            'from' => [
                'email' => $from_email,
                'name' => $from_name
            ],
            'reply_to' => [
                'email' => $reply_to_email, 
                'name' => $reply_to_name
            ],
            'content' => [
                [
                    'type' => 'text/plain',
                    'value' => $text_content
                ],
                [
                    'type' => 'text/html',
                    'value' => $html_content
                ]
            ]
        ];
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer ' . SENDGRID_API_KEY,
            'Content-Type: application/json'
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_VERBOSE, true);
        
        // Fix SSL certificate issues for local development
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        
        // Better error handling
        $verbose = fopen('php://temp', 'w+');
        curl_setopt($ch, CURLOPT_STDERR, $verbose);
        
        $server_response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        
        // Get verbose information
        rewind($verbose);
        $verbose_log = stream_get_contents($verbose);
        fclose($verbose);
        
        curl_close($ch);
        
        // Check if request was successful
        if (empty($curl_error) && $http_status >= 200 && $http_status < 300) {
            error_log("Email sent successfully to $to_email");
            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        } else {
            // Log detailed error
            $error_details = "SendGrid API Error: HTTP Status $http_status, Error: $curl_error";
            if (!empty($server_response)) {
                $error_details .= ", Response: $server_response";
            }
            $error_details .= ", Verbose: " . substr($verbose_log, 0, 500);
            error_log($error_details);
            
            return [
                'success' => false,
                'message' => "Failed to send email: $http_status - $curl_error",
                'details' => $error_details
            ];
        }
    } catch (Exception $e) {
        $error_message = "Error in send_email: " . $e->getMessage();
        error_log($error_message);
        return [
            'success' => false,
            'message' => $error_message
        ];
    }
}

/**
 * Creates HTML email template with standard PCFY branding
 * 
 * @param string $title Email title/heading
 * @param string $content Main email content (can include HTML)
 * @param string $button_text (Optional) Call-to-action button text
 * @param string $button_url (Optional) Call-to-action button URL
 * @return string Complete HTML email template
 */
function create_email_template($title, $content, $button_text = null, $button_url = null) {
    $button_html = '';
    if ($button_text && $button_url) {
        $button_html = '<a href="' . htmlspecialchars($button_url) . '" class="button">' . htmlspecialchars($button_text) . '</a>';
    }
    
    return '
    <!DOCTYPE html>
    <html>
    <head>
        <title>' . htmlspecialchars($title) . '</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #2a3990; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background-color: #f9f9f9; }
            h1 { margin-top: 0; }
            .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
            .button { display: inline-block; background-color: #2a3990; color: white; text-decoration: none; padding: 10px 20px; border-radius: 4px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <h1>' . htmlspecialchars($title) . '</h1>
            </div>
            <div class="content">
                ' . $content . '
                ' . $button_html . '
            </div>
            <div class="footer">
                <p>© ' . date('Y') . ' ' . SITE_NAME . '. All rights reserved.</p>
                <p>Galway, Ireland</p>
            </div>
        </div>
    </body>
    </html>';
}
?> 