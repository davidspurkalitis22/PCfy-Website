<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Contact Us';

// Add additional styles and scripts
$additionalStyles = '<script src="js/faq-toggle.js"></script>';

// Include header
include 'header.php';
?>

    <main>
        <!-- Contact Header -->
        <section class="contact-header">
            <div class="container">
                <h1 class="page-title">Contact Us</h1>
                <p class="section-description">Have questions or need assistance? Reach out to our team.</p>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="contact-form-section">
            <div class="container">
                <div class="contact-content">
                    <div class="contact-info">
                        <h2>Get In Touch</h2>
                        <p>We're here to help with any questions about our services, products, or your existing order.</p>
                        
                        <div class="contact-details">
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div class="contact-text">
                                    <h3>Phone</h3>
                                    <p>085 863 9422</p>
                                    <p class="detail-note">Available Mon-Fri, 9am-6pm</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="contact-text">
                                    <h3>Email</h3>
                                    <p>pcfy.galway@gmail.com</p>
                                    <p class="detail-note">We'll respond within 24 hours</p>
                                </div>
                            </div>
                            
                            <div class="contact-item">
                                <div class="contact-icon">
                                    <i class="fas fa-location-dot"></i>
                                </div>
                                <div class="contact-text">
                                    <h3>Location</h3>
                                    <p>Galway City</p>
                                    <p class="detail-note">By appointment only</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="social-contact">
                            <h3>Connect With Us</h3>
                            <div class="social-icons">
                                <a href="#" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-instagram"></i></a>
                                <a href="#" class="social-icon"><i class="fab fa-youtube"></i></a>
                            </div>
                        </div>
                    </div>
                    
                    <div class="contact-form">
                        <h2>Send Us a Message</h2>
                        
                        <?php if (isset($_SESSION['contact_success'])): ?>
                        <div class="alert alert-success">
                            <?php 
                            echo $_SESSION['contact_success']; 
                            unset($_SESSION['contact_success']); 
                            ?>
                        </div>
                        <?php endif; ?>
                        
                        <?php if (isset($_SESSION['contact_error'])): ?>
                        <div class="alert alert-error">
                            <?php 
                            echo $_SESSION['contact_error']; 
                            unset($_SESSION['contact_error']); 
                            ?>
                        </div>
                        <?php endif; ?>
                        
                        <form action="process_contact.php" method="post" id="contact-form">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="name" name="name" required 
                                        value="<?php echo isset($_SESSION['form_data']['name']) ? htmlspecialchars($_SESSION['form_data']['name']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" required
                                        value="<?php echo isset($_SESSION['form_data']['email']) ? htmlspecialchars($_SESSION['form_data']['email']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-tag"></i>
                                    <input type="text" id="subject" name="subject" required
                                        value="<?php echo isset($_SESSION['form_data']['subject']) ? htmlspecialchars($_SESSION['form_data']['subject']) : ''; ?>">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="message">Your Message</label>
                                <div class="textarea-icon">
                                    <i class="fas fa-comment-alt"></i>
                                    <textarea id="message" name="message" rows="5" required><?php echo isset($_SESSION['form_data']['message']) ? htmlspecialchars($_SESSION['form_data']['message']) : ''; ?></textarea>
                                </div>
                            </div>
                            
                            <div class="form-group submit-group">
                                <button type="submit" class="btn btn-glow">Send Message</button>
                            </div>
                        </form>
                        <?php unset($_SESSION['form_data']); // Clear form data after displaying ?>
                    </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="container">
                <h2 class="section-title">Frequently Asked Questions</h2>
                
                <div class="faq-container">
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>What are your business hours?</h3>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Our business hours are Monday to Friday, 9am to 6pm. We're available for appointments and consultations during these hours.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>How can I check the status of my order?</h3>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>You can check your order status by contacting us via email or phone with your order number. We'll provide you with the latest updates on your order.</p>
                        </div>
                    </div>
                    
                    <div class="faq-item">
                        <div class="faq-question">
                            <h3>Do you offer on-site services?</h3>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-answer">
                            <p>Yes, we offer on-site services for businesses and special cases within Galway City. Please contact us for availability and pricing for on-site support.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?> 