<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Repair Services';

// Include header
include 'header.php';
?>

    <main>
        <!-- PC Repair Services -->
        <section class="repair-categories">
            <div class="container">
                <!-- Hardware Repairs -->
                <div class="repair-category">
                    <div class="category-header">
                        <h3>Hardware Repairs</h3>
                        <div class="category-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                    </div>
                    <div class="category-content">
                        <p>We diagnose and repair all types of hardware issues, from component replacements to complex system repairs.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Component replacement (RAM, GPU, CPU, etc.)</li>
                            <li><i class="fas fa-check"></i> Cooling system optimization</li>
                        </ul>
                        <div class="service-action">
                            <a href="#book-repair" class="btn btn-primary">Book Hardware Repair</a>
                        </div>
                    </div>
                </div>

                <!-- Software Solutions -->
                <div class="repair-category">
                    <div class="category-header">
                        <h3>Software Solutions</h3>
                        <div class="category-icon">
                            <i class="fas fa-code"></i>
                        </div>
                    </div>
                    <div class="category-content">
                        <p>Our software experts can resolve system crashes, remove malware, and optimize your PC's performance.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Operating system installation and repair</li>
                            <li><i class="fas fa-check"></i> Virus and malware removal</li>
                            <li><i class="fas fa-check"></i> Software conflict resolution</li>
                            <li><i class="fas fa-check"></i> Driver updates and optimization</li>
                            <li><i class="fas fa-check"></i> Data backup and recovery solutions</li>
                        </ul>
                        <div class="service-action">
                            <a href="#book-repair" class="btn btn-primary">Book Software Service</a>
                        </div>
                    </div>
                </div>

                <!-- Diagnostics -->
                <div class="repair-category">
                    <div class="category-header">
                        <h3>Diagnostics</h3>
                        <div class="category-icon">
                            <i class="fas fa-search"></i>
                        </div>
                    </div>
                    <div class="category-content">
                        <p>Not sure what's wrong with your PC? Our comprehensive diagnostics service will identify all issues.</p>
                        <ul class="service-list">
                            <li><i class="fas fa-check"></i> Complete system performance analysis</li>
                            <li><i class="fas fa-check"></i> Hardware component testing</li>
                            <li><i class="fas fa-check"></i> Software conflict identification</li>
                            <li><i class="fas fa-check"></i> Thermal and noise analysis</li>
                            <li><i class="fas fa-check"></i> Detailed report with recommended solutions</li>
                        </ul>
                        <div class="service-action">
                            <a href="#book-repair" class="btn btn-primary">Book Diagnostics</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Booking Form -->
        <section id="book-repair" class="booking-section">
            <div class="container">
                <h2 class="section-title">Book a Repair</h2>
                
                <?php if (isset($_SESSION['booking_success'])): ?>
                <div class="alert alert-success">
                    <?php 
                    echo $_SESSION['booking_success']; 
                    unset($_SESSION['booking_success']); 
                    ?>
                </div>
                <?php endif; ?>
                
                <?php if (isset($_SESSION['booking_error'])): ?>
                <div class="alert alert-error">
                    <?php 
                    echo $_SESSION['booking_error']; 
                    unset($_SESSION['booking_error']); 
                    ?>
                </div>
                <?php endif; ?>
                
                <div class="booking-form">
                    <form action="process_repair_booking.php" method="post" id="repair-booking-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Your Name</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="name" name="name" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-envelope"></i>
                                    <input type="email" id="email" name="email" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-phone"></i>
                                    <input type="tel" id="phone" name="phone" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="service-type">Service Type</label>
                                <select id="service-type" name="service-type" required>
                                    <option value="" disabled selected>Select a service</option>
                                    <option value="hardware">Hardware Repair</option>
                                    <option value="software">Software Solution</option>
                                    <option value="diagnostics">Diagnostics</option>
                                    <option value="other">Other (please specify)</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="preferred-date">Preferred Date (Optional)</label>
                                <div class="input-with-icon">
                                    <i class="fas fa-calendar"></i>
                                    <input type="date" id="preferred-date" name="preferred-date" min="<?php echo date('Y-m-d'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="issue">Describe Your Issue</label>
                            <div class="textarea-icon">
                                <i class="fas fa-comment-alt"></i>
                                <textarea id="issue" name="issue" rows="4" required></textarea>
                            </div>
                        </div>
                        <div class="form-group submit-group">
                            <button type="submit" class="btn btn-glow">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        <!-- Why Choose Us -->
        <section class="why-choose-us">
            <div class="container">
                <h2 class="section-title">Why Choose Our Repair Services</h2>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-tools"></i>
                        </div>
                        <h3>Expert Technicians</h3>
                        <p>Our repair team consists of certified professionals with years of experience in PC repairs.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Quick Turnaround</h3>
                        <p>Most repairs are completed within 24-48 hours, getting you back up and running fast.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <h3>Warranty Included</h3>
                        <p>All our repairs come with a 90-day warranty for your peace of mind.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">
                            <i class="fas fa-euro-sign"></i>
                        </div>
                        <h3>Competitive Pricing</h3>
                        <p>We offer transparent pricing with no hidden fees or surprise charges.</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?> 