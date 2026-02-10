// Complete CRUD System with Image Handling

// Global State
let currentGuideId = null;
let currentStep = 1;
const totalSteps = 4;
let bookingData = {};
let currentFilter = 'all';
let currentSort = 'rating';
let searchQuery = '';

// Guide Profile Modal Logic handled via inline JS in PHP loop

// Function to handle booking a guide from profile modal
function bookGuide(guideId) {
    // Close the profile modal
    const modal = document.getElementById(`modal-guide-${guideId}`);
    if (modal) {
        modal.classList.remove('show');
    }

    // Simulate navigation to booking page
    window.location.href = `book.php?guide_id=${guideId}`;
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

    setTimeout(() => modal.classList.add('show'), 10);
}

function submitHotelBooking(hotelName) {
    const checkIn = document.getElementById('hotelCheckIn').value;
    const guests = document.getElementById('hotelGuests').value;
    const contactName = document.getElementById('hotelContactName').value;

    if (!checkIn || !contactName) {
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
    displayAllGuides();
    populateGuideSelect();
    setMinDate();
    initDatePickers();
    initBookingForm();
    initMobileSidebar();
    initSearch();
    initFilters();
    updateUserInterface();
    checkNotifications();

    // Initialize weather system
    initWeatherSystem();

    // Initialize spots filters
    initSpotsFilters();

    // Initialize booking form
    initBookingProgress();

    // Initialize file upload
    initFileUpload();
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    init();

});

// ========== BOOKING FUNCTIONS ==========

// Initialize booking progress
function initBookingProgress() {
    // Set initial step
    currentStep = 1;
    updateProgress(currentStep);

    // Add click handlers to progress steps
    document.querySelectorAll('.progress-step').forEach(step => {
        step.addEventListener('click', function () {
            const stepNumber = parseInt(this.getAttribute('data-step'));
            if (stepNumber < currentStep) {
                currentStep = stepNumber;
                updateProgress(currentStep);

                // Show the clicked step
                document.querySelectorAll('.booking-step').forEach(s => s.classList.remove('active'));
                document.getElementById(`step-${stepNumber}`).classList.add('active');
            }
        });
    });
}

// Update progress bar
function updateProgress(step) {
    const progressSteps = document.querySelectorAll('.progress-step');

    progressSteps.forEach((el, index) => {
        const stepNum = index + 1;
        if (stepNum < step) {
            el.classList.remove('active');
            el.classList.add('completed');
        } else if (stepNum === step) {
            el.classList.add('active');
            el.classList.remove('completed');
        } else {
            el.classList.remove('active', 'completed');
        }
    });
}

