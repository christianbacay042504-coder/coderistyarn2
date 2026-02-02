// Complete CRUD System with Image Handling

// Tour Guide Data with Enhanced CRUD Operations
const guides = [
    {
        id: 1,
        name: "Rico Mendoza",
        photo: "https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=2070&auto=format&fit=crop",
        specialty: "Mt. Balagbag Hiking Expert",
        category: "mountain",
        description: "Certified mountain guide with 10 years of experience leading Mt. Balagbag expeditions. Safety-first approach with extensive knowledge of local trails.",
        bio: "Rico is a born and raised SJDM local who has been exploring Mt. Balagbag since childhood. As a certified mountaineer and wilderness first responder, he ensures safe and memorable hiking experiences. He's passionate about environmental conservation and educating visitors about the Sierra Madre ecosystem.",
        areas: "Mt. Balagbag, Tuntong Falls, Mountain trails",
        rating: 5.0,
        reviewCount: 127,
        priceRange: "₱2,000 - ₱3,500 per day",
        priceMin: 2000,
        priceMax: 3500,
        languages: "English, Tagalog",
        contact: "+63 917 123 4567",
        email: "rico.mendoza@sjdmguide.ph",
        schedules: "Available: Daily (4 AM - 12 PM)",
        experience: "10 years",
        experienceYears: 10,
        groupSize: "1-15 hikers",
        verified: true,
        totalTours: 450
    },
    {
        id: 2,
        name: "Anna Marie Santos",
        photo: "https://images.unsplash.com/photo-1438761681033-6461ffad8d80?q=80&w=2070&auto=format&fit=crop",
        specialty: "Nature & Waterfall Tours",
        category: "nature",
        description: "Expert nature guide specializing in Kaytitinga Falls and forest eco-tours. Passionate about sustainable tourism and local ecology.",
        bio: "Anna Marie is an environmental science graduate who turned her love for nature into a career. She specializes in eco-tourism and works closely with Dumagat communities to promote sustainable travel. Her tours are educational, fun, and environmentally responsible.",
        areas: "Kaytitinga Falls, Forest trails, Eco-tourism sites",
        rating: 4.9,
        reviewCount: 89,
        priceRange: "₱2,500 - ₱4,000 per day",
        priceMin: 2500,
        priceMax: 4000,
        languages: "English, Tagalog",
        contact: "+63 928 234 5678",
        email: "annamarie.santos@sjdmguide.ph",
        schedules: "Available: Wed-Sun (6 AM - 4 PM)",
        experience: "7 years",
        experienceYears: 7,
        groupSize: "1-12 people",
        verified: true,
        totalTours: 320
    },
    {
        id: 3,
        name: "Father Jose Reyes",
        photo: "https://images.unsplash.com/photo-1542103749-8ef59b94f47e?q=80&w=2070&auto=format&fit=crop",
        specialty: "Religious & Pilgrimage Tours",
        category: "religious",
        description: "Former parish coordinator offering spiritual tours to Grotto of Our Lady of Lourdes and Padre Pio shrine with historical insights.",
        bio: "Father Jose has served various parishes in SJDM for over 15 years. Now a licensed tour guide, he combines spiritual guidance with historical knowledge, making pilgrimage tours meaningful and educational. Perfect for church groups and faith-based travelers.",
        areas: "Grotto of Our Lady of Lourdes, Padre Pio Mountain, Churches",
        rating: 4.8,
        reviewCount: 156,
        priceRange: "₱1,500 - ₱2,500 per day",
        priceMin: 1500,
        priceMax: 2500,
        languages: "English, Tagalog, Spanish",
        contact: "+63 939 345 6789",
        email: "jose.reyes@sjdmguide.ph",
        schedules: "Available: Daily (8 AM - 5 PM)",
        experience: "15 years",
        experienceYears: 15,
        groupSize: "1-30 people",
        verified: true,
        totalTours: 580
    },
    {
        id: 4,
        name: "Michael Cruz",
        photo: "https://images.unsplash.com/photo-1500648767791-00dcc994a43e?q=80&w=1887&auto=format&fit=crop",
        specialty: "Adventure & Extreme Sports",
        category: "adventure",
        description: "Adrenaline enthusiast offering adventure packages including hiking, rappelling, and team building activities at Paradise Adventure Camp.",
        bio: "Michael is a certified adventure tour guide and team building facilitator. He specializes in creating thrilling outdoor experiences while maintaining the highest safety standards. Perfect for corporate groups, students, and adventure seekers.",
        areas: "Paradise Adventure Camp, Mt. Balagbag, Extreme trails",
        rating: 4.9,
        reviewCount: 94,
        priceRange: "₱3,000 - ₱5,000 per day",
        priceMin: 3000,
        priceMax: 5000,
        languages: "English, Tagalog",
        contact: "+63 950 456 7890",
        email: "michael.cruz@sjdmguide.ph",
        schedules: "Available: Fri-Sun (7 AM - 6 PM)",
        experience: "8 years",
        experienceYears: 8,
        groupSize: "5-25 people",
        verified: true,
        totalTours: 280
    },
    {
        id: 5,
        name: "Linda Bautista",
        photo: "https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=1887&auto=format&fit=crop",
        specialty: "Farm & Food Tours",
        category: "food",
        description: "Local farmer and culinary tour guide showcasing SJDM's agricultural heritage, orchid farms, and authentic Bulacan cuisine.",
        bio: "Linda grew up in a farming family and knows every farm, restaurant, and food spot in SJDM. Her farm-to-table tours include visits to orchid gardens, pineapple farms, and local eateries. She also teaches traditional cooking methods and shares recipes.",
        areas: "Orchid Garden, Pineapple Farms, Local restaurants, Markets",
        rating: 4.8,
        reviewCount: 72,
        priceRange: "₱2,000 - ₱3,500 per day",
        priceMin: 2000,
        priceMax: 3500,
        languages: "English, Tagalog",
        contact: "+63 961 567 8901",
        email: "linda.bautista@sjdmguide.ph",
        schedules: "Available: Tue-Sun (8 AM - 3 PM)",
        experience: "6 years",
        experienceYears: 6,
        groupSize: "1-10 people",
        verified: true,
        totalTours: 210
    },
    {
        id: 6,
        name: "Carlos Villanueva",
        photo: "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1887&auto=format&fit=crop",
        specialty: "City & Cultural Tours",
        category: "city",
        description: "Urban guide and local historian showcasing SJDM's transformation from rural town to modern city while preserving cultural heritage.",
        bio: "Carlos is a third-generation SJDM resident and local historian. He offers city tours that blend modern attractions with historical landmarks, sharing stories of the city's rapid development. He knows the best spots for photos, food, and shopping.",
        areas: "City proper, Malls, Historical sites, Urban attractions",
        rating: 4.7,
        reviewCount: 68,
        priceRange: "₱1,800 - ₱3,000 per day",
        priceMin: 1800,
        priceMax: 3000,
        languages: "English, Tagalog",
        contact: "+63 972 678 9012",
        email: "carlos.villanueva@sjdmguide.ph",
        schedules: "Available: Daily (9 AM - 7 PM)",
        experience: "5 years",
        experienceYears: 5,
        groupSize: "1-20 people",
        verified: true,
        totalTours: 195
    }
];

