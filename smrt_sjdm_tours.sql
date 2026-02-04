-- SJDM Tours Database - Complete Structure and Data
-- Import this file to set up the complete database with all tables and data

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS `smrt_sjdm_tours` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `smrt_sjdm_tours`;

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `guide_id` int(11) NULL,
    `tour_name` varchar(200) NOT NULL,
    `destination` varchar(200) NULL,
    `booking_date` date NOT NULL,
    `check_in_date` date NULL,
    `check_out_date` date NULL,
    `number_of_people` int(11) NOT NULL,
    `contact_number` varchar(50) NULL,
    `email` varchar(255) NULL,
    `special_requests` text NULL,
    `total_amount` decimal(10, 2) NOT NULL,
    `payment_method` enum(
        'pay_later',
        'gcash',
        'bank_transfer'
    ) DEFAULT 'pay_later',
    `status` enum(
        'pending',
        'confirmed',
        'cancelled',
        'completed'
    ) DEFAULT 'pending',
    `booking_reference` varchar(50) NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `homepage_content`
--

CREATE TABLE `homepage_content` (
    `id` int(11) NOT NULL,
    `content_type` enum(
        'hero_title',
        'hero_subtitle',
        'hero_button_text',
        'stat_title',
        'stat_value',
        'section_title'
    ) NOT NULL,
    `content_key` varchar(100) NOT NULL,
    `content_value` text NOT NULL,
    `display_order` int(11) DEFAULT 0,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `homepage_content`
--

INSERT INTO
    `homepage_content` (
        `id`,
        `content_type`,
        `content_key`,
        `content_value`,
        `display_order`,
        `status`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'hero_title',
        'main_title',
        'Welcome to San Jose del Monte, Bulacan',
        1,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        2,
        'hero_subtitle',
        'main_subtitle',
        'The Balcony of Metropolis - Where Nature Meets Progress',
        2,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        3,
        'hero_button_text',
        'main_button',
        'Find Your Guide',
        3,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        4,
        'section_title',
        'featured_destinations',
        'Featured Destinations',
        4,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        5,
        'section_title',
        'why_visit',
        'Why Visit San Jose del Monte?',
        5,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        6,
        'stat_title',
        'natural_attractions',
        'Natural Attractions',
        1,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        7,
        'stat_value',
        'natural_attractions',
        '10+',
        1,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        8,
        'stat_title',
        'travel_time',
        'From Metro Manila',
        2,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        9,
        'stat_value',
        'travel_time',
        '30 min',
        2,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        10,
        'stat_title',
        'climate',
        'Perfect Climate',
        3,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    ),
    (
        11,
        'stat_value',
        'climate',
        'Year-round',
        3,
        'active',
        '2026-02-03 10:57:00',
        '2026-02-03 10:57:00'
    );

-- --------------------------------------------------------

--
-- Table structure for table `hotels`
--

CREATE TABLE `hotels` (
    `id` int(11) NOT NULL,
    `name` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `category` enum(
        'luxury',
        'mid-range',
        'budget',
        'event'
    ) NOT NULL,
    `location` varchar(200) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `contact_info` varchar(200) DEFAULT NULL,
    `website` varchar(200) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `price_range` varchar(100) DEFAULT NULL,
    `rating` decimal(3, 2) DEFAULT 0.00,
    `review_count` int(11) DEFAULT 0,
    `amenities` text DEFAULT NULL,
    `services` text DEFAULT NULL,
    `image_url` varchar(500) DEFAULT NULL,
    `latitude` decimal(10, 8) DEFAULT NULL,
    `longitude` decimal(11, 8) DEFAULT NULL,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `hotels`
--

INSERT INTO
    `hotels` (
        `id`,
        `name`,
        `description`,
        `category`,
        `location`,
        `address`,
        `contact_info`,
        `website`,
        `email`,
        `phone`,
        `price_range`,
        `rating`,
        `review_count`,
        `amenities`,
        `services`,
        `image_url`,
        `latitude`,
        `longitude`,
        `status`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'Hotel Sogo',
        'Conveniently located budget hotel offering comfortable rooms with essential amenities for travelers visiting San Jose del Monte.',
        'budget',
        'City Center',
        'Maharlika Highway, San Jose del Monte City, Bulacan',
        '+63 2 8888 8888',
        'https://www.hotelsogo.com/',
        'info.sogo@hotelsogo.com',
        '+63 917 888 8888',
        '₱800 - ₱1,500 per night',
        4.20,
        234,
        'Air conditioning, TV, Hot shower, Free WiFi, 24/7 front desk',
        NULL,
        'https://images.unsplash.com/photo-1566073771259-6a8506099945?q=80&w=2070&auto=format&fit=crop',
        14.80000000,
        121.05000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        2,
        'Hotel Turista',
        'Mid-range hotel providing comfortable accommodations with modern amenities perfect for business and leisure travelers.',
        'mid-range',
        'City Proper',
        'Tungkong Mangga, San Jose del Monte City, Bulacan',
        '+63 2 7777 7777',
        'https://www.hotelturista.com.ph/',
        'info@hotelturista.com.ph',
        '+63 917 777 7777',
        '₱1,500 - ₱3,000 per night',
        4.50,
        189,
        'Air conditioning, TV, Mini fridge, Free WiFi, Restaurant',
        NULL,
        'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?q=80&w=2070&auto=format&fit=crop',
        14.81000000,
        121.06000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        3,
        'Staycation Amaia',
        'Modern residential-style accommodation offering extended stay options with home-like amenities and facilities.',
        'mid-range',
        'Tungkong Mangga',
        'Amaia Scapes, San Jose del Monte City, Bulacan',
        '+63 2 6666 6666',
        'https://www.amaialand.com/',
        'sjdm@amaialand.com',
        '+63 917 666 6666',
        '₱2,000 - ₱4,000 per night',
        4.60,
        156,
        'Kitchenette, Living area, Bedroom, Free WiFi, Swimming pool',
        NULL,
        'https://images.unsplash.com/photo-1580587771525-78b9dba3b914?q=80&w=2070&auto=format&fit=crop',
        14.82000000,
        121.07000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        4,
        'Los Arcos',
        'Resort-style accommodation with swimming pools and event facilities, perfect for families and group gatherings.',
        'luxury',
        'Paradise Area',
        'Paradise 3, San Jose del Monte City, Bulacan',
        '+63 2 5555 5555',
        'https://www.losarcoresort.com/',
        'info@losarcoresort.com',
        '+63 917 555 5555',
        '₱3,000 - ₱6,000 per night',
        4.70,
        298,
        'Swimming pools, Restaurant, Function rooms, Free WiFi, Parking',
        NULL,
        'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?q=80&w=2070&auto=format&fit=crop',
        14.83000000,
        121.08000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        5,
        'Pacific Waves Resort',
        'Beach-themed resort offering water activities and relaxation facilities in San Jose del Monte.',
        'luxury',
        'Paradise Area',
        'Paradise 3, San Jose del Monte City, Bulacan',
        '+63 2 4444 4444',
        'https://www.pacificwavesresort.com/',
        'info@pacificwavesresort.com',
        '+63 917 444 4444',
        '₱3,500 - ₱7,000 per night',
        4.80,
        312,
        'Water park, Restaurant, Cabanas, Free WiFi, Parking',
        NULL,
        'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?q=80&w=2070&auto=format&fit=crop',
        14.84000000,
        121.09000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        6,
        'Local Lodges Paradise 3',
        'Budget-friendly local accommodations offering authentic SJDM experience near tourist attractions.',
        'budget',
        'Paradise 3 Area',
        'Paradise 3, San Jose del Monte City, Bulacan',
        '+63 2 3333 3333',
        NULL,
        NULL,
        '+63 917 333 3333',
        '₱600 - ₱1,200 per night',
        3.80,
        89,
        'Basic room, Fan/AC, Shared bathroom, Common area',
        NULL,
        'https://images.unsplash.com/photo-1566665797739-1674de7a421a?q=80&w=2070&auto=format&fit=crop',
        14.85000000,
        121.10000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        7,
        "Escobar's",
        'Popular local restaurant serving authentic Filipino cuisine and comfort food in a cozy atmosphere.',
        'mid-range',
        'City Center',
        'Maharlika Highway, San Jose del Monte City, Bulacan',
        '+63 2 2222 2222',
        'https://www.escobarsrestaurant.com/',
        'info@escobarsrestaurant.com',
        '+63 917 222 2222',
        '₱200 - ₱500 per meal',
        4.40,
        445,
        'Air conditioning, Private rooms, Parking, Free WiFi',
        NULL,
        'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?q=80&w=2070&auto=format&fit=crop',
        14.86000000,
        121.11000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        8,
        'Roadside Dampa',
        'Casual roadside eatery offering fresh seafood and grilled specialties at affordable prices.',
        'budget',
        'City Center',
        'Along Maharlika Highway, San Jose del Monte City, Bulacan',
        '+63 2 1111 1111',
        NULL,
        NULL,
        '+63 917 111 1111',
        '₱100 - ₱300 per meal',
        4.10,
        267,
        'Open-air seating, Grilling station, Parking',
        NULL,
        'https://images.unsplash.com/photo-1504674900247-0877df9cc836?q=80&w=2070&auto=format&fit=crop',
        14.87000000,
        121.12000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        9,
        "Max's SM SJDM",
        'Well-known restaurant chain serving classic Filipino dishes and fried chicken in SM City San Jose del Monte.',
        'mid-range',
        'Tungkong Mangga',
        'SM City San Jose del Monte, Bulacan',
        '+63 2 9999 9999',
        'https://www.maxsrestaurant.com/',
        'sm.sjdm@maxsrestaurant.com',
        '+63 917 999 9999',
        '₱250 - ₱600 per meal',
        4.30,
        523,
        'Air conditioning, Family-friendly, Parking, Free WiFi',
        NULL,
        'https://images.unsplash.com/photo-1563906267048-b0e351b2f5c9?q=80&w=2070&auto=format&fit=crop',
        14.88000000,
        121.13000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    ),
    (
        10,
        'Local Carinderias',
        'Collection of local eateries serving authentic home-cooked Filipino meals at budget-friendly prices.',
        'budget',
        'Various Areas',
        'Multiple locations in San Jose del Monte City',
        'Various contacts',
        NULL,
        NULL,
        'Various numbers',
        '₱50 - ₱150 per meal',
        3.90,
        178,
        'Simple seating, Local atmosphere, Affordable prices',
        NULL,
        'https://images.unsplash.com/photo-1506257266358-2ed037b69a05?q=80&w=2070&auto=format&fit=crop',
        14.89000000,
        121.14000000,
        'active',
        '2026-02-04 16:00:00',
        '2026-02-04 16:00:00'
    );

-- --------------------------------------------------------

--
-- Table structure for table `login_activity`
--

CREATE TABLE `login_activity` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
    `ip_address` varchar(45) DEFAULT NULL,
    `user_agent` varchar(255) DEFAULT NULL,
    `status` enum('success', 'failed') DEFAULT 'success'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_tours`
--

CREATE TABLE `saved_tours` (
    `id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `tour_name` varchar(200) NOT NULL,
    `tour_description` text DEFAULT NULL,
    `saved_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tourist_spots`
--

CREATE TABLE `tourist_spots` (
    `id` int(11) NOT NULL,
    `name` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `category` enum(
        'nature',
        'historical',
        'religious',
        'farm',
        'park',
        'urban'
    ) NOT NULL,
    `location` varchar(200) DEFAULT NULL,
    `address` text DEFAULT NULL,
    `operating_hours` varchar(100) DEFAULT NULL,
    `entrance_fee` varchar(100) DEFAULT NULL,
    `difficulty_level` enum(
        'easy',
        'moderate',
        'difficult'
    ) DEFAULT 'moderate',
    `duration` varchar(100) DEFAULT NULL,
    `best_time_to_visit` varchar(100) DEFAULT NULL,
    `activities` text DEFAULT NULL,
    `amenities` text DEFAULT NULL,
    `contact_info` varchar(200) DEFAULT NULL,
    `website` varchar(200) DEFAULT NULL,
    `image_url` varchar(500) DEFAULT NULL,
    `latitude` decimal(10, 8) DEFAULT NULL,
    `longitude` decimal(11, 8) DEFAULT NULL,
    `rating` decimal(3, 2) DEFAULT 0.00,
    `review_count` int(11) DEFAULT 0,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `tourist_spots`
--

INSERT INTO
    `tourist_spots` (
        `id`,
        `name`,
        `description`,
        `category`,
        `location`,
        `address`,
        `operating_hours`,
        `entrance_fee`,
        `difficulty_level`,
        `duration`,
        `best_time_to_visit`,
        `activities`,
        `amenities`,
        `contact_info`,
        `website`,
        `image_url`,
        `latitude`,
        `longitude`,
        `rating`,
        `review_count`,
        `status`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'Mt. Balagbag',
        'Popular hiking destination in San Jose del Monte with stunning views of the Sierra Madre mountains. Features challenging trails and rewarding summit experiences.',
        'nature',
        'San Jose del Monte',
        'Barangay Kaytitinga, San Jose del Monte City',
        '4:00 AM - 6:00 PM',
        '₱50 per person',
        'difficult',
        '4-6 hours',
        'Early morning (4-6 AM)',
        'Hiking, Photography, Nature observation',
        'Rest areas, Basic toilets',
        'Local guides available',
        NULL,
        'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?q=80&w=2070&auto=format&fit=crop',
        NULL,
        NULL,
        4.80,
        245,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        2,
        'Kaytitinga Falls',
        'Beautiful waterfall with natural swimming pools and lush surroundings. Perfect for swimming and picnics.',
        'nature',
        'Kaytitinga',
        'Barangay Kaytitinga, San Jose del Monte City',
        '6:00 AM - 5:00 PM',
        '₱30 per person',
        'moderate',
        '3-5 hours',
        'Morning to afternoon',
        'Swimming, Picnicking, Photography',
        'Changing areas, Picnic tables',
        'Local guides available',
        NULL,
        'https://images.unsplash.com/photo-1509316785289-025f5b846b35?q=80&w=2076&auto=format&fit=crop',
        NULL,
        NULL,
        4.60,
        189,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        3,
        'Abes Farm',
        'Organic farm offering educational tours, animal interactions, and fresh produce. Great for family visits.',
        'farm',
        'San Jose del Monte',
        'Various locations in SJDM',
        '8:00 AM - 5:00 PM',
        '₱150 per person',
        'easy',
        '2-3 hours',
        'Weekends',
        'Farm tour, Animal feeding, Organic farming education',
        'Restaurant, Gift shop, Rest areas',
        'Contact farm directly',
        NULL,
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop',
        NULL,
        NULL,
        4.70,
        156,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        4,
        'City Oval (People\'s Park)',
        'Central park in SJDM perfect for jogging, picnics, and family gatherings. Features playground and sports facilities.',
        'park',
        'City Proper',
        'City Oval, San Jose del Monte City',
        '5:00 AM - 10:00 PM',
        'Free',
        'easy',
        '1-3 hours',
        'Morning or evening',
        'Jogging, Picnicking, Sports, Playground',
        'Playground, Sports courts, Benches',
        'City Parks Office',
        NULL,
        'https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop',
        NULL,
        NULL,
        4.30,
        98,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        5,
        'The Rising Heart Monument',
        'Iconic heart-shaped monument symbolizing SJDM\'s growth and progress. Popular photo spot with night illumination.',
        'urban',
        'City Proper',
        'Along Maharlika Highway, San Jose del Monte City',
        '24/7',
        'Free',
        'easy',
        '30 minutes - 1 hour',
        'Evening for illumination',
        'Photography, Sightseeing',
        'Parking, Viewing areas',
        'City Tourism Office',
        NULL,
        'https://images.unsplash.com/photo-1518709268805-4e9042af2176?q=80&w=2068&auto=format&fit=crop',
        NULL,
        NULL,
        4.50,
        203,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        6,
        'Our Lady of Lourdes Parish',
        'Historic church with beautiful architecture and religious significance. Popular for pilgrimages and weddings.',
        'religious',
        'City Proper',
        'Lourdes Subdivision, San Jose del Monte City',
        '5:00 AM - 7:00 PM',
        'Free',
        'easy',
        '1-2 hours',
        'Weekends, Feast days',
        'Religious services, Pilgrimage, Photography',
        'Parking, Rest areas',
        'Parish Office',
        NULL,
        'https://images.unsplash.com/photo-1544919982-b61976a0d7ed?q=80&w=2069&auto=format&fit=crop',
        NULL,
        NULL,
        4.40,
        134,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        7,
        'Otso-Otso Falls',
        'Secluded waterfall with crystal clear pools and natural rock formations. Requires some hiking to reach.',
        'nature',
        'San Jose del Monte',
        'Barangay areas, San Jose del Monte City',
        '6:00 AM - 4:00 PM',
        '₱150 per person',
        'moderate',
        '4-6 hours',
        'Morning',
        'Swimming, Hiking, Photography',
        'Basic facilities at base',
        'Local guides recommended',
        NULL,
        'https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop',
        NULL,
        NULL,
        4.70,
        167,
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        8,
        'Tungtong Falls',
        'Spectacular 25-meter waterfall located within a dramatic canyon formation in San Jose del Monte. Named after the local term "tungtong" meaning "to climb or ascend," this waterfall features a unique rock amphitheater that creates perfect acoustics and breathtaking visual effects when sunlight hits the cascading water.',
        'nature',
        'San Jose del Monte',
        'Barangay areas, San Jose del Monte City',
        '7:00 AM - 5:00 PM',
        '₱100 per person',
        'moderate',
        '3-4 hours',
        'November to May',
        'Hiking, Swimming, Photography, Nature observation',
        'Natural pools, Rest areas, Picnic spots',
        'Local guides available',
        'https://www.facebook.com/TungtongFallsSJDM',
        'https://images.unsplash.com/photo-1511884642898-4c92249e20b6?q=80&w=2070&auto=format&fit=crop',
        14.81250000,
        121.05000000,
        4.70,
        142,
        'active',
        '2026-02-01 10:46:42',
        '2026-02-01 10:46:42'
    ),
    (
        9,
        'Padre Pio Parish',
        'Modern Catholic church dedicated to Saint Pio of Pietrelcina (Padre Pio). Features contemporary architecture with traditional religious elements. Popular for masses, baptisms, and community events.',
        'religious',
        'San Jose del Monte',
        'Various barangays in SJDM',
        '5:00 AM - 7:00 PM',
        'Free',
        'easy',
        '1-2 hours',
        'Weekends, Feast days',
        'Religious services, Community events, Weddings',
        'Parking, Air conditioning, Chapel',
        'Parish Office',
        'https://www.facebook.com/PadrePioParishSJDM',
        'https://images.unsplash.com/photo-1501703384627-e08e60c431c0?q=80&w=2070&auto=format&fit=crop',
        14.80000000,
        121.03330000,
        4.30,
        87,
        'active',
        '2026-02-01 10:46:42',
        '2026-02-01 10:46:42'
    ),
    (
        10,
        'Burong Falls',
        'Hidden gem waterfall tucked away in the mountains of San Jose del Monte. Less crowded than other falls, offering a peaceful natural swimming experience with crystal clear waters and serene surroundings.',
        'nature',
        'San Jose del Monte',
        'Remote barangay areas, San Jose del Monte City',
        '6:00 AM - 4:00 PM',
        '₱80 per person',
        'moderate',
        '4-5 hours',
        'Dry season (November - May)',
        'Swimming, Picnicking, Photography, Meditation',
        'Natural pools, Shaded areas, Basic trail markers',
        'Local guides recommended',
        'https://www.facebook.com/BurongFallsSJDM',
        'https://images.unsplash.com/photo-1551632811-561732d1e306?q=80&w=2070&auto=format&fit=crop',
        14.82500000,
        121.06670000,
        4.60,
        93,
        'active',
        '2026-02-01 10:46:42',
        '2026-02-01 10:46:42'
    ),
    (
        11,
        'Paradise Hill Farm',
        'Scenic hillside farm resort offering panoramic views of SJDM valley. Features accommodation, restaurant, event spaces, and agricultural tours. Perfect for retreats, team buildings, and family getaways.',
        'farm',
        'San Jose del Monte',
        'Hilltop location, San Jose del Monte City',
        '7:00 AM - 8:00 PM',
        '₱200 per person (day tour)',
        'easy',
        '4-6 hours',
        'Year-round (cooler in morning/evening)',
        'Farm tour, Animal interactions, Meals, Events',
        'Restaurant, Accommodation, Event halls, Gift shop',
        '+63 912 345 6789',
        'https://www.paradisehillfarm.com',
        'https://images.unsplash.com/photo-1500382017468-9049fed747ef?q=80&w=2070&auto=format&fit=crop',
        14.85000000,
        121.08330000,
        4.80,
        201,
        'active',
        '2026-02-01 10:46:42',
        '2026-02-01 10:46:42'
    );

-- --------------------------------------------------------

--
-- Table structure for table `tour_guides` (UPDATED - price_range removed)
--

CREATE TABLE `tour_guides` (
    `id` int(11) NOT NULL,
    `name` varchar(100) NOT NULL,
    `specialty` varchar(200) DEFAULT NULL,
    `category` enum(
        'mountain',
        'city',
        'farm',
        'waterfall',
        'historical',
        'general'
    ) NOT NULL,
    `description` text DEFAULT NULL,
    `bio` text DEFAULT NULL,
    `areas_of_expertise` text DEFAULT NULL,
    `rating` decimal(3, 2) DEFAULT 0.00,
    `review_count` int(11) DEFAULT 0,
    `languages` varchar(200) DEFAULT NULL,
    `contact_number` varchar(20) DEFAULT NULL,
    `email` varchar(100) DEFAULT NULL,
    `schedules` text DEFAULT NULL,
    `experience_years` int(11) DEFAULT NULL,
    `group_size` varchar(50) DEFAULT NULL,
    `verified` tinyint(1) DEFAULT 0,
    `total_tours` int(11) DEFAULT 0,
    `photo_url` varchar(500) DEFAULT NULL,
    `status` enum('active', 'inactive') DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `tour_guides` (UPDATED - price_range removed)
--

INSERT INTO
    `tour_guides` (
        `id`,
        `name`,
        `specialty`,
        `category`,
        `description`,
        `bio`,
        `areas_of_expertise`,
        `rating`,
        `review_count`,
        `languages`,
        `contact_number`,
        `email`,
        `schedules`,
        `experience_years`,
        `group_size`,
        `verified`,
        `total_tours`,
        `photo_url`,
        `status`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'Rico Mendoza',
        'Mt. Balagbag Hiking Expert',
        'mountain',
        'Certified mountain guide with 10 years of experience leading Mt. Balagbag expeditions. Safety-first approach with extensive knowledge of local trails.',
        'Rico is a born and raised SJDM local who has been exploring Mt. Balagbag since childhood. As a certified mountaineer and wilderness first responder, he ensures safe and memorable hiking experiences. He\'s passionate about environmental conservation and educating visitors about the Sierra Madre ecosystem.',
        'Mt. Balagbag, Tuntong Falls, Mountain trails',
        5.00,
        127,
        'English, Tagalog',
        '+63 917 123 4567',
        'rico.mendoza@sjdmguide.ph',
        'Available: Daily (4 AM - 12 PM)',
        10,
        '1-15 hikers',
        1,
        450,
        'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?q=80&w=2070&auto=format&fit=crop',
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        2,
        'Maria Santos',
        'City Tour Specialist',
        'city',
        'Knowledgeable local guide specializing in SJDM city tours and historical landmarks. Great storyteller with deep local connections.',
        'Maria has been guiding tours in San Jose del Monte for 8 years. She loves sharing the rich history and culture of the city with visitors. Her tours are educational, entertaining, and personalized to each group\'s interests.',
        'City proper, Malls, Historical sites, Urban attractions',
        4.80,
        89,
        'English, Tagalog, Spanish',
        '+63 927 987 6543',
        'maria.santos@sjdmguide.ph',
        'Available: Weekdays (9 AM - 5 PM)',
        8,
        '5-20 people',
        1,
        234,
        'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=1888&auto=format&fit=crop',
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        3,
        'Carlos Dela Cruz',
        'Farm and Eco-Tourism Guide',
        'farm',
        'Expert in agricultural tours and eco-tourism experiences. Specializes in organic farming and sustainable practices.',
        'Carlos comes from a family of farmers and has dedicated his career to promoting sustainable agriculture and eco-tourism. He makes learning about farming fun and engaging for visitors of all ages.',
        'Abes Farm, Local farms, Agricultural sites, Eco-tourism locations',
        4.90,
        76,
        'English, Tagalog',
        '+63 937 456 7890',
        'carlos.delacruz@sjdmguide.ph',
        'Available: Weekends (8 AM - 4 PM)',
        6,
        '10-30 people',
        1,
        187,
        'https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=1887&auto=format&fit=crop',
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        4,
        'Ana Reyes',
        'Waterfall Adventure Guide',
        'waterfall',
        'Specialized guide for waterfall tours and outdoor adventures. Focuses on safety and environmental awareness.',
        'Ana is passionate about the natural beauty of SJDM\'s waterfalls. With 7 years of experience, she ensures visitors have safe and memorable experiences while learning about conservation.',
        'Kaytitinga Falls, Otso-Otso Falls, Waterfall trails',
        4.70,
        65,
        'English, Tagalog',
        '+63 947 234 5678',
        'ana.reyes@sjdmguide.ph',
        'Available: Weekends (6 AM - 3 PM)',
        7,
        '8-20 people',
        1,
        156,
        'https://images.unsplash.com/photo-1544161515-4ab6ce6db874?q=80&w=2070&auto=format&fit=crop',
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        5,
        'James Lim',
        'Historical and Cultural Guide',
        'historical',
        'Expert in local history, cultural heritage, and religious sites. Provides in-depth historical context and stories.',
        'James holds a degree in History and has been guiding cultural tours for 9 years. He brings the past to life through engaging storytelling and detailed historical knowledge.',
        'Historical sites, Churches, Museums, Cultural landmarks',
        4.60,
        92,
        'English, Tagalog, Chinese',
        '+63 957 890 1234',
        'james.lim@sjdmguide.ph',
        'Available: Daily (10 AM - 6 PM)',
        9,
        '15-25 people',
        1,
        298,
        'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?q=80&w=1887&auto=format&fit=crop',
        'active',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    );

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
    `id` int(11) NOT NULL,
    `first_name` varchar(50) NOT NULL,
    `last_name` varchar(50) NOT NULL,
    `email` varchar(100) NOT NULL,
    `password` varchar(255) NOT NULL,
    `user_type` enum('user', 'admin') DEFAULT 'user',
    `status` enum(
        'active',
        'inactive',
        'suspended'
    ) DEFAULT 'active',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    `last_login` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO
    `users` (
        `id`,
        `first_name`,
        `last_name`,
        `email`,
        `password`,
        `user_type`,
        `status`,
        `created_at`,
        `updated_at`,
        `last_login`
    )
VALUES (
        1,
        'Admin',
        'SJDM',
        'adminlgu@gmail.com',
        '$2y$10$3DQxAUR/H3QdYWRerZKzMuqCvXoRwVpQDFuiE8SU6BvydNnM5CiBS',
        'admin',
        'active',
        '2026-01-30 15:02:22',
        '2026-02-02 14:56:28',
        '2026-02-02 14:56:28'
    ),
    (
        4,
        'Ian',
        'Jovero',
        'christianbacay042504@gmail.com',
        '$2y$10$pgyID2NX3.S.7QRB1I4GaOWoKrhDwRvN2bwS8xEvNxjlCR8KlM7pO',
        'user',
        'active',
        '2026-01-31 16:00:05',
        '2026-02-02 14:55:23',
        '2026-02-02 14:55:23'
    ),
    (
        5,
        'angel',
        'hernandez',
        'angelhernandez@gmail.com',
        '$2y$10$3Utff.JPzrx6MhyCiN5GUe305KNvbVmM5119XgUh.goaOVIY6p6JK',
        'user',
        'active',
        '2026-02-02 10:00:47',
        '2026-02-02 10:39:38',
        '2026-02-02 10:39:38'
    );

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

ALTER TABLE `bookings`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_user_id` (`user_id`),
ADD KEY `idx_guide_id` (`guide_id`),
ADD KEY `idx_booking_date` (`booking_date`),
ADD KEY `idx_status` (`status`),
ADD KEY `idx_booking_reference` (`booking_reference`);

ALTER TABLE `homepage_content`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_content_type` (`content_type`),
ADD KEY `idx_content_key` (`content_key`),
ADD KEY `idx_status` (`status`);

ALTER TABLE `hotels`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`category`),
ADD KEY `idx_status` (`status`),
ADD KEY `idx_rating` (`rating`);

ALTER TABLE `login_activity`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_user_id` (`user_id`);

ALTER TABLE `saved_tours`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_user_id` (`user_id`);

ALTER TABLE `tourist_spots`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`category`),
ADD KEY `idx_status` (`status`),
ADD KEY `idx_rating` (`rating`);

ALTER TABLE `tour_guides`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`category`),
ADD KEY `idx_status` (`status`),
ADD KEY `idx_rating` (`rating`),
ADD KEY `idx_verified` (`verified`);

ALTER TABLE `users`
ADD PRIMARY KEY (`id`),
ADD UNIQUE KEY `email` (`email`),
ADD KEY `idx_email` (`email`),
ADD KEY `idx_user_type` (`user_type`);

-- --------------------------------------------------------

--
-- AUTO_INCREMENT for dumped tables
--

ALTER TABLE `bookings` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `homepage_content`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

ALTER TABLE `hotels`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 5;

ALTER TABLE `login_activity`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 77;

ALTER TABLE `saved_tours`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `tourist_spots`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 12;

ALTER TABLE `tour_guides`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

ALTER TABLE `users`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

-- --------------------------------------------------------

--
-- Constraints for dumped tables
--

ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
ADD CONSTRAINT `fk_bookings_guide_id` FOREIGN KEY (`guide_id`) REFERENCES `tour_guides` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE `login_activity`
ADD CONSTRAINT `login_activity_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Complete! The SJDM Tours database is now ready for use.
-- Features included:
-- - Homepage content management system
-- - Tour guides without price_range (using price_min/price_max)
-- - Tourist spots with ratings and reviews
-- - Hotels with categories and amenities
-- - User management and authentication
-- - Booking system
-- - Login activity tracking
-- - Saved tours functionality
-- - Hotel booking tips system
-- - Local culture and heritage system
-- - Travel tips and recommendations system
--

-- --------------------------------------------------------

--
-- Table structure for table `travel_tips`
--

CREATE TABLE `travel_tips` (
    `id` int(11) NOT NULL,
    `category` varchar(50) NOT NULL,
    `title` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` enum('yes', 'no') DEFAULT 'yes',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `travel_tips`
--

INSERT INTO
    `travel_tips` (
        `id`,
        `category`,
        `title`,
        `description`,
        `icon`,
        `display_order`,
        `is_active`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'transportation',
        'Getting to SJDM',
        '30-45 minutes from Metro Manila\nVia NLEX - Bocaue Exit\nBuses from Cubao to Bulacan\nPrivate car recommended for tours\nRide-sharing apps available',
        'directions',
        1,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        2,
        'hiking',
        'For Mountain Hikers',
        'Start early (5-6 AM recommended)\nBring at least 2L water per person\nWear proper hiking shoes\nApply sunscreen and insect repellent\nHire local guides for safety',
        'hiking',
        2,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        3,
        'waterfalls',
        'Visiting Waterfalls',
        'Wear water shoes or trekking sandals\nTrails can be muddy and slippery\nBring plastic bags for electronics\nSwimming allowed in designated areas\nFollow Leave No Trace principles',
        'water',
        3,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        4,
        'budget',
        'Budget Planning',
        'Tour guide fees: ₱1,500-3,500/day\nEntrance fees: ₱50-200 per site\nMeals: ₱150-300 per person\nTransportation: ₱500-1,000\nTotal budget: ₱2,500-5,000/person',
        'savings',
        4,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        5,
        'season',
        'Best Time to Visit',
        'November to February - cool weather\nMarch to May - summer, hot but clear\nAvoid July-September rainy season\nWeekdays less crowded\nEarly morning for mountain hikes',
        'event',
        5,
        'yes',
        '2026-02-01 10:35:03',
        '2026-2026-02-01 10:35:03'
    ),
    (
        6,
        'essentials',
        'What to Bring',
        'Comfortable hiking attire\nExtra clothes & towel\nSunscreen & insect repellent\nFirst aid kit & personal meds\nReusable water bottle & snacks',
        'inventory_2',
        6,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        7,
        'local',
        'Local Tips',
        'Mobile signal available in most areas\nATMs available in malls & town centers\nBring cash for entrance fees\nRespect local communities\nAsk permission before taking photos',
        'smartphone',
        7,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        8,
        'safety',
        'Safety Reminders',
        'Always book with licensed guides\nCheck weather before hiking\nStay on marked trails\nDon\'t swim during heavy rain\nEmergency hotline: 911',
        'warning',
        8,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    );

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `travel_tips`
--
ALTER TABLE `travel_tips`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`category`),
ADD KEY `idx_active` (`is_active`),
ADD KEY `idx_order` (`display_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `travel_tips`
--
ALTER TABLE `travel_tips`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 9;

-- --------------------------------------------------------

--
-- Table structure for table `local_culture`
--

CREATE TABLE `local_culture` (
    `id` int(11) NOT NULL,
    `category` varchar(50) NOT NULL,
    `title` varchar(200) NOT NULL,
    `description` text DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` enum('yes', 'no') DEFAULT 'yes',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `local_culture`
--

INSERT INTO
    `local_culture` (
        `id`,
        `category`,
        `title`,
        `description`,
        `icon`,
        `display_order`,
        `is_active`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'identity',
        'City Identity',
        'Balcony of Metropolis nickname\nHighly Urbanized City since 2001\nGateway to Northern Luzon\nBlend of urban and rural life\nGrowing residential communities',
        'location_city',
        1,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        2,
        'industries',
        'Local Industries',
        'Orchid cultivation\nPineapple farming\nReal estate development\nSmall-scale agriculture\nTourism and hospitality',
        'agriculture',
        2,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        3,
        'indigenous',
        'Indigenous Culture',
        'Dumagat communities\nForest conservation practices\nTraditional weaving\nNature-based livelihood\nCultural preservation efforts',
        'diversity_3',
        3,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        4,
        'cuisine',
        'Local Cuisine',
        'Bulacan dishes - Ensaymada, Chicharon\nFresh seafood specialties\nFarm-to-table organic produce\nLocal bakeries and delicacies\nStreet food culture',
        'restaurant',
        4,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        5,
        'festivals',
        'Festivals',
        'City Foundation Day celebrations\nBarangay fiestas throughout the year\nReligious processions\nCommunity cultural events\nModern urban festivals',
        'celebration',
        5,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        6,
        'community',
        'Community Life',
        'Mall culture - SM & Starmall\nGrowing residential communities\nFamily-oriented city\nActive church communities\nSports and recreation focus',
        'groups',
        6,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    );

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `local_culture`
--
ALTER TABLE `local_culture`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`category`),
ADD KEY `idx_active` (`is_active`),
ADD KEY `idx_order` (`display_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `local_culture`
--
ALTER TABLE `local_culture`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 7;

-- --------------------------------------------------------

--
-- Table structure for table `hotel_booking_tips`
--

CREATE TABLE `hotel_booking_tips` (
    `id` int(11) NOT NULL,
    `tip_category` varchar(50) NOT NULL,
    `tip_title` varchar(200) NOT NULL,
    `tip_content` text NOT NULL,
    `tip_icon` varchar(50) DEFAULT NULL,
    `tip_order` int(11) DEFAULT 0,
    `is_active` enum('yes', 'no') DEFAULT 'yes',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `hotel_booking_tips`
--

INSERT INTO
    `hotel_booking_tips` (
        `id`,
        `tip_category`,
        `tip_title`,
        `tip_content`,
        `tip_icon`,
        `tip_order`,
        `is_active`,
        `created_at`,
        `updated_at`
    )
VALUES (
        1,
        'categories',
        'Available Hotel Categories',
        'Luxury Hotels: 1 option available\nMid-Range Hotels: 1 option available\nBudget Hotels: 1 option available\nEvent Venues: 1 option available',
        'location_on',
        1,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        2,
        'pricing',
        'Available Price Ranges',
        'Budget: ₱800 - ₱1,800 per night\nMid-Range: ₱1,500 - ₱3,500 per night\nLuxury: ₱3,000 - ₱8,000 per night\nEvent: ₱5,000 - ₱15,000 per event',
        'payments',
        2,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        3,
        'statistics',
        'Hotel Statistics',
        'Total Hotels: 4 available\nAverage Rating: 4.4/5 stars\nCheck-in Time: 2:00 PM (Standard)\nCheck-out Time: 12:00 PM (Standard)',
        'analytics',
        3,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        4,
        'booking',
        'Booking Advice',
        'Book 1-2 weeks ahead for weekend stays\nConfirm transportation arrangements\nCheck hotel policies on group sizes\nAsk about tour guide coordination services',
        'phone',
        4,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    ),
    (
        5,
        'location',
        'Location Strategy',
        'Nature Tours: Stay in eastern SJDM resorts\nFarm Tours: Choose farm stays or resorts on Paradise Farm Street\nCity Tours: Central hotels like Hotel Savano or budget options\nReligious Tours: Hotels with easy transport to Grotto & Padre Pio',
        'map',
        5,
        'yes',
        '2026-02-01 10:35:03',
        '2026-02-01 10:35:03'
    );

-- --------------------------------------------------------

--
-- Indexes for dumped tables
--

--
-- Indexes for table `hotel_booking_tips`
--
ALTER TABLE `hotel_booking_tips`
ADD PRIMARY KEY (`id`),
ADD KEY `idx_category` (`tip_category`),
ADD KEY `idx_active` (`is_active`),
ADD KEY `idx_order` (`tip_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `hotel_booking_tips`
--
ALTER TABLE `hotel_booking_tips`
MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,
AUTO_INCREMENT = 6;

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `admin_mark` char(1) NOT NULL DEFAULT 'A',
    `role_title` varchar(100) DEFAULT 'Administrator',
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO
    `admins` (
        `id`,
        `user_id`,
        `admin_mark`,
        `role_title`
    )
VALUES (
        1,
        1,
        'A',
        'System Administrator'
    );

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_settings`
--

CREATE TABLE `dashboard_settings` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `setting_key` varchar(50) NOT NULL,
    `setting_value` text NOT NULL,
    `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `idx_key` (`setting_key`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `dashboard_settings`
--

INSERT INTO
    `dashboard_settings` (
        `setting_key`,
        `setting_value`
    )
VALUES (
        'page_title',
        'Dashboard Overview'
    ),
    (
        'page_subtitle',
        'System statistics and analytics'
    ),
    (
        'admin_logo_text',
        'SJDM ADMIN'
    );

-- --------------------------------------------------------

--
-- Table structure for table `dashboard_widgets`
--

CREATE TABLE `dashboard_widgets` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `widget_type` varchar(50) NOT NULL DEFAULT 'stat',
    `title` varchar(100) NOT NULL,
    `subtitle` varchar(255) DEFAULT NULL,
    `icon` varchar(50) DEFAULT NULL,
    `color_class` varchar(20) DEFAULT NULL,
    `query_key` varchar(50) NOT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `dashboard_widgets`
--

INSERT INTO
    `dashboard_widgets` (
        `title`,
        `subtitle`,
        `icon`,
        `color_class`,
        `query_key`,
        `display_order`
    )
VALUES (
        'Total Users',
        'Registered users in system',
        'people',
        'blue',
        'totalUsers',
        1
    ),
    (
        'Active Users',
        'Currently active accounts',
        'check_circle',
        'green',
        'activeUsers',
        2
    ),
    (
        'Total Bookings',
        'All-time bookings',
        'event',
        'orange',
        'totalBookings',
        3
    ),
    (
        'Today\'s Logins',
        'Successful login attempts',
        'login',
        'purple',
        'todayLogins',
        4
    ),
    (
        'Tour Guides',
        'Available tour guides',
        'tour',
        'teal',
        'totalGuides',
        5
    ),
    (
        'Destinations',
        'Tourist spots available',
        'landscape',
        'pink',
        'totalDestinations',
        6
    ),
    (
        'Hotels',
        'Available accommodations',
        'hotel',
        'yellow',
        'totalHotels',
        7
    ),
    (
        'Pending Bookings',
        'Awaiting confirmation',
        'pending_actions',
        'red',
        'pendingBookings',
        8
    ),
    (
        'Monthly Revenue',
        'This month\'s earnings',
        'payments',
        'green',
        'monthlyRevenue',
        9
    );

-- --------------------------------------------------------

--
-- Table structure for table `admin_menu`
--

CREATE TABLE `admin_menu` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(50) NOT NULL,
    `icon` varchar(50) NOT NULL,
    `link` varchar(255) NOT NULL,
    `badge_query` varchar(50) DEFAULT NULL,
    `display_order` int(11) DEFAULT 0,
    `is_active` tinyint(1) DEFAULT 1,
    PRIMARY KEY (`id`)
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_menu`
--

INSERT INTO
    `admin_menu` (
        `title`,
        `icon`,
        `link`,
        `badge_query`,
        `display_order`
    )
VALUES (
        'Dashboard',
        'dashboard',
        'dashboard.php',
        NULL,
        1
    ),
    (
        'User Management',
        'people',
        'user-management.php',
        'totalUsers',
        2
    ),
    (
        'Tour Guides',
        'tour',
        'tour-guides.php',
        NULL,
        3
    ),
    (
        'Destinations',
        'place',
        'destinations.php',
        NULL,
        4
    ),
    (
        'Hotels',
        'hotel',
        'hotels.php',
        NULL,
        5
    ),
    (
        'Bookings',
        'event',
        'bookings.php',
        'totalBookings',
        6
    ),
    (
        'Analytics',
        'analytics',
        'analytics.php',
        NULL,
        7
    ),
    (
        'Reports',
        'description',
        'reports.php',
        NULL,
        8
    ),
    (
        'Settings',
        'settings',
        'settings.php',
        NULL,
        9
    );

-- --------------------------------------------------------

--
-- Constraints for dumped tables (Updated)
--

ALTER TABLE `admins`
ADD CONSTRAINT `admins_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Complete! The SJDM Tours database is now even more dynamic.
-- Features added:
-- - Admin specialized table with 'A' mark
-- - Dynamic dashboard settings (title, logo)
-- - Dynamic dashboard widgets
-- - Dynamic admin sidebar menu
--