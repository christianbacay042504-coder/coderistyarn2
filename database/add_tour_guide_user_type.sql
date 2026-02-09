-- Add tour_guide user type to users table
-- Created: February 9, 2026

USE sjdm_tours;

-- Modify the user_type enum to include 'tour_guide'
ALTER TABLE users MODIFY COLUMN user_type ENUM('user', 'admin', 'tour_guide') DEFAULT 'user';

-- Create tour_guides table for additional tour guide information
CREATE TABLE IF NOT EXISTS tour_guides (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    license_number VARCHAR(50) UNIQUE,
    specialization TEXT,
    experience_years INT DEFAULT 0,
    languages TEXT,
    hourly_rate DECIMAL(10,2),
    availability_status ENUM('available', 'busy', 'offline') DEFAULT 'available',
    rating DECIMAL(3,2) DEFAULT 0.00,
    total_tours INT DEFAULT 0,
    bio TEXT,
    contact_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Create tour_guide_availability table for managing schedules
CREATE TABLE IF NOT EXISTS tour_guide_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_guide_id INT NOT NULL,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('available', 'booked', 'unavailable') DEFAULT 'available',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE CASCADE
);

-- Create tour_guide_reviews table
CREATE TABLE IF NOT EXISTS tour_guide_reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_guide_id INT NOT NULL,
    user_id INT NOT NULL,
    booking_id INT,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    review TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
    FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert sample tour guide data
INSERT INTO users (first_name, last_name, email, password, user_type, status) VALUES
('Juan', 'Santos', 'juan.santos@tourguide.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'tour_guide', 'active'),
('Maria', 'Reyes', 'maria.reyes@tourguide.com', '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS', 'tour_guide', 'active');

-- Insert corresponding tour guide details
INSERT INTO tour_guides (user_id, license_number, specialization, experience_years, languages, hourly_rate, contact_number, bio) VALUES
(11, 'TG-001-2026', 'Historical Tours, Nature Walks', 5, 'English, Filipino, Basic Japanese', 1500.00, '09123456789', 'Experienced tour guide specializing in the rich history and natural beauty of San Jose del Monte.'),
(12, 'TG-002-2026', 'Adventure Tours, Mountain Hiking', 3, 'English, Filipino', 1200.00, '09987654321', 'Adventure enthusiast with extensive knowledge of mountain trails and outdoor activities.');

-- Add indexes for better performance
CREATE INDEX idx_tour_guides_user_id ON tour_guides(user_id);
CREATE INDEX idx_tour_guide_availability_guide_id ON tour_guide_availability(tour_guide_id);
CREATE INDEX idx_tour_guide_availability_date ON tour_guide_availability(available_date);
CREATE INDEX idx_tour_guide_reviews_guide_id ON tour_guide_reviews(tour_guide_id);
