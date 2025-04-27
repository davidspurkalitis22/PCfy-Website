<?php
// Start session
session_start();

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Home';

// Include header
include 'header.php';
?>

    <!-- Hero Banner Section -->
    <section class="hero-banner custom-pc-hero">
        <div class="hero-overlay"></div>
        <div class="banner-content">
            <h1 class="banner-title animate-text">Build Your Dream PC Now!</h1>
            <p class="banner-subtitle animate-text">Custom builds, repairs, and premium components - all in one place</p>
            <div class="hero-cta">
                <a href="custom-pcs.php#pc-builder" class="btn btn-glow">Start Building</a>
                <a href="repair-services.php" class="btn btn-secondary">Explore Services</a>
            </div>
        </div>
        <div class="floating-icons">
            <div class="floating-icon"><i class="fas fa-microchip"></i></div>
            <div class="floating-icon"><i class="fas fa-memory"></i></div>
            <div class="floating-icon"><i class="fas fa-tv"></i></div>
            <div class="floating-icon"><i class="fas fa-hdd"></i></div>
            <div class="floating-icon"><i class="fas fa-fan"></i></div>
        </div>
    </section>

    <main>
        <!-- Featured PC Build Section -->
        <section class="featured-builds">
            <div class="container">
                <h2 class="section-title">Featured Gaming PCs</h2>
                <div class="builds-showcase">
                    <!-- Budget Build -->
                    <div class="pc-showcase-card">
                        <div class="pc-image">
                            <img src="pc1.png" alt="Budget Gaming PC" onerror="this.src='images/placeholder.jpg'">
                            <div class="pc-category">Budget</div>
                        </div>
                        <div class="pc-info">
                            <h3>Starter Pro</h3>
                            <p>Perfect for esports and casual gaming at 1080p resolution with smooth framerates. Ideal for games like Fortnite, CS2, and League of Legends with optimized components for reliable performance.</p>
                            <a href="custom-pcs.php" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                    
                    <!-- Mid Range Build -->
                    <div class="pc-showcase-card">
                        <div class="pc-image">
                            <img src="pc2.png" alt="Mid Range Gaming PC" onerror="this.src='images/placeholder.jpg'">
                            <div class="pc-category">Mid Range</div>
                        </div>
                        <div class="pc-info">
                            <h3>Elite Gaming</h3>
                            <p>Powerful performance for demanding games at 1440p with high graphic settings. Experience ray tracing and smooth gameplay in AAA titles like Cyberpunk 2077 and Elden Ring with zero compromises.</p>
                            <a href="custom-pcs.php" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                    
                    <!-- High End Build -->
                    <div class="pc-showcase-card">
                        <div class="pc-image">
                            <img src="pc3.png" alt="High End Gaming PC" onerror="this.src='images/placeholder.jpg'">
                            <div class="pc-category">High End</div>
                        </div>
                        <div class="pc-info">
                            <h3>Ultimate Legend</h3>
                            <p>Uncompromising 4K gaming and content creation with top-tier components. Perfect for streamers, video editors, and enthusiasts who demand the absolute best performance for multitasking and heavy workloads.</p>
                            <a href="custom-pcs.php" class="btn btn-primary">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PC Background Image Section -->
        <section class="pc-background">
            <div class="image-container">
                <img src="pcbackground.jpg" alt="Gaming PC Setup" class="full-width-image">
                <div class="image-overlay">
                    <h2>Experience Next-Level Computing</h2>
                    <p>Specifications are limitless...</p>
                </div>
            </div>
        </section>

        <!-- Featured Section -->
        <section class="featured-section">
            <div class="container">
                <div class="featured-grid">
                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <h3>2 Year Warranty</h3>
                        <p>All the Custom PCs and repairs come with a comprehensive two-year warranty </p>
                    </div>

                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3>Quick Service</h3>
                        <p>Fast Building/Repairing of your PC</p>
                    </div>

                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fas fa-star"></i>
                        </div>
                        <h3>Premium Parts</h3>
                        <p>All the parts are brand new and from trusted manufacturers</p>
                    </div>

                    <div class="featured-card">
                        <div class="featured-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Support 24/7</h3>
                        <p>Round-the-clock customer support</p>
                    </div>
                </div>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?>