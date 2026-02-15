// Complete CRUD System with Image Handling

function initBookingForm() {
    console.log('initBookingForm called');
    // Add event listeners for form validation
    const form = document.getElementById('tourDetailsForm');
    if (form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();
        });
    }
    // Initialize tour guide dropdown
    const guideSelect = document.getElementById('selectedGuide');
    if (guideSelect) {
        guideSelect.addEventListener('change', function() {
            console.log('Guide selected');
        });
    }
    // Initialize date inputs
    const dateInput = document.getElementById('checkInDate');
    if (dateInput) {
        dateInput.addEventListener('change', function() {
            console.log('Date selected');
        });
    }
    console.log('📋 Booking form initialized');
}

// Global State
let currentGuideId = null;
let currentStep = 1;
const totalSteps = 5;   
let bookingData = {};
let currentFilter = 'all';
let currentSort = 'rating';
let searchQuery = '';

// Guide Profile Modal Logic handled via inline JS in PHP loop

// Function to handle booking a guide from profile modal
function bookGuide(guideId) {
    // Close profile modal if open
    const modal = document.getElementById(`modal-guide-${guideId}`);
    if (modal) {
        modal.classList.remove('show');
        // Reset body overflow to prevent screen freezing
        document.body.style.overflow = 'auto';
        // Clear current modal reference
        if (typeof currentModal !== 'undefined' && currentModal === `modal-guide-${guideId}`) {
            currentModal = null;
        }
    }

    // Redirect to booking page with guide parameter
    setTimeout(() => {
        window.location.href = `book.php?guide=${guideId}`;
    }, 300);
}

