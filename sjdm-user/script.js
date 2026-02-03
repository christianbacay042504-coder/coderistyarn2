// Complete CRUD System with Image Handling

// Tour guides will be loaded from database via PHP
// No hardcoded guide data needed anymore

// Global State
let currentGuideId = null;
let currentStep = 1;
const totalSteps = 4;
let bookingData = {};
let currentFilter = 'all';
let currentSort = 'rating';
let searchQuery = '';

// Guide Profile Modal - Updated to work with database-driven content
function viewGuideProfile(guideId) {
    console.log('viewGuideProfile called with guideId:', guideId);
    
    // Create modal with enhanced aesthetics
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content guide-profile-modal">
            <div class="modal-header">
                <div class="modal-title">
                    <span class="material-icons-outlined modal-icon">person</span>
                    <h2>Guide Profile</h2>
                </div>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="loading-message">
                    <div class="loading-spinner">
                        <div class="spinner-circle"></div>
                        <span class="material-icons-outlined">hourglass_empty</span>
                    </div>
                    <p>Loading guide profile...</p>
                    <p><small>Guide ID: ${guideId}</small></p>
                </div>
            </div>
        </div>
    `;
    
    // Add custom styles for enhanced aesthetics
    const style = document.createElement('style');
    style.textContent = `
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.6);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .modal-overlay.show {
            opacity: 1;
        }
        
        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            transform: scale(0.9) translateY(20px);
            transition: transform 0.3s ease;
        }
        
        .modal-overlay.show .modal-content {
            transform: scale(1) translateY(0);
        }
        
        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 24px 32px;
            border-radius: 16px 16px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .modal-title {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .modal-title h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        
        .modal-icon {
            font-size: 28px;
        }
        
        .close-modal {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .close-modal:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: scale(1.1);
        }
        
        .modal-body {
            padding: 32px;
        }
        
        .loading-message {
            text-align: center;
            padding: 60px 20px;
        }
        
        .loading-spinner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .spinner-circle {
            width: 60px;
            height: 60px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .loading-spinner .material-icons-outlined {
            font-size: 32px;
            color: #667eea;
        }
        
        .loading-message p {
            color: #666;
            margin: 8px 0;
            font-size: 16px;
        }
        
        .loading-message small {
            color: #999;
            font-size: 14px;
        }
        
        .guide-profile-content {
            animation: fadeInUp 0.5s ease;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .guide-profile-header {
            display: flex;
            gap: 24px;
            margin-bottom: 32px;
            padding-bottom: 24px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .guide-profile-photo {
            position: relative;
            flex-shrink: 0;
        }
        
        .guide-profile-photo img {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            object-fit: cover;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
        }
        
        .verified-badge {
            position: absolute;
            bottom: -8px;
            right: -8px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            border: 2px solid white;
            animation: verifiedPulse 2s infinite;
            z-index: 10;
        }
        
        @keyframes verifiedPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 6px 20px rgba(16, 185, 129, 0.4);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 8px 25px rgba(16, 185, 129, 0.6);
            }
        }
        
        .verified-badge .material-icons-outlined {
            font-size: 14px;
            animation: verifiedIconSpin 3s infinite;
        }
        
        @keyframes verifiedIconSpin {
            0%, 100% {
                transform: rotate(0deg);
            }
            25% {
                transform: rotate(5deg);
            }
            75% {
                transform: rotate(-5deg);
            }
        }
        
        .guide-profile-info {
            flex: 1;
        }
        
        .guide-name-section {
            margin-bottom: 12px;
        }
        
        .guide-profile-info h3 {
            margin: 0 0 8px 0;
            font-size: 28px;
            font-weight: 700;
            color: #1f2937;
        }
        
        .verified-ribbon {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-top: 8px;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            animation: ribbonShine 3s infinite;
        }
        
        @keyframes ribbonShine {
            0%, 100% {
                box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            }
            50% {
                box-shadow: 0 6px 16px rgba(16, 185, 129, 0.5);
            }
        }
        
        .verified-ribbon .material-icons-outlined {
            font-size: 14px;
        }
        
        .verified-glow {
            position: absolute;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            border-radius: 20px;
            animation: glowPulse 2s infinite;
            pointer-events: none;
        }
        
        @keyframes glowPulse {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.6;
                transform: scale(1.05);
            }
        }
        
        .verification-section {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
            border: 1px solid #a7f3d0;
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 32px;
            animation: verificationSlideIn 0.6s ease;
        }
        
        @keyframes verificationSlideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .verification-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        
        .verification-header .material-icons-outlined {
            font-size: 24px;
            color: #10b981;
        }
        
        .verification-header h4 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            color: #065f46;
        }
        
        .verification-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
        }
        
        .verification-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 16px;
            background: white;
            border-radius: 12px;
            border: 1px solid #d1fae5;
            transition: all 0.3s ease;
        }
        
        .verification-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.2);
            border-color: #10b981;
        }
        
        .verification-item .material-icons-outlined {
            font-size: 20px;
            color: #10b981;
            flex-shrink: 0;
        }
        
        .verification-item span {
            font-size: 14px;
            font-weight: 500;
            color: #047857;
        }
        
        .guide-specialty {
            color: #667eea;
            font-size: 16px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .guide-rating {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        
        .guide-category-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f3f4f6;
            color: #4b5563;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 500;
        }
        
        .guide-description-section {
            margin-bottom: 32px;
        }
        
        .guide-description-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 16px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .guide-description-section p {
            color: #4b5563;
            line-height: 1.6;
            margin: 0;
        }
        
        .guide-details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 24px;
            margin-bottom: 32px;
        }
        
        .detail-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 20px;
            background: #f9fafb;
            border-radius: 12px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .detail-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }
        
        .detail-item .material-icons-outlined {
            color: #667eea;
            font-size: 24px;
            flex-shrink: 0;
        }
        
        .detail-item strong {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 4px;
        }
        
        .detail-item p {
            color: #6b7280;
            margin: 0;
            font-size: 14px;
        }
        
        .guide-booking-section {
            background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 24px;
        }
        
        .guide-booking-section h4 {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0 0 12px 0;
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
        }
        
        .guide-booking-section p {
            color: #4b5563;
            margin-bottom: 20px;
        }
        
        .booking-actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-secondary {
            background: #f3f4f6;
            color: #4b5563;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background: #e5e7eb;
            transform: translateY(-2px);
        }
        
        .error-message {
            text-align: center;
            padding: 60px 20px;
        }
        
        .error-message .material-icons-outlined {
            font-size: 48px;
            color: #ef4444;
            margin-bottom: 16px;
        }
        
        .error-message h3 {
            color: #ef4444;
            margin: 0 0 8px 0;
        }
        
        .error-message p {
            color: #6b7280;
            margin: 0 0 24px 0;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
            }
            
            .modal-header {
                padding: 20px 24px;
            }
            
            .modal-body {
                padding: 24px;
            }
            
            .guide-profile-header {
                flex-direction: column;
                text-align: center;
                gap: 16px;
            }
            
            .guide-profile-info h3 {
                font-size: 24px;
            }
            
            .guide-details-grid {
                grid-template-columns: 1fr;
            }
            
            .booking-actions {
                flex-direction: column;
            }
        }
    `;
    document.head.appendChild(style);
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
    
    // Fetch guide data from the DOM (since we're working with database-driven content)
    const guideCard = document.querySelector(`[data-guide-id="${guideId}"]`);
    if (guideCard) {
        // Extract data from the guide card
        const guideName = guideCard.querySelector('.guide-name')?.textContent || 'Unknown Guide';
        const guideSpecialty = guideCard.querySelector('.guide-specialty')?.textContent || '';
        const guideDescription = guideCard.querySelector('.guide-description')?.textContent || '';
        const guidePhoto = guideCard.querySelector('.guide-photo img')?.src || '';
        const guideCategory = guideCard.getAttribute('data-category') || '';
        const ratingValue = guideCard.querySelector('.rating-value')?.textContent || '0';
        const reviewCount = guideCard.querySelector('.review-count')?.textContent || '(0 reviews)';
        const experienceYears = guideCard.querySelector('.meta-item:nth-child(1)')?.textContent || '';
        const languages = guideCard.querySelector('.meta-item:nth-child(2)')?.textContent || '';
        const groupSize = guideCard.querySelector('.meta-item:nth-child(3)')?.textContent || '';
        const isVerified = guideCard.querySelector('.verified-badge') !== null;
        
        // Update modal with guide information
        setTimeout(() => {
            const modalBody = modal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <div class="guide-profile-content">
                    <div class="guide-profile-header">
                        <div class="guide-profile-photo">
                            <img src="${guidePhoto}" alt="${guideName}" />
                            ${isVerified ? `
                            <div class="verified-badge">
                                <span class="material-icons-outlined">verified</span>
                                <span>Verified Guide</span>
                            </div>
                            <div class="verified-glow"></div>
                            ` : ''}
                        </div>
                        <div class="guide-profile-info">
                            <div class="guide-name-section">
                                <h3>${guideName}</h3>
                                ${isVerified ? `
                                <div class="verified-ribbon">
                                    <span class="material-icons-outlined">verified_user</span>
                                    <span>Trusted Professional</span>
                                </div>
                                ` : ''}
                            </div>
                            <p class="guide-specialty">${guideSpecialty}</p>
                            <div class="guide-rating">
                                ${generateStarRating(parseFloat(ratingValue))}
                                <span class="rating-value">${ratingValue}</span>
                                <span class="review-count">${reviewCount}</span>
                            </div>
                            <div class="guide-category-badge">
                                <span class="material-icons-outlined">category</span>
                                ${getCategoryDisplayName(guideCategory)}
                            </div>
                        </div>
                    </div>
                    
                    ${isVerified ? `
                    <div class="verification-section">
                        <div class="verification-header">
                            <span class="material-icons-outlined">security</span>
                            <h4>Verification Details</h4>
                        </div>
                        <div class="verification-grid">
                            <div class="verification-item">
                                <span class="material-icons-outlined">check_circle</span>
                                <span>Identity Verified</span>
                            </div>
                            <div class="verification-item">
                                <span class="material-icons-outlined">workspace_premium</span>
                                <span>Professional Certified</span>
                            </div>
                            <div class="verification-item">
                                <span class="material-icons-outlined">reviews</span>
                                <span>Background Checked</span>
                            </div>
                            <div class="verification-item">
                                <span class="material-icons-outlined">handshake</span>
                                <span>Trusted by Community</span>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                    
                    <div class="guide-description-section">
                        <h4><span class="material-icons-outlined">info</span> About</h4>
                        <p>${guideDescription}</p>
                    </div>
                    
                    <div class="guide-details-grid">
                        <div class="detail-item">
                            <span class="material-icons-outlined">schedule</span>
                            <div>
                                <strong>Experience</strong>
                                <p>${experienceYears}</p>
                            </div>
                        </div>
                        <div class="detail-item">
                            <span class="material-icons-outlined">translate</span>
                            <div>
                                <strong>Languages</strong>
                                <p>${languages}</p>
                            </div>
                        </div>
                        ${groupSize ? `
                        <div class="detail-item">
                            <span class="material-icons-outlined">groups</span>
                            <div>
                                <strong>Group Size</strong>
                                <p>${groupSize}</p>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                    
                    <div class="guide-booking-section">
                        <h4><span class="material-icons-outlined">calendar_today</span> Booking Information</h4>
                        <p>To book this guide and get detailed pricing information, please click the button below.</p>
                        <div class="booking-actions">
                            <button class="btn-primary" onclick="bookGuide(${guideId})">
                                <span class="material-icons-outlined">calendar_today</span>
                                Book This Guide
                            </button>
                            <button class="btn-secondary" onclick="this.closest('.modal-overlay').remove()">
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }, 500);
    } else {
        // Handle case where guide card is not found
        setTimeout(() => {
            const modalBody = modal.querySelector('.modal-body');
            modalBody.innerHTML = `
                <div class="error-message">
                    <span class="material-icons-outlined">error</span>
                    <h3>Guide Not Found</h3>
                    <p>Unable to load guide information. Please try again later.</p>
                    <button class="btn-secondary" onclick="this.closest('.modal-overlay').remove()">Close</button>
                </div>
            `;
        }, 500);
    }
}

// Helper function to get display name for category
function getCategoryDisplayName(category) {
    const categoryNames = {
        'mountain': 'Mountain Hiking',
        'city': 'City Tours',
        'farm': 'Farm & Eco-Tourism',
        'waterfall': 'Waterfall Tours',
        'historical': 'Historical Tours',
        'general': 'General Tours'
    };
    return categoryNames[category] || category;
}

// Helper function to generate star rating HTML
function generateStarRating(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 !== 0;
    let starsHTML = '';
    
    for (let i = 0; i < fullStars; i++) {
        starsHTML += '<span class="material-icons-outlined">star</span>';
    }
    
    if (hasHalfStar) {
        starsHTML += '<span class="material-icons-outlined">star_half</span>';
    }
    
    const emptyStars = 5 - Math.ceil(rating);
    for (let i = 0; i < emptyStars; i++) {
        starsHTML += '<span class="material-icons-outlined">star_outline</span>';
    }
    
    return starsHTML;
}

// Function to handle booking a guide from profile modal
function bookGuide(guideId) {
    // Close the profile modal
    document.querySelector('.modal-overlay').remove();
    
    // Navigate to booking page with pre-selected guide
    window.location.href = `book.php?guide=${guideId}`;
}

// Hotel Booking Modal
function showBookingModal(hotelName) {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content hotel-booking-modal">
            <div class="modal-header">
                <h2>Book ${hotelName}</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Check-in Date *</label>
                    <input type="date" id="hotelCheckIn" required>
                </div>
                <div class="form-group">
                    <label>Check-out Date *</label>
                    <input type="date" id="hotelCheckOut" required>
                </div>
                <div class="form-group">
                    <label>Number of Guests *</label>
                    <select id="hotelGuests" required>
                        <option value="1">1 Guest</option>
                        <option value="2">2 Guests</option>
                        <option value="3">3 Guests</option>
                        <option value="4">4 Guests</option>
                        <option value="5+">5+ Guests (Contact hotel)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Room Type</label>
                    <select id="roomType">
                        <option value="standard">Standard Room</option>
                        <option value="deluxe">Deluxe Room</option>
                        <option value="family">Family Room</option>
                        <option value="suite">Suite</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Special Requests</label>
                    <textarea id="hotelRequests" rows="3" placeholder="Any special requirements..."></textarea>
                </div>
                <div class="form-group">
                    <label>Contact Information</label>
                    <input type="text" id="hotelContactName" placeholder="Your Name" required>
                    <input type="email" id="hotelContactEmail" placeholder="Email Address" required style="margin-top: 10px;">
                    <input type="tel" id="hotelContactPhone" placeholder="Phone Number" required style="margin-top: 10px;">
                </div>
                <button class="btn-submit" style="width: 100%; margin-top: 20px;" onclick="submitHotelBooking('${hotelName}')">
                    <span class="material-icons-outlined">hotel</span>
                    Submit Booking Request
                </button>
                <p style="text-align: center; margin-top: 15px; color: var(--text-secondary); font-size: 14px;">
                    You'll receive a confirmation email within 24 hours
                </p>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    
    // Set min dates
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    document.getElementById('hotelCheckIn').min = today.toISOString().split('T')[0];
    document.getElementById('hotelCheckIn').value = today.toISOString().split('T')[0];
    document.getElementById('hotelCheckOut').min = tomorrow.toISOString().split('T')[0];
    document.getElementById('hotelCheckOut').value = tomorrow.toISOString().split('T')[0];
    
    setTimeout(() => modal.classList.add('show'), 10);
}

function submitHotelBooking(hotelName) {
    const checkIn = document.getElementById('hotelCheckIn').value;
    const checkOut = document.getElementById('hotelCheckOut').value;
    const guests = document.getElementById('hotelGuests').value;
    const contactName = document.getElementById('hotelContactName').value;
    
    if (!checkIn || !checkOut || !contactName) {
        showNotification('Please fill in all required fields', 'error');
        return;
    }
    
    showNotification(`Booking request for ${hotelName} submitted!`, 'success');
    document.querySelector('.modal-overlay').remove();
    
    // Save to booking history
    const hotelBooking = {
        id: Date.now(),
        hotelName: hotelName,
        checkIn: checkIn,
        checkOut: checkOut,
        guests: guests,
        contactName: contactName,
        date: new Date().toISOString(),
        status: 'pending'
    };
    
    const hotelBookings = JSON.parse(localStorage.getItem('hotelBookings')) || [];
    hotelBookings.push(hotelBooking);
    localStorage.setItem('hotelBookings', JSON.stringify(hotelBookings));
}

// Hotel Filters Function
function filterHotels() {
    const type = document.getElementById('hotelTypeFilter').value;
    const nearby = document.getElementById('nearbySpotFilter').value;
    const price = document.getElementById('priceFilter').value;
    
    const cards = document.querySelectorAll('.travelry-card');
    
    cards.forEach(card => {
        const cardType = card.getAttribute('data-category');
        const cardNearby = card.getAttribute('data-nearby');
        const cardPrice = card.getAttribute('data-price');
        
        let show = true;
        
        if (type !== 'all' && type !== cardType) show = false;
        if (nearby !== 'all' && nearby !== cardNearby) show = false;
        if (price !== 'all' && price !== cardPrice) show = false;
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Initialize hotel filters
function initHotelFilters() {
    const filters = ['hotelTypeFilter', 'nearbySpotFilter', 'priceFilter'];
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', filterHotels);
        }
    });
}

// Add to your existing init function
function init() {
    // ... existing code ...
    
    // Initialize hotel filters when hotels page is shown
    if (window.location.hash === '#hotels' || document.getElementById('hotels')?.classList.contains('active')) {
        setTimeout(initHotelFilters, 100);
    }
}

// WEATHER FUNCTIONS
async function fetchWeatherData() {
    const url = "https://api.openweathermap.org/data/2.5/weather?q=San%20Jose%20Del%20Monte&appid=6c21a0d2aaf514cb8d21d56814312b19&units=metric";
    
    try {
        const response = await fetch(url);
        if (!response.ok) throw new Error(`API Error: ${response.status}`);
        return await response.json();
    } catch (error) {
        console.error("Weather fetch failed:", error);
        return null;
    }
}

function degToCompass(degrees) {
    const directions = ['N', 'NNE', 'NE', 'ENE', 'E', 'ESE', 'SE', 'SSE', 'S', 'SSW', 'SW', 'WSW', 'W', 'WNW', 'NW', 'NNW'];
    const index = Math.round(degrees / 22.5) % 16;
    return directions[index];
}

function getRainChance(conditions, humidity) {
    if (conditions.toLowerCase().includes('rain')) return 80;
    if (conditions.toLowerCase().includes('drizzle')) return 40;
    if (humidity > 80) return 30;
    if (humidity > 60) return 10;
    return 5;
}

function generateAITips(weatherData, rainChance) {
    const tips = [];
    const tempC = weatherData.main.temp;
    const conditions = weatherData.weather[0].description.toLowerCase();
    const humidity = weatherData.main.humidity;
    const windSpeed = weatherData.wind.speed;

    // Clothing advice
    if (tempC > 30) tips.push("Wear light, breathable clothing. Sunscreen is essential.");
    else if (tempC > 25) tips.push("Light clothing recommended. Hat for sun protection.");
    else if (tempC < 20) tips.push("Bring a light jacket or sweater.");

    // Rain advice
    if (rainChance > 50) tips.push("High chance of rain - bring an umbrella or raincoat.");
    else if (rainChance > 30) tips.push("Possible showers - consider carrying an umbrella.");

    // Hiking advice
    if (conditions.includes('rain') || rainChance > 60) {
        tips.push("⚠️ Not ideal for Mt. Balagbag hiking - trails may be slippery.");
    } else if (conditions.includes('clear') || conditions.includes('sun')) {
        tips.push("✓ Perfect weather for Mt. Balagbag hiking! Start early.");
    }

    // Waterfall advice
    if (rainChance > 40) {
        tips.push("⚠️ Use caution at Kaytitinga Falls - water flow may be stronger.");
    }

    // Wind warnings
    if (windSpeed > 20) {
        tips.push("💨 Strong winds - be careful on mountain viewpoints.");
    }

    // Humidity
    if (humidity > 80) {
        tips.push("💧 High humidity - stay hydrated during activities.");
    }

    // Thunderstorm alert
    if (weatherData.weather[0].main === "Thunderstorm") {
        tips.push("⚡ THUNDERSTORM ALERT: Avoid mountain hiking and open areas.");
    }

    return tips;
}

async function updateWeatherUI() {
    const weatherData = await fetchWeatherData();
    if (!weatherData) {
        console.log("Weather data unavailable");
        return;
    }

    // Update main header weather
    updateMainHeaderWeather(weatherData);
    
    // Update spots page weather
    updateSpotsPageWeather(weatherData);
    
    // Update booking page with weather info
    updateBookingWeatherInfo(weatherData);
}

function updateMainHeaderWeather(data) {
    const headerActions = document.querySelector('.header-actions');
    if (!headerActions) return;

    // Remove existing weather widget if present
    const existingWidget = document.querySelector('.weather-widget');
    if (existingWidget) existingWidget.remove();

    const tempC = data.main.temp.toFixed(1);
    const conditions = data.weather[0].main;
    const icon = getWeatherIcon(conditions);
    
    const weatherWidget = document.createElement('div');
    weatherWidget.className = 'weather-widget';
    weatherWidget.innerHTML = `
        <div class="weather-icon">${icon}</div>
        <div class="weather-info">
            <div class="weather-temp">${tempC}°C</div>
            <div class="weather-desc">${conditions}</div>
        </div>
    `;
    
    // Insert before profile dropdown
    const profileDropdown = headerActions.querySelector('.profile-dropdown');
    if (profileDropdown) {
        headerActions.insertBefore(weatherWidget, profileDropdown);
    } else {
        headerActions.appendChild(weatherWidget);
    }
}

function updateSpotsPageWeather(data) {
    const spotsPage = document.getElementById('spots');
    if (!spotsPage || !spotsPage.classList.contains('active')) return;

    const calendarHeader = spotsPage.querySelector('.calendar-header .weather-info');
    if (!calendarHeader) return;

    const tempC = data.main.temp.toFixed(1);
    const conditions = data.weather[0].description;
    const humidity = data.main.humidity;
    const windSpeed = data.wind.speed;
    const windDir = degToCompass(data.wind.deg);
    const rainChance = getRainChance(conditions, humidity);
    
    calendarHeader.innerHTML = `
        <span class="material-icons-outlined">${getWeatherIcon(data.weather[0].main)}</span>
        <span class="temperature">${tempC}°C</span>
        <div class="weather-details">
            <span class="weather-label">${conditions}</span>
            <div class="weather-stats">
                <span>💧 ${humidity}%</span>
                <span>💨 ${windSpeed}m/s ${windDir}</span>
                <span>🌧️ ${rainChance}%</span>
            </div>
        </div>
    `;
}

function updateBookingWeatherInfo(data) {
    const bookingPage = document.getElementById('booking');
    if (!bookingPage || !bookingPage.classList.contains('active')) return;

    // Add weather info to booking form
    const tourDetails = document.getElementById('step-1');
    if (tourDetails && tourDetails.classList.contains('active')) {
        const formContainer = tourDetails.querySelector('.form-container');
        if (formContainer && !formContainer.querySelector('.weather-alert')) {
            const weatherAlert = createWeatherAlert(data);
            formContainer.insertBefore(weatherAlert, formContainer.querySelector('.form-group'));
        }
    }
}

function createWeatherAlert(data) {
    const tempC = data.main.temp.toFixed(1);
    const conditions = data.weather[0].description;
    const humidity = data.main.humidity;
    const rainChance = getRainChance(conditions, humidity);
    const tips = generateAITips(data, rainChance);
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'weather-alert';
    alertDiv.innerHTML = `
        <div class="weather-alert-header">
            <span class="material-icons-outlined">info</span>
            <h4>Today's Weather: ${tempC}°C, ${conditions}</h4>
        </div>
        <div class="weather-alert-body">
            <p><strong>Tour Conditions:</strong> ${getTourConditions(conditions, rainChance)}</p>
            ${tips.length > 0 ? `
                <div class="weather-tips">
                    <strong>Recommendations:</strong>
                    <ul>
                        ${tips.map(tip => `<li>${tip}</li>`).join('')}
                    </ul>
                </div>
            ` : ''}
        </div>
    `;
    
    return alertDiv;
}

function getTourConditions(conditions, rainChance) {
    if (rainChance > 60) return "Poor - Consider rescheduling outdoor tours";
    if (rainChance > 30) return "Fair - Bring rain gear for outdoor activities";
    if (conditions.includes('clear') || conditions.includes('sun')) return "Excellent - Perfect for all tours";
    if (conditions.includes('cloud')) return "Good - Comfortable for most activities";
    return "Moderate - Check specific tour requirements";
}

function getWeatherIcon(condition) {
    const icons = {
        'Clear': 'wb_sunny',
        'Clouds': 'cloud',
        'Rain': 'umbrella',
        'Drizzle': 'grain',
        'Thunderstorm': 'flash_on',
        'Snow': 'ac_unit',
        'Mist': 'blur_on',
        'Fog': 'blur_on'
    };
    return icons[condition] || 'wb_sunny';
}

// Add CSS for weather components
function addWeatherStyles() {
    if (!document.querySelector('#weather-styles')) {
        const styleSheet = document.createElement('style');
        styleSheet.id = 'weather-styles';
        styleSheet.textContent = `
            /* Weather Widget */
            .weather-widget {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 8px 12px;
                background: var(--bg-light);
                border-radius: 20px;
                border: 1px solid var(--border);
                cursor: pointer;
                transition: all 0.2s;
            }

            .weather-widget:hover {
                background: var(--gray-100);
            }

            .weather-icon {
                font-size: 20px;
                color: var(--primary);
            }

            .weather-info {
                display: flex;
                flex-direction: column;
            }

            .weather-temp {
                font-weight: 600;
                font-size: 14px;
                color: var(--text-primary);
            }

            .weather-desc {
                font-size: 11px;
                color: var(--text-secondary);
                text-transform: capitalize;
            }

            /* Weather Details in Calendar */
            .weather-details {
                display: flex;
                flex-direction: column;
                gap: 4px;
            }

            .weather-stats {
                display: flex;
                gap: 8px;
                font-size: 12px;
                color: var(--text-secondary);
            }

            /* Weather Alert */
            .weather-alert {
                background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
                border-left: 4px solid var(--info);
                padding: 16px;
                border-radius: var(--radius-md);
                margin-bottom: 24px;
            }

            .weather-alert-header {
                display: flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 12px;
            }

            .weather-alert-header h4 {
                margin: 0;
                color: var(--text-primary);
                font-size: 15px;
            }

            .weather-alert-body {
                color: var(--text-secondary);
                font-size: 14px;
            }

            .weather-tips {
                margin-top: 12px;
                padding-top: 12px;
                border-top: 1px solid rgba(0,0,0,0.1);
            }

            .weather-tips ul {
                margin: 8px 0 0 0;
                padding-left: 20px;
            }

            .weather-tips li {
                margin-bottom: 4px;
                font-size: 13px;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .weather-widget {
                    padding: 6px 10px;
                }
                
                .weather-temp {
                    font-size: 13px;
                }
                
                .weather-desc {
                    font-size: 10px;
                }
                
                .weather-stats {
                    flex-direction: column;
                    gap: 2px;
                }
            }
        `;
        document.head.appendChild(styleSheet);
    }
}

// Initialize weather system
function initWeatherSystem() {
    addWeatherStyles();
    
    // Load weather on initial page load
    setTimeout(() => {
        updateWeatherUI();
    }, 1000);
    
    // Refresh weather every 15 minutes
    setInterval(updateWeatherUI, 15 * 60 * 1000);
    
    // Update weather when switching to spots page
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', function() {
            setTimeout(() => {
                if (document.getElementById('spots')?.classList.contains('active')) {
                    updateWeatherUI();
                }
            }, 300);
        });
    });
}

// Initialize
function init() {
    displayAllGuides();
    populateGuideSelect();
    setMinDate();
    initDatePickers();
    initBookingForm();
    initMobileSidebar();
    initSearch();
    initFilters();
    initProfileDropdown();
    updateUserInterface();
    checkNotifications();
    
    // Initialize weather system
    initWeatherSystem();
    
    // Initialize spots filters
    initSpotsFilters();
}

// Update User Interface
function updateUserInterface() {
    const currentUser = JSON.parse(localStorage.getItem('currentUser'));
    if (currentUser) {
        updateUserProfile(currentUser);
    }
}

function updateUserProfile(user) {
    document.querySelectorAll('.profile-avatar').forEach(avatar => {
        avatar.textContent = user.name.charAt(0).toUpperCase();
    });
    const profileName = document.querySelector('.profile-details h3');
    const profileEmail = document.querySelector('.profile-details p');
    if (profileName) profileName.textContent = user.name;
    if (profileEmail) profileEmail.textContent = user.email;
}

// Mobile Sidebar
function initMobileSidebar() {
    const menuToggle = document.createElement('button');
    menuToggle.className = 'mobile-menu-toggle';
    menuToggle.innerHTML = '<span class="material-icons-outlined">menu</span>';
    menuToggle.onclick = () => {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('active');
        document.body.classList.toggle('sidebar-open');
    };
    
    const header = document.querySelector('.main-header');
    if (header) {
        header.insertBefore(menuToggle, header.firstChild);
    }
    
    document.querySelectorAll('.nav-item').forEach(item => {
        item.addEventListener('click', () => {
            if (window.innerWidth < 1024) {
                document.querySelector('.sidebar').classList.remove('active');
                document.body.classList.remove('sidebar-open');
            }
        });
    });
}

// Search Functionality
function initSearch() {
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', debounce((e) => {
            searchQuery = e.target.value.toLowerCase();
            performSearch(searchQuery);
        }, 300));
        
        // Add search on Enter key
        searchInput.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchQuery = e.target.value.toLowerCase();
                performSearch(searchQuery);
            }
        });
    }
}

// Debounce function to limit search calls
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Main search function
function performSearch(query) {
    if (!query || query.trim() === '') {
        showHomePage();
        return;
    }
    
    const results = {
        guides: searchGuides(query),
        destinations: searchDestinations(query)
    };
    
    displaySearchResults(results, query);
}

// Search guides - Updated to work with database-driven content
function searchGuides(query) {
    // Since guides are now loaded from database via PHP, 
    // this function will work with DOM elements instead of array
    const guideCards = document.querySelectorAll('.guide-card');
    const results = [];
    
    guideCards.forEach(card => {
        const guideName = card.querySelector('.guide-name')?.textContent.toLowerCase() || '';
        const guideSpecialty = card.querySelector('.guide-specialty')?.textContent.toLowerCase() || '';
        const guideDescription = card.querySelector('.guide-description')?.textContent.toLowerCase() || '';
        
        if (guideName.includes(query) || 
            guideSpecialty.includes(query) || 
            guideDescription.includes(query)) {
            results.push({
                element: card,
                id: card.getAttribute('data-guide-id'),
                name: card.querySelector('.guide-name')?.textContent || '',
                specialty: card.querySelector('.guide-specialty')?.textContent || '',
                category: card.getAttribute('data-category')
            });
        }
    });
    
    return results;
}

// Search destinations from home page
function searchDestinations(query) {
    const destinations = [
        {
            name: "Mt. Balagbag",
            description: "Known as the 'Mt. Pulag of Bulacan,' this 777-meter peak offers stunning views of Metro Manila and surrounding mountains. Perfect for beginner hikers!",
            category: "nature",
            image: "https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Mt.+Balagbag"
        },
        {
            name: "Kaytitinga Falls",
            description: "A hidden gem with three-level cascading falls nestled in the forest. One hour trek through pristine nature awaits adventure seekers.",
            category: "nature",
            image: "https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Kaytitinga+Falls"
        },
        {
            name: "Grotto of Our Lady of Lourdes",
            description: "A spiritual sanctuary replica of the French basilica. Beautiful compound with meditation areas and breathtaking views from the second floor.",
            category: "religious",
            image: "https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Grotto+Lourdes"
        },
        {
            name: "Padre Pio Mountain of Healing",
            description: "Features a giant statue of St. Padre Pio on the hill. Open 24/7 for prayer, meditation, and peaceful reflection with panoramic city views.",
            category: "religious",
            image: "https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Padre+Pio"
        }
    ];
    
    return destinations.filter(dest => 
        dest.name.toLowerCase().includes(query) ||
        dest.description.toLowerCase().includes(query) ||
        dest.category.toLowerCase().includes(query)
    );
}

// Display search results
function displaySearchResults(results, query) {
    const contentArea = document.querySelector('.content-area');
    const pageTitle = document.getElementById('pageTitle');
    
    pageTitle.textContent = `Search Results for "${query}"`;
    
    let html = '<div class="search-results">';
    
    // Guides section
    if (results.guides.length > 0) {
        html += `
            <section class="search-section">
                <h2 class="section-title">Tour Guides (${results.guides.length})</h2>
                <div class="guides-grid">
                    ${results.guides.map(guide => createGuideCard(guide)).join('')}
                </div>
            </section>
        `;
    }
    
    // Destinations section
    if (results.destinations.length > 0) {
        html += `
            <section class="search-section">
                <h2 class="section-title">Destinations (${results.destinations.length})</h2>
                <div class="destinations-grid">
                    ${results.destinations.map(dest => createDestinationCard(dest)).join('')}
                </div>
            </section>
        `;
    }
    
    // No results
    if (results.guides.length === 0 && results.destinations.length === 0) {
        html += `
            <div class="no-results">
                <span class="material-icons-outlined">search_off</span>
                <h3>No results found for "${query}"</h3>
                <p>Try searching for guides or destinations</p>
                <button class="btn-hero" onclick="clearSearch()">Clear Search</button>
            </div>
        `;
    }
    
    html += '</div>';
    contentArea.innerHTML = html;
}

// Create destination card for search results
function createDestinationCard(destination) {
    return `
        <div class="destination-card" onclick="viewDestination('${destination.name}')">
            <div class="destination-img">
                <img src="${destination.image}" alt="${destination.name}">
            </div>
            <div class="destination-content">
                <h3>${destination.name}</h3>
                <p>${destination.description}</p>
                <span class="category-badge">${destination.category}</span>
            </div>
        </div>
    `;
}

// View destination details
function viewDestination(name) {
    // Navigate to tourist spots page with filter
    window.location.href = `tourist-spots.php?search=${encodeURIComponent(name)}`;
}

// Clear search and show home page
function clearSearch() {
    searchQuery = '';
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) searchInput.value = '';
    showHomePage();
}

// Show original home page
function showHomePage() {
    const contentArea = document.querySelector('.content-area');
    const pageTitle = document.getElementById('pageTitle');
    
    pageTitle.textContent = 'Home';
    
    // Restore original home page content
    contentArea.innerHTML = `
        <!-- HOME PAGE -->
        <div class="hero">
            <h1>Welcome to San Jose del Monte, Bulacan</h1>
            <p>The Balcony of Metropolis - Where Nature Meets Progress</p>
            <button class="btn-hero" onclick="window.location.href='user-guides.php'">Find Your Guide</button>
        </div>

        <h2 class="section-title">Featured Destinations</h2>
        <div class="destinations-grid">
            <div class="destination-card">
                <div class="destination-img">
                    <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Mt.+Balagbag" alt="Mt. Balagbag">
                </div>
                <div class="destination-content">
                    <h3>Mt. Balagbag</h3>
                    <p>Known as the "Mt. Pulag of Bulacan," this 777-meter peak offers stunning views of Metro Manila and surrounding mountains. Perfect for beginner hikers!</p>
                </div>
            </div>
            <div class="destination-card">
                <div class="destination-img">
                    <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Kaytitinga+Falls" alt="Kaytitinga Falls">
                </div>
                <div class="destination-content">
                    <h3>Kaytitinga Falls</h3>
                    <p>A hidden gem with three-level cascading falls nestled in the forest. One hour trek through pristine nature awaits adventure seekers.</p>
                </div>
            </div>
            <div class="destination-card">
                <div class="destination-img">
                    <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Grotto+Lourdes" alt="Grotto of Our Lady of Lourdes">
                </div>
                <div class="destination-content">
                    <h3>Grotto of Our Lady of Lourdes</h3>
                    <p>A spiritual sanctuary replica of the French basilica. Beautiful compound with meditation areas and breathtaking views from the second floor.</p>
                </div>
            </div>
            <div class="destination-card">
                <div class="destination-img">
                    <img src="https://via.placeholder.com/400x300/2c5f2d/ffffff?text=Padre+Pio" alt="Padre Pio Mountain of Healing">
                </div>
                <div class="destination-content">
                    <h3>Padre Pio Mountain of Healing</h3>
                    <p>Features a giant statue of St. Padre Pio on the hill. Open 24/7 for prayer, meditation, and peaceful reflection with panoramic city views.</p>
                </div>
            </div>
        </div>

        <h2 class="section-title">Why Visit San Jose del Monte?</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>10+</h3>
                <p>Natural Attractions</p>
            </div>
            <div class="stat-card">
                <h3>30 min</h3>
                <p>From Metro Manila</p>
            </div>
            <div class="stat-card">
                <h3>Year-round</h3>
                <p>Perfect Climate</p>
            </div>
        </div>
    `;
}

function filterAndDisplayGuides() {
    // Since guides are now loaded from database via PHP, work with DOM elements
    const guideCards = document.querySelectorAll('.guide-card');
    
    guideCards.forEach(card => {
        const guideCategory = card.getAttribute('data-category');
        const guideName = card.querySelector('.guide-name')?.textContent.toLowerCase() || '';
        const guideSpecialty = card.querySelector('.guide-specialty')?.textContent.toLowerCase() || '';
        const guideDescription = card.querySelector('.guide-description')?.textContent.toLowerCase() || '';
        
        // Check if card matches search query
        const matchesSearch = searchQuery === '' || 
            guideName.includes(searchQuery) ||
            guideSpecialty.includes(searchQuery) ||
            guideDescription.includes(searchQuery);
        
        // Check if card matches filter
        const matchesFilter = currentFilter === 'all' || guideCategory === currentFilter;
        
        // Show or hide card based on filters
        const shouldShow = matchesSearch && matchesFilter;
        card.style.display = shouldShow ? 'block' : 'none';
    });
}

// Sort displayed guides in DOM
function sortDisplayedGuides(sortBy) {
    const container = document.getElementById('guidesList');
    if (!container) return;
    
    const cards = Array.from(container.querySelectorAll('.guide-card'));
    
    cards.sort((a, b) => {
        switch(sortBy) {
            case 'rating':
                const ratingA = parseFloat(a.querySelector('.rating-value')?.textContent || '0');
                const ratingB = parseFloat(b.querySelector('.rating-value')?.textContent || '0');
                return ratingB - ratingA;
            case 'experience':
                const expA = parseInt(a.querySelector('.meta-item:nth-child(1)')?.textContent.match(/\d+/)?.[0] || '0');
                const expB = parseInt(b.querySelector('.meta-item:nth-child(1)')?.textContent.match(/\d+/)?.[0] || '0');
                return expB - expA;
            case 'price-low':
                const priceA = parseInt(a.querySelector('.price-tag')?.textContent.match(/₱(\d+)/)?.[1] || '0');
                const priceB = parseInt(b.querySelector('.price-tag')?.textContent.match(/₱(\d+)/)?.[1] || '0');
                return priceA - priceB;
            case 'price-high':
                const priceHighA = parseInt(a.querySelector('.price-tag')?.textContent.match(/₱(\d+)/)?.[1] || '0');
                const priceHighB = parseInt(b.querySelector('.price-tag')?.textContent.match(/₱(\d+)/)?.[1] || '0');
                return priceHighB - priceHighA;
            case 'reviews':
                const reviewsA = parseInt(a.querySelector('.review-count')?.textContent.match(/\d+/)?.[0] || '0');
                const reviewsB = parseInt(b.querySelector('.review-count')?.textContent.match(/\d+/)?.[0] || '0');
                return reviewsB - reviewsA;
            default:
                return 0;
        }
    });
    
    // Re-append sorted cards
    cards.forEach(card => container.appendChild(card));
}

function displayFilteredGuides(guidesList) {
    const container = document.getElementById('guidesList');
    if (!container) return;
    
    if (guidesList.length === 0) {
        container.innerHTML = `
            <div class="no-results">
                <span class="material-icons-outlined">search_off</span>
                <h3>No guides found</h3>
                <p>Try adjusting your search or filters</p>
                <button class="btn-hero" onclick="resetFilters()">Clear Filters</button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = guidesList.map(g => createGuideCard(g)).join('');
}

