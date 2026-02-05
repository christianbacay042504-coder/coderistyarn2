// Login Page Functionality with Database Integration
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const socialButtons = document.querySelectorAll('.social-btn');
    const forgotPassword = document.querySelector('.forgot-password');

    // Toggle Password Visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Change icon
            const icon = this.querySelector('.material-icons-outlined');
            icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    }

    // Show alert message
    function showAlert(message, type) {
        const alertDiv = document.getElementById('alertMessage');
        alertDiv.textContent = message;
        alertDiv.className = 'alert ' + type;
        alertDiv.style.display = 'block';
        
        setTimeout(() => {
            alertDiv.style.display = 'none';
        }, 5000);
    }

    // Handle logout message display
    const logoutMessage = document.getElementById('logoutMessage');
    if (logoutMessage) {
        setTimeout(() => {
            logoutMessage.style.display = 'none';
        }, 5000);
    }

    // Form Validation
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value.trim();
            const remember = document.getElementById('remember').checked;
            
            // Basic validation
            if (!email || !password) {
                showAlert('Please fill in all fields', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showAlert('Please enter a valid email address', 'error');
                return;
            }
            
            // Show loading state
            const submitBtn = document.getElementById('loginBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <span>Authenticating...</span>
                <span class="material-icons-outlined">hourglass_empty</span>
            `;
            submitBtn.disabled = true;
            
            // Create FormData
            const formData = new FormData(loginForm);
            
            // Send AJAX request to server
            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                if (data.success) {
                    showAlert(data.message, 'success');
                    
                    // Store email if remember me is checked
                    if (remember) {
                        localStorage.setItem('rememberMe', 'true');
                        localStorage.setItem('userEmail', email);
                    }
                    
                    // Redirect based on user type
                    setTimeout(() => {
                        if (data.user_type === 'admin') {
                            window.location.href = 'admin/dashboard.php';
                        } else {
                            window.location.href = 'sjdm-user/index.php';
                        }
                    }, 1000);
                } else {
                    showAlert(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                let errorMessage = 'An error occurred. Please try again.';
                
                if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Network error. Please check your connection.';
                } else if (error.message.includes('JSON')) {
                    errorMessage = 'Server response error. Please try again.';
                }
                
                showAlert(errorMessage, 'error');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });
    }

    // Social Login Buttons
    socialButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.classList.contains('google') ? 'Google' : 'Facebook';
            
            // Show loading
            const originalText = this.innerHTML;
            this.innerHTML = `
                <span class="material-icons-outlined">hourglass_empty</span>
                Connecting...
            `;
            this.disabled = true;
            
            // Simulate social login
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                
                showNotification(`Successfully connected with ${platform}!`, 'success');
                
                // In real app, this would redirect to OAuth flow
                // For demo, just show success and redirect
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
                
            }, 2000);
        });
    });

    // Forgot Password
    if (forgotPassword) {
        forgotPassword.addEventListener('click', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            
            if (!email) {
                showNotification('Please enter your email address first', 'info');
                document.getElementById('email').focus();
                return;
            }
            
            if (!isValidEmail(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            showNotification(`Password reset link sent to ${email}`, 'success');
            
            // Simulate sending reset email
            console.log('Sending password reset email to:', email);
        });
    }

    // Auto-fill email if remembered
    const rememberedEmail = localStorage.getItem('userEmail');
    if (rememberedEmail && localStorage.getItem('rememberMe') === 'true') {
        document.getElementById('email').value = rememberedEmail;
        document.getElementById('remember').checked = true;
    }

    // Helper Functions
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function showNotification(message, type = 'success') {
        // Remove existing notification if any
        const existingNotification = document.querySelector('.notification-toast');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification-toast notification-${type}`;
        
        let icon = 'check_circle';
        if (type === 'error') icon = 'error';
        if (type === 'info') icon = 'info';
        
        notification.innerHTML = `
            <span class="material-icons-outlined">${icon}</span>
            <span>${message}</span>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Add show class after a delay
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Add CSS for notification toast
    const style = document.createElement('style');
    style.textContent = `
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10000;
            max-width: 400px;
            border-left: 4px solid var(--success);
        }
        
        .notification-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .notification-error {
            border-left-color: var(--danger);
        }
        
        .notification-info {
            border-left-color: var(--info);
        }
        
        .notification-toast .material-icons-outlined {
            font-size: 24px;
        }
        
        .notification-success .material-icons-outlined {
            color: var(--success);
        }
        
        .notification-error .material-icons-outlined {
            color: var(--danger);
        }
        
        .notification-info .material-icons-outlined {
            color: var(--info);
        }
    `;
    document.head.appendChild(style);
});


// register 

// Register Page Functionality
document.addEventListener('DOMContentLoaded', function() {
    const registerForm = document.getElementById('registerForm');
    const togglePassword = document.getElementById('toggleRegisterPassword');
    const passwordInput = document.getElementById('registerPassword');
    const strengthBar = document.getElementById('strengthBar');
    const strengthText = document.getElementById('strengthText');
    const socialButtons = document.querySelectorAll('.social-btn');

    // Toggle Password Visibility
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            const icon = this.querySelector('.material-icons-outlined');
            icon.textContent = type === 'password' ? 'visibility' : 'visibility_off';
        });
    }

    // Password Strength Checker
    if (passwordInput && strengthBar && strengthText) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            // Length check
            if (password.length >= 8) strength += 25;
            // Lowercase check
            if (/[a-z]/.test(password)) strength += 25;
            // Uppercase check
            if (/[A-Z]/.test(password)) strength += 25;
            // Special character or number check
            if (/[0-9!@#$%^&*]/.test(password)) strength += 25;
            
            strengthBar.style.width = `${strength}%`;
            
            // Update strength text and color
            if (strength < 50) {
                strengthBar.style.backgroundColor = 'var(--danger)';
                strengthText.textContent = 'Weak password';
                strengthText.style.color = 'var(--danger)';
            } else if (strength < 75) {
                strengthBar.style.backgroundColor = 'var(--warning)';
                strengthText.textContent = 'Medium password';
                strengthText.style.color = 'var(--warning)';
            } else {
                strengthBar.style.backgroundColor = 'var(--success)';
                strengthText.textContent = 'Strong password';
                strengthText.style.color = 'var(--success)';
            }
        });
    }

    // Form Validation and Submission
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const firstName = document.getElementById('firstName').value.trim();
            const lastName = document.getElementById('lastName').value.trim();
            const email = document.getElementById('registerEmail').value.trim();
            const password = document.getElementById('registerPassword').value;
            const confirmPassword = document.getElementById('confirmPassword').value;
            const terms = document.getElementById('terms').checked;
            
            // Validation
            if (!firstName || !lastName || !email || !password || !confirmPassword) {
                showNotification('Please fill in all fields', 'error');
                return;
            }
            
            if (!isValidEmail(email)) {
                showNotification('Please enter a valid email address', 'error');
                return;
            }
            
            if (password.length < 8) {
                showNotification('Password must be at least 8 characters long', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showNotification('Passwords do not match', 'error');
                return;
            }
            
            if (!terms) {
                showNotification('Please agree to the terms and conditions', 'error');
                return;
            }
            
            // Check password strength
            const strength = calculatePasswordStrength(password);
            if (strength < 50) {
                showNotification('Please use a stronger password', 'warning');
                return;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('.login-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = `
                <span>Creating Account...</span>
                <span class="material-icons-outlined">hourglass_empty</span>
            `;
            submitBtn.disabled = true;
            
            // Simulate API call
            setTimeout(() => {
                // Reset button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                
                // Store user data (in real app, this would be sent to server)
                const userData = {
                    firstName,
                    lastName,
                    email,
                    timestamp: new Date().toISOString()
                };
                
                localStorage.setItem('userData', JSON.stringify(userData));
                localStorage.setItem('isLoggedIn', 'true');
                
                // Show success message
                showNotification('Account created successfully! Welcome to SJDM Tours.', 'success');
                
                // Redirect to main page
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 2000);
                
            }, 2000);
        });
    }

    // Social Register Buttons
    socialButtons.forEach(button => {
        button.addEventListener('click', function() {
            const platform = this.classList.contains('google') ? 'Google' : 'Facebook';
            
            // Show loading
            const originalText = this.innerHTML;
            this.innerHTML = `
                <span class="material-icons-outlined">hourglass_empty</span>
                Connecting...
            `;
            this.disabled = true;
            
            // Simulate social registration
            setTimeout(() => {
                this.innerHTML = originalText;
                this.disabled = false;
                
                showNotification(`Registered with ${platform} successfully!`, 'success');
                
                // In real app, this would handle OAuth
                localStorage.setItem('isLoggedIn', 'true');
                
                setTimeout(() => {
                    window.location.href = 'index.html';
                }, 1000);
                
            }, 2000);
        });
    });

    // Helper Functions
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    function calculatePasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength += 25;
        if (/[a-z]/.test(password)) strength += 25;
        if (/[A-Z]/.test(password)) strength += 25;
        if (/[0-9!@#$%^&*]/.test(password)) strength += 25;
        return strength;
    }

    function showNotification(message, type = 'success') {
        // Remove existing notification if any
        const existingNotification = document.querySelector('.notification-toast');
        if (existingNotification) {
            existingNotification.remove();
        }
        
        // Create notification
        const notification = document.createElement('div');
        notification.className = `notification-toast notification-${type}`;
        
        let icon = 'check_circle';
        if (type === 'error') icon = 'error';
        if (type === 'info') icon = 'info';
        if (type === 'warning') icon = 'warning';
        
        notification.innerHTML = `
            <span class="material-icons-outlined">${icon}</span>
            <span>${message}</span>
        `;
        
        // Add to body
        document.body.appendChild(notification);
        
        // Add show class after a delay
        setTimeout(() => {
            notification.classList.add('show');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    // Add CSS for notification toast
    const style = document.createElement('style');
    style.textContent = `
        .notification-toast {
            position: fixed;
            top: 20px;
            right: 20px;
            background: white;
            padding: 16px 24px;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            display: flex;
            align-items: center;
            gap: 12px;
            transform: translateX(100%);
            opacity: 0;
            transition: all 0.3s ease;
            z-index: 10000;
            max-width: 400px;
            border-left: 4px solid var(--success);
        }
        
        .notification-toast.show {
            transform: translateX(0);
            opacity: 1;
        }
        
        .notification-error {
            border-left-color: var(--danger);
        }
        
        .notification-info {
            border-left-color: var(--info);
        }
        
        .notification-warning {
            border-left-color: var(--warning);
        }
        
        .notification-toast .material-icons-outlined {
            font-size: 24px;
        }
        
        .notification-success .material-icons-outlined {
            color: var(--success);
        }
        
        .notification-error .material-icons-outlined {
            color: var(--danger);
        }
        
        .notification-info .material-icons-outlined {
            color: var(--info);
        }
        
        .notification-warning .material-icons-outlined {
            color: var(--warning);
        }
    `;
    document.head.appendChild(style);
});