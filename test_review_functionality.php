<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Review Functionality</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; }
        .success { color: green; }
        .error { color: red; }
        button { padding: 10px 15px; margin: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>Test Review & Pay Functionality</h1>
    
    <div class="test-section">
        <h2>What This Tests</h2>
        <p>This page will help you verify that the Review & Pay section correctly detects and displays tour details from the form.</p>
        
        <h3>Steps to Test:</h3>
        <ol>
            <li>Go to the booking page: <a href="sjdm-user/book.php">sjdm-user/book.php</a></li>
            <li>Fill out Step 1 (Tour Details):
                <ul>
                    <li>Select a tour guide</li>
                    <li>Select a destination</li>
                    <li>Choose check-in and check-out dates</li>
                    <li>Set number of guests</li>
                </ul>
            </li>
            <li>Fill out Step 2 (Personal Information)</li>
            <li>Proceed to Step 3 (Review & Pay)</li>
            <li><strong>Verify that all tour details are displayed correctly:</strong>
                <ul>
                    <li>✓ Guide name should appear</li>
                    <li>✓ Destination should be displayed</li>
                    <li>✓ Check-in and check-out dates should be formatted</li>
                    <li>✓ Number of guests should be shown</li>
                    <li>✓ Pricing should update based on guest count</li>
                </ul>
            </li>
        </ol>
    </div>

    <div class="test-section">
        <h2>Expected Behavior</h2>
        
        <h3>Tour Summary Section Should Show:</h3>
        <ul>
            <li><strong>Guide:</strong> [Selected Guide Name]</li>
            <li><strong>Destination:</strong> [Selected Destination]</li>
            <li><strong>Check-in:</strong> [Formatted Date - e.g., "February 3, 2026, Tuesday"]</li>
            <li><strong>Check-out:</strong> [Formatted Date - e.g., "February 4, 2026, Wednesday"]</li>
            <li><strong>Guests:</strong> [Number of Guests]</li>
        </ul>
        
        <h3>Price Summary Should Update:</h3>
        <ul>
            <li><strong>Tour Guide Fee:</strong> ₱2,500.00 (fixed)</li>
            <li><strong>Entrance Fees:</strong> ₱100.00 × [Number of Guests]</li>
            <li><strong>Service Fee:</strong> ₱200.00 (fixed)</li>
            <li><strong>Total Amount:</strong> [Calculated Total]</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>Test Scenarios</h2>
        
        <h3>Scenario 1: Basic Test</h3>
        <ul>
            <li>Guide: Rico Mendoza</li>
            <li>Destination: Mt. Balagbag</li>
            <li>Dates: Today + 1 day</li>
            <li>Guests: 2</li>
            <li>Expected Total: ₱2,900.00</li>
        </ul>
        
        <h3>Scenario 2: Different Guest Count</h3>
        <ul>
            <li>Guide: Maria Santos</li>
            <li>Destination: Kaytitinga Falls</li>
            <li>Dates: Next week</li>
            <li>Guests: 5</li>
            <li>Expected Total: ₱3,200.00</li>
        </ul>
    </div>

    <div class="test-section">
        <h2>Quick Access</h2>
        <button onclick="window.open('sjdm-user/book.php', '_blank')">
            Open Booking Page (New Tab)
        </button>
        <button onclick="window.open('check_database.php', '_blank')">
            Check Database Bookings
        </button>
    </div>

    <div class="test-section">
        <h2>Troubleshooting</h2>
        <p>If the review section doesn't update:</p>
        <ul>
            <li>Check browser console for JavaScript errors</li>
            <li>Ensure all form fields in Step 1 are filled</li>
            <li>Verify you're logged in (check session)</li>
            <li>Try refreshing the page and starting over</li>
        </ul>
    </div>

    <script>
        // Auto-check if user is logged in
        fetch('sjdm-user/book.php')
            .then(response => {
                if (response.redirected) {
                    document.querySelector('.test-section').innerHTML += 
                        '<div class="error"><strong>⚠️ Not Logged In:</strong> You need to log in first. <a href="log-in/log-in.php">Login Here</a></div>';
                } else {
                    document.querySelector('.test-section').innerHTML += 
                        '<div class="success"><strong>✅ Session Active:</strong> You can proceed with testing.</div>';
                }
            })
            .catch(() => {
                // Ignore CORS errors for this test
            });
    </script>
</body>
</html>
