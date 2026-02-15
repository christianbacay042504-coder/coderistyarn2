<?php
// admin/export.php

require_once __DIR__ . '/../config/auth.php';

// Ensure only admins can access
if (!function_exists('requireAdmin')) {
    http_response_code(403);
    exit('Access denied');
}
requireAdmin();

$conn = getDatabaseConnection();
if (!$conn) {
    http_response_code(500);
    exit('Database connection failed');
}

$exportType = $_GET['type'] ?? '';
$filename = '';
$headers = [];
$sql = '';

switch ($exportType) {
    case 'login_activity':
        $filename = 'login_activity_' . date('Y-m-d') . '.csv';
        $headers = ['Time', 'User', 'Email', 'IP Address', 'Status'];
        $sql = "
            SELECT 
                la.login_time AS time,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                u.email,
                la.ip_address,
                la.status
            FROM login_activity la
            JOIN users u ON la.user_id = u.id
            ORDER BY la.login_time DESC
        ";
        break;

    case 'admin_activity':
        $filename = 'admin_activity_' . date('Y-m-d') . '.csv';
        $headers = ['Time', 'Admin', 'Action', 'Module', 'Description', 'IP Address'];
        $sql = "
            SELECT 
                aa.timestamp AS time,
                CONCAT(u.first_name, ' ', u.last_name) AS admin_name,
                aa.action,
                aa.module,
                aa.description,
                aa.ip_address
            FROM admin_activity aa
            JOIN admin_users au ON aa.admin_id = au.id
            JOIN users u ON au.user_id = u.id
            ORDER BY aa.timestamp DESC
        ";
        break;

    case 'user_registrations':
        $filename = 'user_registrations_' . date('Y-m-d') . '.csv';
        $headers = ['Registered At', 'Name', 'Email', 'Status'];
        $sql = "
            SELECT 
                created_at AS time,
                CONCAT(first_name, ' ', last_name) AS name,
                email,
                status
            FROM users
            WHERE user_type = 'user'
            ORDER BY created_at DESC
        ";
        break;

    case 'bookings':
        $filename = 'bookings_' . date('Y-m-d') . '.csv';
        $headers = ['Booking Date', 'User', 'Destination', 'Status', 'Total Amount'];
        $sql = "
            SELECT 
                b.booking_date,
                CONCAT(u.first_name, ' ', u.last_name) AS user_name,
                COALESCE(ts.name, 'N/A') AS destination,
                b.status,
                b.total_amount
            FROM bookings b
            JOIN users u ON b.user_id = u.id
            LEFT JOIN tourist_spots ts ON b.destination_id = ts.id
            ORDER BY b.booking_date DESC
        ";
        break;

    default:
        http_response_code(400);
        exit('Invalid export type');
}

$result = $conn->query($sql);
if (!$result) {
    error_log("Export query failed: " . $conn->error);
    http_response_code(500);
    exit('Failed to fetch data');
}

// Force download as CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// Add BOM for Excel compatibility (optional)
fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

fputcsv($output, $headers);

while ($row = $result->fetch_assoc()) {
    // Clean row values
    $cleanRow = array_map(function($value) {
        return $value === null ? 'N/A' : $value;
    }, array_values($row));
    fputcsv($output, $cleanRow);
}

fclose($output);
closeDatabaseConnection($conn);
exit();