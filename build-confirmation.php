<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Custom PC Build Confirmation';

// Include the header
include 'header.php';
?>

<!-- Confirmation Page Content -->
<div class="confirmation-container">
    <div class="container">
        <div class="confirmation-card">
            <div class="confirmation-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Thank You for Your Custom PC Build Request!</h1>
            <p>We have received your custom PC build request and our team is reviewing your specifications.</p>
            
            <div class="confirmation-details">
                <h2>What happens next?</h2>
                <ol>
                    <li>Our technicians will review your component selections for compatibility and optimal performance.</li>
                    <li>We'll prepare a detailed quote based on your specifications and current component pricing.</li>
                    <li>A team member will contact you within 1-2 business days via email or phone to discuss your build.</li>
                    <li>Once you approve the quote, we'll begin building your custom PC.</li>
                    <li>We'll keep you updated throughout the building process.</li>
                </ol>
            </div>
            
            <div class="confirmation-contact">
                <h2>Questions?</h2>
                <p>If you have any questions or need to make changes to your build request, please contact us:</p>
                <ul>
                    <li><i class="fas fa-phone"></i> <a href="tel:085-842-4769">085 842 4769</a></li>
                    <li><i class="fas fa-envelope"></i> <a href="mailto:pcfygalway@gmail.com">pcfygalway@gmail.com</a></li>
                </ul>
            </div>
            
            <div class="confirmation-actions">
                <a href="index.php" class="btn btn-secondary">Return to Home</a>
                <a href="custom-pcs.php" class="btn btn-primary">Create Another Build</a>
            </div>
        </div>
    </div>
</div>

<style>
.confirmation-container {
    padding: 80px 0;
    background-color: #f5f5f5;
    min-height: 80vh;
    display: flex;
    align-items: center;
}

.confirmation-card {
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    padding: 40px;
    text-align: center;
    max-width: 800px;
    margin: 0 auto;
}

.confirmation-icon {
    font-size: 80px;
    color: #4CAF50;
    margin-bottom: 20px;
}

.confirmation-card h1 {
    color: #333;
    margin-bottom: 20px;
    font-size: 32px;
}

.confirmation-card p {
    color: #666;
    font-size: 18px;
    line-height: 1.6;
    margin-bottom: 30px;
}

.confirmation-details {
    text-align: left;
    margin-bottom: 30px;
    padding: 20px;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.confirmation-details h2,
.confirmation-contact h2 {
    font-size: 24px;
    color: #333;
    margin-bottom: 15px;
}

.confirmation-details ol {
    padding-left: 20px;
}

.confirmation-details li {
    margin-bottom: 10px;
    line-height: 1.5;
    color: #555;
}

.confirmation-contact {
    text-align: left;
    margin-bottom: 30px;
}

.confirmation-contact ul {
    list-style: none;
    padding: 0;
}

.confirmation-contact li {
    margin-bottom: 10px;
    font-size: 18px;
}

.confirmation-contact i {
    width: 25px;
    color: #2196F3;
}

.confirmation-contact a {
    color: #2196F3;
    text-decoration: none;
    transition: color 0.3s;
}

.confirmation-contact a:hover {
    color: #0D47A1;
    text-decoration: underline;
}

.confirmation-actions {
    margin-top: 30px;
}

.confirmation-actions .btn {
    margin: 0 10px;
}

.btn-primary {
    background-color: #2196F3;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s;
    display: inline-block;
    border: none;
}

.btn-secondary {
    background-color: #757575;
    color: white;
    padding: 12px 24px;
    border-radius: 4px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s;
    display: inline-block;
    border: none;
}

.btn-primary:hover {
    background-color: #1976D2;
}

.btn-secondary:hover {
    background-color: #616161;
}

@media (max-width: 768px) {
    .confirmation-card {
        padding: 20px;
    }
    
    .confirmation-icon {
        font-size: 60px;
    }
    
    .confirmation-card h1 {
        font-size: 24px;
    }
    
    .confirmation-actions .btn {
        display: block;
        margin: 10px auto;
        width: 80%;
    }
}
</style>

<?php include 'footer.php'; ?> 