// Function to handle guide selection in booking form
function selectGuide(guideId) {
    // Remove previous selection
    document.querySelectorAll('.guide-selection-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card
    const selectedCard = document.querySelector(`[data-guide-id="${guideId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    // Update hidden input
    const hiddenInput = document.getElementById('selectedGuide');
    if (hiddenInput) {
        hiddenInput.value = guideId;
    }
    
    console.log('Selected guide ID:', guideId);
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

// ========== BOOKING FUNCTIONS ==========

// Initialize booking form
function initBookingForm() {
    // Add event listeners for form validation
    const form = document.getElementById('tourDetailsForm');
    if (form) {
        form.addEventListener('submit', handleFormSubmit);
    }

    // Initialize tour guide dropdown
    const guideSelect = document.getElementById('selectedGuide');
    if (guideSelect) {
        guideSelect.addEventListener('change', handleGuideSelection);
    }

    // Initialize date inputs
    const dateInput = document.getElementById('checkInDate');
    if (dateInput) {
        dateInput.addEventListener('change', handleDateSelection);
    }

    console.log('📋 Booking form initialized');
}

// Add to your existing init function
function init() {
    displayAllGuides();
    setMinDate();
    initDatePickers();
    initMobileSidebar();
    initSearch();
    updateUserInterface();
    checkNotifications();

    // Initialize weather system
    initWeatherSystem();

    // Initialize spots filters
    if (typeof initSpotsFilters === 'function') {
        initSpotsFilters();
    } else {
        console.log('initSpotsFilters function not available, skipping...');
    }

    // Initialize booking form
    initBookingProgress();

    // Initialize file upload
    initFileUpload();

    // Initialize booking form last to ensure all dependencies are loaded
    if (typeof initBookingForm === 'function') {
        initBookingForm();
    }

    // Initialize filters if available
    if (typeof initFilters === 'function') {
        initFilters();
    } else {
        console.log('initFilters function not available, skipping...');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function () {
    init();

});

// Handle form submission
function handleFormSubmit(event) {
    event.preventDefault();
    // Form submission logic will be handled by existing submitBooking function
}

// Handle guide selection
function handleGuideSelection() {
    const guideSelect = document.getElementById('selectedGuide');
    const selectedOption = guideSelect.options[guideSelect.selectedIndex];
    
    if (selectedOption && selectedOption.value) {
        console.log('👤 Selected guide:', selectedOption.text);
    }
}

// Handle date selection
function handleDateSelection() {
    const dateInput = document.getElementById('checkInDate');
    if (dateInput && dateInput.value) {
        console.log('📅 Selected date:', dateInput.value);
    }
}

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
        case 4:
            isValid = validateStep4();
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

// Step 2 validation (Calendar Availability)
function validateStep2() {
    const fullName = document.getElementById('fullName');
    const email = document.getElementById('email');
    const contactNumber = document.getElementById('contactNumber');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!fullName || !email || !contactNumber) {
        showNotification('Personal info form is missing on this page', 'error');
        return false;
    }

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

// Step 3 validation (Personal Info)
function validateStep3() {
    // No validation needed here
    return true;
}

// Step 4 validation (Terms & Payment)
function validateStep4() {
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

    const radio = document.querySelector(`input[name="paymentMethod"][value="${method}"]`);
    if (!radio) {
        return;
    }
    radio.checked = true;

    const selected = radio.closest('.payment-option');
    if (!selected) {
        return;
    }
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

    // Generate booking number (only if not already set by server response)
    const bookingNumberEl = document.getElementById('confirmationBookingNumber');
    const detailBookingIdEl = document.getElementById('detailBookingId');
    const existingBookingNumber = bookingNumberEl ? bookingNumberEl.textContent.trim() : '';
    if (!existingBookingNumber) {
        const bookingNumber = 'SJDM-' + Date.now().toString().slice(-8);
        if (bookingNumberEl) bookingNumberEl.textContent = bookingNumber;
        if (detailBookingIdEl) detailBookingIdEl.textContent = bookingNumber;
    } else {
        if (detailBookingIdEl) detailBookingIdEl.textContent = existingBookingNumber;
    }
}

// Submit booking
function submitBooking() {
    if (!validateStep4()) return;

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

    // Send to server (DB) so it shows in admin/bookings.php
    fetch('submit_booking.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: new URLSearchParams(bookingData).toString()
    })
        .then(async (res) => {
            const text = await res.text();
            try {
                return JSON.parse(text);
            } catch (e) {
                throw new Error('Server returned an invalid response. Check submit_booking.php for PHP errors.');
            }
        })
        .then(data => {
            if (!data || !data.success) {
                throw new Error((data && data.message) ? data.message : 'Booking submission failed');
            }

            // Set server booking reference to confirmation UI
            const bookingNumberEl = document.getElementById('confirmationBookingNumber');
            const detailBookingIdEl = document.getElementById('detailBookingId');
            if (bookingNumberEl && data.booking_reference) bookingNumberEl.textContent = data.booking_reference;
            if (detailBookingIdEl && data.booking_reference) detailBookingIdEl.textContent = data.booking_reference;

            // Move to confirmation step
            nextStep();

            // Show enhanced success notification with email status
            let notificationMessage = 'Booking submitted successfully!';
            if (data.email_sent) {
                notificationMessage += ' 📧 Confirmation email sent to your inbox.';
            } else {
                notificationMessage += ' ⚠️ Email confirmation failed, but your booking is confirmed.';
            }
            showNotification(notificationMessage, data.email_sent ? 'success' : 'warning');
        })
        .catch(err => {
            console.error(err);
            showNotification(err.message || 'Booking submission failed', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
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
            if (typeof initSpotsFilters === 'function') {
                initSpotsFilters();
            }
        }, 100);
    }

    // Update weather when switching to relevant pages
}

// Guide Profile
function viewProfile(id) {
    currentGuideId = id;
    // Since we don't have a global guides array, redirect to user-guides.php
    window.location.href = 'user-guides.php';
}

function contactGuide(guideId) {
    // Since we don't have a global guides array, show a simple message
    showNotification('Contact feature available on guide details page', 'info');
    window.location.href = 'user-guides.php';
}

function sendMessage(guideId) {
    showNotification('Message sent successfully!', 'success');
}

function bookThisGuide(id) {
    const guideSelect = document.getElementById('selectedGuide');
    if (guideSelect) {
        guideSelect.value = id;
    }
}

// Initialize weather system
function initWeatherSystem() {
    // Load weather on initial page load
    if (typeof updateWeatherUI === 'function') {
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
}

// Initialize on page load
window.addEventListener('DOMContentLoaded', init);