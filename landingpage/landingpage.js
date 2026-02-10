// Booking page interactivity

document.addEventListener('DOMContentLoaded', function () {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.tab-btn');

    tabButtons.forEach(button => {
        button.addEventListener('click', function () {
            // Remove active class from all tabs
            tabButtons.forEach(btn => btn.classList.remove('active'));

            // Add active class to clicked tab
            this.classList.add('active');

            // Get tab type
            const tabType = this.getAttribute('data-tab');

            // Update search form based on tab
            updateSearchForm(tabType);
        });
    });

    // Smooth scroll behavior
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Navbar scroll effect
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar');

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > 100) {
            navbar.style.boxShadow = '0 4px 12px rgba(0, 0, 0, 0.1)';
        } else {
            navbar.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
        }

        lastScroll = currentScroll;
    });

    // Book Now button functionality
    const bookButtons = document.querySelectorAll('.btn-book');
    bookButtons.forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.destination-item');
            const destinationName = card.querySelector('h3').textContent;
            showBookingModal(destinationName);
        });
    });

    // Search button functionality
    const searchButton = document.querySelector('.btn-search');
    if (searchButton) {
        searchButton.addEventListener('click', function () {
            performSearch();
        });
    }

    // Promo claim buttons
    const claimButtons = document.querySelectorAll('.btn-claim, .btn-promo');
    claimButtons.forEach(button => {
        button.addEventListener('click', function () {
            showClaimNotification();
        });
    });

    // Guide profile buttons
    const hireButtons = document.querySelectorAll('.btn-hire');
    hireButtons.forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.guide-card');
            const guideName = card.querySelector('h3').textContent;
            showGuideProfile(guideName);
        });
    });

    // Animate elements on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe cards for animation
    document.querySelectorAll('.destination-item, .guide-card, .feature-box, .promo-card').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'all 0.6s ease-out';
        observer.observe(el);
    });
});

// Helper Functions

function updateSearchForm(tabType) {
    const destinationInput = document.querySelector('.destination-field input');

    switch (tabType) {
        case 'tours':
            destinationInput.placeholder = 'San Jose del Monte attractions';
            break;
        case 'hotels':
            destinationInput.placeholder = 'Hotels in San Jose del Monte';
            break;
        case 'guides':
            destinationInput.placeholder = 'Find tour guides in SJDM';
            break;
        case 'packages':
            destinationInput.placeholder = 'Tour packages in SJDM';
            break;
    }
}

function performSearch() {
    const destination = document.querySelector('.destination-field input').value;
    const checkin = document.querySelector('.date-field:nth-of-type(2) input').value;
    const checkout = document.querySelector('.date-field:nth-of-type(3) input').value;
    const guests = document.querySelector('.guests-field input').value;

    // Show loading animation
    const searchBtn = document.querySelector('.btn-search');
    const originalContent = searchBtn.innerHTML;
    searchBtn.innerHTML = '<span class="material-icons-outlined rotating">sync</span><span>Searching...</span>';
    searchBtn.style.pointerEvents = 'none';

    // Simulate search (in production, this would make an API call)
    setTimeout(() => {
        searchBtn.innerHTML = originalContent;
        searchBtn.style.pointerEvents = 'auto';

        // Scroll to results
        document.querySelector('.featured-section').scrollIntoView({
            behavior: 'smooth'
        });

        // Show notification
        showNotification('Found 12 amazing destinations for you!', 'success');
    }, 1500);
}

function showBookingModal(destinationName) {
    showNotification(`Booking ${destinationName}... Redirecting to checkout`, 'info');

    // In production, this would open a booking modal or redirect to booking page
    setTimeout(() => {
        // window.location.href = '/booking/checkout?destination=' + encodeURIComponent(destinationName);
    }, 2000);
}

function showGuideProfile(guideName) {
    showNotification(`Loading ${guideName}'s profile...`, 'info');

    // In production, this would navigate to guide profile page
    setTimeout(() => {
        // window.location.href = '/guides/profile?name=' + encodeURIComponent(guideName);
    }, 1500);
}

function showClaimNotification() {
    showNotification('Please sign in to claim this offer!', 'warning');
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.style.cssText = `
        position: fixed;
        top: 100px;
        right: 30px;
        background: ${type === 'success' ? '#10B981' : type === 'warning' ? '#F59E0B' : '#3B82F6'};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 10000;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        animation: slideInRight 0.3s ease-out;
    `;

    const icon = type === 'success' ? 'check_circle' : type === 'warning' ? 'warning' : 'info';
    notification.innerHTML = `
        <span class="material-icons-outlined">${icon}</span>
        <span>${message}</span>
    `;

    document.body.appendChild(notification);

    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add rotation animation for loading icon
const style = document.createElement('style');
style.textContent = `
    @keyframes rotating {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .rotating {
        animation: rotating 1s linear infinite;
    }
    
    @keyframes slideInRight {
        from {
            transform: translateX(400px);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(400px);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);