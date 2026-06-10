// Main JavaScript file

document.addEventListener('DOMContentLoaded', function() {
    // File upload progress indicator (if needed)
    const fileInput = document.getElementById('file');
    if(fileInput) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if(file) {
                const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
                const fileName = file.name;
                const fileType = file.type;
                
                // Update UI with file info
                const fileInfo = document.createElement('div');
                fileInfo.className = 'file-info-display';
                fileInfo.innerHTML = `
                    <p><strong>Selected file:</strong> ${fileName}</p>
                    <p><strong>Size:</strong> ${fileSize} MB</p>
                    <p><strong>Type:</strong> ${fileType}</p>
                `;
                
                const existingInfo = document.querySelector('.file-info-display');
                if(existingInfo) {
                    existingInfo.remove();
                }
                
                fileInput.parentNode.appendChild(fileInfo);
            }
        });
    }
    
    // Category filtering for downloads
    const categoryButtons = document.querySelectorAll('.category-btn');
    categoryButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if(this.href.includes('download.php')) {
                e.preventDefault();
                const category = this.getAttribute('href').split('category=')[1] || '';
                window.location.href = `download.php${category ? '?category=' + category : ''}`;
            }
        });
    });
    
    // Search functionality
    const searchForm = document.querySelector('form[action="download.php"]');
    if(searchForm) {
        const searchInput = searchForm.querySelector('input[name="search"]');
        const categorySelect = searchForm.querySelector('select[name="category"]');
        
        searchForm.addEventListener('submit', function(e) {
            const searchValue = searchInput.value.trim();
            const categoryValue = categorySelect.value;
            
            if(!searchValue && !categoryValue) {
                e.preventDefault();
                window.location.href = 'download.php';
            }
        });
    }
    
    // Portfolio skills tag input
    const skillsInput = document.querySelector('input[name="skills"]');
    if(skillsInput) {
        skillsInput.addEventListener('input', function(e) {
            // Optional: Add live preview of skills as tags
        });
    }
    
    // Feedback rating system
    const ratingStars = document.querySelectorAll('.rating-star');
    ratingStars.forEach(star => {
        star.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            document.querySelector('input[name="rating"]').value = rating;
            
            // Update star display
            ratingStars.forEach(s => {
                if(s.getAttribute('data-rating') <= rating) {
                    s.classList.remove('far');
                    s.classList.add('fas');
                } else {
                    s.classList.remove('fas');
                    s.classList.add('far');
                }
            });
        });
    });
    
    // File type icons mapping
    const fileIconMap = {
        'pdf': 'file-pdf',
        'doc': 'file-word',
        'docx': 'file-word',
        'ppt': 'file-powerpoint',
        'pptx': 'file-powerpoint',
        'zip': 'file-archive',
        'rar': 'file-archive',
        'exe': 'file-code',
        'msi': 'file-code',
        'jpg': 'file-image',
        'png': 'file-image',
        'txt': 'file-alt'
    };
    
    // Update file icons based on extension
    document.querySelectorAll('.file-item').forEach(item => {
        const filename = item.querySelector('h4').textContent;
        const extension = filename.split('.').pop().toLowerCase();
        const icon = fileIconMap[extension] || 'file';
        const iconElement = item.querySelector('.file-icon i');
        if(iconElement) {
            iconElement.className = `fas fa-${icon}`;
        }
    });
    
    // Responsive navbar toggle for mobile
    const createMobileMenu = () => {
        const navbar = document.querySelector('.navbar');
        const navLinks = document.querySelector('.nav-links');
        
        if(window.innerWidth <= 768 && !document.querySelector('.menu-toggle')) {
            const menuToggle = document.createElement('button');
            menuToggle.className = 'menu-toggle';
            menuToggle.innerHTML = '<i class="fas fa-bars"></i>';
            navbar.insertBefore(menuToggle, navLinks);
            
            menuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('show');
            });
            
            // Close menu when clicking outside
            document.addEventListener('click', function(e) {
                if(!navbar.contains(e.target) && navLinks.classList.contains('show')) {
                    navLinks.classList.remove('show');
                }
            });
        }
    };
    
    createMobileMenu();
    window.addEventListener('resize', createMobileMenu);
});


// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const mobileNav = document.getElementById('mobileNav');
    
    if(menuToggle && mobileNav) {
        menuToggle.addEventListener('click', function() {
            mobileNav.classList.toggle('active');
            const icon = this.querySelector('i');
            if(mobileNav.classList.contains('active')) {
                icon.className = 'fas fa-times';
            } else {
                icon.className = 'fas fa-bars';
            }
        });
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if(!menuToggle.contains(event.target) && !mobileNav.contains(event.target)) {
                mobileNav.classList.remove('active');
                menuToggle.querySelector('i').className = 'fas fa-bars';
            }
        });
        
        // Close mobile menu on window resize (if resized to desktop)
        window.addEventListener('resize', function() {
            if(window.innerWidth > 768) {
                mobileNav.classList.remove('active');
                menuToggle.querySelector('i').className = 'fas fa-bars';
            }
        });
    }
});