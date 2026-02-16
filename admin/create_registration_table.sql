CREATE TABLE IF NOT EXISTS registration_tour_guides (
    id int(11) NOT NULL AUTO_INCREMENT,
    status enum('Pending','Approved','Rejected') NOT NULL DEFAULT 'Pending',
    created_at timestamp NOT NULL DEFAULT current_timestamp(),
    updated_at timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    last_name varchar(100) NOT NULL,
    first_name varchar(100) NOT NULL,
    gender enum('male','female') DEFAULT NULL,
    email varchar(255) NOT NULL,
    phone varchar(20) DEFAULT NULL,
    specialty varchar(100) DEFAULT NULL,
    experience_years int DEFAULT 0,
    resume_url varchar(500) DEFAULT NULL,
    cover_letter varchar(1000) DEFAULT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
