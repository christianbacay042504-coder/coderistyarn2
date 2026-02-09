<?php
/**
 * Tour Guide Registration Page
 * Created: February 9, 2026
 */

require_once __DIR__ . '/../config/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    if (isTourGuide()) {
        header('Location: dashboard.php');
    } else {
        header('Location: ../sjdm-user/book.php');
    }
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $licenseNumber = trim($_POST['license_number'] ?? '');
    $specialization = trim($_POST['specialization'] ?? '');
    $experienceYears = intval($_POST['experience_years'] ?? 0);
    $languages = trim($_POST['languages'] ?? '');
    $hourlyRate = floatval($_POST['hourly_rate'] ?? 0);
    $contactNumber = trim($_POST['contact_number'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    
    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($confirmPassword)) {
        $error = 'Please fill in all required fields';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (empty($licenseNumber) || empty($specialization)) {
        $error = 'License number and specialization are required';
    } elseif ($experienceYears < 0) {
        $error = 'Experience years cannot be negative';
    } elseif ($hourlyRate < 0) {
        $error = 'Hourly rate cannot be negative';
    } else {
        // Register tour guide
        $result = registerTourGuide(
            $firstName, $lastName, $email, $password,
            $licenseNumber, $specialization, $experienceYears,
            $languages, $hourlyRate, $contactNumber, $bio
        );
        
        if ($result['success']) {
            $success = $result['message'];
            // Redirect to login after successful registration
            header('refresh:3;url=../log-in.php');
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tour Guide Registration - SJDM Tours</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .registration-container {
            max-width: 800px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .registration-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .registration-form {
            padding: 2rem;
        }
        .form-section {
            margin-bottom: 2rem;
            padding: 1.5rem;
            border: 1px solid #e9ecef;
            border-radius: 10px;
            background: #f8f9fa;
        }
        .form-section h5 {
            color: #667eea;
            margin-bottom: 1rem;
            font-weight: 600;
        }
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 0.75rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-register {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .alert {
            border-radius: 8px;
            border: none;
        }
        .badge-guide {
            background: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="registration-container">
            <div class="registration-header">
                <i class="fas fa-user-tie fa-3x mb-3"></i>
                <h2>Tour Guide Registration</h2>
                <p class="mb-0">Join our team of professional tour guides</p>
                <span class="badge-guide">Professional Guide</span>
            </div>
            
            <div class="registration-form">
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?php echo htmlspecialchars($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo htmlspecialchars($success); ?>
                        <br><small>Redirecting to login page...</small>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-user me-2"></i>Personal Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="first_name" class="form-label">First Name *</label>
                                <input type="text" class="form-control" id="first_name" name="first_name" 
                                       value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="last_name" class="form-label">Last Name *</label>
                                <input type="text" class="form-control" id="last_name" name="last_name" 
                                       value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email Address *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="contact_number" class="form-label">Contact Number</label>
                                <input type="tel" class="form-control" id="contact_number" name="contact_number" 
                                       value="<?php echo htmlspecialchars($_POST['contact_number'] ?? ''); ?>">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Password *</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password *</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Professional Information -->
                    <div class="form-section">
                        <h5><i class="fas fa-briefcase me-2"></i>Professional Information</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="license_number" class="form-label">License Number *</label>
                                <input type="text" class="form-control" id="license_number" name="license_number" 
                                       value="<?php echo htmlspecialchars($_POST['license_number'] ?? ''); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="experience_years" class="form-label">Years of Experience</label>
                                <input type="number" class="form-control" id="experience_years" name="experience_years" 
                                       value="<?php echo htmlspecialchars($_POST['experience_years'] ?? '0'); ?>" min="0">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="specialization" class="form-label">Specialization *</label>
                            <textarea class="form-control" id="specialization" name="specialization" rows="2" required><?php echo htmlspecialchars($_POST['specialization'] ?? ''); ?></textarea>
                                <small class="text-muted">e.g., Historical Tours, Nature Walks, Adventure Tours</small>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="languages" class="form-label">Languages Spoken</label>
                                <input type="text" class="form-control" id="languages" name="languages" 
                                       value="<?php echo htmlspecialchars($_POST['languages'] ?? ''); ?>" 
                                       placeholder="e.g., English, Filipino, Japanese">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="hourly_rate" class="form-label">Hourly Rate (₱)</label>
                                <input type="number" class="form-control" id="hourly_rate" name="hourly_rate" 
                                       value="<?php echo htmlspecialchars($_POST['hourly_rate'] ?? ''); ?>" 
                                       min="0" step="0.01">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio/Description</label>
                            <textarea class="form-control" id="bio" name="bio" rows="4"><?php echo htmlspecialchars($_POST['bio'] ?? ''); ?></textarea>
                                <small class="text-muted">Tell us about yourself and your tour guide experience</small>
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary btn-register">
                            <i class="fas fa-user-plus me-2"></i>Register as Tour Guide
                        </button>
                    </div>
                </form>
                
                <div class="text-center mt-4">
                    <p class="text-muted">Already have an account? <a href="../log-in.php" class="text-decoration-none">Login here</a></p>
                    <p class="text-muted"><a href="../index.php" class="text-decoration-none"><i class="fas fa-arrow-left me-1"></i>Back to Home</a></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Email validation
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!emailRegex.test(email)) {
                this.setCustomValidity('Please enter a valid email address');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
