<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Privacy Policy';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Privacy Policy - PCFY</title>
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
                    <h1>Privacy Policy</h1>
                    <p>This Privacy Policy explains how we collect, use, and protect your personal information</p>
                </div>
                
                <div class="legal-section">
                    <h2>1. Introduction</h2>
                    <p>PCFY ("we," "our," or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit our website or use our services.</p>
                    <p>Please read this Privacy Policy carefully. By accessing or using our website, you acknowledge that you have read, understood, and agree to be bound by all the terms of this Privacy Policy.</p>
                </div>
                
                <div class="legal-section">
                    <h2>2. Information We Collect</h2>
                    <p>We may collect personal information that you voluntarily provide to us when you:</p>
                    <ul>
                        <li>Register an account with us</li>
                        <li>Place an order for products or services</li>
                        <li>Sign up for our newsletter</li>
                        <li>Contact us via our contact form, email, or phone</li>
                        <li>Submit a repair request or booking</li>
                    </ul>
                    <p>The personal information we collect may include:</p>
                    <ul>
                        <li>Name</li>
                        <li>Email address</li>
                        <li>Phone number</li>
                        <li>Postal address</li>
                        <li>Payment information (processed securely through our payment processors)</li>
                        <li>Any other information you choose to provide</li>
                    </ul>
                    <p>We may also automatically collect certain information about your device, including:</p>
                    <ul>
                        <li>IP address</li>
                        <li>Browser type</li>
                        <li>Operating system</li>
                        <li>Pages visited and time spent on those pages</li>
                        <li>Referring website addresses</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>3. How We Use Your Information</h2>
                    <p>We may use the information we collect for various purposes, including to:</p>
                    <ul>
                        <li>Provide, maintain, and improve our services</li>
                        <li>Process transactions and send related information, including confirmations and invoices</li>
                        <li>Respond to your comments, questions, and requests</li>
                        <li>Send you technical notices, updates, security alerts, and support messages</li>
                        <li>Communicate with you about products, services, offers, and events</li>
                        <li>Monitor and analyze trends, usage, and activities in connection with our services</li>
                        <li>Detect, prevent, and address technical issues</li>
                        <li>Comply with legal obligations</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>4. Cookies and Tracking Technologies</h2>
                    <p>We use cookies and similar tracking technologies to track activity on our website and hold certain information. Cookies are files with a small amount of data that may include an anonymous unique identifier.</p>
                    <p>You can instruct your browser to refuse all cookies or to indicate when a cookie is being sent. However, if you do not accept cookies, you may not be able to use some portions of our website.</p>
                    <p>We use cookies for the following purposes:</p>
                    <ul>
                        <li>To maintain your shopping cart and order information</li>
                        <li>To remember your preferences and settings</li>
                        <li>To authenticate users and prevent fraudulent use of user accounts</li>
                        <li>To analyze how our website is used so we can improve it</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>5. Data Security</h2>
                    <p>We have implemented appropriate technical and organizational security measures designed to protect the security of any personal information we process. However, please note that no method of transmission over the Internet or electronic storage is 100% secure.</p>
                    <p>While we strive to use commercially acceptable means to protect your personal information, we cannot guarantee its absolute security. Any information you transmit to us is done at your own risk.</p>
                </div>
                
                <div class="legal-section">
                    <h2>6. Third-Party Service Providers</h2>
                    <p>We may employ third-party companies and individuals to facilitate our services, provide the service on our behalf, perform service-related services, or assist us in analyzing how our service is used.</p>
                    <p>These third parties have access to your personal information only to perform these tasks on our behalf and are obligated not to disclose or use it for any other purpose.</p>
                    <p>Our payment processing is handled by third-party payment processors who comply with PCI-DSS standards. We do not store your full credit card details on our servers.</p>
                </div>
                
                <div class="legal-section">
                    <h2>7. Your Data Protection Rights</h2>
                    <p>Under the General Data Protection Regulation (GDPR) and other applicable data protection laws, you have certain rights regarding your personal data, including:</p>
                    <ul>
                        <li>The right to access - You have the right to request copies of your personal data.</li>
                        <li>The right to rectification - You have the right to request that we correct any information you believe is inaccurate or complete information you believe is incomplete.</li>
                        <li>The right to erasure - You have the right to request that we erase your personal data, under certain conditions.</li>
                        <li>The right to restrict processing - You have the right to request that we restrict the processing of your personal data, under certain conditions.</li>
                        <li>The right to object to processing - You have the right to object to our processing of your personal data, under certain conditions.</li>
                        <li>The right to data portability - You have the right to request that we transfer the data that we have collected to another organization, or directly to you, under certain conditions.</li>
                    </ul>
                    <p>To exercise any of these rights, please contact us at pcfy.galway@gmail.com.</p>
                </div>
                
                <div class="legal-section">
                    <h2>8. Data Retention</h2>
                    <p>We will retain your personal information only for as long as is necessary for the purposes set out in this Privacy Policy. We will retain and use your information to the extent necessary to comply with our legal obligations, resolve disputes, and enforce our policies.</p>
                </div>
                
                <div class="legal-section">
                    <h2>9. Children's Privacy</h2>
                    <p>Our services are not directed to individuals under the age of 16. We do not knowingly collect personal information from children under 16. If we become aware that a child under 16 has provided us with personal information, we will take steps to delete such information.</p>
                </div>
                
                <div class="legal-section">
                    <h2>10. Changes to This Privacy Policy</h2>
                    <p>We may update our Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date.</p>
                    <p>You are advised to review this Privacy Policy periodically for any changes. Changes to this Privacy Policy are effective when they are posted on this page.</p>
                </div>
                
                <div class="legal-section">
                    <h2>11. Contact Us</h2>
                    <p>If you have any questions about this Privacy Policy, please contact us at:</p>
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