<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Returns & Refunds';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Returns & Refunds Policy - PCFY</title>
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
        
        .steps-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .steps-box h3 {
            margin-top: 0;
            color: #333;
            font-size: 18px;
            margin-bottom: 15px;
        }
        
        .steps-box ol {
            padding-left: 20px;
            margin-bottom: 0;
        }
        
        .steps-box li {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <main>
        <div class="container">
            <div class="legal-content">
                <div class="legal-header">
                    <h1>Returns & Refunds Policy</h1>
                    <p>This policy outlines our procedures for returns, exchanges, and refunds</p>
                </div>
                
                <div class="legal-section">
                    <h2>1. Return Policy Overview</h2>
                    <p>At PCFY, we want you to be completely satisfied with your purchase. We accept returns on most items within 14 days of delivery for a full refund or exchange, subject to the conditions outlined in this policy.</p>
                    <p>Different products have different return policies as detailed below. Please read this policy carefully before making a purchase.</p>
                </div>
                
                <div class="legal-section">
                    <h2>2. Standard Products Return Policy</h2>
                    <p>Most standard products (components, peripherals, accessories) purchased from PCFY can be returned within 14 days of delivery, provided they meet the following conditions:</p>
                    <ul>
                        <li>The item is in its original packaging</li>
                        <li>The item is unused, undamaged, and in resalable condition</li>
                        <li>All original tags, labels, and accessories are included</li>
                        <li>You have proof of purchase (order number, receipt, or invoice)</li>
                    </ul>
                    <p>A 15% restocking fee may apply to opened products that are otherwise in resalable condition.</p>
                </div>
                
                <div class="legal-section">
                    <h2>3. Custom PC Builds Return Policy</h2>
                    <p>Custom-built PCs have a more specific return policy due to their customized nature:</p>
                    <ul>
                        <li>Returns are accepted within 14 days of delivery if the PC is in its original condition and packaging</li>
                        <li>A 20% restocking fee applies to all custom PC returns unless the return is due to a defect or error on our part</li>
                        <li>If you wish to change specifications after ordering but before the build begins, please contact us immediately - modification fees may apply</li>
                        <li>Custom builds with special modifications or custom parts may not be eligible for return unless defective</li>
                    </ul>
                    <p>Note: Custom PCs are covered by our 1-year warranty for defects in workmanship and components, which is separate from our return policy.</p>
                </div>
                
                <div class="legal-section">
                    <h2>4. Non-Returnable Items</h2>
                    <p>The following items cannot be returned or exchanged unless they are defective:</p>
                    <ul>
                        <li>Software products with opened packaging or activated licenses</li>
                        <li>Items marked as "Final Sale," "Non-Returnable," or "As-Is"</li>
                        <li>Custom-made products that were built to your exact specifications</li>
                        <li>Products with removed or damaged manufacturer seals</li>
                        <li>Products that have been physically modified or altered by you</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>5. Defective Products</h2>
                    <p>If you receive a defective product:</p>
                    <ul>
                        <li>Contact us within 48 hours of receiving the product</li>
                        <li>Provide a detailed description of the defect and, if possible, photos or videos</li>
                        <li>We may troubleshoot with you to determine if the issue can be resolved remotely</li>
                        <li>If the product is determined to be defective, we will provide a prepaid return label and process a replacement or refund</li>
                    </ul>
                    <p>For defective items, the 14-day return period may be extended, and restocking fees will be waived.</p>
                </div>
                
                <div class="legal-section">
                    <h2>6. Return Process</h2>
                    <div class="steps-box">
                        <h3>How to Return an Item:</h3>
                        <ol>
                            <li>Contact us at pcfygalway@gmail.com or 085 842 4769 to request a return. Please include your order number, the items you wish to return, and the reason for the return.</li>
                            <li>Once your return request is approved, you will receive a Return Merchandise Authorization (RMA) number.</li>
                            <li>Package the item securely in its original packaging, if possible.</li>
                            <li>Include the RMA number on the outside of the package and on a note inside the package.</li>
                            <li>Ship the item to the address provided in the return instructions.</li>
                            <li>Keep the shipping receipt and tracking information until your return is processed.</li>
                        </ol>
                    </div>
                    <p>Returns sent without prior authorization or RMA number may be refused or delayed.</p>
                </div>
                
                <div class="legal-section">
                    <h2>7. Refund Process</h2>
                    <p>Once we receive your returned item and inspect it, we will process your refund:</p>
                    <ul>
                        <li>For approved returns, refunds will be issued to the original payment method used for the purchase</li>
                        <li>Standard refunds typically take 3-5 business days to process after we receive and inspect the item</li>
                        <li>Credit card refunds may take an additional 2-10 business days to appear on your statement, depending on your card issuer</li>
                        <li>Shipping charges are non-refundable unless the return is due to our error</li>
                        <li>Any applicable restocking fees will be deducted from the refund amount</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>8. Exchanges</h2>
                    <p>If you wish to exchange an item for a different product rather than receive a refund:</p>
                    <ul>
                        <li>Follow the same return process outlined above, but specify that you want an exchange</li>
                        <li>Indicate the item you would like to receive instead</li>
                        <li>If the replacement item costs more than the original, you will need to pay the difference</li>
                        <li>If the replacement item costs less, we will refund the difference</li>
                        <li>Exchanges are subject to product availability</li>
                    </ul>
                </div>
                
                <div class="legal-section">
                    <h2>9. Warranty Claims</h2>
                    <p>Warranty claims are different from returns:</p>
                    <ul>
                        <li>Custom PC builds come with a 1-year warranty covering parts and labor</li>
                        <li>Individual components purchased through us are covered by the manufacturer's warranty</li>
                        <li>For warranty service, contact us with your order number and a description of the issue</li>
                        <li>Warranty service may involve repair, component replacement, or system replacement depending on the issue</li>
                    </ul>
                    <p>See our full Warranty Terms for more details on coverage and exclusions.</p>
                </div>
                
                <div class="legal-section">
                    <h2>10. Contact Us</h2>
                    <p>If you have any questions about our Returns & Refunds Policy, please contact us at:</p>
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