// Global State
let currentGuideId = null;
let currentStep = 1;
const totalSteps = 4;
let bookingData = {};
let currentFilter = 'all';
let currentSort = 'rating';
let searchQuery = '';

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
    
    document.addEventListener('click', (e) => {
        if (document.body.classList.contains('sidebar-open') && 
            !e.target.closest('.sidebar') && 
            !e.target.closest('.mobile-menu-toggle')) {
            document.querySelector('.sidebar').classList.remove('active');
            document.body.classList.remove('sidebar-open');
        }
    });
}

// Search Functionality
function initSearch() {
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            searchQuery = e.target.value.toLowerCase();
            filterAndDisplayGuides();
        });
    }
}

// Filter and Sort
function initFilters() {
    const guidesPage = document.getElementById('guides');
    if (!guidesPage) return;
    
    const filterSection = document.createElement('div');
    filterSection.className = 'filter-section';
    filterSection.innerHTML = `
        <div class="filter-container">
            <div class="filter-buttons">
                <button class="filter-btn active" data-filter="all">All Guides</button>
                <button class="filter-btn" data-filter="mountain">Mountain Hiking</button>
                <button class="filter-btn" data-filter="nature">Nature & Waterfall</button>
                <button class="filter-btn" data-filter="religious">Religious Tours</button>
                <button class="filter-btn" data-filter="adventure">Adventure</button>
                <button class="filter-btn" data-filter="food">Food Tours</button>
                <button class="filter-btn" data-filter="city">City Tours</button>
            </div>
            <div class="sort-section">
                <label>Sort by:</label>
                <select id="sortGuides">
                    <option value="rating">Highest Rating</option>
                    <option value="experience">Most Experience</option>
                    <option value="price-low">Price: Low to High</option>
                    <option value="price-high">Price: High to Low</option>
                    <option value="reviews">Most Reviews</option>
                </select>
            </div>
        </div>
    `;
    
    const title = guidesPage.querySelector('.section-title');
    if (title && title.nextSibling) {
        guidesPage.insertBefore(filterSection, title.nextSibling);
    }
    
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            currentFilter = this.dataset.filter;
            filterAndDisplayGuides();
        });
    });
    
    const sortSelect = document.getElementById('sortGuides');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            currentSort = this.value;
            filterAndDisplayGuides();
        });
    }
}

