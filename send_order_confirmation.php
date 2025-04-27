<?php
/**
 * Order Confirmation Email Sender
 * 
 * This file is used to send order confirmation emails to customers
 * after successful checkout.
 */

// Include config file and email helper
require_once 'config.php';
require_once 'email_helper.php';

/**
 * Send order confirmation email to the customer
 * 
 * @param string $order_number The order reference number
 * @param string $first_name Customer's first name
 * @param string $last_name Customer's last name
 * @param string $email Customer's email address
 * @param float $order_total The total amount of the order
 * @param array $order_items Array of items in the order
 * @param string $address Customer's address
 * @param string $city Customer's city
 * @param string $county Customer's county
 * @param string $postal_code Customer's postal code
 * @param string $payment_method Payment method used
 * @return array Status of email sending operation
 */
function send_order_confirmation($order_number, $first_name, $last_name, $email, $order_total, $order_items, 
                               $address, $city, $county, $postal_code, $payment_method) {
    
    // Log the request
    error_log("Sending order confirmation email for order: $order_number to $email");
    
    // Create item list for email
    $items_html = '';
    $items_text = '';
    
    foreach ($order_items as $item) {
        $items_html .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . htmlspecialchars($item['product_name']) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">' . $item['quantity'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">€' . number_format($item['price'], 2) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">€' . number_format($item['price'] * $item['quantity'], 2) . '</td>
        </tr>';
        
        $items_text .= "\n" . $item['product_name'] . " x" . $item['quantity'] . " - €" . number_format($item['price'] * $item['quantity'], 2);
    }
    
    // Create email content 
    $emailContent = '
    <p>Dear ' . htmlspecialchars($first_name . ' ' . $last_name) . ',</p>
    
    <p>Thank you for your order! We\'re pleased to confirm that your order has been received and is now being processed.</p>
    
    <div style="background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; margin: 15px 0; border-radius: 5px;">
        <p><strong>Order Number:</strong> ' . htmlspecialchars($order_number) . '</p>
    </div>
    
    <h3 style="color: #2a3990; border-bottom: 1px solid #eee; padding-bottom: 10px;">Order Summary</h3>
    
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f3f3f3;">
                <th style="padding: 10px; text-align: left;">Product</th>
                <th style="padding: 10px; text-align: center;">Quantity</th>
                <th style="padding: 10px; text-align: right;">Price</th>
                <th style="padding: 10px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            ' . $items_html . '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold;">Order Total:</td>
                <td style="padding: 10px; text-align: right; font-weight: bold;">€' . number_format($order_total, 2) . '</td>
            </tr>
        </tfoot>
    </table>
    
    <h3 style="color: #2a3990; border-bottom: 1px solid #eee; padding-bottom: 10px;">Shipping Address</h3>
    <p>
        ' . htmlspecialchars($first_name . ' ' . $last_name) . '<br>
        ' . htmlspecialchars($address) . '<br>
        ' . htmlspecialchars($city) . ', ' . htmlspecialchars($county) . '<br>
        ' . htmlspecialchars($postal_code) . '
    </p>
    
    <h3 style="color: #2a3990; border-bottom: 1px solid #eee; padding-bottom: 10px;">Payment Method</h3>
    <p>' . ucfirst(htmlspecialchars($payment_method)) . '</p>
    
    <p>You can track your order status by contacting us directly.</p>
    
    <p>If you have any questions about your order, please contact us at ' . SITE_EMAIL . ' or by phone at 085 842 4769.</p>
    
    <p>Thank you for shopping with ' . SITE_NAME . '!</p>';
    
    // Plain text version
    $textContent = "Dear " . $first_name . " " . $last_name . ",

Thank you for your order! We're pleased to confirm that your order has been received and is now being processed.

Order Number: " . $order_number . "

ORDER SUMMARY:
" . $items_text . "
-----------------------------
Order Total: €" . number_format($order_total, 2) . "

SHIPPING ADDRESS:
" . $first_name . " " . $last_name . "
" . $address . "
" . $city . ", " . $county . "
" . $postal_code . "

PAYMENT METHOD:
" . ucfirst($payment_method) . "

You can track your order status by logging into your account on our website or contacting us directly.

If you have any questions about your order, please contact us at " . SITE_EMAIL . " or by phone at 085 863 9422.

Thank you for shopping with " . SITE_NAME . "!

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
Galway, Ireland";
    
    // Generate HTML email using template
    $htmlEmail = create_email_template(
        'Order Confirmation #' . $order_number,
        $emailContent,
        'View Your Order',
        'https://pcfy.ie/orders.php'
    );
    
    // Send email
    return send_email(
        $email,
        $first_name . ' ' . $last_name,
        'Your ' . SITE_NAME . ' Order #' . $order_number . ' Confirmation',
        $htmlEmail,
        $textContent,
        SITE_EMAIL,
        SITE_NAME . ' Orders'
    );
}

