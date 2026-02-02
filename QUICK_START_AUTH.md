# Quick Start: Authentication System

## 🚀 Get Started in 3 Steps

### Step 1: Setup Database
1. Make sure XAMPP is running (Apache and MySQL)
2. Open your browser and go to: `http://localhost/coderistyarn2/setup-database.php`
3. Wait for the success message

### Step 2: Test the System
1. Visit: `http://localhost/coderistyarn2/test-auth-system.php`
2. Verify all tests pass (green checkmarks)

### Step 3: Use the Authentication System
- **Login Page**: `http://localhost/coderistyarn2/log-in/log-in.php`
- **Register Page**: `http://localhost/coderistyarn2/log-in/register.php`

## 🔐 Default Admin Credentials
```
Email: adminlgu@gmail.com
Password: admin123
```

## 🎯 Key Features Ready to Use

### ✅ User Registration
- First name, last name, email
- Password with strength meter
- Email validation
- Duplicate email checking

### ✅ User Login
- Email and password authentication
- Password visibility toggle
- Remember me option
- Session management

### ✅ Security Features
- Password hashing (bcrypt)
- Input validation
- Login activity logging
- Session security

### ✅ Admin Functionality
- Admin user type
- Special dashboard access
- User management capabilities

## 📱 Pages Included

1. **Login Page** (`log-in/log-in.php`)
   - Clean, modern design
   - Responsive layout
   - Form validation
   - AJAX submission

2. **Registration Page** (`log-in/register.php`)
   - Password strength indicator
   - Real-time validation
   - Terms acceptance
   - Success feedback

3. **Admin Dashboard** (`admin/dashboard.php`)
   - Admin-only access
   - User management
   - System overview

4. **User Dashboard** (`sjdm-user/index.php`)
   - Regular user access
   - Personal features
   - Booking management

## 🔧 Protect Your Pages

Add this to the top of any page you want to protect:

```php
<?php
require_once '../config/auth.php';
requireLogin(); // For logged-in users only
// OR
requireAdmin(); // For admin users only
?>
```

## 🛠️ Configuration

### Database Settings
Edit `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sjdm_tours');
```

### Authentication Settings
Edit `config/auth.php` to modify:
- Session timeout
- Password requirements
- Redirect URLs
- Security settings

## 🎨 Customization

### Change Colors
Edit `log-in/styles.css`:
```css
:root {
    --primary: #2c5f2d;      /* Main color */
    --secondary: #97bc62;    /* Accent color */
    --success: #10b981;      /* Success color */
    --danger: #ef4444;       /* Error color */
}
```

### Modify Validation Rules
Edit `log-in/register.php`:
```php
// Change minimum password length
if (strlen($password) < 8) {  // Was 6
    // Error handling
}
```

## 📞 Support

### Common Issues

**Database Connection Failed:**
- Check if MySQL is running in XAMPP
- Verify database credentials in `config/database.php`

**Login Not Working:**
- Run the setup script again
- Check if admin user was created
- Verify password is `admin123`

**Registration Errors:**
- Check browser console for JavaScript errors
- Ensure all fields are filled correctly
- Verify email format

### Testing URLs
- Setup: `http://localhost/coderistyarn2/setup-database.php`
- Test: `http://localhost/coderistyarn2/test-auth-system.php`
- Login: `http://localhost/coderistyarn2/log-in/log-in.php`
- Register: `http://localhost/coderistyarn2/log-in/register.php`

## 🚀 You're Ready!

Your complete authentication system is now ready to use. Start by visiting the login page and try logging in with the default admin credentials!