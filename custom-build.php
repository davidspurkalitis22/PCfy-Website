<?php
// Start session
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom PC Build - PCFY</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .custom-build-container {
            max-width: 900px;
            margin: 30px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        
        .form-intro {
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-intro h1 {
            color: #2c3e50;
            margin-bottom: 15px;
        }
        
        .form-intro p {
            color: #7f8c8d;
            max-width: 700px;
            margin: 0 auto;
        }
        
        .build-form {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .form-section {
            grid-column: 1 / -1;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .form-section h2 {
            color: #2c3e50;
            margin-bottom: 15px;
            font-size: 1.5em;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #34495e;
        }
        
        .form-group label.required:after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3498db;
            outline: none;
        }
        
        .form-group .help-text {
            display: block;
            margin-top: 5px;
            font-size: 0.85em;
            color: #7f8c8d;
        }
        
        .form-buttons {
            grid-column: 1 / -1;
            margin-top: 20px;
            text-align: center;
        }
        
        .btn-submit {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn-submit:hover {
            background-color: #2980b9;
        }
        
        /* Message box for feedback */
        #message-box {
            display: none;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        
        .success {
            background-color: #d4edda;
            border-left: 5px solid #28a745;
            color: #155724;
        }
        
        .error {
            background-color: #f8d7da;
            border-left: 5px solid #dc3545;
            color: #721c24;
        }
        
        @media (max-width: 768px) {
            .build-form {
                grid-template-columns: 1fr;
            }
            
            .custom-build-container {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    
    <div class="custom-build-container">
        <div class="form-intro">
            <h1>Build Your Custom PC</h1>
            <p>Fill out the form below with your desired specifications, and our team will prepare a custom quote for your dream PC. Required fields are marked with an asterisk (*).</p>
        </div>
        
        <div id="message-box"></div>
        
        <form id="custom-build-form" class="build-form">
            <div class="form-section">
                <h2>Contact Information</h2>
                <div class="form-group">
                    <label for="name" class="required">Full Name</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email Address</label>
                    <input type="email" id="email" name="email" required>
                    <span class="help-text">We'll use this to contact you with your quote.</span>
                </div>
                
                <div class="form-group">
                    <label for="phone" class="required">Phone Number</label>
                    <input type="tel" id="phone" name="phone" required>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Core Components</h2>
                
                <div class="form-group">
                    <label for="cpu" class="required">CPU (Processor)</label>
                    <input type="text" id="cpu" name="cpu" required>
                    <span class="help-text">Example: Intel Core i7-12700K or AMD Ryzen 7 5800X</span>
                </div>
                
                <div class="form-group">
                    <label for="motherboard" class="required">Motherboard</label>
                    <input type="text" id="motherboard" name="motherboard" required>
                    <span class="help-text">Example: ASUS ROG Strix Z690-E or MSI MPG B550 Gaming Edge</span>
                </div>
                
                <div class="form-group">
                    <label for="gpu" class="required">Graphics Card (GPU)</label>
                    <input type="text" id="gpu" name="gpu" required>
                    <span class="help-text">Example: NVIDIA RTX 3080 or AMD Radeon RX 6800 XT</span>
                </div>
                
                <div class="form-group">
                    <label for="ram" class="required">RAM (Memory)</label>
                    <input type="text" id="ram" name="ram" required>
                    <span class="help-text">Example: 32GB (2x16GB) Corsair Vengeance RGB DDR4 3600MHz</span>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Storage & Power</h2>
                
                <div class="form-group">
                    <label for="storage" class="required">Primary Storage</label>
                    <input type="text" id="storage" name="storage" required>
                    <span class="help-text">Example: Samsung 970 EVO Plus 1TB NVMe SSD</span>
                </div>
                
                <div class="form-group">
                    <label for="additional_storage">Additional Storage (Optional)</label>
                    <input type="text" id="additional_storage" name="additional_storage">
                    <span class="help-text">Example: Seagate Barracuda 2TB HDD</span>
                </div>
                
                <div class="form-group">
                    <label for="power_supply" class="required">Power Supply (PSU)</label>
                    <input type="text" id="power_supply" name="power_supply" required>
                    <span class="help-text">Example: Corsair RM850x 850W 80+ Gold</span>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Case & Cooling</h2>
                
                <div class="form-group">
                    <label for="case" class="required">PC Case</label>
                    <input type="text" id="case" name="case" required>
                    <span class="help-text">Example: Lian Li O11 Dynamic or NZXT H510</span>
                </div>
                
                <div class="form-group">
                    <label for="cooling">CPU Cooling (Optional)</label>
                    <input type="text" id="cooling" name="cooling">
                    <span class="help-text">Example: NZXT Kraken X63 or Noctua NH-D15</span>
                </div>
            </div>
            
            <div class="form-section">
                <h2>Additional Options</h2>
                
                <div class="form-group">
                    <label for="operating_system">Operating System (Optional)</label>
                    <input type="text" id="operating_system" name="operating_system">
                    <span class="help-text">Example: Windows 11 Home or None</span>
                </div>
                
                <div class="form-group">
                    <label for="additional_notes">Additional Notes or Requests (Optional)</label>
                    <textarea id="additional_notes" name="additional_notes" rows="4"></textarea>
                    <span class="help-text">Any other specifications, preferences, or questions about your build.</span>
                </div>
            </div>
            
            <div class="form-buttons">
                <button type="submit" class="btn-submit">Submit Build Request</button>
            </div>
        </form>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        document.getElementById('custom-build-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const messageBox = document.getElementById('message-box');
            
            // Clear any previous messages
            messageBox.style.display = 'none';
            messageBox.className = '';
            
            // Submit form data via AJAX
            fetch('submit_custom_build.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Display message
                messageBox.textContent = data.message;
                messageBox.className = data.success ? 'success' : 'error';
                messageBox.style.display = 'block';
                
                // Scroll to message
                messageBox.scrollIntoView({ behavior: 'smooth' });
                
                // If successful, reset the form
                if (data.success) {
                    document.getElementById('custom-build-form').reset();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageBox.textContent = 'An error occurred while submitting your request. Please try again.';
                messageBox.className = 'error';
                messageBox.style.display = 'block';
            });
        });
    </script>
</body>
</html> 