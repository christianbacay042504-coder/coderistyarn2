<?php
// SMTP Configuration for SJDM Tours
// IMPORTANT: Update these values with your actual SMTP credential
// Gmail SMTP Configuration with your credentials
putenv('SMTP_HOST=smtp.gmail.com');
putenv('SMTP_PORT=587');
putenv('SMTP_SECURE=tls');
putenv('SMTP_USERNAME=jeanmarcaguilar829@gmail.com');
putenv('SMTP_PASSWORD=rqulwmjxdtzmxfli');
putenv('SMTP_FROM_EMAIL=jeanmarcaguilar829@gmail.com');
putenv('SMTP_FROM_NAME=SJDM Tours');

// SETUP INSTRUCTIONS:
// 1. For Gmail users:
//    - Enable 2-factor authentication on your Google Account
//    - Go to Google Account settings > Security > App passwords
//    - Generate a new App Password for "Mail"
//    - Use that App Password (not your regular password) in SMTP_PASSWORD
// 2. Replace 'your-email@gmail.com' with your actual Gmail address
// 3. Replace 'your-app-password' with your Gmail App Password
// 4. Alternative: Copy .env.example to .env and set values there
//    The system will read from .env file if it exists
?>

