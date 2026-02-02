# Logout Functionality Documentation

## Overview
This document explains the logout functionality implemented in the SJDM Tours website, covering both the user and admin logout processes.

## Files Involved

### Server-side Files
- `admin/logout.php` - Handles admin logout
- `sjdm-user/logout.php` - Handles user logout
- `config/auth.php` - Contains the logoutUser() function

### Client-side File
- `sjdm-user/script.js` - Contains the handleLogout() JavaScript function

## How Logout Works

### Server-side Process
1. Both logout.php files call the `logoutUser()` function from `config/auth.php`
2. The `logoutUser()` function destroys the PHP session
3. The user is redirected to the login page

### Client-side Process
1. The `handleLogout()` function is called when the user clicks "Sign Out"
2. A confirmation dialog appears
3. If confirmed, a fetch request is made to the logout endpoint
4. The server processes the logout and destroys the session
5. Local storage is cleared
6. The user is redirected to the login page

## Implementation Details

### Server-side Code
```php
<?php
require_once __DIR__ . '/../config/auth.php';

// Logout the user
logoutUser();

// Redirect to login page
header('Location: /coderistyarn2/log-in/log-in.php');
exit();
?>
```

### Client-side Code
```javascript
function handleLogout() {
    if (confirm('Are you sure you want to sign out?')) {
        // Make a request to the server-side logout endpoint
        fetch('/coderistyarn2/sjdm-user/logout.php')
            .then(response => {
                if (response.ok) {
                    // Clear local storage
                    localStorage.removeItem('currentUser');
                    showNotification('Logged out successfully', 'info');
                    // Redirect to login page after a brief delay
                    setTimeout(() => {
                        window.location.href = '/coderistyarn2/log-in/log-in.php';
                    }, 1000);
                } else {
                    console.error('Logout failed:', response.statusText);
                    showNotification('Logout failed, please try again', 'error');
                }
            })
            .catch(error => {
                console.error('Error during logout:', error);
                showNotification('Error during logout, please try again', 'error');
            });
    }
}
```

## Security Considerations
- Both session data and local storage are cleared upon logout
- Proper session destruction prevents session hijacking
- The user is redirected to the login page after successful logout
- Error handling ensures graceful failure if logout fails

## Testing
To test the logout functionality:
1. Log in to the system
2. Navigate to the user dashboard
3. Click on the profile dropdown
4. Select "Sign Out"
5. Verify that you are redirected to the login page
6. Verify that you cannot access protected pages without logging in again

## Troubleshooting
- If logout doesn't work, check that the paths in the JavaScript fetch call are correct
- Ensure that the `config/auth.php` file is properly included
- Verify that the session is being properly destroyed
- Check browser console for any JavaScript errors

## Paths Used
- Logout endpoint: `/coderistyarn2/sjdm-user/logout.php`
- Redirect destination: `/coderistyarn2/log-in/log-in.php`