<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Shipping Policy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Shipping Policy - PCFY</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/cart-dropdown.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .legal-content {
            max-width: 900px;
            margin: 40px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        }
        
        .legal-header {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .legal-header h1 {
            font-size: 28px;
            color: #333;
            margin-bottom: 10px;
        }
        
        .legal-header p {
            color: #666;
            font-size: 16px;
        }
        
        .legal-section {
            margin-bottom: 30px;
        }
        
        .legal-section h2 {
            font-size: 22px;
            color: #333;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid #eee;
        }
        
        .legal-section p {
            margin-bottom: 15px;
            line-height: 1.6;
            color: #555;
        }
        
        .legal-section ul {
            margin: 15px 0;
            padding-left: 20px;
        }
        
        .legal-section li {
            margin-bottom: 10px;
            line-height: 1.6;
            color: #555;
        }
        
        .shipping-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        .shipping-table th, .shipping-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .shipping-table th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #333;
        }
        
        .shipping-table tr:last-child td {
            border-bottom: none;
        }
        
        .last-updated {
            margin-top: 40px;
            font-style: italic;
            color: #777;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <div class="legal-content">
                <div class="legal-header">
                    <h1>Shipping Policy</h1>
                    <p>This page details our shipping methods, timeframes, and costs</p>
                </div>
                
                <div class="legal-section">
                    <h2>1. Processing Time</h2>
                    <p>All orders are processed within 1-2 business days (Monday to Friday, excluding holidays) after payment confirmation. If we are experiencing a high volume of orders, delays may occur. If there is a significant delay in shipping your order, we will contact you via email or phone.</p>
                    <p>For custom PC builds, please allow for a 5-10 business day build time depending on the complexity of the build and current order volume.</p>
                </div>
                
                <div class="legal-section">
                    <h2>2. Shipping Methods and Timeframes</h2>
                    <p>PCFY ships throughout Ireland using the following carriers:</p>
                    
                    <table class="shipping-table">
                        <thead>
                            <tr>
                                <th>Shipping Method</th>
                                <th>Estimated Delivery Time</th>
                                <th>Tracking</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Standard Delivery</td>
                                <td>2-4 business days</td>
                                <td>Yes</td>
                            </tr>
                            <tr>
                                <td>Local Pickup (Galway City)</td>
                                <td>By appointment</td>
                                <td>N/A</td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p>Delivery times may vary, especially during peak periods such as holidays, sales events, or adverse weather conditions. We are not responsible for any delays caused by the carrier.</p>
                    <p>For custom PC builds, we recommend local pickup to ensure safe handling of your system. If shipping is required, special packaging will be used, and this may extend processing time by 1-2 days.</p>
                </div>
                
                <div class="legal-section">
                    <h2>3. Shipping Costs</h2>
                    <p>Shipping costs are calculated during checkout based on weight, dimensions, and your delivery address. You will be able to see the shipping cost before completing your purchase.</p>
                    
                    <table class="shipping-table">
                        <thead>
                            <tr>
                                <th>Order Value</th>
                                <th>Standard Delivery</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>Under €50</td>
                                <td>€5.95</td>
                      
                            </tr>
                            <tr>
                                <td>€50 - €100</td>
                                <td>€7.95</td>
                             
                            </tr>
                            <tr>
                                <td>Over €100</td>
                                <td>€9.95</td>
                             
                            </tr>
                            <tr>
                                <td>Over €200</td>
                                <td>FREE</td>
                        
                            </tr>
                        </tbody>
                    </table>
                    
                    <p>Custom PC builds may have additional shipping costs due to size, weight, and special packaging requirements. These will be calculated during checkout or discussed with you directly for custom orders.</p>
                </div>
                
                <div class="legal-section">
                    <h2>4. Local Pickup</h2>
                    <p>We offer local pickup at our location in Galway City. This option is available at checkout. After placing your order, you will receive an email or phone call when your order is ready for pickup.</p>
                    <p>Pickup hours are Monday to Friday, 10:00 AM to 6:00 PM by appointment only. Please bring a valid photo ID and your order confirmation when collecting your order.</p>
                    <p>For custom PC builds, local pickup is strongly recommended to ensure safe handling of your system.</p>
                </div>
                
                <div class="legal-section">
                    <h2>5. Tracking Information</h2>
                    <p>Once your order has been shipped, you will receive a shipping confirmation email that includes your tracking number and a link to track your order.</p>
                    <p>Please allow up to 24 hours after receiving the shipping confirmation email for tracking information to become active.</p>
                </div>
                
                <div class="legal-section">
                    <h2>6. Delivery Issues</h2>
                    <p>If your package shows as delivered but you haven't received it, please check the following:</p>
                    <ul>
                        <li>Check with neighbors or other household members who may have accepted the delivery</li>
                        <li>Look for a delivery notice in your letterbox or around your property</li>
                        <li>Contact the carrier directly using the tracking number provided</li>
                        <li>If you still can't locate your package, please contact us at pcfygalway@gmail.com with your order number and tracking information</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>7. Shipping Large Items</h2>
                    <p>For large items such as custom-built PCs, additional shipping arrangements may be necessary. We may contact you directly to confirm shipping details and arrange a suitable delivery time.</p>
                    <p>Please ensure that someone is available to receive and inspect the package at the time of delivery.</p>
                </div>
                
                <div class="legal-section">
                    <h2>8. Changes to Shipping Address</h2>
                    <p>If you need to change your shipping address after placing an order, please contact us immediately at pcfygalway@gmail.com. We will do our best to accommodate your request, but we cannot guarantee that the shipping address can be changed once an order has been processed.</p>
                </div>
                
                <div class="legal-section">
                    <h2>9. Contact Us</h2>
                    <p>If you have any questions about our shipping policy, please contact us at:</p>
                    <p>Email: pcfygalway@gmail.com</p>
                    <p>Phone: 085 842 4769</p>
                </div>
                
                <p class="last-updated">Last updated: <?php echo date('F d, Y'); ?></p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html> 