# SJDM Tours - Database Setup Instructions

## Overview
This guide will help you set up the complete authentication system with database integration for SJDM Tours.

## Prerequisites
- XAMPP installed with MySQL running
- PHP 7.4 or higher
- Web browser

## Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service

## Step 2: Create Database
1. Open your web browser and go to: `http://localhost/phpmyadmin`
2. Click on "SQL" tab
3. Copy the entire content from `c:\xampp\htdocs\coderistyarn2\database\sjdm_tours.sql`
4. Paste it in the SQL query box
5. Click "Go" to execute

This will:
- Create the `sjdm_tours` database
- Create tables: users, bookings, login_activity, saved_tours
- Insert the default admin user

## Step 3: Configure Database Connection
1. Open `c:\xampp\htdocs\coderistyarn2\config\database.php`
2. Update the database credentials if needed:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');     // Your MySQL username
   define('DB_PASS', '');         // Your MySQL password
   define('DB_NAME', 'sjdm_tours');
   ```

## Step 4: Test the System

### Admin Login
1. Go to: `http://localhost/coderistyarn2/log-in/log-in.php`
2. Use the following credentials:
   - **Email:** `adminlgu3@.gov`
   - **Password:** `admin123`
3. You should be redirected to the admin dashboard

### User Registration
1. Go to: `http://localhost/coderistyarn2/log-in/register.php`
2. Fill out the registration form
3. After successful registration, you'll be redirected to login
4. Login with your new credentials
5. You should be redirected to the user dashboard

## Step 5: Verify Admin Dashboard Features

Once logged in as admin, you can:
- View **Dashboard** with analytics
- Manage **Users** (view all registered users)
- View **Analytics** (login activity, user growth)
- Manage **Bookings** (empty for now)
- Access **Settings**

## File Structure

```
coderistyarn2/
├── admin/
│   ├── dashboard.php          # Admin dashboard page
│   ├── admin-styles.css       # Admin dashboard styles
│   ├── admin-script.js        # Admin dashboard JavaScript
│   └── logout.php             # Logout functionality
├── config/
│   ├── database.php           # Database connection configuration
│   └── auth.php               # Authentication functions
├── database/
│   └── sjdm_tours.sql         # Database schema and default data
├── log-in/
│   ├── log-in.php             # Login page (updated with DB)
│   ├── register.php           # Registration page (updated with DB)
│   ├── log-in.js              # Login JavaScript
│   └── styles.css             # Login/Register styles
├── landingpage/
├── sjdm-user/
└── ...
```

## Database Tables

### users
- Stores user and admin accounts
- Default admin: adminlgu3@.gov / admin123
- User passwords are hashed with `password_hash()`

### login_activity
- Tracks all login attempts (successful and failed)
- Stores IP address and user agent
- Used for analytics

### bookings
- For future booking functionality
- Currently empty

### saved_tours
- For users to save favorite tours
- Currently empty

## Security Features

1. **Password Hashing:** All passwords are hashed using PHP's `password_hash()` with bcrypt
2. **Prepared Statements:** All database queries use prepared statements to prevent SQL injection
3. **Session Management:** Secure session handling for logged-in users
4. **Access Control:** Admin-only areas protected with authentication checks

## Admin Credentials

**Email:** adminlgu3@.gov  
**Password:** admin123

**IMPORTANT:** Change the admin password after first login!

## User Dashboard

Regular users will be redirected to: `http://localhost/coderistyarn2/sjdm-user/index.php`

## Troubleshooting

### Database Connection Failed
- Verify MySQL is running in XAMPP
- Check database credentials in `config/database.php`
- Ensure database `sjdm_tours` exists

### Page Not Found (404)
- Check your XAMPP htdocs path
- Ensure all files are in `c:\xampp\htdocs\coderistyarn2\`
- Clear browser cache

### Login Not Working
- Verify database was created successfully
- Check PHP errors in: `C:\xampp\php\logs\php_error_log`
- Enable error display by adding to your PHP file:
  ```php
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
  ```

## Next Steps

1. Customize the admin dashboard with your branding
2. Implement user management features (edit, delete)
3. Add booking functionality
4. Create tour management system
5. Add email notifications
6. Implement password reset functionality

## Support

For any issues, check:
- PHP error logs: `C:\xampp\php\logs\php_error_log`
- MySQL error logs: `C:\xampp\mysql\data\*.err`
- Browser console for JavaScript errors

---

**Created:** January 30, 2026  
**Version:** 1.0.0  
**SJDM Tours Authentication System**
