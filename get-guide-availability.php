<?php
/**
 * API: get-guide-availability.php
 * Reads the guide_availability table (set by tour guide in dashboard.php)
 * and returns it as JSON for the booking calendar in user-book.php
 *
 * Place this file at: /api/get-guide-availability.php
 *
 * Query params:
 *   guide_id  (int)  - required
 *   year      (int)  - e.g. 2026
 *   month     (int)  - 1-12
 */

header('Content-Type: application/json');
ob_start();

session_start();

if (!isset($_SESSION['user_id'])) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

require_once '../config/database.php';

$guide_id = intval($_GET['guide_id'] ?? 0);
$year     = intval($_GET['year']     ?? date('Y'));
$month    = intval($_GET['month']    ?? date('m'));

if ($guide_id <= 0) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => 'Invalid guide ID']);
    exit();
}

$month = max(1, min(12, $month));
$year  = max(2020, min(2100, $year));

// First and last day of the requested month
$startDate = sprintf('%04d-%02d-01', $year, $month);
$endDate   = date('Y-m-t', strtotime($startDate));

try {
    $conn = getDatabaseConnection();
    if (!$conn) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Database connection failed']);
        exit();
    }

    // ── 1. Pull every availability slot the guide added in dashboard.php ──
    $stmt = $conn->prepare("
        SELECT id, available_date, start_time, end_time, status
        FROM   guide_availability
        WHERE  guide_id = ?
          AND  available_date BETWEEN ? AND ?
        ORDER  BY available_date ASC, start_time ASC
    ");

    if (!$stmt) {
        ob_end_clean();
        echo json_encode(['success' => false, 'message' => 'Query prepare failed']);
        exit();
    }

    $stmt->bind_param('iss', $guide_id, $startDate, $endDate);
    $stmt->execute();
    $result = $stmt->get_result();

    // Group raw slots by date
    $slotsByDate = [];
    while ($row = $result->fetch_assoc()) {
        $slotsByDate[$row['available_date']][] = $row;
    }
    $stmt->close();

    // ── 2. Pull bookings count per date so we know if slots are taken ──
    $bookingsByDate = [];
    $bStmt = $conn->prepare("
        SELECT tour_date, COUNT(*) AS cnt
        FROM   bookings
        WHERE  guide_id = ?
          AND  tour_date BETWEEN ? AND ?
          AND  status NOT IN ('cancelled','rejected')
        GROUP  BY tour_date
    ");
    if ($bStmt) {
        $bStmt->bind_param('iss', $guide_id, $startDate, $endDate);
        $bStmt->execute();
        $bResult = $bStmt->get_result();
        while ($r = $bResult->fetch_assoc()) {
            $bookingsByDate[$r['tour_date']] = (int)$r['cnt'];
        }
        $bStmt->close();
    }

    closeDatabaseConnection($conn);

    // ── 3. Roll up slots into one day-level status ──
    //   • ALL slots unavailable/booked  → 'unavailable'
    //   • Some available, some not      → 'limited'
    //   • All available                 → 'available'
    $availability = [];

    foreach ($slotsByDate as $date => $slots) {
        $totalSlots     = count($slots);
        $availableCount = 0;
        $timeSlots      = [];

        foreach ($slots as $s) {
            if ($s['status'] === 'available') {
                $availableCount++;
            }
            $timeSlots[] = [
                'id'     => $s['id'],
                'start'  => date('g:i A', strtotime($s['start_time'])),
                'end'    => date('g:i A', strtotime($s['end_time'])),
                'status' => $s['status'],
            ];
        }

        $bookingCount = $bookingsByDate[$date] ?? 0;

        if ($availableCount === 0) {
            $dayStatus  = 'unavailable';
            $dayMessage = 'Fully booked';
        } elseif ($bookingCount > 0 || $availableCount < $totalSlots) {
            $dayStatus  = 'limited';
            $dayMessage = $availableCount . ' slot' . ($availableCount > 1 ? 's' : '') . ' left';
        } else {
            $dayStatus  = 'available';
            $dayMessage = $totalSlots . ' slot' . ($totalSlots > 1 ? 's' : '') . ' available';
        }

        $availability[] = [
            'date'       => $date,
            'status'     => $dayStatus,
            'message'    => $dayMessage,
            'time_slots' => $timeSlots,
        ];
    }

    ob_end_clean();
    echo json_encode([
        'success'      => true,
        'guide_id'     => $guide_id,
        'year'         => $year,
        'month'        => $month,
        'availability' => $availability,
    ]);

} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
