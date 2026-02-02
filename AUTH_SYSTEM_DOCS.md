# SJDM Tours Authentication System

## Overview
A complete database-driven login and registration system with secure authentication, password hashing, session management, and user role handling.

## Features
- ✅ User registration with validation
- ✅ Secure login with password hashing
- ✅ Admin and regular user roles
- ✅ Session-based authentication
- ✅ Password strength checking
- ✅ Login activity logging
- ✅ Remember me functionality
- ✅ Responsive design
- ✅ Error handling and notifications

## File Structure
```
coderistyarn2/
├── config/
│   ├── database.php          # Database configuration
│   └── auth.php             # Authentication functions
├── log-in/
│   ├── log-in.php           # Login page
│   ├── register.php         # Registration page
│   ├── log-in.js            # Frontend JavaScript
│   └── styles.css           # Styling
└── database/
    └── sjdm_tours.sql       # Database schema
```

## Database Schema

### Users Table
```sql
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    user_type ENUM('user', 'admin') DEFAULT 'user',
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL
);
```

### Login Activity Table
```sql
CREATE TABLE login_activity (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    login_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    status ENUM('success', 'failed') DEFAULT 'success',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

## Setup Instructions

### 1. Run Database Setup
Visit: `http://localhost/coderistyarn2/setup-database.php`

This will:
- Create the database if it doesn't exist
- Create all required tables
- Insert the default admin user

### 2. Default Admin Credentials
```
Email: adminlgu@gmail.com
Password: admin123
```

### 3. Test the System
Visit: `http://localhost/coderistyarn2/test-auth-system.php`

## Authentication Functions

### Login Function
```php
loginUser($email, $password)
```
Returns: `['success' => true/false, 'message' => '...', 'user_type' => '...']`

### Registration Function
```php
registerUser($firstName, $lastName, $email, $password)
```
Returns: `['success' => true/false, 'message' => '...', 'user_id' => ...]`

### Check Authentication Status
```php
isLoggedIn()     // Returns true/false
isAdmin()        // Returns true/false
getCurrentUser() // Returns user data array
```

### Require Authentication
```php
requireLogin()   // Redirects to login if not logged in
requireAdmin()   // Redirects to login if not admin
```

## Frontend Features

### Login Page (`log-in/log-in.php`)
- Email and password fields
- Password visibility toggle
- Remember me checkbox
- Forgot password link
- Social login buttons (UI only)
- Responsive design
- Form validation
- AJAX submission

### Registration Page (`log-in/register.php`)
- First name and last name fields
- Email field with validation
- Password with strength meter
- Password confirmation
- Terms and conditions checkbox
- Social registration buttons (UI only)
- Responsive design
- Form validation
- AJAX submission

### JavaScript Features
- Form validation
- Password strength checker
- Toggle password visibility
- Notification system
- AJAX form submission
- Loading states
- Auto-fill remembered email

## Security Features

### Password Security
- Passwords hashed using `password_hash()` with bcrypt
- Minimum 6 characters required
- Password strength validation
- No password storage in plain text

### Session Security
- Secure session management
- Session data includes user ID and type
- Automatic session cleanup on logout

### Input Validation
- Server-side validation
- Client-side validation
- Email format checking
- Required field validation

### Activity Logging
- Logs all login attempts
- Stores IP address and user agent
- Tracks success/failure status
- Useful for security monitoring

## Usage Examples

### Protecting Pages
```php
<?php
require_once '../config/auth.php';
requireLogin(); // This will redirect to login if not authenticated

// Page content here
?>
```

### Admin-Only Pages
```php
<?php
require_once '../config/auth.php';
requireAdmin(); // This will redirect to login if not admin

// Admin-only content here
?>
```

### Getting Current User
```php
<?php
require_once '../config/auth.php';
$user = getCurrentUser();

echo "Welcome, " . $user['first_name'] . " " . $user['last_name'];
?>
```

## API Endpoints

### Login Endpoint
**POST** `/log-in/log-in.php`
```json
{
  "email": "user@example.com",
  "password": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Login successful",
  "user_type": "user"
}
```

### Registration Endpoint
**POST** `/log-in/register.php`
```json
{
  "firstName": "John",
  "lastName": "Doe",
  "email": "john@example.com",
  "password": "password123",
  "confirmPassword": "password123"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Registration successful",
  "user_id": 123
}
```

## Customization

### Changing Password Requirements
Edit `log-in/register.php`:
```php
if (strlen($password) < 8) { // Change minimum length
    // Error message
}
```

### Adding Custom Fields
1. Add field to database table
2. Update registration form
3. Update `registerUser()` function
4. Update frontend validation

### Styling
Modify `log-in/styles.css` to change:
- Colors (CSS variables)
- Layout
- Typography
- Spacing

## Troubleshooting

### Database Connection Issues
1. Check `config/database.php` credentials
2. Ensure MySQL is running
3. Verify database exists
4. Check user permissions

### Login Not Working
1. Verify user exists in database
2. Check password hash in database
3. Ensure session is starting properly
4. Check browser console for JavaScript errors

### Registration Issues
1. Check email uniqueness constraint
2. Verify password requirements
3. Ensure all required fields are filled
4. Check database table structure

## Testing

### Manual Testing
1. Visit login page
2. Try invalid credentials
3. Try valid credentials
4. Test registration flow
5. Check session persistence
6. Test logout functionality

### Automated Testing
Run: `http://localhost/coderistyarn2/test-auth-system.php`

## Dependencies
- PHP 7.4+
- MySQL 5.7+
- Modern web browser
- Google Fonts (Playfair Display, Inter)
- Material Icons

## Browser Support
- Chrome 60+
- Firefox 55+
- Safari 12+
- Edge 79+

## Notes
- All passwords are securely hashed
- Session data is stored server-side
- Email uniqueness is enforced
- User status can be active/inactive/suspended
- Admin users have special privileges
- Login activity is logged for security