function filterAndDisplayGuides() {
    let filtered = guides.filter(guide => {
        const matchesSearch = searchQuery === '' || 
            guide.name.toLowerCase().includes(searchQuery) ||
            guide.specialty.toLowerCase().includes(searchQuery) ||
            guide.areas.toLowerCase().includes(searchQuery);
        
        const matchesFilter = currentFilter === 'all' || guide.category === currentFilter;
        
        return matchesSearch && matchesFilter;
    });
    
    filtered = sortGuides(filtered, currentSort);
    displayFilteredGuides(filtered);
}

function sortGuides(guidesList, sortBy) {
    const sorted = [...guidesList];
    
    switch(sortBy) {
        case 'rating':
            sorted.sort((a, b) => b.rating - a.rating);
            break;
        case 'experience':
            sorted.sort((a, b) => b.experienceYears - a.experienceYears);
            break;
        case 'price-low':
            sorted.sort((a, b) => a.priceMin - b.priceMin);
            break;
        case 'price-high':
            sorted.sort((a, b) => b.priceMax - a.priceMax);
            break;
        case 'reviews':
            sorted.sort((a, b) => b.reviewCount - a.reviewCount);
            break;
    }
    
    return sorted;
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
                    <span class="price-tag">${g.priceRange}</span>
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
                <button class="btn-hero" onclick="showPage('guides')">Browse Tour Guides</button>
            </div>
        `;
        return;
    }
    
    const favoriteGuides = guides.filter(g => favorites.includes(g.id));
    container.innerHTML = favoriteGuides.map(g => createGuideCard(g)).join('');
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

// Profile Dropdown
function initProfileDropdown() {
    const profileButton = document.getElementById('profileButton');
    const profileMenu = document.getElementById('profileMenu');

    if (profileButton && profileMenu) {
        profileButton.addEventListener('click', function(e) {
            e.stopPropagation();
            profileMenu.classList.toggle('active');
        });

        document.addEventListener('click', function(e) {
            if (!profileButton.contains(e.target) && !profileMenu.contains(e.target)) {
                profileMenu.classList.remove('active');
            }
        });

        const dropdownItems = profileMenu.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const text = this.textContent.trim();
                profileMenu.classList.remove('active');
                
                if (text === 'My Account') {
                    showPage('my-account');
                } else if (text === 'Booking History') {
                    showPage('booking-history');
                    displayUserBookings();
                } else if (text === 'Saved Tours') {
                    showPage('saved-tours');
                    displayFavorites();
                } else if (text === 'Sign Out') {
                    handleLogout();
                }
            });
        });
    }
}

function handleLogout() {
    if (confirm('Are you sure you want to sign out?')) {
        // Make a request to the server-side logout endpoint
        fetch('/coderistyarn2/sjdm-user/logout.php')
            .then(response => {
                if (response.ok) {
                    // Clear local storage
                    localStorage.removeItem('currentUser');
                    showNotification('Logged out successfully', 'info');
                    // Redirect to login page after a brief delay
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
    if (currentStep === 4) {
        updateConfirmationSummary();
    }
    updateProgress(currentStep);
}

function prevStep(current) {
    currentStep = current - 1;
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

function submitBooking() {
    if (!validateStep3()) return;
    
    bookingData.bookingDate = new Date().toISOString();
    bookingData.status = 'pending';
    
    const userBookings = JSON.parse(localStorage.getItem('userBookings')) || [];
    userBookings.push(bookingData);
    localStorage.setItem('userBookings', JSON.stringify(userBookings));
    
    currentStep = 4;
    updateProgress(currentStep);
    
    showNotification('Booking submitted successfully!', 'success');
}

// Filter functions for HTML version
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


//content for tourist and hotels cards


// Initialize on page load
window.addEventListener('DOMContentLoaded', init);