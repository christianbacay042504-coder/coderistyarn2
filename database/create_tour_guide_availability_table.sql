-- Create tour_guide_availability table
-- This table stores availability slots for tour guides

CREATE TABLE IF NOT EXISTS tour_guide_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tour_guide_id INT NOT NULL,
    available_date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    status ENUM('available', 'booked', 'unavailable') DEFAULT 'available',
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tour_guide_id) REFERENCES tour_guides(id) ON DELETE CASCADE,
    INDEX idx_guide_date (tour_guide_id, available_date),
    INDEX idx_date_status (available_date, status),
    INDEX idx_guide_date_time (tour_guide_id, available_date, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert some sample availability data for testing
INSERT INTO tour_guide_availability (tour_guide_id, available_date, start_time, end_time, status) VALUES
(1, CURDATE() + INTERVAL 1 DAY, '08:00:00', '12:00:00', 'available'),
(1, CURDATE() + INTERVAL 1 DAY, '13:00:00', '17:00:00', 'available'),
(1, CURDATE() + INTERVAL 2 DAY, '09:00:00', '13:00:00', 'available'),
(1, CURDATE() + INTERVAL 2 DAY, '14:00:00', '18:00:00', 'limited'),
(1, CURDATE() + INTERVAL 3 DAY, '08:00:00', '12:00:00', 'booked'),
(1, CURDATE() + INTERVAL 3 DAY, '13:00:00', '17:00:00', 'available');
