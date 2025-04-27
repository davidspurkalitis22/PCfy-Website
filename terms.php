<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Terms & Conditions';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Terms & Conditions - PCFY</title>
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
                    <h1>Terms and Conditions</h1>
                    <p>Please read these terms and conditions carefully before using our services</p>
                </div>
                
                <div class="legal-section">
                    <h2>1. Introduction</h2>
                    <p>Welcome to PCFY. These terms and conditions outline the rules and regulations for the use of PCFY's website.</p>
                    <p>By accessing this website, we assume you accept these terms and conditions in full. Do not continue to use PCFY's website if you do not accept all of the terms and conditions stated on this page.</p>
                </div>
                
                <div class="legal-section">
                    <h2>2. Definitions</h2>
                    <p>The following terminology applies to these Terms and Conditions, Privacy Statement and Disclaimer Notice and any or all Agreements: "Client", "You" and "Your" refers to you, the person accessing this website and accepting the Company's terms and conditions. "The Company", "Ourselves", "We", "Our" and "Us", refers to our Company (PCFY). "Party", "Parties", or "Us", refers to both the Client and ourselves, or either the Client or ourselves.</p>
                </div>
                
                <div class="legal-section">
                    <h2>3. Products and Services</h2>
                    <p>All products and services displayed on our website are subject to availability. We reserve the right to discontinue any product or service at any time.</p>
                    <p>Prices for our products and services are subject to change without notice. We reserve the right to modify or discontinue the Service (or any part or content thereof) without notice at any time.</p>
                    <p>We shall not be liable to you or to any third-party for any modification, price change, suspension or discontinuance of the Service.</p>
                </div>
                
                <div class="legal-section">
                    <h2>4. Orders and Payments</h2>
                    <p>When you place an order with us, you are offering to purchase a product or service. All orders are subject to acceptance and availability.</p>
                    <p>Payment for all orders must be made in full at the time of ordering. We accept payment via the methods displayed on our website.</p>
                    <p>For custom PC builds, a 50% deposit is required to begin the build process, with the remaining balance due before delivery or collection.</p>
                </div>
                
                <div class="legal-section">
                    <h2>5. Delivery and Collection</h2>
                    <p>Orders for in-stock items are typically processed within 1-2 business days. Delivery times vary depending on location and are not guaranteed.</p>
                    <p>For custom PC builds, please allow 5-10 business days for completion, depending on complexity and component availability.</p>
                    <p>Local collection is available from our location in Galway City by appointment only.</p>
                </div>
                
                <div class="legal-section">
                    <h2>6. Warranty and Returns</h2>
                    <p>All custom PC builds come with a 1-year warranty covering all parts and labor, unless otherwise specified.</p>
                    <p>Component-only purchases are covered by the manufacturer's warranty.</p>
                    <p>For repairs, we offer a 90-day warranty on the specific repair work completed.</p>
                    <p>Returns for unopened products are accepted within 14 days of purchase. A 15% restocking fee may apply.</p>
                </div>
                
                <div class="legal-section">
                    <h2>7. User Accounts</h2>
                    <p>When you create an account with us, you guarantee that the information you provide is accurate, complete, and current at all times.</p>
                    <p>You are responsible for maintaining the confidentiality of your account and password and for restricting access to your computer. You agree to accept responsibility for all activities that occur under your account or password.</p>
                </div>
                
                <div class="legal-section">
                    <h2>8. Intellectual Property</h2>
                    <p>The content, organization, graphics, design, compilation, magnetic translation, digital conversion, and other matters related to the Site are protected under applicable copyrights, trademarks, and other proprietary rights.</p>
                    <p>The copying, redistribution, use, or publication by you of any such matters or any part of the Site is strictly prohibited.</p>
                </div>
                
                <div class="legal-section">
                    <h2>9. Limitation of Liability</h2>
                    <p>PCFY shall not be liable for any direct, indirect, incidental, special, or consequential damages that result from the use of, or the inability to use, the Site or the products and services purchased through the Site.</p>
                    <p>In no event shall our total liability to you for all damages, losses, and causes of action exceed the amount paid by you, if any, for accessing this Site or purchasing products or services from us.</p>
                </div>
                
                <div class="legal-section">
                    <h2>10. Governing Law</h2>
                    <p>These terms and conditions are governed by and construed in accordance with the laws of Ireland, and you irrevocably submit to the exclusive jurisdiction of the courts in Ireland.</p>
                </div>
                
                <p class="last-updated">Last updated: <?php echo date('F d, Y'); ?></p>
            </div>
        </div>
    </main>

    <?php include 'footer.php'; ?>
</body>
</html> 