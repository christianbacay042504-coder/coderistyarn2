<?php
// Remove Tour Guide Registrations Menu Item
// This script removes the Tour Guide Registrations menu item from admin_menu_items table

require_once '../config/database.php';

// Get database connection
$conn = getDatabaseConnection();

// Remove the menu item
$deleteQuery = "DELETE FROM admin_menu_items WHERE menu_name = 'Tour Guide Registrations' AND menu_url = 'tour-guide-registrations.php'";
$stmt = $conn->prepare($deleteQuery);

if ($stmt->execute()) {
    echo "Tour Guide Registrations menu item removed successfully!\n";
} else {
    echo "Error removing menu item: " . $conn->error . "\n";
}
$stmt->close();

// Reset the display orders to original values
$updates = [
    ['name' => 'Destinations', 'order' => 4],
    ['name' => 'Hotels', 'order' => 5],
    ['name' => 'Bookings', 'order' => 6],
    ['name' => 'Analytics', 'order' => 7],
    ['name' => 'Reports', 'order' => 8],
    ['name' => 'Settings', 'order' => 9]
];

foreach ($updates as $update) {
    $updateQuery = "UPDATE admin_menu_items SET display_order = ? WHERE menu_name = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("is", $update['order'], $update['name']);
    
    if ($stmt->execute()) {
        echo "Reset {$update['name']} to order {$update['order']}\n";
    } else {
        echo "Error resetting {$update['name']}: " . $conn->error . "\n";
    }
    $stmt->close();
}

// Show final menu order
echo "\nFinal admin menu order:\n";
$selectQuery = "SELECT * FROM admin_menu_items WHERE is_active = 1 ORDER BY display_order ASC";
$result = $conn->query($selectQuery);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "ID: " . $row['id'] . " - " . $row['menu_name'] . " (" . $row['menu_url'] . ") - Order: " . $row['display_order'] . "\n";
    }
} else {
    echo "No active menu items found.\n";
}

closeDatabaseConnection($conn);
?>
