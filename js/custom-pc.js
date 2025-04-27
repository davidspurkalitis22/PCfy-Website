// Custom PC Builder JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Cache form elements
    const form = document.getElementById('custom-pc-form');
    const cpuSelect = document.getElementById('cpu');
    const motherboardSelect = document.getElementById('motherboard');
    const ramSelect = document.getElementById('ram');
    const gpuSelect = document.getElementById('gpu');
    const priceDisplay = document.getElementById('price-display');
    
    // Component compatibility rules
    const compatibility = {
        // CPU socket types
        cpuSockets: {
            // AMD
            'AMD Ryzen 5 5600X': 'AM4',
            'AMD Ryzen 7 5800X': 'AM4',
            'AMD Ryzen 9 5900X': 'AM4',
            'AMD Ryzen 5 7600X': 'AM5',
            'AMD Ryzen 7 7700X': 'AM5',
            'AMD Ryzen 9 7900X': 'AM5',
            'AMD Ryzen 7 7800X3D': 'AM5',
            'AMD Ryzen 9 7950X': 'AM5',
            
            // Intel
            'Intel Core i5-12600K': 'LGA1700',
            'Intel Core i7-12700K': 'LGA1700',
            'Intel Core i9-12900K': 'LGA1700',
            'Intel Core i5-13600K': 'LGA1700',
            'Intel Core i7-13700K': 'LGA1700',
            'Intel Core i9-13900K': 'LGA1700',
            'Intel Core i5-13400F': 'LGA1700',
            'Intel Core i5-13500': 'LGA1700',
            'Intel Core i7-13700F': 'LGA1700'
        },
        
        // Motherboard socket and memory type
        motherboards: {
            // AMD AM4
            'ASUS ROG Strix B550-F': { socket: 'AM4', memory: 'DDR4' },
            'MSI MPG B550 GAMING EDGE': { socket: 'AM4', memory: 'DDR4' },
            'Gigabyte B550 AORUS ELITE': { socket: 'AM4', memory: 'DDR4' },
            'MSI MAG X570 TOMAHAWK': { socket: 'AM4', memory: 'DDR4' },
            'ASUS TUF Gaming X570-Plus': { socket: 'AM4', memory: 'DDR4' },
            'Gigabyte X570 AORUS ELITE': { socket: 'AM4', memory: 'DDR4' },
            
            // AMD AM5
            'ASUS ROG Strix B650-A': { socket: 'AM5', memory: 'DDR5' },
            'MSI MPG B650 EDGE WIFI': { socket: 'AM5', memory: 'DDR5' },
            'Gigabyte B650 AORUS ELITE AX': { socket: 'AM5', memory: 'DDR5' },
            'ASUS ROG Strix X670E-E': { socket: 'AM5', memory: 'DDR5' },
            'MSI MPG X670E CARBON WIFI': { socket: 'AM5', memory: 'DDR5' },
            'Gigabyte X670E AORUS MASTER': { socket: 'AM5', memory: 'DDR5' },
            'ASRock B650M PG RIPTIDE': { socket: 'AM5', memory: 'DDR5' },
            'MSI MAG B650 TOMAHAWK WIFI': { socket: 'AM5', memory: 'DDR5' },
            'ASUS TUF GAMING B650-PLUS WIFI': { socket: 'AM5', memory: 'DDR5' },
            
            // Intel LGA 1700 with DDR5
            'ASUS TUF Gaming Z690-PLUS': { socket: 'LGA1700', memory: 'DDR5' },
            'MSI MAG Z690 TOMAHAWK': { socket: 'LGA1700', memory: 'DDR5' },
            'ASUS ROG Strix Z790-A': { socket: 'LGA1700', memory: 'DDR5' },
            'MSI MPG Z790 EDGE WIFI': { socket: 'LGA1700', memory: 'DDR5' },
            'Gigabyte Z790 AORUS ELITE AX': { socket: 'LGA1700', memory: 'DDR5' },
            'MSI MAG B760 TOMAHAWK WIFI': { socket: 'LGA1700', memory: 'DDR5' },
            'ASUS TUF GAMING B760-PLUS WIFI': { socket: 'LGA1700', memory: 'DDR5' },
            'Gigabyte B760 AORUS ELITE AX': { socket: 'LGA1700', memory: 'DDR5' },
            
            // Intel LGA 1700 with DDR4
            'Gigabyte Z690 AORUS ELITE': { socket: 'LGA1700', memory: 'DDR4' },
            'ASUS PRIME Z690-P': { socket: 'LGA1700', memory: 'DDR4' },
            'MSI PRO Z690-A': { socket: 'LGA1700', memory: 'DDR4' },
            'ASUS PRIME Z790-P': { socket: 'LGA1700', memory: 'DDR4' },
            'MSI PRO Z790-A': { socket: 'LGA1700', memory: 'DDR4' },
            'MSI PRO B760-P': { socket: 'LGA1700', memory: 'DDR4' },
            'ASRock B760M Pro RS/D4': { socket: 'LGA1700', memory: 'DDR4' }
        },
        
        // RAM memory types
        ram: {
            'Corsair Vengeance 16GB (2x8GB) DDR4 3200': 'DDR4',
            'G.Skill Ripjaws V 16GB (2x8GB) DDR4 3600': 'DDR4',
            'Corsair Vengeance 32GB (2x16GB) DDR4 3200': 'DDR4',
            'G.Skill Trident Z 32GB (2x16GB) DDR4 3600': 'DDR4',
            'Corsair Vengeance 16GB (2x8GB) DDR5 5200': 'DDR5',
            'G.Skill Trident Z5 16GB (2x8GB) DDR5 5600': 'DDR5',
            'Corsair Vengeance 32GB (2x16GB) DDR5 5200': 'DDR5',
            'G.Skill Trident Z5 32GB (2x16GB) DDR5 5600': 'DDR5'
        },
        
        // Cooling requirements for high-end CPUs
        coolingRequirements: {
            // High-end CPUs need better cooling
            'AMD Ryzen 9 5900X': ['Noctua NH-D15', 'NZXT Kraken X53 240mm AIO', 'Corsair iCUE H100i Elite 240mm AIO'],
            'AMD Ryzen 9 7900X': ['Noctua NH-D15', 'NZXT Kraken X53 240mm AIO', 'Corsair iCUE H100i Elite 240mm AIO'],
            'AMD Ryzen 9 7950X': ['Noctua NH-D15', 'NZXT Kraken X53 240mm AIO', 'Corsair iCUE H100i Elite 240mm AIO'],
            'Intel Core i9-12900K': ['Noctua NH-D15', 'NZXT Kraken X53 240mm AIO', 'Corsair iCUE H100i Elite 240mm AIO'],
            'Intel Core i9-13900K': ['Noctua NH-D15', 'NZXT Kraken X53 240mm AIO', 'Corsair iCUE H100i Elite 240mm AIO']
        },
        
        // Power supply requirements for high-end GPUs
        psuRequirements: {
            // NVIDIA 30 Series
            'NVIDIA GeForce RTX 3060': ['EVGA 600W 80+ Bronze', 'Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 3060 Ti': ['EVGA 600W 80+ Bronze', 'Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 3070': ['Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 3070 Ti': ['EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 3080': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 3080 Ti': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            
            // NVIDIA 40 Series
            'NVIDIA GeForce RTX 4060': ['EVGA 600W 80+ Bronze', 'Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 4060 Ti': ['Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 4070': ['EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 4070 Ti': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 4080': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'NVIDIA GeForce RTX 4090': ['Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            
            // AMD RX 6000 Series
            'AMD Radeon RX 6600 XT': ['EVGA 600W 80+ Bronze', 'Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 6700 XT': ['Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 6750 XT': ['EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 6800 XT': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 6900 XT': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            
            // AMD RX 7000 Series
            'AMD Radeon RX 7600': ['EVGA 600W 80+ Bronze', 'Corsair CX650M 650W 80+ Bronze', 'EVGA SuperNOVA 650 G5 650W 80+ Gold', 'EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 7700 XT': ['EVGA SuperNOVA 750 GT 750W 80+ Gold', 'Corsair RM750x 750W 80+ Gold', 'Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 7800 XT': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 7900 XT': ['Corsair RM850x 850W 80+ Gold', 'EVGA SuperNOVA 850 G6 850W 80+ Gold', 'Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium'],
            'AMD Radeon RX 7900 XTX': ['Seasonic FOCUS GX-1000 1000W 80+ Gold', 'Corsair HX1000 1000W 80+ Platinum', 'Corsair HX1200 1200W 80+ Platinum', 'Seasonic PRIME TX-1300 1300W 80+ Titanium', 'Corsair AX1600i 1600W 80+ Titanium']
        }
    };
    
    // Estimated component prices for calculation
    const prices = {};
    
    // Extract prices from select options and store them
    function extractPrices() {
        // Process all select elements with options that contain prices
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            const options = select.querySelectorAll('option');
            options.forEach(option => {
                if (option.value && option.textContent.includes('$')) {
                    // Extract price from format like "Component Name - $123"
                    const match = option.textContent.match(/\$(\d+(\.\d+)?)/);
                    if (match) {
                        prices[option.value] = parseFloat(match[1]);
                    }
                }
            });
        });
    }
    
    // Calculate total price based on selected components
    function calculatePrice() {
        let total = 0;
        
        // Add up component prices
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            if (select.value && prices[select.value]) {
                total += prices[select.value];
            }
        });
        
        // Add build fee
        total += 50; // $50 build fee
        
        // Update price display
        if (priceDisplay) {
            priceDisplay.textContent = '€' + total.toFixed(2);
        }
        
        return total;
    }
    
    // Check CPU and motherboard compatibility
    function checkCpuMotherboardCompatibility() {
        const cpu = cpuSelect.value;
        const motherboard = motherboardSelect.value;
        
        if (!cpu || !motherboard) return true; // Skip check if either not selected
        
        const cpuSocket = compatibility.cpuSockets[cpu];
        const motherboardSocket = compatibility.motherboards[motherboard]?.socket;
        
        return cpuSocket === motherboardSocket;
    }
    
    // Check motherboard and RAM compatibility
    function checkMotherboardRamCompatibility() {
        const motherboard = motherboardSelect.value;
        const ram = ramSelect.value;
        
        if (!motherboard || !ram) return true; // Skip check if either not selected
        
        const motherboardMemory = compatibility.motherboards[motherboard]?.memory;
        const ramType = compatibility.ram[ram];
        
        return motherboardMemory === ramType;
    }
    
    // Check CPU and cooling compatibility
    function checkCpuCoolingCompatibility() {
        const cpu = cpuSelect.value;
        const cooling = document.getElementById('cooling').value;
        
        if (!cpu || !cooling) return true; // Skip check if either not selected
        
        // Check if CPU requires specific cooling
        if (compatibility.coolingRequirements[cpu]) {
            return compatibility.coolingRequirements[cpu].includes(cooling);
        }
        
        return true; // If no specific requirement, any cooling is fine
    }
    
    // Check GPU and PSU compatibility
    function checkGpuPsuCompatibility() {
        const gpu = gpuSelect.value;
        const psu = document.getElementById('power_supply').value;
        
        if (!gpu || !psu) return true; // Skip check if either not selected
        
        // Check if GPU requires specific PSU
        if (compatibility.psuRequirements[gpu]) {
            return compatibility.psuRequirements[gpu].includes(psu);
        }
        
        return true; // If no specific requirement, any PSU is fine
    }
    
    // Update compatibility warnings
    function updateCompatibilityWarnings() {
        // Remove existing warnings
        const existingWarnings = form.querySelectorAll('.compatibility-warning');
        existingWarnings.forEach(warning => warning.remove());
        
        // Check CPU and motherboard compatibility
        if (!checkCpuMotherboardCompatibility()) {
            showWarning(motherboardSelect, 'This motherboard is not compatible with the selected CPU socket type.');
        }
        
        // Check motherboard and RAM compatibility
        if (!checkMotherboardRamCompatibility()) {
            showWarning(ramSelect, 'This RAM type is not compatible with the selected motherboard.');
        }
        
        // Check CPU and cooling compatibility
        if (!checkCpuCoolingCompatibility()) {
            showWarning(document.getElementById('cooling'), 'This CPU requires better cooling. Please select a more powerful cooler.');
        }
        
        // Check GPU and PSU compatibility
        if (!checkGpuPsuCompatibility()) {
            const gpu = gpuSelect.value;
            let recommendedPsu = '';
            
            // Get the lowest wattage PSU recommendation for this GPU
            if (compatibility.psuRequirements[gpu] && compatibility.psuRequirements[gpu].length > 0) {
                // Find the first recommended PSU that mentions a wattage
                for (const psu of compatibility.psuRequirements[gpu]) {
                    const wattMatch = psu.match(/(\d+)W/);
                    if (wattMatch) {
                        recommendedPsu = wattMatch[1] + 'W or higher';
                        break;
                    }
                }
            }
            
            // Create a more detailed message
            let psuMessage = 'This power supply is not sufficient for the selected GPU.';
            if (recommendedPsu) {
                psuMessage += ` We recommend a ${recommendedPsu} power supply for optimal performance and stability.`;
            } else {
                psuMessage += ' Please select a higher wattage PSU.';
            }
            
            showWarning(document.getElementById('power_supply'), psuMessage);
        }
    }
    
    // Show compatibility warning
    function showWarning(element, message) {
        const warning = document.createElement('div');
        warning.className = 'compatibility-warning';
        warning.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
        warning.style.color = '#e74c3c';
        warning.style.fontSize = '14px';
        warning.style.marginTop = '10px';
        warning.style.fontWeight = '500';
        
        // Insert warning after the element
        element.parentNode.insertBefore(warning, element.nextSibling);
    }
    
    // Check overall build compatibility
    function isBuildCompatible() {
        return (
            checkCpuMotherboardCompatibility() &&
            checkMotherboardRamCompatibility() &&
            checkCpuCoolingCompatibility() &&
            checkGpuPsuCompatibility()
        );
    }
    
    // Filter compatible options
    function filterCompatibleOptions() {
        // Filter motherboards based on CPU selection
        if (cpuSelect.value) {
            const cpuSocket = compatibility.cpuSockets[cpuSelect.value];
            
            // Loop through motherboard options
            Array.from(motherboardSelect.options).forEach(option => {
                if (option.value) {
                    const motherboardSocket = compatibility.motherboards[option.value]?.socket;
                    if (motherboardSocket !== cpuSocket) {
                        option.style.color = '#aaa';
                    } else {
                        option.style.color = '';
                    }
                }
            });
        }
        
        // Filter RAM based on motherboard selection
        if (motherboardSelect.value) {
            const motherboardMemory = compatibility.motherboards[motherboardSelect.value]?.memory;
            
            // Loop through RAM options
            Array.from(ramSelect.options).forEach(option => {
                if (option.value) {
                    const ramType = compatibility.ram[option.value];
                    if (ramType !== motherboardMemory) {
                        option.style.color = '#aaa';
                    } else {
                        option.style.color = '';
                    }
                }
            });
        }
    }
    
    // Add CSS styles for compatibility
    function addCompatibilityStyles() {
        const style = document.createElement('style');
        style.textContent = `
            .compatibility-warning {
                color: #e74c3c;
                font-size: 14px;
                margin-top: 10px;
                font-weight: 500;
            }
            .component-selection select option[disabled] {
                color: #aaa;
            }
            .incompatible-component {
                border-color: #e74c3c !important;
                background-color: rgba(231, 76, 60, 0.05) !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // Initialize the form
    function initForm() {
        // Extract prices from options
        extractPrices();
        
        // Add compatibility styles
        addCompatibilityStyles();
        
        // Add event listeners to component selects
        cpuSelect.addEventListener('change', function() {
            filterCompatibleOptions();
            updateCompatibilityWarnings();
            calculatePrice();
        });
        
        motherboardSelect.addEventListener('change', function() {
            filterCompatibleOptions();
            updateCompatibilityWarnings();
            calculatePrice();
        });
        
        // Add specific handler for GPU to show power requirements
        gpuSelect.addEventListener('change', function() {
            updateCompatibilityWarnings();
            calculatePrice();
            showGpuPowerRequirements();
        });
        
        // Add change listeners to all other selects
        const selects = form.querySelectorAll('select');
        selects.forEach(select => {
            if (select !== cpuSelect && select !== motherboardSelect && select !== gpuSelect) {
                select.addEventListener('change', function() {
                    updateCompatibilityWarnings();
                    calculatePrice();
                });
            }
        });
        
        // Add form submission listener
        form.addEventListener('submit', function(e) {
            // Check compatibility before allowing submission
            if (!isBuildCompatible()) {
                e.preventDefault();
                alert('Please resolve compatibility issues before submitting your build request.');
                return false;
            }
            
            // At this point, the form is compatible and would submit
            // The JavaScript added to custom-pcs.php will handle the actual submission
        });
        
        // Initial calculations and updates
        calculatePrice();
        updateCompatibilityWarnings();
        showGpuPowerRequirements();
        filterCompatibleOptions();
    }
    
    // Display recommended PSU wattage for selected GPU
    function showGpuPowerRequirements() {
        // Remove any existing power recommendation
        const existingRec = form.querySelector('.gpu-power-recommendation');
        if (existingRec) {
            existingRec.remove();
        }
        
        const gpu = gpuSelect.value;
        if (!gpu) return;
        
        // Get minimum recommended PSU wattage
        let recommendedWattage = '';
        if (compatibility.psuRequirements[gpu] && compatibility.psuRequirements[gpu].length > 0) {
            for (const psu of compatibility.psuRequirements[gpu]) {
                const wattMatch = psu.match(/(\d+)W/);
                if (wattMatch) {
                    recommendedWattage = wattMatch[1];
                    break;
                }
            }
        }
        
        if (recommendedWattage) {
            // Create recommendation element
            const recommendation = document.createElement('div');
            recommendation.className = 'gpu-power-recommendation';
            recommendation.innerHTML = `<i class="fas fa-info-circle"></i> Recommended PSU: ${recommendedWattage}W or higher`;
            recommendation.style.fontSize = '14px';
            recommendation.style.marginTop = '10px';
            recommendation.style.color = '#3498db';
            recommendation.style.fontWeight = '500';
            
            // Insert after GPU select
            gpuSelect.parentNode.insertBefore(recommendation, gpuSelect.nextSibling);
        }
    }
    
    // Initialize when DOM is ready
    initForm();
}); 