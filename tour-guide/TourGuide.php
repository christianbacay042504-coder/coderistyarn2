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
                $profile['person_name'] = $profile['name'] ?? $profile['license_number'] ?? '';
                $profile['specialization'] = $profile['specialty'] ?? 'General Tours';
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
                        name = ?, 
                        specialty = ?, 
                        experience_years = ?, 
                        languages = ?, 
                        contact_number = ?, 
                        bio = ?,
                        updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->bind_param(
                    "ssisssi", 
                    $data['person_name'],
                    $data['specialization'], 
                    $data['experience_years'],
                    $data['languages'],
                    $data['contact_number'],
                    $data['bio'],
                    $this->userId
                );
            } else {
                // Create new profile
                $stmt = $this->conn->prepare("
                    INSERT INTO tour_guides (
                        user_id, name, specialty, experience_years, 
                        languages, contact_number, bio, 
                        rating, total_tours, availability_status, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                $stmt->bind_param(
                    "issisdsii", 
                    $this->userId,
                    $data['person_name'],
                    $data['specialization'], 
                    $data['experience_years'],
                    $data['languages'],
                    $data['contact_number'],
                    $data['bio'],
                    4.5,
                    0,
                    'available'
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
            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return [];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];

            $stmt = $this->conn->prepare("
                SELECT gr.*, u.first_name, u.last_name, b.tour_name, b.destination 
                FROM guide_reviews gr 
                JOIN users u ON gr.user_id = u.id 
                LEFT JOIN bookings b ON gr.booking_id = b.id
                WHERE gr.guide_id = ? 
                ORDER BY gr.created_at DESC 
                LIMIT 20
            ");
            $stmt->bind_param("i", $tourGuideId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $reviews = [];
            while ($row = $result->fetch_assoc()) {
                $reviews[] = $row;
            }
            
            return $reviews;
            
        } catch (Exception $e) {
            error_log("Error getting reviews: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Calculate average rating from guide_reviews
     */
    public function getAverageRating() {
        if (!$this->conn) {
            return 0;
        }

        try {
            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return 0;
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];

            $stmt = $this->conn->prepare("
                SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                FROM guide_reviews 
                WHERE guide_id = ?
            ");
            $stmt->bind_param("i", $tourGuideId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $data = $result->fetch_assoc();
                return $data['avg_rating'] ? round($data['avg_rating'], 1) : 0;
            }
            
            return 0;
            
        } catch (Exception $e) {
            error_log("Error calculating average rating: " . $e->getMessage());
            return 0;
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
            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return [];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];
            
            $stmt = $this->conn->prepare("
                SELECT * FROM tour_guide_availability 
                WHERE tour_guide_id = ? 
                AND available_date >= CURDATE() 
                ORDER BY available_date ASC, start_time ASC
            ");
            $stmt->bind_param("i", $tourGuideId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $availability = [];
            while ($row = $result->fetch_assoc()) {
                $availability[] = $row;
            }
            
            return $availability;
            
        } catch (Exception $e) {
            error_log("Error getting availability: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Add availability slot
     */
    public function addAvailability($date, $startTime, $endTime, $status = 'available') {
        if (!$this->conn) {
            error_log("Database connection failed in addAvailability");
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // Validate inputs
            if (empty($date) || empty($startTime) || empty($endTime)) {
                error_log("Invalid inputs: date=$date, startTime=$startTime, endTime=$endTime");
                return ['success' => false, 'message' => 'All fields are required'];
            }

            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            if (!$guideIdStmt) {
                error_log("Failed to prepare guide ID query: " . $this->conn->error);
                return ['success' => false, 'message' => 'Database query preparation failed'];
            }
            
            $guideIdStmt->bind_param("i", $this->userId);
            if (!$guideIdStmt->execute()) {
                error_log("Failed to execute guide ID query: " . $guideIdStmt->error);
                return ['success' => false, 'message' => 'Failed to find tour guide profile'];
            }
            
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                error_log("No tour guide found for user_id: $this->userId");
                return ['success' => false, 'message' => 'Tour guide profile not found'];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];
            error_log("Found tour guide ID: $tourGuideId for user: $this->userId");
            
            // Check if availability already exists for this date and time
            $checkStmt = $this->conn->prepare("
                SELECT id FROM tour_guide_availability 
                WHERE tour_guide_id = ? AND available_date = ? AND start_time = ? AND end_time = ?
            ");
            if (!$checkStmt) {
                error_log("Failed to prepare check query: " . $this->conn->error);
                return ['success' => false, 'message' => 'Database query preparation failed'];
            }
            
            $checkStmt->bind_param("isss", $tourGuideId, $date, $startTime, $endTime);
            if (!$checkStmt->execute()) {
                error_log("Failed to execute check query: " . $checkStmt->error);
                return ['success' => false, 'message' => 'Failed to check existing availability'];
            }
            
            if ($checkStmt->get_result()->num_rows > 0) {
                error_log("Availability already exists for guide: $tourGuideId, date: $date, time: $startTime-$endTime");
                return ['success' => false, 'message' => 'Availability already exists for this time slot'];
            }
            
            // Insert new availability
            $stmt = $this->conn->prepare("
                INSERT INTO tour_guide_availability 
                (tour_guide_id, available_date, start_time, end_time, status, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            if (!$stmt) {
                error_log("Failed to prepare insert query: " . $this->conn->error);
                return ['success' => false, 'message' => 'Database query preparation failed'];
            }
            
            $stmt->bind_param("issss", $tourGuideId, $date, $startTime, $endTime, $status);
            
            error_log("Attempting to insert: guide_id=$tourGuideId, date=$date, start=$startTime, end=$endTime, status=$status");
            
            $result = $stmt->execute();
            
            if ($result) {
                $insertedId = $this->conn->insert_id;
                error_log("Successfully inserted availability with ID: $insertedId");
                return ['success' => true, 'message' => 'Availability added successfully!'];
            } else {
                error_log("Failed to insert availability: " . $stmt->error);
                return ['success' => false, 'message' => 'Failed to add availability: ' . $stmt->error];
            }
            
        } catch (Exception $e) {
            error_log("Exception in addAvailability: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while adding availability: ' . $e->getMessage()];
        }
    }

    /**
     * Update availability slot
     */
    public function updateAvailabilitySlot($availabilityId, $status) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // First get the tour guide ID to verify ownership
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return ['success' => false, 'message' => 'Tour guide profile not found'];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];
            
            // Update availability
            $stmt = $this->conn->prepare("
                UPDATE tour_guide_availability 
                SET status = ?, updated_at = NOW() 
                WHERE id = ? AND tour_guide_id = ?
            ");
            $stmt->bind_param("sii", $status, $availabilityId, $tourGuideId);
            $result = $stmt->execute();
            
            if ($result && $stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Availability updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'Availability not found or no changes made'];
            }
            
        } catch (Exception $e) {
            error_log("Error updating availability: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while updating availability'];
        }
    }

    /**
     * Delete availability slot
     */
    public function deleteAvailability($availabilityId) {
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Database connection failed'];
        }

        try {
            // First get the tour guide ID to verify ownership
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return ['success' => false, 'message' => 'Tour guide profile not found'];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];
            
            // Delete availability
            $stmt = $this->conn->prepare("
                DELETE FROM tour_guide_availability 
                WHERE id = ? AND tour_guide_id = ?
            ");
            $stmt->bind_param("ii", $availabilityId, $tourGuideId);
            $result = $stmt->execute();
            
            if ($result && $stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Availability deleted successfully!'];
            } else {
                return ['success' => false, 'message' => 'Availability not found'];
            }
            
        } catch (Exception $e) {
            error_log("Error deleting availability: " . $e->getMessage());
            return ['success' => false, 'message' => 'An error occurred while deleting availability'];
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
            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return [];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];

            $sql = "
                SELECT b.*, u.first_name, u.last_name, u.email as tourist_email,
                       ts.name as destination, ts.image_url
                FROM bookings b 
                JOIN users u ON b.user_id = u.id 
                JOIN tourist_spots ts ON b.destination_id = ts.id 
                WHERE b.tour_guide_id = ?
            ";
            
            $params = [$tourGuideId];
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
            // First get the tour guide ID to verify ownership
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return ['success' => false, 'message' => 'Tour guide profile not found'];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];

            $stmt = $this->conn->prepare("
                UPDATE bookings SET status = ?, updated_at = NOW() 
                WHERE id = ? AND tour_guide_id = ?
            ");
            $stmt->bind_param("sii", $status, $bookingId, $tourGuideId);
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
            // First get the tour guide ID from the tour_guides table
            $guideIdStmt = $this->conn->prepare("SELECT id FROM tour_guides WHERE user_id = ?");
            $guideIdStmt->bind_param("i", $this->userId);
            $guideIdStmt->execute();
            $guideIdResult = $guideIdStmt->get_result();
            
            if ($guideIdResult->num_rows === 0) {
                return [
                    'total_bookings' => 0,
                    'pending_bookings' => 0,
                    'confirmed_bookings' => 0,
                    'completed_bookings' => 0,
                    'cancelled_bookings' => 0,
                    'total_earnings' => 0
                ];
            }
            
            $tourGuideId = $guideIdResult->fetch_assoc()['id'];

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
            $stmt->bind_param("i", $tourGuideId);
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
?>
