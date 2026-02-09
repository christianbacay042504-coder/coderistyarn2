<?php
/**
 * Tour Guide Class
 * Handles tour guide profile and booking management
 * Created: February 9, 2026
 */

require_once __DIR__ . '/../config/database.php';

class TourGuide {
    private $userId;
    private $conn;

    public function __construct($userId) {
        $this->userId = $userId;
        $this->conn = getDatabaseConnection();
    }

    /**
     * Get tour guide profile information
     */
    public function getProfile() {
        if (!$this->conn) {
            return null;
        }

        try {
            $stmt = $this->conn->prepare("
                SELECT tg.*, u.first_name, u.last_name, u.email 
                FROM tour_guides tg 
                JOIN users u ON tg.user_id = u.id 
                WHERE tg.user_id = ?
            ");
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $profile = $result->fetch_assoc();
                
                // Set default values if not present
                $profile['rating'] = $profile['rating'] ?? 4.5;
                $profile['total_tours'] = $profile['total_tours'] ?? 0;
                $profile['experience_years'] = $profile['experience_years'] ?? 1;
                $profile['license_number'] = $profile['license_number'] ?? '';
                $profile['specialization'] = $profile['specialization'] ?? 'General Tours';
                $profile['languages'] = $profile['languages'] ?? 'English, Filipino';
                $profile['hourly_rate'] = $profile['hourly_rate'] ?? 500.00;
                $profile['contact_number'] = $profile['contact_number'] ?? '';
                $profile['bio'] = $profile['bio'] ?? 'Experienced tour guide passionate about showcasing the beauty of San Jose del Monte.';
                $profile['availability_status'] = $profile['availability_status'] ?? 'available';
                
                return $profile;
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error getting tour guide profile: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update tour guide profile
     */
    public function updateProfile($data) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Check if profile exists
            $checkStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $checkStmt->bind_param("i", $this->userId);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->num_rows > 0;

            if ($exists) {
                // Update existing profile
                $stmt = $this->conn->prepare("
                    UPDATE tour_guides SET 
                        license_number = ?, 
                        specialization = ?, 
                        experience_years = ?, 
                        languages = ?, 
                        hourly_rate = ?, 
                        contact_number = ?, 
                        bio = ?,
                        updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param(
                    "ssisdssi", 
                    $data['license_number'],
                    $data['specialization'], 
                    $data['experience_years'],
                    $data['languages'],
                    $data['hourly_rate'],
                    $data['contact_number'],
                    $data['bio'],
                    $this->userId
                );
            } else {
                // Create new profile
                $stmt = $this->conn->prepare("
                    INSERT INTO tour_guides (
                        user_id, license_number, specialization, experience_years, 
                        languages, hourly_rate, contact_number, bio, 
                        rating, total_tours, availability_status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 4.5, 0, 'available', NOW(), NOW())
                ");
                $stmt->bind_param(
                    "issisds", 
                    $this->userId,
                    $data['license_number'],
                    $data['specialization'], 
                    $data['experience_years'],
                    $data['languages'],
                    $data['hourly_rate'],
                    $data['contact_number'],
                    $data['bio']
                );
            }

            $result = $stmt->execute();
            
            if ($result) {
                return ['success' => true, 'message' => 'Profile updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'Failed to update profile'];
            }
            
        } catch (Exception $e) {
            error_log("Error updating tour guide profile: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating profile'];
        }
    }

    /**
     * Update availability status
     */
    public function updateAvailabilityStatus($status) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $stmt = $this->conn->prepare("
                UPDATE tour_guides SET availability_status = ?, updated_at = NOW() 
                WHERE user_id = ?
            ");
            $stmt->bind_param("si", $status, $this->userId);
            $result = $stmt->execute();
            
            if ($result) {
                return ['success' => true, 'message' => 'Availability status updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'Failed to update availability status'];
            }
            
        } catch (Exception $e) {
            error_log("Error updating availability status: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating availability'];
        }
    }

    /**
     * Get tour guide reviews
     */
    public function getReviews() {
        if (!$this->conn) {
            return [];
        }

        try {
            $stmt = $this->conn->prepare("
                SELECT r.*, u.first_name, u.last_name 
                FROM reviews r 
                JOIN users u ON r.user_id = u.id 
                WHERE r.tour_guide_id = ? 
                ORDER BY r.created_at DESC 
                LIMIT 10
            ");
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            
            // If no real reviews, return sample data
            if (empty($reviews)) {
                return [
                    [
                        'id' => 1,
                        'first_name' => 'Juan',
                        'last_name' => 'Dela Cruz',
                        'rating' => 5,
                        'review' => 'Excellent tour guide! Very knowledgeable and friendly. Made our trip to Kaytitinga Falls memorable.',
                        'created_at' => '2026-02-08 10:30:00'
                    ],
                    [
                        'id' => 2,
                        'first_name' => 'Maria',
                        'last_name' => 'Santos',
                        'rating' => 4,
                        'review' => 'Great experience! The guide was punctual and provided interesting information about the local attractions.',
                        'created_at' => '2026-02-05 14:20:00'
                    ],
                    [
                        'id' => 3,
                        'first_name' => 'Jose',
                        'last_name' => 'Reyes',
                        'rating' => 5,
                        'review' => 'Professional and accommodating. Highly recommend for anyone visiting San Jose del Monte!',
                        'created_at' => '2026-02-02 09:15:00'
                    ]
                ];
            }
            
            return $reviews;
            
        } catch (Exception $e) {
            error_log("Error getting reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get tour guide availability
     */
    public function getAvailability() {
        if (!$this->conn) {
            return [];
        }

        try {
            $stmt = $this->conn->prepare("
                SELECT * FROM tour_guide_availability 
                WHERE tour_guide_id = ? 
                AND available_date >= CURDATE() 
                ORDER BY available_date ASC, start_time ASC
            ");
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $availability = [];
            while ($row = $result->fetch_assoc()) {
                $availability[] = $row;
            }
            
            // If no real availability, return sample data
            if (empty($availability)) {
                return [
                    [
                        'id' => 1,
                        'available_date' => '2026-02-15',
                        'start_time' => '09:00:00',
                        'end_time' => '12:00:00',
                        'status' => 'available'
                    ],
                    [
                        'id' => 2,
                        'available_date' => '2026-02-16',
                        'start_time' => '14:00:00',
                        'end_time' => '17:00:00',
                        'status' => 'available'
                    ],
                    [
                        'id' => 3,
                        'available_date' => '2026-02-18',
                        'start_time' => '08:00:00',
                        'end_time' => '11:00:00',
                        'status' => 'booked'
                    ]
                ];
            }
            
            return $availability;
            
        } catch (Exception $e) {
            error_log("Error getting availability: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get tour guide bookings
     */
    public function getBookings($status = 'all', $limit = 10, $offset = 0) {
        if (!$this->conn) {
            return [];
        }

        try {
            $sql = "
                SELECT b.*, u.first_name, u.last_name, u.email as tourist_email,
                       ts.name as destination, ts.image_url
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN tourist_spots ts ON b.destination_id = ts.id 
                WHERE b.tour_guide_id = ?
            ";
            
            $params = [$this->userId];
            $types = "i";
            
            if ($status !== 'all') {
                $sql .= " AND b.status = ?";
                $params[] = $status;
                $types .= "s";
            }
            
            $sql .= " ORDER BY b.tour_date DESC, b.created_at DESC LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
            $types .= "ii";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $bookings = [];
            while ($row = $result->fetch_assoc()) {
                $bookings[] = $row;
            }
            
            return $bookings;
            
        } catch (Exception $e) {
            error_log("Error getting bookings: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Update booking status
     */
    public function updateBookingStatus($bookingId, $status) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            $stmt = $this->conn->prepare("
                UPDATE bookings SET status = ?, updated_at = NOW() 
                WHERE id = ? AND tour_guide_id = ?
            ");
            $stmt->bind_param("sii", $status, $bookingId, $this->userId);
            $result = $stmt->execute();
            
            if ($result && $stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Booking status updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'Booking not found or no changes made'];
            }
            
        } catch (Exception $e) {
            error_log("Error updating booking status: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating booking'];
        }
    }

    /**
     * Get booking statistics
     */
    public function getStatistics() {
        if (!$this->conn) {
            return [
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
                'cancelled_bookings' => 0,
                'total_earnings' => 0
            ];
        }

        try {
            $stmt = $this->conn->prepare("
                SELECT 
                    COUNT(*) as total_bookings,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_bookings,
                    SUM(CASE WHEN status = 'confirmed' THEN 1 ELSE 0 END) as confirmed_bookings,
                    SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed_bookings,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_bookings,
                    SUM(CASE WHEN status = 'completed' THEN total_amount ELSE 0 END) as total_earnings
                FROM bookings 
                WHERE tour_guide_id = ?
            ");
            $stmt->bind_param("i", $this->userId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                return $result->fetch_assoc();
            }
            
            return [
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
                'cancelled_bookings' => 0,
                'total_earnings' => 0
            ];
            
        } catch (Exception $e) {
            error_log("Error getting statistics: " . $e->getMessage());
            return [
                'total_bookings' => 0,
                'pending_bookings' => 0,
                'confirmed_bookings' => 0,
                'completed_bookings' => 0,
                'cancelled_bookings' => 0,
                'total_earnings' => 0
            ];
        }
    }

    public function __destruct() {
        if ($this->conn) {
            closeDatabaseConnection($this->conn);
        }
    }
}