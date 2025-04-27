<?php
// Start session
session_start();

// Include database connection
require_once 'config.php';

// Check if the custom_builds table exists and create it if needed
$check_table_sql = "SHOW TABLES LIKE 'custom_builds'";
$result = $conn->query($check_table_sql);

if ($result->num_rows == 0) {
    // Table doesn't exist, create it
    $create_table_sql = "CREATE TABLE IF NOT EXISTS custom_builds (
        id INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        cpu VARCHAR(255) NOT NULL,
        motherboard VARCHAR(255) NOT NULL,
        gpu VARCHAR(255) NOT NULL,
        ram VARCHAR(255) NOT NULL,
        storage VARCHAR(255) NOT NULL,
        additional_storage VARCHAR(255) NOT NULL,
        cooling VARCHAR(255) NOT NULL,
        pc_case VARCHAR(255) NOT NULL,
        power_supply VARCHAR(255) NOT NULL,
        operating_system VARCHAR(255) NOT NULL,
        additional_notes TEXT,
        status VARCHAR(50) NOT NULL DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if ($conn->query($create_table_sql) !== TRUE) {
        error_log("Error creating custom_builds table: " . $conn->error);
    }
}

// Initialize variables for user menu
$isLoggedIn = isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
$firstname = $isLoggedIn ? $_SESSION['firstname'] : '';

// Set page title
$pageTitle = 'Custom PC Builder';

// Add additional styles and scripts
$additionalStyles = '<link rel="stylesheet" href="css/custom-pc.css">
<script src="js/custom-pc.js"></script>
<script src="js/custom-pc-form.js"></script>';

// Include the header
include 'header.php';
?>

