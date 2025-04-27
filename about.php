<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'About Us';

// Include header
include 'header.php';
?>

<!-- About Header -->
<section class="about-header">
    <div class="container">
        <h1 class="page-title">About Us</h1>
        <p class="section-description">Learn about our company, our mission, and the team behind PCFY.</p>
    </div>
</section>

<!-- Company Info -->
<section class="company-info">
    <div class="container">
        <div class="info-content">
            <div class="info-text">
                <h2>My Story</h2>
                <p>PCFY was founded in 2025 by a me (Davids Purkalitis) in Galway with a simple mission: to provide high-quality custom PC builds, reliable repair services, and premium components at fair prices.</p>
                <p>It started as a small university project by building a website which turned into actual business. I'll try helping our customers find the perfect PC solutions for their needs.</p>
                <p>Whether you're a gamer looking for a custom PC build, a professional needing a powerful workstation, or someone who just needs their computer fixed, Im here to help with honest advice and quality service.</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="team-section">
    <div class="container">
        <h2 class="section-title">Meet the Founder</h2>
        <p class="section-description">The person behind PCFY who makes it all happen.</p>
        
        <div class="team-grid">
            <div class="team-member">
                <div class="member-image">
                    <img src="images/davids.jpg" alt="Davids Purkalitis">
                </div>
                <h3>Davids Purkalitis</h3>
                <p class="member-role">Computing and Digital Media Student</p>
                <p class="member-bio">As the sole founder of PCFY, I handle all aspects of the business - from custom PC builds and repairs to customer service. My passion for technology and attention to detail ensures that every customer receives personalized service and high-quality solutions.</p>
            </div>
        </div>
    </div>
</section>

<style>
.team-member .member-image {
    width: 250px;
    height: 250px;
    border-radius: 1px;
    overflow: hidden;
    margin: 0 auto 15px;
}

.team-member .member-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: center;
}
</style>

<?php include 'footer.php'; ?> 