function resetFilters() {
    searchQuery = '';
    currentFilter = 'all';
    currentSort = 'rating';
    
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) searchInput.value = '';
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === 'all');
    });
    
    const sortSelect = document.getElementById('sortGuides');
    if (sortSelect) sortSelect.value = 'rating';
    
    filterAndDisplayGuides();
}

function createGuideCard(g) {
    const isFav = isFavorite(g.id);
    return `
        <div class="guide-card" onclick="viewProfile(${g.id})">
            <div class="guide-photo">
                <img src="${g.photo}" alt="${g.name}">
                <button class="favorite-btn ${isFav ? 'active' : ''}" onclick="event.stopPropagation(); toggleFavorite(${g.id})">
                    <span class="material-icons-outlined">${isFav ? 'favorite' : 'favorite_border'}</span>
                </button>
                ${g.verified ? '<span class="verified-badge"><span class="material-icons-outlined">verified</span></span>' : ''}
            </div>
            <div class="guide-info">
                <div class="guide-name">${g.name}</div>
                <span class="guide-specialty">${g.specialty}</span>
                <p class="guide-description">${g.description}</p>
                <div class="guide-meta">
                    <div class="meta-item">
                        <span class="material-icons-outlined">place</span>
                        <span>${g.areas.split(',')[0]}</span>
                    </div>
                    <div class="meta-item">
                        <span class="material-icons-outlined">language</span>
                        <span>${g.languages}</span>
                    </div>
                    <div class="meta-item">
                        <span class="material-icons-outlined">work</span>
                        <span>${g.experience}</span>
                    </div>
                    <div class="rating-display">
                        <span class="material-icons-outlined">star</span>
                        <span class="rating-value">${g.rating.toFixed(1)}</span>
                        <span class="review-count">(${g.reviewCount})</span>
                    </div>
                </div>
                <div class="guide-footer">
                    <button class="btn-view-profile">View Profile</button>
                </div>
            </div>
        </div>
    `;
}