<!-- Custom PC Hero Section -->
    <section class="hero-banner custom-pc-hero">
        <div class="hero-overlay"></div>
        <div class="banner-content">
        <h1 class="banner-title animate-text">Build Your Custom Gaming PC</h1>
        <p class="banner-subtitle animate-text">Choose your components and create the perfect PC for your gaming and productivity needs</p>
            <div class="hero-cta">
            <a href="#pc-builder" class="btn btn-glow">Start Building Now</a>
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
        <!-- Specifications Section -->
        <section id="specs" class="specs-section">
            <div class="container">
                <h2 class="section-title">Specifications Comparison</h2>
                <p class="section-description" style="text-align: center; margin-bottom: 2rem; color: var(--gray);">
                    Note: The following specifications are examples only. Contact us for the most current configurations and options available.
                </p>
                
                <div class="specs-container">
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-microchip"></i>
                        </div>
                        <div class="spec-details">
                            <h3>Processor</h3>
                            <div class="spec-comparison">
                                <div class="spec-tier entry">
                                    <div class="spec-tier-label">Entry</div>
                                    <p>Intel Core i5-13400F / AMD Ryzen 5 7600X</p>
                                </div>
                                <div class="spec-tier mid">
                                    <div class="spec-tier-label">Mid</div>
                                    <p>Intel Core i7-13700K / AMD Ryzen 7 7800X3D</p>
                                </div>
                                <div class="spec-tier high">
                                    <div class="spec-tier-label">Premium</div>
                                    <p>Intel Core i9-13900K / AMD Ryzen 9 7950X</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-memory"></i>
                        </div>
                        <div class="spec-details">
                            <h3>Memory</h3>
                            <div class="spec-comparison">
                                <div class="spec-tier entry">
                                    <div class="spec-tier-label">Entry</div>
                                    <p>16GB DDR4-3200MHz / DDR5-4800MHz</p>
                                </div>
                                <div class="spec-tier mid">
                                    <div class="spec-tier-label">Mid</div>
                                    <p>32GB DDR5-5600MHz</p>
                                </div>
                                <div class="spec-tier high">
                                    <div class="spec-tier-label">Premium</div>
                                    <p>64GB DDR5-6000MHz</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-tv"></i>
                        </div>
                        <div class="spec-details">
                            <h3>Graphics Card</h3>
                            <div class="spec-comparison">
                                <div class="spec-tier entry">
                                    <div class="spec-tier-label">Entry</div>
                                    <p>NVIDIA RTX 3060 / AMD RX 6600 XT</p>
                                </div>
                                <div class="spec-tier mid">
                                    <div class="spec-tier-label">Mid</div>
                                    <p>NVIDIA RTX 4070 / AMD RX 7700 XT</p>
                                </div>
                                <div class="spec-tier high">
                                    <div class="spec-tier-label">Premium</div>
                                    <p>NVIDIA RTX 4090 / AMD RX 7900 XTX</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="spec-details">
                            <h3>Storage</h3>
                            <div class="spec-comparison">
                                <div class="spec-tier entry">
                                    <div class="spec-tier-label">Entry</div>
                                    <p>1TB NVMe SSD</p>
                                </div>
                                <div class="spec-tier mid">
                                    <div class="spec-tier-label">Mid</div>
                                    <p>2TB NVMe SSD</p>
                                </div>
                                <div class="spec-tier high">
                                    <div class="spec-tier-label">Premium</div>
                                    <p>4TB NVMe SSD + 4TB HDD</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="spec-item">
                        <div class="spec-icon">
                            <i class="fas fa-fan"></i>
                        </div>
                        <div class="spec-details">
                            <h3>Cooling</h3>
                            <div class="spec-comparison">
                                <div class="spec-tier entry">
                                    <div class="spec-tier-label">Entry</div>
                                    <p>Air Cooling</p>
                                </div>
                                <div class="spec-tier mid">
                                    <div class="spec-tier-label">Mid</div>
                                    <p>240mm AIO Liquid Cooling</p>
                                </div>
                                <div class="spec-tier high">
                                    <div class="spec-tier-label">Premium</div>
                                    <p>360mm AIO Liquid Cooling</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PC Builder Section -->
        <section id="pc-builder" class="pc-builder-section">
            <div class="container">
                <h2 class="section-title">Build Your Custom PC</h2>
                <p class="section-description" style="text-align: center; margin-bottom: 2rem; color: var(--gray);">
                    Design your perfect PC by selecting the components below. Our team will review your selections and contact you with a quote.
                </p>
                
                <form id="custom-pc-form" class="pc-builder-form" method="post">
                    <div class="builder-grid">
                        <!-- CPU Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-microchip"></i>
                                <h3>CPU (Processor)</h3>
                            </div>
                            <div class="component-selection">
                                <select name="cpu" id="cpu">
                                    <option value="">Select a CPU...</option>
                                    <!-- AMD CPUs -->
                                    <optgroup label="AMD Ryzen CPUs">
                                        <!-- Ryzen 5000 Series -->
                                        <option value="AMD Ryzen 5 5600X">AMD Ryzen 5 5600X - $199</option>
                                        <option value="AMD Ryzen 7 5800X">AMD Ryzen 7 5800X - $299</option>
                                        <option value="AMD Ryzen 9 5900X">AMD Ryzen 9 5900X - $399</option>
                                        <!-- Ryzen 7000 Series -->
                                        <option value="AMD Ryzen 5 7600X">AMD Ryzen 5 7600X - $249</option>
                                        <option value="AMD Ryzen 7 7700X">AMD Ryzen 7 7700X - $349</option>
                                        <option value="AMD Ryzen 9 7900X">AMD Ryzen 9 7900X - $449</option>
                                        <option value="AMD Ryzen 7 7800X3D">AMD Ryzen 7 7800X3D - $419</option>
                                        <option value="AMD Ryzen 9 7950X">AMD Ryzen 9 7950X - $549</option>
                                    </optgroup>
                                    <!-- Intel CPUs -->
                                    <optgroup label="Intel Core CPUs">
                                        <!-- 12th Gen -->
                                        <option value="Intel Core i5-12600K">Intel Core i5-12600K - $229</option>
                                        <option value="Intel Core i7-12700K">Intel Core i7-12700K - $329</option>
                                        <option value="Intel Core i9-12900K">Intel Core i9-12900K - $449</option>
                                        <!-- 13th Gen -->
                                        <option value="Intel Core i5-13600K">Intel Core i5-13600K - $279</option>
                                        <option value="Intel Core i7-13700K">Intel Core i7-13700K - $379</option>
                                        <option value="Intel Core i9-13900K">Intel Core i9-13900K - $499</option>
                                        <option value="Intel Core i5-13400F">Intel Core i5-13400F - $189</option>
                                        <option value="Intel Core i5-13500">Intel Core i5-13500 - $219</option>
                                        <option value="Intel Core i7-13700F">Intel Core i7-13700F - $349</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Motherboard Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-server"></i>
                                <h3>Motherboard</h3>
                            </div>
                            <div class="component-selection">
                                <select name="motherboard" id="motherboard">
                                    <option value="">Select a Motherboard...</option>
                                    <!-- AMD AM4 Motherboards (For Ryzen 5000 series) -->
                                    <optgroup label="AMD AM4 Motherboards">
                                        <option value="ASUS ROG Strix B550-F">ASUS ROG Strix B550-F - $149</option>
                                        <option value="MSI MPG B550 GAMING EDGE">MSI MPG B550 GAMING EDGE - $159</option>
                                        <option value="Gigabyte B550 AORUS ELITE">Gigabyte B550 AORUS ELITE - $139</option>
                                        <option value="MSI MAG X570 TOMAHAWK">MSI MAG X570 TOMAHAWK - $189</option>
                                        <option value="ASUS TUF Gaming X570-Plus">ASUS TUF Gaming X570-Plus - $179</option>
                                        <option value="Gigabyte X570 AORUS ELITE">Gigabyte X570 AORUS ELITE - $199</option>
                                    </optgroup>
                                    
                                    <!-- AMD AM5 Motherboards (For Ryzen 7000 series) -->
                                    <optgroup label="AMD AM5 Motherboards">
                                        <option value="ASUS ROG Strix B650-A">ASUS ROG Strix B650-A - $219</option>
                                        <option value="MSI MPG B650 EDGE WIFI">MSI MPG B650 EDGE WIFI - $239</option>
                                        <option value="Gigabyte B650 AORUS ELITE AX">Gigabyte B650 AORUS ELITE AX - $229</option>
                                        <option value="ASUS ROG Strix X670E-E">ASUS ROG Strix X670E-E - $349</option>
                                        <option value="MSI MPG X670E CARBON WIFI">MSI MPG X670E CARBON WIFI - $369</option>
                                        <option value="Gigabyte X670E AORUS MASTER">Gigabyte X670E AORUS MASTER - $399</option>
                                        <option value="ASRock B650M PG RIPTIDE">ASRock B650M PG RIPTIDE - $169</option>
                                        <option value="MSI MAG B650 TOMAHAWK WIFI">MSI MAG B650 TOMAHAWK WIFI - $259</option>
                                        <option value="ASUS TUF GAMING B650-PLUS WIFI">ASUS TUF GAMING B650-PLUS WIFI - $209</option>
                                    </optgroup>
                                    
                                    <!-- Intel LGA 1700 Motherboards (For 12th/13th Gen) -->
                                    <optgroup label="Intel LGA 1700 Motherboards">
                                        <!-- Z690 Motherboards -->
                                        <option value="ASUS TUF Gaming Z690-PLUS">ASUS TUF Gaming Z690-PLUS DDR5 - $229</option>
                                        <option value="MSI MAG Z690 TOMAHAWK">MSI MAG Z690 TOMAHAWK DDR5 - $259</option>
                                        <option value="Gigabyte Z690 AORUS ELITE">Gigabyte Z690 AORUS ELITE DDR4 - $249</option>
                                        <option value="ASUS PRIME Z690-P">ASUS PRIME Z690-P DDR4 - $209</option>
                                        <option value="MSI PRO Z690-A">MSI PRO Z690-A DDR4 - $199</option>
                                        
                                        <!-- Z790 Motherboards -->
                                        <option value="ASUS ROG Strix Z790-A">ASUS ROG Strix Z790-A DDR5 - $319</option>
                                        <option value="MSI MPG Z790 EDGE WIFI">MSI MPG Z790 EDGE WIFI DDR5 - $339</option>
                                        <option value="Gigabyte Z790 AORUS ELITE AX">Gigabyte Z790 AORUS ELITE AX DDR5 - $329</option>
                                        <option value="ASUS PRIME Z790-P">ASUS PRIME Z790-P DDR4 - $259</option>
                                        <option value="MSI PRO Z790-A">MSI PRO Z790-A DDR4 - $249</option>
                                        
                                        <!-- B760 Motherboards -->
                                        <option value="MSI MAG B760 TOMAHAWK WIFI">MSI MAG B760 TOMAHAWK WIFI DDR5 - $219</option>
                                        <option value="ASUS TUF GAMING B760-PLUS WIFI">ASUS TUF GAMING B760-PLUS WIFI DDR5 - $199</option>
                                        <option value="Gigabyte B760 AORUS ELITE AX">Gigabyte B760 AORUS ELITE AX DDR5 - $209</option>
                                        <option value="MSI PRO B760-P">MSI PRO B760-P DDR4 - $159</option>
                                        <option value="ASRock B760M Pro RS/D4">ASRock B760M Pro RS/D4 DDR4 - $139</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <!-- GPU Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-vr-cardboard"></i>
                                <h3>GPU (Graphics Card)</h3>
                            </div>
                            <div class="component-selection">
                                <select name="gpu" id="gpu">
                                    <option value="">Select a GPU...</option>
                                    <!-- NVIDIA GPUs -->
                                    <optgroup label="NVIDIA GeForce RTX GPUs">
                                        <!-- RTX 30 Series -->
                                        <option value="NVIDIA GeForce RTX 3060">NVIDIA GeForce RTX 3060 - $329</option>
                                        <option value="NVIDIA GeForce RTX 3060 Ti">NVIDIA GeForce RTX 3060 Ti - $399</option>
                                        <option value="NVIDIA GeForce RTX 3070">NVIDIA GeForce RTX 3070 - $499</option>
                                        <option value="NVIDIA GeForce RTX 3070 Ti">NVIDIA GeForce RTX 3070 Ti - $599</option>
                                        <option value="NVIDIA GeForce RTX 3080">NVIDIA GeForce RTX 3080 - $699</option>
                                        <option value="NVIDIA GeForce RTX 3080 Ti">NVIDIA GeForce RTX 3080 Ti - $899</option>
                                        
                                        <!-- RTX 40 Series -->
                                        <option value="NVIDIA GeForce RTX 4060">NVIDIA GeForce RTX 4060 - $299</option>
                                        <option value="NVIDIA GeForce RTX 4060 Ti">NVIDIA GeForce RTX 4060 Ti - $399</option>
                                        <option value="NVIDIA GeForce RTX 4070">NVIDIA GeForce RTX 4070 - $599</option>
                                        <option value="NVIDIA GeForce RTX 4070 Ti">NVIDIA GeForce RTX 4070 Ti - $799</option>
                                        <option value="NVIDIA GeForce RTX 4080">NVIDIA GeForce RTX 4080 - $1,099</option>
                                        <option value="NVIDIA GeForce RTX 4090">NVIDIA GeForce RTX 4090 - $1,599</option>
                                    </optgroup>
                                    
                                    <!-- AMD GPUs -->
                                    <optgroup label="AMD Radeon RX GPUs">
                                        <!-- RX 6000 Series -->
                                        <option value="AMD Radeon RX 6600 XT">AMD Radeon RX 6600 XT - $299</option>
                                        <option value="AMD Radeon RX 6700 XT">AMD Radeon RX 6700 XT - $479</option>
                                        <option value="AMD Radeon RX 6750 XT">AMD Radeon RX 6750 XT - $549</option>
                                        <option value="AMD Radeon RX 6800 XT">AMD Radeon RX 6800 XT - $649</option>
                                        <option value="AMD Radeon RX 6900 XT">AMD Radeon RX 6900 XT - $799</option>
                                        
                                        <!-- RX 7000 Series -->
                                        <option value="AMD Radeon RX 7600">AMD Radeon RX 7600 - $269</option>
                                        <option value="AMD Radeon RX 7700 XT">AMD Radeon RX 7700 XT - $449</option>
                                        <option value="AMD Radeon RX 7800 XT">AMD Radeon RX 7800 XT - $549</option>
                                        <option value="AMD Radeon RX 7900 XT">AMD Radeon RX 7900 XT - $849</option>
                                        <option value="AMD Radeon RX 7900 XTX">AMD Radeon RX 7900 XTX - $999</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <!-- RAM Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-memory"></i>
                                <h3>RAM (Memory)</h3>
                            </div>
                            <div class="component-selection">
                                <select name="ram" id="ram">
                                    <option value="">Select RAM...</option>
                                    <!-- DDR4 Options -->
                                    <optgroup label="DDR4 Memory">
                                        <option value="Corsair Vengeance 16GB (2x8GB) DDR4 3200">Corsair Vengeance 16GB (2x8GB) DDR4 3200 - $69</option>
                                        <option value="G.Skill Ripjaws V 16GB (2x8GB) DDR4 3600">G.Skill Ripjaws V 16GB (2x8GB) DDR4 3600 - $79</option>
                                        <option value="Corsair Vengeance 32GB (2x16GB) DDR4 3200">Corsair Vengeance 32GB (2x16GB) DDR4 3200 - $119</option>
                                        <option value="G.Skill Trident Z 32GB (2x16GB) DDR4 3600">G.Skill Trident Z 32GB (2x16GB) DDR4 3600 - $139</option>
                                    </optgroup>
                                    <!-- DDR5 Options -->
                                    <optgroup label="DDR5 Memory">
                                        <option value="Corsair Vengeance 16GB (2x8GB) DDR5 5200">Corsair Vengeance 16GB (2x8GB) DDR5 5200 - $99</option>
                                        <option value="G.Skill Trident Z5 16GB (2x8GB) DDR5 5600">G.Skill Trident Z5 16GB (2x8GB) DDR5 5600 - $119</option>
                                        <option value="Corsair Vengeance 32GB (2x16GB) DDR5 5200">Corsair Vengeance 32GB (2x16GB) DDR5 5200 - $159</option>
                                        <option value="G.Skill Trident Z5 32GB (2x16GB) DDR5 5600">G.Skill Trident Z5 32GB (2x16GB) DDR5 5600 - $189</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Storage Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-hdd"></i>
                                <h3>Primary Storage</h3>
                            </div>
                            <div class="component-selection">
                                <select name="storage" id="storage">
                                    <option value="">Select Primary Storage...</option>
                                    <option value="Samsung 970 EVO 500GB M.2 NVMe SSD">Samsung 970 EVO 500GB M.2 NVMe SSD - $69</option>
                                    <option value="Samsung 970 EVO 1TB M.2 NVMe SSD">Samsung 970 EVO 1TB M.2 NVMe SSD - $119</option>
                                    <option value="Samsung 980 PRO 1TB PCIe 4.0 NVMe SSD">Samsung 980 PRO 1TB PCIe 4.0 NVMe SSD - $149</option>
                                    <option value="WD Black SN850X 2TB NVMe SSD">WD Black SN850X 2TB NVMe SSD - $199</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Additional Storage Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-database"></i>
                                <h3>Additional Storage</h3>
                            </div>
                            <div class="component-selection">
                                <select name="additional_storage" id="additional_storage">
                                    <option value="">Select Additional Storage (Optional)...</option>
                                    <option value="None">None - $0</option>
                                    <option value="Western Digital Blue 1TB HDD">Western Digital Blue 1TB HDD - $39</option>
                                    <option value="Seagate Barracuda 2TB HDD">Seagate Barracuda 2TB HDD - $49</option>
                                    <option value="Western Digital Black 2TB HDD">Western Digital Black 2TB HDD - $79</option>
                                    <option value="Samsung 870 EVO 1TB SATA SSD">Samsung 870 EVO 1TB SATA SSD - $89</option>
                                    <option value="Crucial MX500 2TB SATA SSD">Crucial MX500 2TB SATA SSD - $159</option>
                                    <option value="Samsung 970 EVO Plus 2TB NVMe SSD">Samsung 970 EVO Plus 2TB NVMe SSD - $179</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Cooling Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-fan"></i>
                                <h3>CPU Cooling</h3>
                            </div>
                            <div class="component-selection">
                                <select name="cooling" id="cooling">
                                    <option value="">Select CPU Cooling...</option>
                                    <option value="Cooler Master Hyper 212 RGB">Cooler Master Hyper 212 RGB - $39</option>
                                    <option value="Noctua NH-D15">Noctua NH-D15 - $89</option>
                                    <option value="NZXT Kraken X53 240mm AIO">NZXT Kraken X53 240mm AIO - $129</option>
                                    <option value="Corsair iCUE H100i Elite 240mm AIO">Corsair iCUE H100i Elite 240mm AIO - $139</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Case Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-desktop"></i>
                                <h3>Case</h3>
                            </div>
                            <div class="component-selection">
                                <select name="case" id="case">
                                    <option value="">Select a Case...</option>
                                    <option value="Cooler Master Q300L">Cooler Master Q300L - $59</option>
                                    <option value="NZXT H510 Flow">NZXT H510 Flow - $79</option>
                                    <option value="Corsair 4000D Airflow">Corsair 4000D Airflow - $99</option>
                                    <option value="Phanteks P360A">Phanteks P360A - $89</option>
                                    <option value="Phanteks P400A">Phanteks P400A - $119</option>
                                    <option value="Fractal Design Meshify 2">Fractal Design Meshify 2 - $139</option>
                                    <option value="Corsair 5000D Airflow">Corsair 5000D Airflow - $169</option>
                                    <option value="Lian Li O11 Dynamic">Lian Li O11 Dynamic - $149</option>
                                    <option value="Lian Li O11 Dynamic Evo">Lian Li O11 Dynamic Evo - $179</option>
                                    <option value="Corsair 7000D Airflow">Corsair 7000D Airflow - $219</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Power Supply Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fas fa-bolt"></i>
                                <h3>Power Supply</h3>
                            </div>
                            <div class="component-selection">
                                <select name="power_supply" id="power_supply">
                                    <option value="">Select a Power Supply...</option>
                                    <!-- Bronze-rated Power Supplies -->
                                    <optgroup label="80+ Bronze PSUs">
                                        <option value="EVGA 500W 80+ Bronze">EVGA 500W 80+ Bronze - $45</option>
                                        <option value="EVGA 600W 80+ Bronze">EVGA 600W 80+ Bronze - $49</option>
                                        <option value="Corsair CX650M 650W 80+ Bronze">Corsair CX650M 650W 80+ Bronze - $69</option>
                                    </optgroup>

                                    <!-- Gold-rated Power Supplies -->
                                    <optgroup label="80+ Gold PSUs">
                                        <option value="EVGA SuperNOVA 650 G5 650W 80+ Gold">EVGA SuperNOVA 650 G5 650W 80+ Gold - $89</option>
                                        <option value="EVGA SuperNOVA 750 GT 750W 80+ Gold">EVGA SuperNOVA 750 GT 750W 80+ Gold - $99</option>
                                        <option value="Corsair RM750x 750W 80+ Gold">Corsair RM750x 750W 80+ Gold - $109</option>
                                        <option value="Corsair RM850x 850W 80+ Gold">Corsair RM850x 850W 80+ Gold - $129</option>
                                        <option value="EVGA SuperNOVA 850 G6 850W 80+ Gold">EVGA SuperNOVA 850 G6 850W 80+ Gold - $139</option>
                                        <option value="Seasonic FOCUS GX-1000 1000W 80+ Gold">Seasonic FOCUS GX-1000 1000W 80+ Gold - $169</option>
                                    </optgroup>

                                    <!-- Platinum/Titanium-rated Power Supplies -->
                                    <optgroup label="80+ Platinum/Titanium PSUs">
                                        <option value="Corsair HX1000 1000W 80+ Platinum">Corsair HX1000 1000W 80+ Platinum - $189</option>
                                        <option value="Corsair HX1200 1200W 80+ Platinum">Corsair HX1200 1200W 80+ Platinum - $219</option>
                                        <option value="Seasonic PRIME TX-1300 1300W 80+ Titanium">Seasonic PRIME TX-1300 1300W 80+ Titanium - $349</option>
                                        <option value="Corsair AX1600i 1600W 80+ Titanium">Corsair AX1600i 1600W 80+ Titanium - $449</option>
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Operating System Selection -->
                        <div class="builder-component">
                            <div class="component-header">
                                <i class="fab fa-windows"></i>
                                <h3>Operating System</h3>
                            </div>
                            <div class="component-selection">
                                <select name="operating_system" id="operating_system">
                                    <option value="">Select an Operating System...</option>
                                    <option value="None (I will install my own OS)">None (I will install my own OS) - $0</option>
                                    <option value="Windows 11 Home">Windows 11 Home - $119</option>
                                    <option value="Windows 11 Pro">Windows 11 Pro - $159</option>
                                    <option value="Ubuntu Linux (Free)">Ubuntu Linux (Free) - $0</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Customer Information -->
                    <div class="customer-info">
                        <h3>Your Information</h3>
                        <div class="info-grid">
                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="phone">Phone</label>
                                <input type="tel" id="phone" name="phone" required>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label for="additional-notes">Additional Requirements or Questions</label>
                            <textarea id="additional-notes" name="additional_notes" rows="4"></textarea>
                        </div>
                        
                        <!-- This hidden field will receive the full build summary -->
                        <input type="hidden" id="build_summary" name="build_summary" value="">
                    </div>
                    
                    <!-- Price Estimate -->
                    <div class="price-estimate">
                        <h3>Estimated Price: <span id="price-display">€0.00</span></h3>
                        <p class="price-note">* Final price includes €50 build service fee. Actual price may vary based on component availability and additional customizations.</p>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-submit">
                        <button type="submit" class="btn btn-glow">Submit Build Request</button>
                    </div>
                </form>
            </div>
        </section>
    </main>

<?php include 'footer.php'; ?> 