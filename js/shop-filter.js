// Shop product filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    // Shop product filtering functionality
    const filterBtns = document.querySelectorAll('.filter-btn');
    const productCards = document.querySelectorAll('.product-card');
    const sortBySelect = document.getElementById('sort-by');
    const brandSelect = document.getElementById('brand');
    const subcategoryItems = document.querySelectorAll('.subcategory-item');
    let activeSubcategory = null;
    
    // Initialize sorting on page load
    setTimeout(() => {
        applyFilters();
    }, 100);
    
    // Category filter functionality with subcategory toggling
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            this.classList.add('active');
            
            // Get filter value
            const filterValue = this.getAttribute('data-filter');
            
            // Hide all subcategory menus
            document.querySelectorAll('.subcategory-menu').forEach(menu => {
                menu.classList.remove('active');
            });
            
            // Show subcategory menu for selected category if it exists
            if (filterValue !== 'all') {
                const subcategoryMenu = document.querySelector(`.subcategory-menu[data-parent="${filterValue}"]`);
                if (subcategoryMenu) {
                    subcategoryMenu.classList.add('active');
                }
            }
            
            // Reset subcategory selection
            subcategoryItems.forEach(item => item.classList.remove('active'));
            activeSubcategory = null;
            
            // Show/hide products based on filter
            productCards.forEach(card => {
                if (filterValue === 'all' || card.getAttribute('data-category') === filterValue) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
            
            // Apply other filters after changing category
            applyFilters();
        });
    });
    
    // Subcategory filter functionality
    subcategoryItems.forEach(item => {
        item.addEventListener('click', function() {
            const subfilterValue = this.getAttribute('data-subfilter');
            const parentCategory = this.parentElement.getAttribute('data-parent');
            
            // Toggle active state
            if (this.classList.contains('active')) {
                this.classList.remove('active');
                activeSubcategory = null;
            } else {
                // Remove active class from all subcategory items in this group
                this.parentElement.querySelectorAll('.subcategory-item').forEach(i => {
                    i.classList.remove('active');
                });
                
                this.classList.add('active');
                activeSubcategory = subfilterValue;
            }
            
            // Filter products by subcategory
            productCards.forEach(card => {
                // Only apply subcategory filter to products in the parent category
                if (card.getAttribute('data-category') === parentCategory) {
                    if (!activeSubcategory || card.getAttribute('data-subcategory') === activeSubcategory) {
                        card.style.display = '';
                    } else {
                        card.style.display = 'none';
                    }
                }
            });
            
            // Apply other filters
            applyFilters();
        });
    });
    
    // Apply filters automatically when dropdowns change
    sortBySelect.addEventListener('change', applyFilters);
    brandSelect.addEventListener('change', applyFilters);
    
    function applyFilters() {
        const sortValue = sortBySelect.value;
        const brand = brandSelect.value;
        
        // Get visible products based on category and subcategory filters
        let visibleProducts = Array.from(productCards).filter(card => {
            return card.style.display !== 'none';
        });
        
        // Filter by brand
        if (brand !== 'all') {
            visibleProducts = visibleProducts.filter(card => {
                const productName = card.querySelector('h3').textContent.toLowerCase();
                return productName.includes(brand.toLowerCase());
            });
        }
        
        // Sort products
        if (sortValue !== 'featured') {
            visibleProducts.sort((a, b) => {
                if (sortValue === 'price-low' || sortValue === 'price-high') {
                    // Extract the prices without currency symbols and convert to numbers
                    let priceA = a.querySelector('.product-price').textContent;
                    let priceB = b.querySelector('.product-price').textContent;
                    
                    // Remove currency symbol and convert to number
                    priceA = parseFloat(priceA.replace(/[^0-9.,]/g, '').replace(',', '.'));
                    priceB = parseFloat(priceB.replace(/[^0-9.,]/g, '').replace(',', '.'));
                    
                    // Debug
                    console.log(`${a.querySelector('h3').textContent}: ${priceA}`);
                    console.log(`${b.querySelector('h3').textContent}: ${priceB}`);
                    
                    if (sortValue === 'price-low') {
                        return priceA - priceB; // ascending order
                    } else {
                        return priceB - priceA; // descending order
                    }
                } else if (sortValue === 'rating') {
                    // Count stars instead of relying on review counts
                    const starsA = a.querySelectorAll('.fa-star:not(.fa-star-half-alt):not(.far)').length;
                    const starsB = b.querySelectorAll('.fa-star:not(.fa-star-half-alt):not(.far)').length;
                    
                    // Add 0.5 for half stars
                    const halfStarsA = a.querySelectorAll('.fa-star-half-alt').length * 0.5;
                    const halfStarsB = b.querySelectorAll('.fa-star-half-alt').length * 0.5;
                    
                    return (starsB + halfStarsB) - (starsA + halfStarsA);
                } else if (sortValue === 'newest') {
                    // For demonstration, use product position as proxy for newness
                    // Products at the bottom considered newer
                    const indexA = Array.from(productCards).indexOf(a);
                    const indexB = Array.from(productCards).indexOf(b);
                    return indexB - indexA;
                }
                
                return 0; // Default return if no condition matches
            });
        }
        
        // Hide all products first
        productCards.forEach(card => {
            card.style.display = 'none';
        });
        
        // Show only filtered and sorted products
        visibleProducts.forEach((card, index) => {
            card.style.display = '';
            
            // Move the element in the DOM to match the sort order
            const parent = card.parentNode;
            parent.appendChild(card);
        });
        
        // Show no results message if needed
        const noResultsMessage = document.querySelector('.no-results');
        if (visibleProducts.length === 0 && noResultsMessage) {
            noResultsMessage.style.display = 'block';
        } else if (noResultsMessage) {
            noResultsMessage.style.display = 'none';
        }
    }
}); 