function displayAllGuides() {
    filterAndDisplayGuides();
}

// Favorites System
function isFavorite(guideId) {
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    return favorites.includes(guideId);
}

function toggleFavorite(guideId) {
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const index = favorites.indexOf(guideId);
    
    if (index > -1) {
        favorites.splice(index, 1);
        showNotification('Removed from favorites', 'info');
    } else {
        favorites.push(guideId);
        showNotification('Added to favorites', 'success');
    }
    
    localStorage.setItem('favorites', JSON.stringify(favorites));
    
    const favBtn = event.currentTarget;
    const icon = favBtn.querySelector('.material-icons-outlined');
    if (icon) {
        icon.textContent = isFavorite(guideId) ? 'favorite' : 'favorite_border';
        favBtn.classList.toggle('active');
    }
    
    if (document.getElementById('saved-tours').classList.contains('active')) {
        displayFavorites();
    }
}

function displayFavorites() {
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const container = document.getElementById('savedToursList');
    
    if (favorites.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <span class="material-icons-outlined">favorite_border</span>
                <h3>No saved tours yet</h3>
                <p>Start adding your favorite tour guides!</p>
                <button class="btn-hero" onclick="window.location.href='user-guides.php'">Browse Tour Guides</button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = `
        <div class="favorites-loading">
            <p>Loading your favorite guides...</p>
            <p><small>This feature will be enhanced with database integration</small></p>
        </div>
    `;
}

// Notifications
function checkNotifications() {
    const notifications = JSON.parse(localStorage.getItem('notifications')) || [];
    const unreadCount = notifications.filter(n => !n.read).length;
    
    const badge = document.querySelector('.notification-badge');
    if (badge) {
        badge.textContent = unreadCount;
        badge.style.display = unreadCount > 0 ? 'flex' : 'none';
    }
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <span class="material-icons-outlined">
            ${type === 'success' ? 'check_circle' : type === 'error' ? 'error' : 'info'}
        </span>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function initProfileDropdown() {
    // Temporarily disabled to prevent conflicts with inline script
    // This function is now handled in index.php
    console.log('Profile dropdown initialization skipped - using inline script');
}

function handleLogout() {
    showLogoutModal();
}

function showLogoutModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content logout-modal">
            <div class="modal-header">
                <h2>Sign Out Confirmation</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="logout-message">
                    <span class="material-icons-outlined logout-icon">logout</span>
                    <p>Are you sure you want to sign out?</p>
                </div>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="this.closest('.modal-overlay').remove()">
                        Cancel
                    </button>
                    <button class="btn-confirm-logout" onclick="confirmLogout()">
                        <span class="material-icons-outlined">logout</span>
                        Sign Out
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function confirmLogout() {
    // Close the modal
    document.querySelector('.modal-overlay').remove();
    
    // Make a request to the server-side logout endpoint
    fetch('/coderistyarn2/sjdm-user/logout.php')
        .then(response => {
            if (response.ok) {
                // Clear local storage
                localStorage.removeItem('currentUser');
                
                // Show success message
                showNotification('Successfully signed out!', 'success');
                
                // Redirect to login page after a short delay
                setTimeout(() => {
                    window.location.href = '/coderistyarn2/log-in/log-in.php';
                }, 1000);
            } else {
                console.error('Logout failed:', response.statusText);
                showNotification('Logout failed, please try again', 'error');
            }
        })
        .catch(error => {
            console.error('Error during logout:', error);
            showNotification('Error during logout, please try again', 'error');
        });
}

// Profile Menu Modal Functions
function showMyAccountModal() {
    const currentUser = JSON.parse(localStorage.getItem('currentUser')) || { name: 'User', email: 'user@example.com' };
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content account-modal">
            <div class="modal-header">
                <h2>My Account</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="account-overview">
                    <div class="account-avatar">
                        <span class="material-icons-outlined">account_circle</span>
                    </div>
                    <div class="account-info">
                        <h3>${currentUser.name}</h3>
                        <p>${currentUser.email}</p>
                        <span class="member-badge">Member since 2024</span>
                    </div>
                </div>
                <div class="account-actions">
                    <button class="btn-primary" onclick="editProfile()">
                        <span class="material-icons-outlined">edit</span>
                        Edit Profile
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function showBookingHistoryModal() {
    const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content booking-history-modal">
            <div class="modal-header">
                <h2>Booking History</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="empty-state">
                    <span class="material-icons-outlined">event_busy</span>
                    <h3>Booking History</h3>
                    <p>Your booking history will appear here</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function showSavedToursModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content saved-tours-modal">
            <div class="modal-header">
                <h2>Saved Tours</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="empty-state">
                    <span class="material-icons-outlined">favorite_border</span>
                    <h3>Saved Tours</h3>
                    <p>Your favorite tours will appear here</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function showSettingsModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content settings-modal">
            <div class="modal-header">
                <h2>Settings</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="settings-section">
                    <h3>Settings</h3>
                    <p>Settings panel coming soon</p>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function showHelpSupportModal() {
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content help-support-modal">
            <div class="modal-header">
                <h2>Help & Support</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="help-sections">
                    <div class="help-section">
                        <h3>Contact Support</h3>
                        <p>support@sjdmtours.com</p>
                        <p>+63 2 1234 5678</p>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

// Booking History
function displayUserBookings() {
    const container = document.getElementById('bookingsList');
    if (!container) return;
    
    const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
    
    if (userBookings.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <span class="material-icons-outlined">event_busy</span>
                <h3>No bookings yet</h3>
                <p>Start exploring and book your first tour!</p>
                <button class="btn-hero" onclick="showPage('guides')">Browse Tour Guides</button>
            </div>
        `;
        return;
    }
    
    container.innerHTML = userBookings.reverse().map(booking => `
        <div class="booking-card">
            <div class="booking-header">
                <div>
                    <h3>${booking.guideName}</h3>
                    <p class="booking-destination">${booking.destination}</p>
                </div>
                <span class="status-badge status-${booking.status}">${booking.status.toUpperCase()}</span>
            </div>
            <div class="booking-details">
                <div class="detail-item">
                    <span class="material-icons-outlined">calendar_today</span>
                    <span>${formatDate(booking.checkIn)} - ${formatDate(booking.checkOut)}</span>
                </div>
                <div class="detail-item">
                    <span class="material-icons-outlined">people</span>
                    <span>${booking.guests} guest${booking.guests > 1 ? 's' : ''}</span>
                </div>
                <div class="detail-item">
                    <span class="material-icons-outlined">confirmation_number</span>
                    <span>${booking.bookingNumber}</span>
                </div>
                <div class="detail-item">
                    <span class="material-icons-outlined">payments</span>
                    <span>₱${booking.totalAmount ? booking.totalAmount.toLocaleString() : '2,600'}</span>
                </div>
            </div>
            <div class="booking-actions">
                ${booking.status === 'pending' ? `
                    <button class="btn-cancel" onclick="cancelBooking('${booking.bookingNumber}')">
                        <span class="material-icons-outlined">cancel</span>
                        Cancel Booking
                    </button>
                ` : ''}
                ${booking.status === 'completed' ? `
                    <button class="btn-review" onclick="showReviewModal(${booking.guideId})">
                        <span class="material-icons-outlined">rate_review</span>
                        Leave a Review
                    </button>
                ` : ''}
                <button class="btn-view" onclick="viewBookingDetails('${booking.bookingNumber}')">
                    <span class="material-icons-outlined">visibility</span>
                    View Details
                </button>
            </div>
        </div>
    `).join('');
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const options = { month: 'short', day: 'numeric', year: 'numeric' };
    return date.toLocaleDateString('en-US', options);
}

function cancelBooking(bookingNumber) {
    if (!confirm('Are you sure you want to cancel this booking?')) return;
    
    const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
    const index = bookings.findIndex(b => b.bookingNumber === bookingNumber);
    
    if (index > -1) {
        bookings[index].status = 'cancelled';
        localStorage.setItem('userBookings', JSON.stringify(bookings));
        showNotification('Booking cancelled successfully', 'info');
        displayUserBookings();
    }
}

function viewBookingDetails(bookingNumber) {
    const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
    const booking = bookings.find(b => b.bookingNumber === bookingNumber);
    
    if (!booking) return;
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content booking-detail-modal">
            <div class="modal-header">
                <h2>Booking Details</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="detail-section">
                    <h3>Tour Information</h3>
                    <div class="detail-grid">
                        <div><strong>Tour Guide:</strong> ${booking.guideName}</div>
                        <div><strong>Destination:</strong> ${booking.destination}</div>
                        <div><strong>Check-in:</strong> ${formatDate(booking.checkIn)}</div>
                        <div><strong>Check-out:</strong> ${formatDate(booking.checkOut)}</div>
                        <div><strong>Guests:</strong> ${booking.guests}</div>
                        <div><strong>Booking ID:</strong> ${booking.bookingNumber}</div>
                    </div>
                </div>
                <div class="detail-section">
                    <h3>Contact Information</h3>
                    <div class="detail-grid">
                        <div><strong>Name:</strong> ${booking.fullName}</div>
                        <div><strong>Email:</strong> ${booking.email}</div>
                        <div><strong>Phone:</strong> ${booking.contactNumber}</div>
                    </div>
                </div>
                ${booking.specialRequests ? `
                    <div class="detail-section">
                        <h3>Special Requests</h3>
                        <p>${booking.specialRequests}</p>
                    </div>
                ` : ''}
                <div class="detail-section">
                    <h3>Payment Summary</h3>
                    <div class="price-breakdown">
                        <div class="price-row">
                            <span>Guide Fee:</span>
                            <span>₱2,500.00</span>
                        </div>
                        <div class="price-row">
                            <span>Entrance Fees:</span>
                            <span>₱${100 * (booking.nights || 1)}.00</span>
                        </div>
                        <div class="price-row total">
                            <span>Total:</span>
                            <span>₱${booking.totalAmount || 2600}.00</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function showReviewModal(guideId) {
    const guide = guides.find(g => g.id === guideId);
    if (!guide) return;
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content review-modal">
            <div class="modal-header">
                <h2>Leave a Review for ${guide.name}</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="rating-input">
                    <label>Your Rating:</label>
                    <div class="star-rating">
                        ${[5,4,3,2,1].map(i => `
                            <input type="radio" name="rating" id="star${i}" value="${i}">
                            <label for="star${i}">★</label>
                        `).join('')}
                    </div>
                </div>
                <div class="form-group">
                    <label>Your Review:</label>
                    <textarea id="reviewText" rows="5" placeholder="Share your experience..."></textarea>
                </div>
                <button class="btn-submit" onclick="submitReview(${guideId})">Submit Review</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function submitReview(guideId) {
    const rating = document.querySelector('input[name="rating"]:checked');
    const reviewText = document.getElementById('reviewText');
    
    if (!rating) {
        showNotification('Please select a rating', 'error');
        return;
    }
    
    if (!reviewText.value.trim()) {
        showNotification('Please write a review', 'error');
        return;
    }
    
    const reviews = JSON.parse(localStorage.getItem('reviews')) || [];
    const currentUser = JSON.parse(localStorage.getItem('currentUser')) || { name: 'Anonymous' };
    
    const review = {
        id: Date.now(),
        guideId: guideId,
        userName: currentUser.name,
        rating: parseInt(rating.value),
        comment: reviewText.value.trim(),
        date: new Date().toISOString()
    };
    
    reviews.push(review);
    localStorage.setItem('reviews', JSON.stringify(reviews));
    
    showNotification('Review submitted successfully!', 'success');
    document.querySelector('.modal-overlay').remove();
    
    updateGuideRating(guideId);
}

function updateGuideRating(guideId) {
    const reviews = JSON.parse(localStorage.getItem('reviews')) || [];
    const guideReviews = reviews.filter(r => r.guideId === guideId);
    
    if (guideReviews.length > 0) {
        const avgRating = guideReviews.reduce((sum, r) => sum + r.rating, 0) / guideReviews.length;
        const guide = guides.find(g => g.id === guideId);
        if (guide) {
            guide.rating = parseFloat(avgRating.toFixed(1));
            guide.reviewCount = guideReviews.length;
        }
    }
}

// Date Pickers
function setMinDate() {
    const today = new Date().toISOString().split('T')[0];
    const preferredDate = document.getElementById('preferredDate');
    if (preferredDate) {
        preferredDate.setAttribute('min', today);
    }
}

function initDatePickers() {
    const today = new Date();
    const tomorrow = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    
    const checkInInput = document.getElementById('checkInDate');
    const checkOutInput = document.getElementById('checkOutDate');
    
    if (checkInInput && checkOutInput) {
        checkInInput.min = formatDateInput(today);
        checkOutInput.min = formatDateInput(tomorrow);
        checkInInput.value = formatDateInput(today);
        checkOutInput.value = formatDateInput(tomorrow);
        
        checkInInput.addEventListener('change', function() {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(checkInDate.getDate() + 1);
            checkOutInput.min = formatDateInput(nextDay);
            
            const currentCheckOut = new Date(checkOutInput.value);
            if (currentCheckOut < nextDay) {
                checkOutInput.value = formatDateInput(nextDay);
            }
        });
    }
}

function formatDateInput(date) {
    const d = new Date(date);
    let month = '' + (d.getMonth() + 1);
    let day = '' + d.getDate();
    const year = d.getFullYear();
    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;
    return [year, month, day].join('-');
}

function calculateNights(checkIn, checkOut) {
    const oneDay = 24 * 60 * 60 * 1000;
    const firstDate = new Date(checkIn);
    const secondDate = new Date(checkOut);
    return Math.round(Math.abs((secondDate - firstDate) / oneDay));
}

// Page Navigation
function showPage(pageId) {
    document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
    document.querySelectorAll('.nav-item').forEach(n => n.classList.remove('active'));
    
    const page = document.getElementById(pageId);
    if (page) {
        page.classList.add('active');
    }
    
    const navItems = document.querySelectorAll('.nav-item');
    navItems.forEach(item => {
        const itemText = item.textContent.toLowerCase().trim();
        if (itemText.includes(pageId.replace('-', ' '))) {
            item.classList.add('active');
        }
    });

    const pageTitles = {
        'home': 'Home',
        'guides': 'Tour Guides',
        'booking': 'Book Now',
        'spots': 'Tourist Spots',
        'culture': 'Local Culture',
        'tips': 'Travel Tips',
        'profile': 'Guide Profile',
        'booking-history': 'My Bookings',
        'saved-tours': 'Saved Tours',
        'my-account': 'My Account'
    };
    
    const titleEl = document.getElementById('pageTitle');
    if (titleEl) {
        titleEl.textContent = pageTitles[pageId] || 'SJDM Tourism';
    }

    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    if (pageId === 'booking-history') {
        displayUserBookings();
    } else if (pageId === 'saved-tours') {
        displayFavorites();
    } else if (pageId === 'guides') {
        displayAllGuides();
    } else if (pageId === 'spots') {
        setTimeout(() => {
            initSpotsFilters();
        }, 100);
    }
    
    // Update weather when switching to relevant pages
    if (pageId === 'spots' || pageId === 'booking') {
        setTimeout(updateWeatherUI, 300);
    }
}

// Guide Profile
function viewProfile(id) {
    currentGuideId = id;
    const guide = guides.find(g => g.id === id);
    if (!guide) return;
    
    const reviews = JSON.parse(localStorage.getItem('reviews')) || [];
    const guideReviews = reviews.filter(r => r.guideId === id).slice(-5).reverse();

    document.getElementById('profileContent').innerHTML = `
        <div class="profile-header">
            <div class="profile-photo-container">
                <div class="profile-photo">
                    <img src="${guide.photo}" alt="${guide.name}">
                </div>
                <button class="favorite-btn-large ${isFavorite(guide.id) ? 'active' : ''}" onclick="toggleFavorite(${guide.id})">
                    <span class="material-icons-outlined">${isFavorite(guide.id) ? 'favorite' : 'favorite_border'}</span>
                </button>
            </div>
            <div class="profile-details">
                <div class="profile-name-section">
                    <h2>${guide.name}</h2>
                    ${guide.verified ? '<span class="verified-badge-inline"><span class="material-icons-outlined">verified</span> Verified</span>' : ''}
                </div>
                <span class="profile-specialty">${guide.specialty}</span>
                <div class="profile-rating">
                    <span class="material-icons-outlined">star</span>
                    <span class="rating-number">${guide.rating.toFixed(1)}</span>
                    <span class="rating-reviews">(${guide.reviewCount} reviews)</span>
                    <span class="total-tours">${guide.totalTours} tours completed</span>
                </div>
                <p style="color: var(--text-secondary); line-height: 1.8; margin-top: 15px;">${guide.bio}</p>
            </div>
        </div>

        <div class="info-section">
            <h3>Service Information</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="material-icons-outlined">payments</span>
                    <div>
                        <strong>Price Range</strong>
                        <p>${guide.priceRange}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">place</span>
                    <div>
                        <strong>Service Areas</strong>
                        <p>${guide.areas}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">language</span>
                    <div>
                        <strong>Languages</strong>
                        <p>${guide.languages}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">groups</span>
                    <div>
                        <strong>Group Size</strong>
                        <p>${guide.groupSize}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>Availability & Contact</h3>
            <div class="info-grid">
                <div class="info-item">
                    <span class="material-icons-outlined">schedule</span>
                    <div>
                        <strong>Schedule</strong>
                        <p>${guide.schedules}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">phone</span>
                    <div>
                        <strong>Contact Number</strong>
                        <p>${guide.contact}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">email</span>
                    <div>
                        <strong>Email</strong>
                        <p>${guide.email}</p>
                    </div>
                </div>
                <div class="info-item">
                    <span class="material-icons-outlined">work</span>
                    <div>
                        <strong>Experience</strong>
                        <p>${guide.experience} in tourism</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="info-section">
            <h3>Recent Reviews</h3>
            <div class="reviews-container">
                ${guideReviews.length > 0 ? guideReviews.map(review => `
                    <div class="review-card">
                        <div class="review-header">
                            <div class="reviewer-info">
                                <div class="reviewer-avatar">${review.userName.charAt(0)}</div>
                                <div>
                                    <strong>${review.userName}</strong>
                                    <div class="review-date">${formatDate(review.date)}</div>
                                </div>
                            </div>
                            <div class="review-rating">
                                ${Array(review.rating).fill('<span class="material-icons-outlined">star</span>').join('')}
                            </div>
                        </div>
                        <p class="review-text">${review.comment}</p>
                    </div>
                `).join('') : '<p class="no-reviews">No reviews yet. Be the first to review!</p>'}
            </div>
        </div>

        <div style="margin-top: 30px; display: flex; gap: 12px;">
            <button class="btn-book" onclick="bookThisGuide(${guide.id})">
                <span class="material-icons-outlined">event</span>
                Book ${guide.name} Now
            </button>
            <button class="btn-contact" onclick="contactGuide(${guide.id})">
                <span class="material-icons-outlined">chat</span>
                Contact Guide
            </button>
        </div>
    `;

    showPage('profile');
}

function contactGuide(guideId) {
    const guide = guides.find(g => g.id === guideId);
    if (!guide) return;
    
    const modal = document.createElement('div');
    modal.className = 'modal-overlay';
    modal.innerHTML = `
        <div class="modal-content contact-modal">
            <div class="modal-header">
                <h2>Contact ${guide.name}</h2>
                <button class="close-modal" onclick="this.closest('.modal-overlay').remove()">
                    <span class="material-icons-outlined">close</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="contact-info">
                    <div class="contact-item">
                        <span class="material-icons-outlined">phone</span>
                        <div>
                            <strong>Phone</strong>
                            <p>${guide.contact}</p>
                        </div>
                    </div>
                    <div class="contact-item">
                        <span class="material-icons-outlined">email</span>
                        <div>
                            <strong>Email</strong>
                            <p>${guide.email}</p>
                        </div>
                    </div>
                </div>
                <div class="quick-message">
                    <h3>Send Quick Message</h3>
                    <textarea id="quickMessage" rows="4" placeholder="Type your message here..."></textarea>
                    <button class="btn-submit" onclick="sendMessage(${guideId})">
                        <span class="material-icons-outlined">send</span>
                        Send Message
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
    setTimeout(() => modal.classList.add('show'), 10);
}

function sendMessage(guideId) {
    const message = document.getElementById('quickMessage').value;
    if (!message.trim()) {
        showNotification('Please enter a message', 'error');
        return;
    }
    
    showNotification('Message sent successfully!', 'success');
    document.querySelector('.modal-overlay').remove();
}

function bookThisGuide(id) {
    const guideSelect = document.getElementById('selectedGuide');
    if (guideSelect) {
        guideSelect.value = id;
    }
    showPage('booking');
}

function populateGuideSelect() {
    const select = document.getElementById('selectedGuide');
    if (select) {
        select.innerHTML = '<option value="">-- Choose a Guide --</option>' +
            guides.map(g => `<option value="${g.id}">${g.name} - ${g.specialty}</option>`).join('');
    }
}

// Booking Progress
function updateProgress(step) {
    const progressSteps = document.querySelectorAll('.progress-step');
    if (!progressSteps.length) return;
    
    progressSteps.forEach((el, index) => {
        if (index + 1 < step) {
            el.classList.remove('active');
            el.classList.add('completed');
        } else if (index + 1 === step) {
            el.classList.add('active');
            el.classList.remove('completed');
        } else {
            el.classList.remove('active', 'completed');
        }
    });
    
    document.querySelectorAll('.booking-step').forEach(el => {
        el.classList.remove('active');
    });
    const currentStepEl = document.getElementById(`step-${step}`);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
    }
    
    const activeStep = document.querySelector('.booking-step.active');
    if (activeStep) {
        activeStep.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function nextStep(current) {
    if (current === 1 && !validateStep1()) return;
    if (current === 2 && !validateStep2()) return;
    if (current === 3 && !validateStep3()) return;
    
    currentStep = current + 1;
    
    // Update review summary when moving to step 3 (Review & Pay)
    if (currentStep === 3) {
        updateReviewSummary();
    }
    
    if (currentStep === 4) {
        updateConfirmationSummary();
    }
    updateProgress(currentStep);
}

function prevStep(current) {
    currentStep = current - 1;
    
    // Update review summary when going back to step 3 (Review & Pay)
    if (currentStep === 3) {
        updateReviewSummary();
    }
    
    updateProgress(currentStep);
}

function validateStep1() {
    const guide = document.getElementById('selectedGuide');
    const destination = document.getElementById('destination');
    const checkIn = document.getElementById('checkInDate');
    const checkOut = document.getElementById('checkOutDate');
    const guests = document.getElementById('guestCount');
    
    if (!guide || !guide.value) {
        showNotification('Please select a tour guide', 'error');
        return false;
    }
    if (!destination || !destination.value) {
        showNotification('Please select a destination', 'error');
        return false;
    }
    if (!checkIn || !checkIn.value || !checkOut || !checkOut.value) {
        showNotification('Please select both check-in and check-out dates', 'error');
        return false;
    }
    const nights = calculateNights(checkIn.value, checkOut.value);
    if (nights <= 0) {
        showNotification('Check-out date must be after check-in date', 'error');
        return false;
    }
    if (!guests || !guests.value || guests.value < 1) {
        showNotification('Please enter a valid number of guests', 'error');
        return false;
    }
    
    bookingData = {
        ...bookingData,
        guideId: guide.value,
        guideName: guide.options[guide.selectedIndex].text.split(' - ')[0],
        destination: destination.value,
        checkIn: checkIn.value,
        checkOut: checkOut.value,
        guests: guests.value,
        nights: nights
    };
    return true;
}

function validateStep2() {
    const fullName = document.getElementById('fullName');
    const email = document.getElementById('email');
    const contactNumber = document.getElementById('contactNumber');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!fullName || !fullName.value.trim()) {
        showNotification('Please enter your full name', 'error');
        return false;
    }
    if (!email || !email.value || !emailRegex.test(email.value)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    if (!contactNumber || !contactNumber.value || contactNumber.value.length < 10) {
        showNotification('Please enter a valid contact number', 'error');
        return false;
    }
    
    bookingData = {
        ...bookingData,
        fullName: fullName.value.trim(),
        email: email.value,
        contactNumber: contactNumber.value,
        specialRequests: document.getElementById('specialRequests') ? document.getElementById('specialRequests').value : ''
    };
    return true;
}

function validateStep3() {
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked');
    if (!paymentMethod) {
        showNotification('Please select a payment method', 'error');
        return false;
    }
    
    bookingData.payment = {
        method: paymentMethod.value
    };
    return true;
}

function updateReviewSummary() {
    if (!bookingData) return;
    
    // Get form values directly to ensure we have the latest data
    const guideSelect = document.getElementById('selectedGuide');
    const destinationSelect = document.getElementById('destination');
    const checkInInput = document.getElementById('checkInDate');
    const checkOutInput = document.getElementById('checkOutDate');
    const guestsInput = document.getElementById('guestCount');
    
    // Update bookingData with latest form values
    if (guideSelect && guideSelect.value) {
        bookingData.guideId = guideSelect.value;
        bookingData.guideName = guideSelect.options[guideSelect.selectedIndex].text.split(' - ')[0];
    }
    if (destinationSelect) bookingData.destination = destinationSelect.value;
    if (checkInInput) bookingData.checkIn = checkInInput.value;
    if (checkOutInput) bookingData.checkOut = checkOutInput.value;
    if (guestsInput) bookingData.guests = guestsInput.value;
    
    // Calculate nights
    const nights = calculateNights(bookingData.checkIn, bookingData.checkOut);
    bookingData.nights = nights;
    
    // Format dates
    const formattedCheckIn = new Date(bookingData.checkIn).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    const formattedCheckOut = new Date(bookingData.checkOut).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    
    // Update review section elements
    const reviewEls = {
        reviewGuideName: document.getElementById('reviewGuideName'),
        reviewDestination: document.getElementById('reviewDestination'),
        reviewCheckIn: document.getElementById('reviewCheckIn'),
        reviewCheckOut: document.getElementById('reviewCheckOut'),
        reviewGuests: document.getElementById('reviewGuests')
    };
    
    if (reviewEls.reviewGuideName) reviewEls.reviewGuideName.textContent = bookingData.guideName || '-';
    if (reviewEls.reviewDestination) reviewEls.reviewDestination.textContent = bookingData.destination || '-';
    if (reviewEls.reviewCheckIn) reviewEls.reviewCheckIn.textContent = formattedCheckIn;
    if (reviewEls.reviewCheckOut) reviewEls.reviewCheckOut.textContent = formattedCheckOut;
    if (reviewEls.reviewGuests) reviewEls.reviewGuests.textContent = bookingData.guests || '-';
    
    // Update pricing based on actual guest count
    const guestCount = parseInt(bookingData.guests) || 1;
    const guideFee = 2500;
    const entranceFees = 100 * guestCount;
    const serviceFee = 200;
    const total = guideFee + entranceFees + serviceFee;
    
    // Update price summary in review section
    const priceEls = {
        entranceFees: document.querySelector('.price-summary .price-row:nth-child(2) span:last-child'),
        total: document.querySelector('.price-summary .price-row.total span:last-child')
    };
    
    if (priceEls.entranceFees) priceEls.entranceFees.textContent = `₱${entranceFees.toLocaleString()}.00`;
    if (priceEls.total) priceEls.total.textContent = `₱${total.toLocaleString()}.00`;
    
    // Store pricing data for later use
    bookingData.pricing = {
        guideFee,
        entranceFees,
        serviceFee,
        total
    };
}

function updateConfirmationSummary() {
    if (!bookingData) return;
    
    const formattedCheckIn = new Date(bookingData.checkIn).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    const formattedCheckOut = new Date(bookingData.checkOut).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    
    const els = {
        guestName: document.getElementById('guestName'),
        tourGuideName: document.getElementById('tourGuideName'),
        tourDateRange: document.getElementById('tourDateRange'),
        guestContact: document.getElementById('guestContact'),
        summaryTourName: document.getElementById('summaryTourName'),
        summaryCheckIn: document.getElementById('summaryCheckIn'),
        summaryCheckOut: document.getElementById('summaryCheckOut'),
        summaryNights: document.getElementById('summaryNights'),
        confirmationEmail: document.getElementById('confirmationEmail'),
        bookingNumber: document.getElementById('bookingNumber')
    };
    
    if (els.guestName) els.guestName.textContent = bookingData.fullName || '';
    if (els.tourGuideName) els.tourGuideName.textContent = bookingData.guideName || '';
    if (els.tourDateRange) els.tourDateRange.textContent = `${formattedCheckIn} - ${formattedCheckOut}`;
    if (els.guestContact) els.guestContact.textContent = `${bookingData.contactNumber || ''} | ${bookingData.email || ''}`;
    if (els.summaryTourName) els.summaryTourName.textContent = bookingData.destination || '';
    if (els.summaryCheckIn) els.summaryCheckIn.textContent = formattedCheckIn.split(',')[0];
    if (els.summaryCheckOut) els.summaryCheckOut.textContent = formattedCheckOut.split(',')[0];
    if (els.summaryNights) {
        const nights = bookingData.nights || calculateNights(bookingData.checkIn, bookingData.checkOut);
        els.summaryNights.textContent = `${nights} ${nights === 1 ? 'Night' : 'Nights'}`;
    }
    if (els.confirmationEmail) els.confirmationEmail.textContent = bookingData.email || '';
    
    const bookingNumber = `SJDM-${Date.now().toString().slice(-6)}`;
    if (els.bookingNumber) els.bookingNumber.textContent = bookingNumber;
    
    const guideFee = 2500;
    const entranceFee = 100 * (bookingData.nights || 1);
    const serviceFee = 200;
    const total = guideFee + entranceFee + serviceFee;
    
    const feeEls = {
        guideFee: document.getElementById('summaryGuideFee'),
        entranceFees: document.getElementById('summaryEntranceFees'),
        total: document.getElementById('summaryTotal')
    };
    
    if (feeEls.guideFee) feeEls.guideFee.textContent = `₱${guideFee.toLocaleString()}.00`;
    if (feeEls.entranceFees) feeEls.entranceFees.textContent = `₱${entranceFee.toLocaleString()}.00`;
    if (feeEls.total) feeEls.total.textContent = `₱${total.toLocaleString()}.00`;
    
    bookingData.bookingNumber = bookingNumber;
    bookingData.totalAmount = total;
}

function initBookingForm() {
    document.querySelectorAll('.progress-step').forEach(step => {
        step.addEventListener('click', function() {
            const stepNumber = parseInt(this.getAttribute('data-step'));
            if (stepNumber < currentStep) {
                currentStep = stepNumber;
                updateProgress(currentStep);
            }
        });
    });
    
    const paymentRadios = document.querySelectorAll('input[name="paymentMethod"]');
    if (paymentRadios.length) {
        paymentRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const creditCardForm = document.getElementById('creditCardForm');
                if (creditCardForm) {
                    creditCardForm.style.display = this.value === 'credit_card' ? 'block' : 'none';
                }
            });
        });
        const creditCardForm = document.getElementById('creditCardForm');
        if (creditCardForm) creditCardForm.style.display = 'none';
    }
    updateProgress(1);
}

function updateConfirmationPage(bookingData, bookingReference) {
    // Update confirmation page elements with real booking data
    const els = {
        bookingNumber: document.getElementById('bookingNumber'),
        guestName: document.getElementById('guestName'),
        tourGuideName: document.getElementById('tourGuideName'),
        tourDateRange: document.getElementById('tourDateRange'),
        guestContact: document.getElementById('guestContact'),
        confirmationEmail: document.getElementById('confirmationEmail'),
        summaryTourName: document.getElementById('summaryTourName'),
        summaryCheckIn: document.getElementById('summaryCheckIn'),
        summaryCheckOut: document.getElementById('summaryCheckOut'),
        summaryNights: document.getElementById('summaryNights')
    };
    
    // Format dates
    const formattedCheckIn = new Date(bookingData.check_in_date).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    const formattedCheckOut = new Date(bookingData.check_out_date).toLocaleDateString('en-US', { 
        year: 'numeric', month: 'long', day: 'numeric', weekday: 'long'
    });
    
    // Calculate nights
    const nights = calculateNights(bookingData.check_in_date, bookingData.check_out_date);
    
    // Get guide name from select element
    const guideSelect = document.getElementById('selectedGuide');
    const guideName = guideSelect.options[guideSelect.selectedIndex].text.split(' - ')[0];
    
    // Update elements
    if (els.bookingNumber) els.bookingNumber.textContent = bookingReference;
    if (els.guestName) els.guestName.textContent = bookingData.full_name;
    if (els.tourGuideName) els.tourGuideName.textContent = guideName;
    if (els.tourDateRange) els.tourDateRange.textContent = `${formattedCheckIn} - ${formattedCheckOut}`;
    if (els.guestContact) els.guestContact.textContent = `${bookingData.contact_number} | ${bookingData.email}`;
    if (els.confirmationEmail) els.confirmationEmail.textContent = bookingData.email;
    if (els.summaryTourName) els.summaryTourName.textContent = bookingData.destination;
    if (els.summaryCheckIn) els.summaryCheckIn.textContent = formattedCheckIn.split(',')[0];
    if (els.summaryCheckOut) els.summaryCheckOut.textContent = formattedCheckOut.split(',')[0];
    if (els.summaryNights) els.summaryNights.textContent = `${nights} ${nights === 1 ? 'Night' : 'Nights'}`;
    
    // Calculate and update pricing
    const guideFee = 2500;
    const entranceFees = 100 * parseInt(bookingData.guest_count);
    const serviceFee = 200;
    const total = guideFee + entranceFees + serviceFee;
    
    const feeEls = {
        guideFee: document.getElementById('summaryGuideFee'),
        entranceFees: document.getElementById('summaryEntranceFees'),
        total: document.getElementById('summaryTotal')
    };
    
    if (feeEls.guideFee) feeEls.guideFee.textContent = `₱${guideFee.toLocaleString()}.00`;
    if (feeEls.entranceFees) feeEls.entranceFees.textContent = `₱${entranceFees.toLocaleString()}.00`;
    if (feeEls.total) feeEls.total.textContent = `₱${total.toLocaleString()}.00`;
}

function submitBooking() {
    if (!validateStep3()) return;
    
    // Show loading state
    const submitButton = document.querySelector('.btn-submit');
    const originalText = submitButton.innerHTML;
    submitButton.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Processing...';
    submitButton.disabled = true;
    
    // Collect all booking data
    const bookingData = {
        guide_id: document.getElementById('selectedGuide').value,
        destination: document.getElementById('destination').value,
        check_in_date: document.getElementById('checkInDate').value,
        check_out_date: document.getElementById('checkOutDate').value,
        guest_count: document.getElementById('guestCount').value,
        full_name: document.getElementById('fullName').value,
        email: document.getElementById('email').value,
        contact_number: document.getElementById('contactNumber').value,
        special_requests: document.getElementById('specialRequests').value,
        payment_method: document.querySelector('input[name="paymentMethod"]:checked').value
    };
    
    // Send booking to server
    fetch('process_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(bookingData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store booking reference for confirmation page
            window.bookingReference = data.booking_reference;
            window.bookingId = data.booking_id;
            
            // Update confirmation page with booking details
            updateConfirmationPage(bookingData, data.booking_reference);
            
            // Move to confirmation step
            currentStep = 4;
            updateProgress(currentStep);
            
            showNotification('Booking submitted successfully!', 'success');
        } else {
            throw new Error(data.message || 'Booking failed');
        }
    })
    .catch(error => {
        console.error('Booking error:', error);
        showNotification(error.message || 'Failed to submit booking. Please try again.', 'error');
        
        // Reset button state
        submitButton.innerHTML = originalText;
        submitButton.disabled = false;
    });
}

function filterSpots() {
    const category = document.getElementById('categoryFilter').value;
    const activity = document.getElementById('activityFilter').value;
    const duration = document.getElementById('durationFilter').value;
    
    const cards = document.querySelectorAll('.travelry-card');
    
    cards.forEach(card => {
        const cardCategory = card.getAttribute('data-category');
        const cardActivity = card.getAttribute('data-activity');
        const cardDuration = card.getAttribute('data-duration');
        
        let show = true;
        
        // Filter by category
        if (category !== 'all' && category !== cardCategory) {
            show = false;
        }
        
        // Filter by activity level
        if (activity !== 'all' && activity !== cardActivity) {
            show = false;
        }
        
        // Filter by duration
        if (duration !== 'all') {
            if (duration === '1-2' && !cardDuration.includes('1-2')) {
                show = false;
            } else if (duration === '2-4') {
                if (!cardDuration.includes('2-3') && !cardDuration.includes('3-4') && !cardDuration.includes('2-4') && !cardDuration.includes('3-5')) {
                    show = false;
                }
            } else if (duration === '4+') {
                if (!cardDuration.includes('4-5') && !cardDuration.includes('5-7') && !cardDuration.includes('4-6')) {
                    show = false;
                }
            }
        }
        
        card.style.display = show ? 'block' : 'none';
    });
    
    // Check if any cards are visible
    const visibleCards = Array.from(cards).filter(card => card.style.display !== 'none');
    const noResults = document.querySelector('.no-results-spots');
    
    if (visibleCards.length === 0) {
        if (!noResults) {
            const spotsGrid = document.getElementById('spotsGrid');
            const message = document.createElement('div');
            message.className = 'no-results-spots';
            message.innerHTML = `
                <div class="empty-state">
                    <span class="material-icons-outlined">search_off</span>
                    <h3>No destinations found</h3>
                    <p>Try adjusting your filters to find the perfect tour</p>
                    <button class="btn-hero" onclick="resetSpotFilters()">Reset Filters</button>
                </div>
            `;
            spotsGrid.appendChild(message);
        }
    } else if (noResults) {
        noResults.remove();
    }
}

function resetSpotFilters() {
    document.getElementById('categoryFilter').value = 'all';
    document.getElementById('activityFilter').value = 'all';
    document.getElementById('durationFilter').value = 'all';
    filterSpots();
}

// Initialize spots page filters
function initSpotsFilters() {
    const filters = ['categoryFilter', 'activityFilter', 'durationFilter'];
    filters.forEach(filterId => {
        const filter = document.getElementById(filterId);
        if (filter) {
            filter.addEventListener('change', filterSpots);
        }
    });
}

// Helper functions for profile modals
function editProfile() {
    showNotification('Profile editing coming soon!', 'info');
}

function changePassword() {
    showNotification('Password change coming soon!', 'info');
}

function viewAllBookings() {
    document.querySelector('.modal-overlay').remove();
    showNotification('Redirecting to full booking history...', 'info');
}

function removeFavorite(guideId) {
    toggleFavorite(guideId);
    showSavedToursModal(); // Refresh the modal
}

function viewAllFavorites() {
    document.querySelector('.modal-overlay').remove();
    showNotification('Redirecting to all favorites...', 'info');
}

function saveSettings() {
    showNotification('Settings saved successfully!', 'success');
    setTimeout(() => {
        document.querySelector('.modal-overlay').remove();
    }, 1000);
}

function contactSupport() {
    showNotification('Connecting to support chat...', 'info');
}

function viewFAQs() {
    showNotification('Opening FAQs...', 'info');
}

function emailSupport() {
    window.location.href = 'mailto:support@sjdmtours.com?subject=Help Request';
}

function viewFAQs() {
    showNotification('Opening FAQs...', 'info');
}




// Initialize on page load
window.addEventListener('DOMContentLoaded', init);