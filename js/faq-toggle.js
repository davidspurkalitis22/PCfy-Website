// FAQ Toggle Functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqItems = document.querySelectorAll('.faq-item');
    
    if (faqItems.length === 0) return; // Exit if no FAQ items exist on the page
    
    faqItems.forEach(item => {
        const question = item.querySelector('.faq-question');
        const answer = item.querySelector('.faq-answer');
        const icon = item.querySelector('.toggle-icon i');
        
        // Initially hide all answers
        if (answer) {
            answer.style.display = 'none';
        }
        
        question.addEventListener('click', function() {
            // Toggle answer visibility
            if (answer && answer.style.display === 'none') {
                answer.style.display = 'block';
                if (icon) {
                    icon.classList.remove('fa-chevron-down');
                    icon.classList.add('fa-chevron-up');
                }
            } else if (answer) {
                answer.style.display = 'none';
                if (icon) {
                    icon.classList.remove('fa-chevron-up');
                    icon.classList.add('fa-chevron-down');
                }
            }
        });
    });
}); 