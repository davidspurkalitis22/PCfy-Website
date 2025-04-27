document.addEventListener('DOMContentLoaded', function() {
    // Get the form element
    const form = document.getElementById('custom-pc-form');
    
    if (!form) return; // Exit if form doesn't exist on this page
    
    // Add a function to populate the build summary before submission
    function populateBuildSummary() {
        const components = {
            'CPU': document.getElementById('cpu').value,
            'Motherboard': document.getElementById('motherboard').value,
            'GPU': document.getElementById('gpu').value,
            'RAM': document.getElementById('ram').value,
            'Primary Storage': document.getElementById('storage').value,
            'Additional Storage': document.getElementById('additional_storage').value,
            'CPU Cooling': document.getElementById('cooling').value,
            'Case': document.getElementById('case').value,
            'Power Supply': document.getElementById('power_supply').value,
            'Operating System': document.getElementById('operating_system').value
        };
        
        // Create a formatted summary
        let summary = "Custom PC Build Summary:\n\n";
        for (const [component, value] of Object.entries(components)) {
            if (value) {
                summary += `${component}: ${value}\n`;
            }
        }
        
        summary += `\nEstimated Price: ${document.getElementById('price-display').textContent}\n`;
        summary += `Customer Name: ${document.getElementById('name').value}\n`;
        summary += `Customer Email: ${document.getElementById('email').value}\n`;
        summary += `Customer Phone: ${document.getElementById('phone').value}\n`;
        
        const notes = document.getElementById('additional-notes').value;
        if (notes) {
            summary += `\nAdditional Notes: ${notes}\n`;
        }
        
        // Set the hidden field value
        document.getElementById('build_summary').value = summary;
    }
    
    // Create a status message element
    function createStatusMessage() {
        let statusElement = document.getElementById('submission-status');
        if (!statusElement) {
            statusElement = document.createElement('div');
            statusElement.id = 'submission-status';
            statusElement.style.padding = '15px';
            statusElement.style.margin = '15px 0';
            statusElement.style.borderRadius = '5px';
            statusElement.style.display = 'none';
            
            // Insert before submit button
            const submitContainer = form.querySelector('.form-submit');
            form.insertBefore(statusElement, submitContainer);
        }
        return statusElement;
    }
    
    // Show status message
    function showStatus(message, isError = false) {
        const statusElement = createStatusMessage();
        statusElement.textContent = message;
        statusElement.style.display = 'block';
        
        if (isError) {
            statusElement.style.backgroundColor = '#ffebee';
            statusElement.style.color = '#c62828';
            statusElement.style.border = '1px solid #ef9a9a';
        } else {
            statusElement.style.backgroundColor = '#e8f5e9';
            statusElement.style.color = '#2e7d32';
            statusElement.style.border = '1px solid #a5d6a7';
        }
        
        // Scroll to status message
        statusElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Show loading status
        showStatus('Processing your request, please wait...', false);
        
        // Disable the submit button to prevent double submissions
        const submitButton = form.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.textContent = 'Submitting...';
        
        // Populate the build summary
        populateBuildSummary();
        
        // Create form data
        const formData = new FormData(form);
        
        // Simple form submission without relying on response
        fetch('submit_custom_build.php', {
            method: 'POST',
            body: formData
        })
        .then(() => {
            // Immediately redirect after submission attempt regardless of result
            window.location.href = "build-confirmation.php";
        })
        .catch(() => {
            // Redirect even if there's an error
            window.location.href = "build-confirmation.php";
        });
        
        // Fallback - redirect after 3 seconds no matter what
        setTimeout(function() {
            window.location.href = "build-confirmation.php";
        }, 3000);
    });
}); 