-- SJDM Tours Database Schema
-- Created: January 30, 2026

CREATE DATABASE IF NOT EXISTS sjdm_tours;
USE sjdm_tours;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_email (email),
    INDEX idx_user_type (user_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User
-- Email: adminlgu@gmail.com
-- Password: admin123 (hashed with password_hash)
INSERT INTO users (first_name, last_name, email, password, user_type, status) 
VALUES ('Admin', 'SJDM', 'adminlgu@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active');
-- Note: The password hash above is for 'admin123'

-- Bookings Table (for future use)
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tour_name VARCHAR(200) NOT NULL,
    booking_date DATE NOT NULL,
    number_of_people INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_booking_date (booking_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Login Activity Table (for analytics)
CREATE TABLE IF NOT EXISTS login_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_login_time (login_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Saved Tours Table
CREATE TABLE IF NOT EXISTS saved_tours (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    tour_name VARCHAR(200) NOT NULL,
    tour_description TEXT,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tourist Spots Table
CREATE TABLE IF NOT EXISTS tourist_spots (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('nature', 'historical', 'religious', 'farm', 'park', 'urban') NOT NULL,
    location VARCHAR(200),
    address TEXT,
    operating_hours VARCHAR(100),
    entrance_fee VARCHAR(100),
    difficulty_level ENUM('easy', 'moderate', 'difficult') DEFAULT 'moderate',
    duration VARCHAR(100),
    best_time_to_visit VARCHAR(100),
    activities TEXT,
    amenities TEXT,
    contact_info VARCHAR(200),
    website VARCHAR(200),
    image_url VARCHAR(500),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tour Guides Table
CREATE TABLE IF NOT EXISTS tour_guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    specialty VARCHAR(200),
    category ENUM('mountain', 'city', 'farm', 'waterfall', 'historical', 'general') NOT NULL,
    description TEXT,
    bio TEXT,
    areas_of_expertise TEXT,
    rating DECIMAL(3, 2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    price_range VARCHAR(100),
    price_min DECIMAL(10, 2),
    price_max DECIMAL(10, 2),
    languages VARCHAR(200),
    contact_number VARCHAR(20),
    email VARCHAR(100),
    schedules TEXT,
    experience_years INT,
    group_size VARCHAR(50),
    verified BOOLEAN DEFAULT FALSE,
    total_tours INT DEFAULT 0,
    photo_url VARCHAR(500),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_rating (rating),
    INDEX idx_verified (verified)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Hotels Table
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    category ENUM('luxury', 'mid-range', 'budget', 'event') NOT NULL,
    location VARCHAR(200),
    address TEXT,
    contact_info VARCHAR(200),
    website VARCHAR(200),
    email VARCHAR(100),
    phone VARCHAR(20),
    price_range VARCHAR(100),
    rating DECIMAL(3, 2) DEFAULT 0.00,
    review_count INT DEFAULT 0,
    amenities TEXT,
    services TEXT,
    image_url VARCHAR(500),
    latitude DECIMAL(10, 8),
    longitude DECIMAL(11, 8),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_category (category),
    INDEX idx_status (status),
    INDEX idx_rating (rating)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Sample Tourist Spots Data
INSERT INTO tourist_spots (name, description, category, location, address, operating_hours, entrance_fee, difficulty_level, duration, best_time_to_visit, activities, amenities, contact_info, image_url, rating, review_count) VALUES
('Mt. Balagbag', 'Popular hiking destination in San Jose del Monte with stunning views of the Sierra Madre mountains. Features challenging trails and rewarding summit experiences.', 'nature', 'San Jose del Monte', 'Barangay Kaytitinga, San Jose del Monte City', '4:00 AM - 6:00 PM', '₱50 per person', 'difficult', '4-6 hours', 'Early morning (4-6 AM)', 'Hiking, Photography, Nature observation', 'Rest areas, Basic toilets', 'Local guides available', 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop', 4.8, 245),
('Kaytitinga Falls', 'Beautiful waterfall with natural swimming pools and lush surroundings. Perfect for swimming and picnics.', 'nature', 'Kaytitinga', 'Barangay Kaytitinga, San Jose del Monte City', '6:00 AM - 5:00 PM', '₱30 per person', 'moderate', '3-5 hours', 'Morning to afternoon', 'Swimming, Picnicking, Photography', 'Changing areas, Picnic tables', 'Local guides available', 'https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop', 4.6, 189),
('Abes Farm', 'Organic farm offering educational tours, animal interactions, and fresh produce. Great for family visits.', 'farm', 'San Jose del Monte', 'Various locations in SJDM', '8:00 AM - 5:00 PM', '₱150 per person', 'easy', '2-3 hours', 'Weekends', 'Farm tour, Animal feeding, Organic farming education', 'Restaurant, Gift shop, Rest areas', 'Contact farm directly', 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop', 4.7, 156),
('City Oval (People\'s Park)', 'Central park in SJDM perfect for jogging, picnics, and family gatherings. Features playground and sports facilities.', 'park', 'City Proper', 'City Oval, San Jose del Monte City', '5:00 AM - 10:00 PM', 'Free', 'easy', '1-3 hours', 'Morning or evening', 'Jogging, Picnicking, Sports, Playground', 'Playground, Sports courts, Benches', 'City Parks Office', 'https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop', 4.3, 98),
('The Rising Heart Monument', 'Iconic heart-shaped monument symbolizing SJDM\'s growth and progress. Popular photo spot with night illumination.', 'urban', 'City Proper', 'Along Maharlika Highway, San Jose del Monte City', '24/7', 'Free', 'easy', '30 minutes - 1 hour', 'Evening for illumination', 'Photography, Sightseeing', 'Parking, Viewing areas', 'City Tourism Office', 'https://images.unsplash.com/photo-1518709268805-4e9042af2176?q=80&w=2068&auto=format&fit=crop', 4.5, 203),
('Our Lady of Lourdes Parish', 'Historic church with beautiful architecture and religious significance. Popular for pilgrimages and weddings.', 'religious', 'City Proper', 'Lourdes Subdivision, San Jose del Monte City', '5:00 AM - 7:00 PM', 'Free', 'easy', '1-2 hours', 'Weekends, Feast days', 'Religious services, Pilgrimage, Photography', 'Parking, Rest areas', 'Parish Office', 'https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop', 4.4, 134),
('Otso-Otso Falls', 'Secluded waterfall with crystal clear pools and natural rock formations. Requires some hiking to reach.', 'nature', 'San Jose del Monte', 'Barangay areas, San Jose del Monte City', '6:00 AM - 4:00 PM', '₱150 per person', 'moderate', '4-6 hours', 'Morning', 'Swimming, Hiking, Photography', 'Basic facilities at base', 'Local guides recommended', 'https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop', 4.7, 167);

-- Insert Sample Tour Guides Data
INSERT INTO tour_guides (name, specialty, category, description, bio, areas_of_expertise, rating, review_count, price_range, price_min, price_max, languages, contact_number, email, schedules, experience_years, group_size, verified, total_tours, photo_url) VALUES
('Rico Mendoza', 'Mt. Balagbag Hiking Expert', 'mountain', 'Certified mountain guide with 10 years of experience leading Mt. Balagbag expeditions. Safety-first approach with extensive knowledge of local trails.', 'Rico is a born and raised SJDM local who has been exploring Mt. Balagbag since childhood. As a certified mountaineer and wilderness first responder, he ensures safe and memorable hiking experiences. He\'s passionate about environmental conservation and educating visitors about the Sierra Madre ecosystem.', 'Mt. Balagbag, Tuntong Falls, Mountain trails', 5.0, 127, '₱2,000 - ₱3,500 per day', 2000, 3500, 'English, Tagalog', '+63 917 123 4567', 'rico.mendoza@sjdmguide.ph', 'Available: Daily (4 AM - 12 PM)', 10, '1-15 hikers', TRUE, 450, 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=2070&auto=format&fit=crop'),
('Maria Santos', 'City Tour Specialist', 'city', 'Knowledgeable local guide specializing in SJDM city tours and historical landmarks. Great storyteller with deep local connections.', 'Maria has been guiding tours in San Jose del Monte for 8 years. She loves sharing the rich history and culture of the city with visitors. Her tours are educational, entertaining, and personalized to each group\'s interests.', 'City proper, Malls, Historical sites, Urban attractions', 4.8, 89, '₱1,500 - ₱2,500 per day', 1500, 2500, 'English, Tagalog, Spanish', '+63 927 987 6543', 'maria.santos@sjdmguide.ph', 'Available: Weekdays (9 AM - 5 PM)', 8, '5-20 people', TRUE, 234, 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1888&auto=format&fit=crop'),
('Carlos Dela Cruz', 'Farm and Eco-Tourism Guide', 'farm', 'Expert in agricultural tours and eco-tourism experiences. Specializes in organic farming and sustainable practices.', 'Carlos comes from a family of farmers and has dedicated his career to promoting sustainable agriculture and eco-tourism. He makes learning about farming fun and engaging for visitors of all ages.', 'Abes Farm, Local farms, Agricultural sites, Eco-tourism locations', 4.9, 76, '₱1,800 - ₱3,000 per day', 1800, 3000, 'English, Tagalog', '+63 937 456 7890', 'carlos.delacruz@sjdmguide.ph', 'Available: Weekends (8 AM - 4 PM)', 6, '10-30 people', TRUE, 187, 'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1887&auto=format&fit=crop'),
('Ana Reyes', 'Waterfall Adventure Guide', 'waterfall', 'Specialized guide for waterfall tours and outdoor adventures. Focuses on safety and environmental awareness.', 'Ana is passionate about the natural beauty of SJDM\'s waterfalls. With 7 years of experience, she ensures visitors have safe and memorable experiences while learning about conservation.', 'Kaytitinga Falls, Otso-Otso Falls, Waterfall trails', 4.7, 65, '₱2,200 - ₱3,800 per day', 2200, 3800, 'English, Tagalog', '+63 947 234 5678', 'ana.reyes@sjdmguide.ph', 'Available: Weekends (6 AM - 3 PM)', 7, '8-20 people', TRUE, 156, 'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?q=80&w=2070&auto=format&fit=crop'),
('James Lim', 'Historical and Cultural Guide', 'historical', 'Expert in local history, cultural heritage, and religious sites. Provides in-depth historical context and stories.', 'James holds a degree in History and has been guiding cultural tours for 9 years. He brings the past to life through engaging storytelling and detailed historical knowledge.', 'Historical sites, Churches, Museums, Cultural landmarks', 4.6, 92, '₱1,600 - ₱2,800 per day', 1600, 2800, 'English, Tagalog, Chinese', '+63 957 890 1234', 'james.lim@sjdmguide.ph', 'Available: Daily (10 AM - 6 PM)', 9, '15-25 people', TRUE, 298, 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1887&auto=format&fit=crop');

-- Insert Sample Hotels Data
INSERT INTO hotels (name, description, category, location, address, contact_info, price_range, rating, review_count, amenities, image_url) VALUES
('SJDM Grand Hotel', 'Luxury hotel in the heart of San Jose del Monte with modern amenities and excellent service.', 'luxury', 'City Center', 'Maharlika Highway, San Jose del Monte City', '+63 2 1234 5678', '₱3,000 - ₱8,000 per night', 4.8, 156, 'Swimming pool, Restaurant, Spa, Gym, Free WiFi', 'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop'),
('SJDM City Inn', 'Comfortable mid-range hotel perfect for business and leisure travelers.', 'mid-range', 'City Proper', 'Along Maharlika Highway, San Jose del Monte City', '+63 2 9876 5432', '₱1,500 - ₱3,500 per night', 4.3, 89, 'Restaurant, Parking, 24/7 Front Desk, Free WiFi', 'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=2070&auto=format&fit=crop'),
('Budget Lodge SJDM', 'Affordable accommodation for budget-conscious travelers and backpackers.', 'budget', 'Near City Center', 'Various locations in SJDM', '+63 2 5555 1234', '₱800 - ₱1,800 per night', 3.9, 67, 'Basic rooms, Fan/AC, Shared bathroom, Common area', 'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=2070&auto=format&fit=crop'),
('SJDM Event Center', 'Perfect venue for conferences, weddings, and large gatherings with complete facilities.', 'event', 'City Proper', 'Maharlika Highway, San Jose del Monte City', '+63 2 1111 2222', '₱5,000 - ₱15,000 per event', 4.6, 45, 'Conference halls, Catering, Parking, Audio/Visual equipment', 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=2070&auto=format&fit=crop');
