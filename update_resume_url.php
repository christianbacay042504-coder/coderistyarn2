<?php
require_once 'config/database.php';

$conn = getDatabaseConnection();
$stmt = $conn->prepare('UPDATE tour_guides SET resume = ? WHERE id = ?');
$resumeUrl = 'http://localhost/coderistyarn2/uploads/resumes/698bdb7ebb537_resume.pdf';
$guideId = 17;
$stmt->bind_param('si', $resumeUrl, $guideId);
$stmt->execute();
$stmt->close();
$conn->close();
echo 'Updated resume URL for guide ID ' . $guideId;
echo 'New URL: ' . $resumeUrl;
?>
