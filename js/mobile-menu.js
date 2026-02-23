/**
 * Mobile Menu Handler
 * Handles mobile menu toggle functionality for both landing page and dashboards
 */

document.addEventListener('DOMContentLoaded', function() {
    // Landing page mobile menu
    const mobileMenu = document.querySelector('.mobile-menu');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenu && navLinks) {
        mobileMenu.addEventListener('click', function() {
            this.classList.toggle('active');
            navLinks.classList.toggle('active');
        });
        
        // Close menu when clicking on a link
        const links = navLinks.querySelectorAll('a');
        links.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('active');
                navLinks.classList.remove('active');
            });
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenu.contains(e.target) && !navLinks.contains(e.target)) {
                mobileMenu.classList.remove('active');
                navLinks.classList.remove('active');
            }
        });
    }
    
    // Dashboard mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (mobileMenuToggle && sidebar) {
        mobileMenuToggle.addEventListener('click', function() {
            sidebar.classList.toggle('active');
            document.body.classList.toggle('sidebar-open');
        });
        
        // Close sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!mobileMenuToggle.contains(e.target) && !sidebar.contains(e.target)) {
                sidebar.classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });
        
        // Close sidebar when clicking on a nav item
        const navItems = sidebar.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', function() {
                setTimeout(() => {
                    sidebar.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                }, 300);
            });
        });
    }
    
    // Admin dashboard mobile menu
    const adminMenuToggle = document.querySelector('.admin-menu-toggle');
    const adminSidebar = document.querySelector('.admin-container .sidebar');
    
    if (adminMenuToggle && adminSidebar) {
        adminMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
            document.body.classList.toggle('admin-sidebar-open');
        });
        
        // Close admin sidebar when clicking outside
        document.addEventListener('click', function(e) {
            if (!adminMenuToggle.contains(e.target) && !adminSidebar.contains(e.target)) {
                adminSidebar.classList.remove('active');
                document.body.classList.remove('admin-sidebar-open');
            }
        });
    }
    
    // Handle window resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            if (window.innerWidth > 768) {
                // Reset mobile menus on desktop
                if (mobileMenu && navLinks) {
                    mobileMenu.classList.remove('active');
                    navLinks.classList.remove('active');
                }
                if (sidebar) {
                    sidebar.classList.remove('active');
                    document.body.classList.remove('sidebar-open');
                }
                if (adminSidebar) {
                    adminSidebar.classList.remove('active');
                    document.body.classList.remove('admin-sidebar-open');
                }
            }
        }, 250);
    });
    
    // Smooth scroll for anchor links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    anchorLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            
            if (targetElement) {
                const offsetTop = targetElement.offsetTop - 80; // Account for fixed header
                window.scrollTo({
                    top: offsetTop,
                    behavior: 'smooth'
                });
            }
        });
    });
    
    // Add touch support for mobile devices
    if ('ontouchstart' in window) {
        document.body.classList.add('touch-device');
    }
});

// Export for use in other files if needed
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initMobileMenu: function() {
            // Re-initialize mobile menu
            document.dispatchEvent(new Event('DOMContentLoaded'));
        }
    };
}