/**
 * Send order notification to admin
 * 
 * @param string $order_number The order reference number
 * @param string $first_name Customer's first name
 * @param string $last_name Customer's last name
 * @param string $email Customer's email address
 * @param float $order_total The total amount of the order
 * @param array $order_items Array of items in the order
 * @return array Status of email sending operation
 */
function send_order_notification_to_admin($order_number, $first_name, $last_name, $email, $order_total, $order_items) {
    
    // Create item list for email
    $items_html = '';
    $items_text = '';
    
    foreach ($order_items as $item) {
        $items_html .= '
        <tr>
            <td style="padding: 10px; border-bottom: 1px solid #eee;">' . htmlspecialchars($item['product_name']) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: center;">' . $item['quantity'] . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">€' . number_format($item['price'], 2) . '</td>
            <td style="padding: 10px; border-bottom: 1px solid #eee; text-align: right;">€' . number_format($item['price'] * $item['quantity'], 2) . '</td>
        </tr>';
        
        $items_text .= "\n" . $item['product_name'] . " x" . $item['quantity'] . " - €" . number_format($item['price'] * $item['quantity'], 2);
    }
    
    // Create email content for admin notification
    $emailContent = '
    <p>A new order has been placed on your website.</p>
    
    <div style="background-color: #f9f9f9; border: 1px solid #eee; padding: 15px; margin: 15px 0; border-radius: 5px;">
        <p><strong>Order Number:</strong> ' . htmlspecialchars($order_number) . '</p>
        <p><strong>Customer:</strong> ' . htmlspecialchars($first_name . ' ' . $last_name) . ' (' . htmlspecialchars($email) . ')</p>
        <p><strong>Total Amount:</strong> €' . number_format($order_total, 2) . '</p>
    </div>
    
    <h3 style="color: #2a3990; border-bottom: 1px solid #eee; padding-bottom: 10px;">Order Details</h3>
    
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
        <thead>
            <tr style="background-color: #f3f3f3;">
                <th style="padding: 10px; text-align: left;">Product</th>
                <th style="padding: 10px; text-align: center;">Quantity</th>
                <th style="padding: 10px; text-align: right;">Price</th>
                <th style="padding: 10px; text-align: right;">Total</th>
            </tr>
        </thead>
        <tbody>
            ' . $items_html . '
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 10px; text-align: right; font-weight: bold;">Order Total:</td>
                <td style="padding: 10px; text-align: right; font-weight: bold;">€' . number_format($order_total, 2) . '</td>
            </tr>
        </tfoot>
    </table>
    
    <p>Please log in to the admin panel to process this order.</p>';
    
    // Plain text version for admin
    $textContent = "A new order has been placed on your website.

Order Number: " . $order_number . "
Customer: " . $first_name . " " . $last_name . " (" . $email . ")
Total Amount: €" . number_format($order_total, 2) . "

ORDER DETAILS:
" . $items_text . "
-----------------------------
Order Total: €" . number_format($order_total, 2) . "

Please log in to the admin panel to process this order.

© " . date('Y') . " " . SITE_NAME . ". All rights reserved.
This is an automated message from your website.";
    
    // HTML email
    $htmlEmail = create_email_template(
        'New Order #' . $order_number,
        $emailContent
    );
    
    // Send email to admin with reply-to set to the customer
    return send_email(
        SITE_EMAIL,
        SITE_NAME . ' Admin',
        'New Order #' . $order_number . ' Received',
        $htmlEmail,
        $textContent,
        SITE_EMAIL,
        SITE_NAME . ' Orders',
        $email,
        $first_name . ' ' . $last_name
    );
} 