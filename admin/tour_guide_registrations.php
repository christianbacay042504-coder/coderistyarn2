<?php
/**
 * Admin Tour Guide Registration Viewer
 * Simple interface to view and manage tour guide registrations
 * Created: February 16, 2026
 */

require_once __DIR__ . '/functions/tour_guide_registration.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $id = $_POST['id'];
    $status = $_POST['status'];
    $notes = $_POST['notes'];
    
    $result = updateRegistrationStatus($id, $status, $notes, 1); // Admin ID = 1 for demo
    
    if ($result['success']) {
        $message = "Status updated successfully!";
        $messageType = "success";
    } else {
        $message = "Error updating status: " . $result['message'];
        $messageType = "error";
    }
}

// Get all registrations
$registrations = getTourGuideRegistrations();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Registrations - Admin</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            border-bottom: 2px solid #3b82f6;
            padding-bottom: 10px;
        }
        
        .message {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            border-left: 4px solid;
        }
        
        .message.success {
            background-color: #d4edda;
            border-color: #28a745;
            color: #155724;
        }
        
        .message.error {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #495057;
        }
        
        tr:hover {
            background-color: #f8f9fa;
        }
        
        .status {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
        }
        
        .status.pending {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .status.under_review {
            background-color: #cce5ff;
            color: #004085;
        }
        
        .status.approved {
            background-color: #d4edda;
            color: #155724;
        }
        
        .status.rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            margin-right: 5px;
        }
        
        .btn-primary {
            background-color: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #0056b3;
        }
        
        .btn-success {
            background-color: #28a745;
            color: white;
        }
        
        .btn-success:hover {
            background-color: #1e7e34;
        }
        
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover {
            color: black;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .form-group textarea {
            height: 80px;
            resize: vertical;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 0.9em;
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tour Guide Registrations</h1>
        
        <?php if (isset($message)): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?php echo count($registrations); ?></div>
                <div class="stat-label">Total Applications</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="stat-number"><?php echo count(array_filter($registrations, fn($r) => $r['status'] === 'pending')); ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="stat-number"><?php echo count(array_filter($registrations, fn($r) => $r['status'] === 'approved')); ?></div>
                <div class="stat-label">Approved</div>
            </div>
        </div>
        
        <?php if (empty($registrations)): ?>
            <div class="empty-state">
                <h3>No tour guide registrations found</h3>
                <p>Applications will appear here when tour guides submit their registration forms.</p>
            </div>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Specialization</th>
                        <th>Experience</th>
                        <th>Application Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($registrations as $registration): ?>
                        <tr>
                            <td><?php echo $registration['id']; ?></td>
                            <td><?php echo htmlspecialchars($registration['first_name'] . ' ' . $registration['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($registration['email']); ?></td>
                            <td><?php echo ucfirst($registration['specialization']); ?></td>
                            <td><?php echo $registration['years_experience']; ?> years</td>
                            <td><?php echo date('M j, Y', strtotime($registration['application_date'])); ?></td>
                            <td>
                                <span class="status <?php echo $registration['status']; ?>">
                                    <?php echo str_replace('_', ' ', $registration['status']); ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-primary" onclick="viewDetails(<?php echo $registration['id']; ?>)">View</button>
                                <button class="btn btn-success" onclick="updateStatus(<?php echo $registration['id']; ?>, 'approved')">Approve</button>
                                <button class="btn btn-danger" onclick="updateStatus(<?php echo $registration['id']; ?>, 'rejected')">Reject</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Status Update Modal -->
    <div id="statusModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Update Registration Status</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_status">
                <input type="hidden" name="id" id="modalId">
                
                <div class="form-group">
                    <label for="status">New Status:</label>
                    <select name="status" id="modalStatus" required>
                        <option value="pending">Pending</option>
                        <option value="under_review">Under Review</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="notes">Admin Notes:</label>
                    <textarea name="notes" id="modalNotes" placeholder="Add any notes about this decision..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Status</button>
                <button type="button" class="btn" onclick="closeModal()">Cancel</button>
            </form>
        </div>
    </div>
    
    <!-- Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDetailsModal()">&times;</span>
            <h2>Registration Details</h2>
            <div id="detailsContent">
                <!-- Details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script>
        function updateStatus(id, status) {
            document.getElementById('modalId').value = id;
            document.getElementById('modalStatus').value = status;
            document.getElementById('statusModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('statusModal').style.display = 'none';
        }
        
        function closeDetailsModal() {
            document.getElementById('detailsModal').style.display = 'none';
        }
        
        function viewDetails(id) {
            // Simple details view - in production, you'd fetch this via AJAX
            const details = {
                1: {
                    name: 'Guide Test',
                    email: 'testguide1771215728@example.com',
                    phone: '09123456789',
                    address: '123 Test Street, Test City, Test Province',
                    specialization: 'mountain',
                    experience: '5 years',
                    dotAccreditation: 'DOT-1771215684',
                    languages: 'English (Fluent), Filipino (Native)',
                    status: 'pending'
                }
            };
            
            const detail = details[id];
            if (detail) {
                document.getElementById('detailsContent').innerHTML = `
                    <p><strong>Name:</strong> ${detail.name}</p>
                    <p><strong>Email:</strong> ${detail.email}</p>
                    <p><strong>Phone:</strong> ${detail.phone}</p>
                    <p><strong>Address:</strong> ${detail.address}</p>
                    <p><strong>Specialization:</strong> ${detail.specialization}</p>
                    <p><strong>Experience:</strong> ${detail.experience}</p>
                    <p><strong>DOT Accreditation:</strong> ${detail.dotAccreditation}</p>
                    <p><strong>Languages:</strong> ${detail.languages}</p>
                    <p><strong>Status:</strong> <span class="status ${detail.status}">${detail.status}</span></p>
                `;
                document.getElementById('detailsModal').style.display = 'block';
            }
        }
        
        // Close modals when clicking outside
        window.onclick = function(event) {
            const statusModal = document.getElementById('statusModal');
            const detailsModal = document.getElementById('detailsModal');
            
            if (event.target == statusModal) {
                statusModal.style.display = 'none';
            }
            if (event.target == detailsModal) {
                detailsModal.style.display = 'none';
            }
        }
    </script>
</body>
</html>
