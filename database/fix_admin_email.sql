-- Fix Admin Email Address
-- Run this if you already have the admin user with wrong email

-- Update existing admin user email (if exists)
UPDATE users 
SET email = 'adminlgu@gmail.com' 
WHERE email = 'adminlgu3@gov.ph';

-- Or insert new admin user with correct email
INSERT INTO users (first_name, last_name, email, password, user_type, status) 
VALUES ('Admin', 'SJDM', 'adminlgu@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'active')
ON DUPLICATE KEY UPDATE 
    email = 'adminlgu@gmail.com',
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';