# Tour Guide Registration System

## Overview
This system provides a complete tour guide registration functionality for the SJDM Tours website. It includes:
- Database table creation
- Registration form handling
- File upload management
- Admin interface for managing applications
- Multi-language support for tour guides

## Files Created

### 1. Database Table (`database/create_registration_tour_guide_table.sql`)
- **registration_tour_guide**: Main table storing all registration data
- **tour_guide_languages**: Related table for multiple languages per guide

### 2. Core Functions (`functions/tour_guide_registration.php`)
- `saveTourGuideRegistration()` - Saves new registration
- `handleFileUploads()` - Processes document uploads
- `saveLanguages()` - Stores multiple languages
- `getTourGuideRegistrations()` - Retrieves all registrations
- `getTourGuideRegistrationById()` - Gets single registration
- `updateRegistrationStatus()` - Updates application status

### 3. Registration Handler (`save_registration_tour_guide.php`)
- Processes form submissions from register-guide.php
- Validates all required fields
- Handles file uploads
- Returns JSON responses

### 4. Admin Interface (`admin/tour_guide_registrations.php`)
- View all tour guide applications
- Update application status (pending, under_review, approved, rejected)
- View detailed registration information
- Dashboard with statistics

### 5. Setup and Test Files
- `create_tour_guide_tables.php` - Creates database tables
- `test_tour_guide_registration.php` - Tests the registration system

## Database Schema

### registration_tour_guide Table
| Field | Type | Description |
|-------|------|-------------|
| id | int(11) | Primary key, auto-increment |
| application_date | timestamp | When application was submitted |
| last_name | varchar(100) | Applicant's last name |
| first_name | varchar(100) | Applicant's first name |
| middle_initial | varchar(5) | Middle initial (optional) |
| preferred_name | varchar(100) | Preferred tour name |
| date_of_birth | date | Date of birth |
| gender | enum('male','female') | Gender |
| home_address | text | Complete home address |
| primary_phone | varchar(20) | Primary contact number |
| secondary_phone | varchar(20) | Secondary contact (optional) |
| email | varchar(255) | Email address (unique) |
| emergency_contact_name | varchar(100) | Emergency contact person |
| emergency_contact_relationship | varchar(50) | Relationship to applicant |
| emergency_contact_phone | varchar(20) | Emergency contact number |
| dot_accreditation | varchar(100) | DOT license number (unique) |
| accreditation_expiry | date | DOT license expiry |
| specialization | enum('mountain','waterfall','cultural','adventure','photography') | Primary specialization |
| years_experience | int(3) | Years of experience |
| first_aid_certified | enum('yes','no') | First aid certification status |
| first_aid_expiry | date | First aid certificate expiry |
| base_location | varchar(255) | Primary base location |
| employment_type | enum('full-time','part-time','weekends') | Employment preference |
| has_vehicle | enum('yes','no') | Owns transport vehicle |
| resume_file | varchar(255) | Resume file path |
| dot_id_file | varchar(255) | DOT ID file path |
| government_id_file | varchar(255) | Government ID file path |
| nbi_clearance_file | varchar(255) | NBI clearance file path |
| first_aid_certificate_file | varchar(255) | First aid certificate path |
| id_photo_file | varchar(255) | ID photo file path |
| status | enum('pending','under_review','approved','rejected') | Application status |
| admin_notes | text | Admin review notes |
| review_date | datetime | When application was reviewed |
| reviewed_by | int(11) | Admin user ID who reviewed |

### tour_guide_languages Table
| Field | Type | Description |
|-------|------|-------------|
| id | int(11) | Primary key, auto-increment |
| registration_id | int(11) | Foreign key to registration_tour_guide |
| language | varchar(50) | Language name |
| proficiency | enum('native','fluent','conversational') | Proficiency level |

## Form Fields Supported

### Personal Information
- Last Name, First Name, Middle Initial
- Preferred Name/Alias
- Date of Birth, Gender
- Complete Home Address
- Primary/Secondary Phone Numbers
- Email Address
- Emergency Contact Details

### Professional Qualifications
- DOT Accreditation Number & Expiry
- Languages Spoken (multiple with proficiency)
- Specialization/Expertise
- Years of Experience
- First Aid/CPR Certification & Expiry

### Logistics & Availability
- Primary Base Location
- Employment Type (full-time, part-time, weekends)
- Vehicle Ownership

### Document Uploads
- Resume/CV (PDF, DOC, DOCX - Max 5MB)
- DOT ID (PDF, JPG, PNG - Max 5MB)
- Government ID (PDF, JPG, PNG - Max 5MB)
- NBI Clearance (PDF, JPG, PNG - Max 5MB)
- First Aid Certificate (PDF, JPG, PNG - Max 5MB)
- 2x2 ID Photo (JPG, PNG - Max 2MB)

## File Upload Structure
Uploaded files are stored in: `uploads/tour_guide_documents/`
- Files are renamed with unique prefixes for security
- File paths are stored relative to the project root

## Usage Instructions

### 1. Setup Database Tables
```bash
php create_tour_guide_tables.php
```

### 2. Test the System
```bash
php test_tour_guide_registration.php
```

### 3. Access Admin Interface
Navigate to: `admin/tour_guide_registrations.php`

### 4. Form Integration
The `register-guide.php` form is already configured to work with this system. The form submits to `save_registration_tour_guide.php` which processes the registration.

## Features

### Security
- Input validation and sanitization
- File type and size restrictions
- SQL injection prevention with prepared statements
- Unique email and DOT accreditation constraints

### User Experience
- Multi-step registration form
- Real-time form validation
- File upload progress indication
- Responsive design for mobile devices

### Admin Features
- Dashboard with application statistics
- Status management (pending → under_review → approved/rejected)
- Detailed application view
- Admin notes for review decisions

### Data Management
- Transaction-based registration (all or nothing)
- Foreign key constraints for data integrity
- Comprehensive error handling and logging

## Error Handling
- Database connection failures
- File upload errors
- Invalid form data
- Missing required fields
- Duplicate email/DOT numbers

## Future Enhancements
- Email notifications for status updates
- Export functionality for registrations
- Advanced filtering and search
- Integration with user authentication system
- Automated document verification

## Support
For issues or questions about the tour guide registration system, please check the error logs or contact the development team.