// Next step function
function nextStep() {
    const activeStep = document.querySelector('.booking-step.active');
    const stepNumber = parseInt(activeStep.id.replace('step-', ''));

    // Validate current step
    let isValid = true;
    switch (stepNumber) {
        case 1:
            isValid = validateStep1();
            break;
        case 2:
            isValid = validateStep2();
            break;
        case 3:
            isValid = validateStep3();
            break;
    }

    if (!isValid) return;

    // Move to next step
    currentStep = stepNumber + 1;

    // Update UI
    activeStep.classList.remove('active');
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgress(currentStep);

    // Update review if going to step 3
    if (currentStep === 3) {
        updateReviewSummary();
    }

    // Update confirmation if going to step 4
    if (currentStep === 4) {
        updateConfirmationDetails();
    }

    // Scroll to top of step
    setTimeout(() => {
        document.getElementById(`step-${currentStep}`).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

// Previous step function
function prevStep() {
    const activeStep = document.querySelector('.booking-step.active');
    const stepNumber = parseInt(activeStep.id.replace('step-', ''));

    // Move to previous step
    currentStep = stepNumber - 1;

    // Update UI
    activeStep.classList.remove('active');
    document.getElementById(`step-${currentStep}`).classList.add('active');
    updateProgress(currentStep);

    // Update review if going back to step 3
    if (currentStep === 3) {
        updateReviewSummary();
    }

    // Scroll to top of step
    setTimeout(() => {
        document.getElementById(`step-${currentStep}`).scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }, 100);
}

// Step 1 validation
function validateStep1() {
    const guide = document.getElementById('selectedGuide');
    const destination = document.getElementById('destination');
    const checkIn = document.getElementById('checkInDate');
    const guests = document.getElementById('guestCount');

    if (!guide.value) {
        showNotification('Please select a tour guide', 'error');
        return false;
    }
    if (!destination.value) {
        showNotification('Please select a destination', 'error');
        return false;
    }
    if (!checkIn.value) {
        showNotification('Please select a tour date', 'error');
        return false;
    }
    if (!guests.value || guests.value < 1) {
        showNotification('Please enter a valid number of guests', 'error');
        return false;
    }

    // Check if date is in the past
    const selectedDate = new Date(checkIn.value);
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    if (selectedDate < today) {
        showNotification('Please select a future date', 'error');
        return false;
    }

    return true;
}

// Step 2 validation
function validateStep2() {
    const fullName = document.getElementById('fullName');
    const email = document.getElementById('email');
    const contactNumber = document.getElementById('contactNumber');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!fullName.value.trim()) {
        showNotification('Please enter your full name', 'error');
        return false;
    }
    if (!email.value || !emailRegex.test(email.value)) {
        showNotification('Please enter a valid email address', 'error');
        return false;
    }
    if (!contactNumber.value || contactNumber.value.length < 10) {
        showNotification('Please enter a valid contact number', 'error');
        return false;
    }

    return true;
}

// Step 3 validation
function validateStep3() {
    const terms = document.getElementById('termsAgreement');
    const cancellation = document.getElementById('cancellationPolicy');

    if (!terms.checked) {
        showNotification('Please agree to the terms and conditions', 'error');
        return false;
    }
    if (!cancellation.checked) {
        showNotification('Please acknowledge the cancellation policy', 'error');
        return false;
    }

    return true;
}

// Payment method selection
function selectPayment(method) {
    const options = document.querySelectorAll('.payment-option');
    options.forEach(opt => opt.classList.remove('active'));

    const selected = document.querySelector(`[value="${method}"]`).closest('.payment-option');
    selected.classList.add('active');

    // Since we only have cash payment, no need to show/hide details
    // The payment notice is always visible
}

// Update review summary
function updateReviewSummary() {
    // Get form values
    const guideSelect = document.getElementById('selectedGuide');
    const destination = document.getElementById('destination').value;
    const date = document.getElementById('checkInDate').value;
    const guests = document.getElementById('guestCount').value;
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contactNumber').value;

    // Update display
    document.getElementById('reviewGuideName').textContent = guideSelect.options[guideSelect.selectedIndex].text.split(' - ')[0];
    document.getElementById('reviewDestination').textContent = destination;
    document.getElementById('reviewDate').textContent = formatDate(date);
    document.getElementById('reviewGuests').textContent = guests + ' guest' + (guests > 1 ? 's' : '');
    document.getElementById('reviewFullName').textContent = fullName;
    document.getElementById('reviewEmail').textContent = email;
    document.getElementById('reviewContact').textContent = contact;

    // Update prices
    const guideFee = 2500;
    const entrancePerPerson = 100;
    const entranceTotal = entrancePerPerson * guests;
    const serviceFee = 200;
    const platformFee = 100;
    const total = guideFee + entranceTotal + serviceFee + platformFee;

    document.getElementById('priceGuestCount').textContent = guests;
    document.getElementById('priceEntrance').textContent = '₱' + entranceTotal.toLocaleString() + '.00';
    document.getElementById('priceTotal').textContent = '₱' + total.toLocaleString() + '.00';
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Update confirmation details
function updateConfirmationDetails() {
    // Get form values
    const guideSelect = document.getElementById('selectedGuide');
    const destination = document.getElementById('destination').value;
    const date = document.getElementById('checkInDate').value;
    const guests = document.getElementById('guestCount').value;
    const fullName = document.getElementById('fullName').value;
    const email = document.getElementById('email').value;
    const contact = document.getElementById('contactNumber').value;
    const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;

    // Update confirmation page
    document.getElementById('detailDestination').textContent = destination;
    document.getElementById('detailTourDate').textContent = formatDate(date);
    document.getElementById('detailGuide').textContent = guideSelect.options[guideSelect.selectedIndex].text.split(' - ')[0];
    document.getElementById('detailGuests').textContent = guests + ' guest' + (guests > 1 ? 's' : '');
    document.getElementById('detailGuestName').textContent = fullName;
    document.getElementById('detailGuestEmail').textContent = email;
    document.getElementById('detailGuestContact').textContent = contact;
    document.getElementById('confirmationPaymentMethod').textContent = 'Pay on Arrival';

    // Update prices
    const guideFee = 2500;
    const entrancePerPerson = 100;
    const entranceTotal = entrancePerPerson * guests;
    const total = guideFee + entranceTotal + 300; // 300 service charges

    document.getElementById('confirmationGuestCount').textContent = guests;
    document.getElementById('confirmationEntrance').textContent = '₱' + entranceTotal.toLocaleString() + '.00';
    document.getElementById('confirmationTotal').textContent = '₱' + total.toLocaleString() + '.00';

    // Generate booking number
    const bookingNumber = 'SJDM-' + Date.now().toString().slice(-8);
    document.getElementById('confirmationBookingNumber').textContent = bookingNumber;
    document.getElementById('detailBookingId').textContent = bookingNumber;
}

// Submit booking
function submitBooking() {
    if (!validateStep3()) return;

    // Show loading
    const submitBtn = document.querySelector('.btn-confirm');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="material-icons-outlined">hourglass_empty</span> Processing...';
    submitBtn.disabled = true;

    // Collect all data
    const bookingData = {
        guide_id: document.getElementById('selectedGuide').value,
        destination: document.getElementById('destination').value,
        date: document.getElementById('checkInDate').value,
        guests: document.getElementById('guestCount').value,
        full_name: document.getElementById('fullName').value,
        email: document.getElementById('email').value,
        contact: document.getElementById('contactNumber').value,
        address: document.getElementById('address').value,
        nationality: document.getElementById('nationality').value,
        emergency_name: document.getElementById('emergencyName').value,
        emergency_contact: document.getElementById('emergencyContact').value,
        special_requests: document.getElementById('specialRequests').value,
        payment_method: document.querySelector('input[name="paymentMethod"]:checked').value,
        terms_agreed: true,
        cancellation_acknowledged: true,
        booking_date: new Date().toISOString()
    };

    // Send to server (simulate with setTimeout for demo)
    setTimeout(() => {
        // Store in localStorage for demo
        const bookings = JSON.parse(localStorage.getItem('userBookings')) || [];
        const bookingNumber = 'SJDM-' + Date.now().toString().slice(-8);

        const newBooking = {
            id: Date.now(),
            bookingNumber: bookingNumber,
            ...bookingData,
            status: 'pending', // Pending confirmation from tour guide
            dateBooked: new Date().toISOString(),
            start_time: '09:00:00',
            end_time: '17:00:00',
            total_amount: 2500 + (100 * bookingData.guests) + 300 // guide fee + entrance + service
        };

        bookings.push(newBooking);
        localStorage.setItem('userBookings', JSON.stringify(bookings));

        // Also save to tour guide bookings (separate storage for tour guide access)
        const guideBookings = JSON.parse(localStorage.getItem('tourGuideBookings')) || [];
        guideBookings.push(newBooking);
        localStorage.setItem('tourGuideBookings', JSON.stringify(guideBookings));

        // Move to confirmation step
        nextStep();

        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Show success notification
        showNotification('Booking submitted successfully!', 'success');
    }, 1500);
}

// File upload initialization
function initFileUpload() {
    const fileInput = document.getElementById('paymentProof');
    const fileName = document.getElementById('fileName');

    if (fileInput && fileName) {
        fileInput.addEventListener('change', function (e) {
            if (this.files.length > 0) {
                fileName.textContent = this.files[0].name;
            } else {
                fileName.textContent = 'No file chosen';
            }
        });
    }
}

// Confirmation page functions
function printConfirmation() {
    window.print();
}

function downloadTicket() {
    showNotification('E-Ticket download will be available soon', 'info');
}

function saveToCalendar() {
    const date = document.getElementById('checkInDate').value;
    const destination = document.getElementById('destination').value;

    // Create .ics file content
    const icsContent = `BEGIN:VCALENDAR
VERSION:2.0
BEGIN:VEVENT
SUMMARY:SJDM Tour - ${destination}
DTSTART:${date.replace(/-/g, '')}T070000Z
LOCATION:SJDM City Hall Parking Area
DESCRIPTION:Tour booking confirmation. Please arrive 15 minutes early.
END:VEVENT
END:VCALENDAR`;

    // Create download link
    const blob = new Blob([icsContent], { type: 'text/calendar' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `sjdmtour-${date}.ics`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    showNotification('Calendar event added successfully!', 'success');
}

function shareBooking() {
    const bookingNumber = document.getElementById('confirmationBookingNumber').textContent;
    const destination = document.getElementById('detailDestination').textContent;
    const date = document.getElementById('detailTourDate').textContent;

    const shareText = `I just booked a tour to ${destination} on ${date} with SJDM Tours! Booking Reference: ${bookingNumber}`;

    if (navigator.share) {
        navigator.share({
            title: 'My SJDM Tour Booking',
            text: shareText,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Booking details copied to clipboard!', 'success');
        });
    }
}

// ========== EXISTING FUNCTIONS (Keep all existing functions below) ==========

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

// Search guides
function searchGuides(query) {
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
    const guideCards = document.querySelectorAll('.guide-card');

    guideCards.forEach(card => {
        const guideCategory = card.getAttribute('data-category');
        const guideName = card.querySelector('.guide-name')?.textContent.toLowerCase() || '';
        const guideSpecialty = card.querySelector('.guide-specialty')?.textContent.toLowerCase() || '';
        const guideDescription = card.querySelector('.guide-description')?.textContent.toLowerCase() || '';

        const matchesSearch = searchQuery === '' ||
            guideName.includes(searchQuery) ||
            guideSpecialty.includes(searchQuery) ||
            guideDescription.includes(searchQuery);

        const matchesFilter = currentFilter === 'all' || guideCategory === currentFilter;

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
        switch (sortBy) {
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
    const existingNotification = document.querySelector('.notification-banner');
    if (existingNotification) {
        existingNotification.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification-banner ${type}`;

    const icons = {
        success: 'check_circle',
        error: 'error',
        warning: 'warning',
        info: 'info'
    };

    notification.innerHTML = `
        <span class="material-icons-outlined notification-icon">${icons[type] || 'info'}</span>
        <span class="notification-message">${message}</span>
        <button class="notification-close" onclick="this.parentElement.remove()">
            <span class="material-icons-outlined">close</span>
        </button>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 400);
    }, 3000);
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

    if (checkInInput) {
        checkInInput.min = formatDateInput(today);
        checkInInput.value = formatDateInput(today);
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

// Initialize booking form
function initBookingForm() {
    // Initialize currentStep based on active step
    const activeStep = document.querySelector('.booking-step.active');
    if (activeStep) {
        currentStep = parseInt(activeStep.id.replace('step-', ''));
    } else {
        currentStep = 1;
    }

    document.querySelectorAll('.progress-step').forEach(step => {
        step.addEventListener('click', function () {
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
            radio.addEventListener('change', function () {
                const creditCardForm = document.getElementById('creditCardForm');
                if (creditCardForm) {
                    creditCardForm.style.display = this.value === 'credit_card' ? 'block' : 'none';
                }
            });
        });
        const creditCardForm = document.getElementById('creditCardForm');
        if (creditCardForm) creditCardForm.style.display = 'none';
    }
    updateProgress(currentStep);
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

// ========== WEATHER FUNCTIONS ==========

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
        item.addEventListener('click', function () {
            setTimeout(() => {
                if (document.getElementById('spots')?.classList.contains('active')) {
                    updateWeatherUI();
                }
            }, 300);
        });
    });
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', init);