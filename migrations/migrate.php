<?php
/**
 * Database Migration - Vastu Samiksha
 * Run: php migrations/migrate.php
 */

require_once __DIR__ . '/../config/database.php';

echo "=== Vastu Samiksha Database Migration ===\n\n";

try {
    // Create database if not exists
    $pdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    echo "✓ Database created/selected\n";

    // Users
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `password` VARCHAR(255) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `dob` DATE DEFAULT NULL,
        `gender` ENUM('male','female','other') DEFAULT NULL,
        `avatar` VARCHAR(255) DEFAULT NULL,
        `role` ENUM('user','consultant') DEFAULT 'user',
        `is_active` TINYINT(1) DEFAULT 1,
        `last_login` DATETIME DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_role (`role`),
        INDEX idx_email (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ users table\n";

    // Blog categories
    $pdo->exec("CREATE TABLE IF NOT EXISTS `blog_categories` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `name_hi` VARCHAR(100) DEFAULT NULL,
        `slug` VARCHAR(100) NOT NULL UNIQUE,
        `description` TEXT DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ blog_categories table\n";

    // Blogs
    $pdo->exec("CREATE TABLE IF NOT EXISTS `blogs` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `title_hi` VARCHAR(255) DEFAULT NULL,
        `slug` VARCHAR(255) NOT NULL UNIQUE,
        `content` LONGTEXT NOT NULL,
        `content_hi` LONGTEXT DEFAULT NULL,
        `excerpt` TEXT DEFAULT NULL,
        `excerpt_hi` TEXT DEFAULT NULL,
        `image` VARCHAR(255) DEFAULT NULL,
        `category_id` INT UNSIGNED DEFAULT NULL,
        `author_id` INT UNSIGNED NOT NULL,
        `status` ENUM('draft','published') DEFAULT 'draft',
        `views` INT UNSIGNED DEFAULT 0,
        `meta_title` VARCHAR(255) DEFAULT NULL,
        `meta_description` TEXT DEFAULT NULL,
        `meta_keywords` VARCHAR(255) DEFAULT NULL,
        `published_at` DATETIME DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`category_id`) REFERENCES `blog_categories`(`id`) ON DELETE SET NULL,
        FOREIGN KEY (`author_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX idx_status (`status`),
        INDEX idx_slug (`slug`),
        FULLTEXT idx_search (`title`, `content`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ blogs table\n";

    // Videos
    $pdo->exec("CREATE TABLE IF NOT EXISTS `videos` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `title_hi` VARCHAR(255) DEFAULT NULL,
        `youtube_url` VARCHAR(500) NOT NULL,
        `youtube_id` VARCHAR(20) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `description_hi` TEXT DEFAULT NULL,
        `thumbnail` VARCHAR(255) DEFAULT NULL,
        `category` VARCHAR(50) DEFAULT 'general',
        `status` ENUM('draft','published') DEFAULT 'published',
        `views` INT UNSIGNED DEFAULT 0,
        `duration` VARCHAR(10) DEFAULT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_status (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ videos table\n";

    // Ebooks
    $pdo->exec("CREATE TABLE IF NOT EXISTS `ebooks` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `title_hi` VARCHAR(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `description_hi` TEXT DEFAULT NULL,
        `file_path` VARCHAR(255) NOT NULL,
        `cover_image` VARCHAR(255) DEFAULT NULL,
        `pages` INT UNSIGNED DEFAULT NULL,
        `is_free` TINYINT(1) DEFAULT 1,
        `downloads` INT UNSIGNED DEFAULT 0,
        `status` ENUM('draft','published') DEFAULT 'published',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ ebooks table\n";

    // Submissions (user requirements)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `submissions` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) DEFAULT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `dob` DATE DEFAULT NULL,
        `time_of_birth` TIME DEFAULT NULL,
        `gender` ENUM('male','female','other') DEFAULT NULL,
        `property_type` ENUM('house','flat','commercial','plot') DEFAULT NULL,
        `area_sqft` INT UNSIGNED DEFAULT NULL,
        `floors` INT UNSIGNED DEFAULT NULL,
        `year_built` YEAR DEFAULT NULL,
        `facing_direction` VARCHAR(20) DEFAULT NULL,
        `entrance_direction` VARCHAR(20) DEFAULT NULL,
        `latitude` DECIMAL(10,8) DEFAULT NULL,
        `longitude` DECIMAL(11,8) DEFAULT NULL,
        `address` TEXT DEFAULT NULL,
        `specific_concerns` TEXT DEFAULT NULL,
        `floor_plan` VARCHAR(255) DEFAULT NULL,
        `photos` JSON DEFAULT NULL,
        `additional_notes` TEXT DEFAULT NULL,
        `numerology_request` TEXT DEFAULT NULL,
        `status` ENUM('pending','in_progress','completed') DEFAULT 'pending',
        `priority` ENUM('normal','high','urgent') DEFAULT 'normal',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX idx_status (`status`),
        INDEX idx_user (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ submissions table\n";

    // Consultations (consultant responses)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `consultations` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `submission_id` INT UNSIGNED NOT NULL,
        `consultant_id` INT UNSIGNED NOT NULL,
        `analysis` LONGTEXT DEFAULT NULL,
        `remedies` LONGTEXT DEFAULT NULL,
        `numerology_report` LONGTEXT DEFAULT NULL,
        `report_file` VARCHAR(255) DEFAULT NULL,
        `priority` ENUM('normal','important','critical') DEFAULT 'normal',
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`submission_id`) REFERENCES `submissions`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`consultant_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ consultations table\n";

    // Questions (Q&A)
    $pdo->exec("CREATE TABLE IF NOT EXISTS `questions` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `user_id` INT UNSIGNED NOT NULL,
        `title` VARCHAR(255) NOT NULL,
        `body` TEXT NOT NULL,
        `category` VARCHAR(50) DEFAULT 'general',
        `is_public` TINYINT(1) DEFAULT 1,
        `is_answered` TINYINT(1) DEFAULT 0,
        `views` INT UNSIGNED DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX idx_answered (`is_answered`),
        INDEX idx_public (`is_public`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ questions table\n";

    // Question replies
    $pdo->exec("CREATE TABLE IF NOT EXISTS `question_replies` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `question_id` INT UNSIGNED NOT NULL,
        `user_id` INT UNSIGNED NOT NULL,
        `reply` TEXT NOT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`question_id`) REFERENCES `questions`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ question_replies table\n";

    // Chat messages
    $pdo->exec("CREATE TABLE IF NOT EXISTS `chat_messages` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `sender_id` INT UNSIGNED NOT NULL,
        `receiver_id` INT UNSIGNED NOT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`sender_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`receiver_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
        INDEX idx_conversation (`sender_id`, `receiver_id`),
        INDEX idx_unread (`receiver_id`, `is_read`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ chat_messages table\n";

    // Quizzes
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quizzes` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `title` VARCHAR(255) NOT NULL,
        `title_hi` VARCHAR(255) DEFAULT NULL,
        `description` TEXT DEFAULT NULL,
        `description_hi` TEXT DEFAULT NULL,
        `time_limit` INT UNSIGNED DEFAULT 300,
        `cover_image` VARCHAR(255) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ quizzes table\n";

    // Quiz questions
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_questions` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `quiz_id` INT UNSIGNED NOT NULL,
        `question` TEXT NOT NULL,
        `question_hi` TEXT DEFAULT NULL,
        `options` JSON NOT NULL,
        `options_hi` JSON DEFAULT NULL,
        `correct_answer` INT UNSIGNED NOT NULL,
        `explanation` TEXT DEFAULT NULL,
        `explanation_hi` TEXT DEFAULT NULL,
        `points` INT UNSIGNED DEFAULT 10,
        `sort_order` INT UNSIGNED DEFAULT 0,
        FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ quiz_questions table\n";

    // Quiz attempts
    $pdo->exec("CREATE TABLE IF NOT EXISTS `quiz_attempts` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `quiz_id` INT UNSIGNED NOT NULL,
        `user_id` INT UNSIGNED DEFAULT NULL,
        `player_name` VARCHAR(100) DEFAULT NULL,
        `score` INT UNSIGNED DEFAULT 0,
        `total_points` INT UNSIGNED DEFAULT 0,
        `answers` JSON DEFAULT NULL,
        `time_taken` INT UNSIGNED DEFAULT 0,
        `completed_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`quiz_id`) REFERENCES `quizzes`(`id`) ON DELETE CASCADE,
        INDEX idx_score (`quiz_id`, `score` DESC)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ quiz_attempts table\n";

    // Testimonials
    $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `location` VARCHAR(100) DEFAULT NULL,
        `content` TEXT NOT NULL,
        `content_hi` TEXT DEFAULT NULL,
        `rating` TINYINT UNSIGNED DEFAULT 5,
        `avatar` VARCHAR(255) DEFAULT NULL,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ testimonials table\n";

    // Contact messages
    $pdo->exec("CREATE TABLE IF NOT EXISTS `contact_messages` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL,
        `phone` VARCHAR(20) DEFAULT NULL,
        `subject` VARCHAR(255) DEFAULT NULL,
        `message` TEXT NOT NULL,
        `is_read` TINYINT(1) DEFAULT 0,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ contact_messages table\n";

    // Newsletter subscribers
    $pdo->exec("CREATE TABLE IF NOT EXISTS `newsletter_subscribers` (
        `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `is_active` TINYINT(1) DEFAULT 1,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ newsletter_subscribers table\n";

    // Site settings
    $pdo->exec("CREATE TABLE IF NOT EXISTS `site_settings` (
        `skey` VARCHAR(100) PRIMARY KEY,
        `svalue` TEXT DEFAULT NULL,
        `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "✓ site_settings table\n";

    echo "\n=== Seeding Data ===\n\n";

    // Seed consultant account
    $hash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->exec("INSERT IGNORE INTO `users` (id, name, email, password, role) VALUES 
        (1, 'Samiksha Dubey', 'samiksha@vastusamiksha.com', '$hash', 'consultant')");
    echo "✓ Consultant account (samiksha@vastusamiksha.com / admin123)\n";

    // Seed demo user
    $hash2 = password_hash('user123', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->exec("INSERT IGNORE INTO `users` (id, name, email, password, phone, role) VALUES 
        (2, 'राहुल शर्मा', 'rahul@example.com', '$hash2', '+919876543210', 'user')");
    echo "✓ Demo user (rahul@example.com / user123)\n";

    // Seed blog categories
    $pdo->exec("INSERT IGNORE INTO `blog_categories` (id, name, name_hi, slug) VALUES 
        (1, 'Vastu Tips', 'वास्तु टिप्स', 'vastu-tips'),
        (2, 'Numerology', 'अंक ज्योतिष', 'numerology'),
        (3, 'Home Vastu', 'घर का वास्तु', 'home-vastu'),
        (4, 'Office Vastu', 'ऑफिस वास्तु', 'office-vastu'),
        (5, 'Remedies', 'उपाय', 'remedies'),
        (6, 'Name Correction', 'नाम सुधार', 'name-correction'),
        (7, 'Astrology', 'ज्योतिष', 'astrology'),
        (8, 'Feng Shui', 'फेंग शुई', 'feng-shui')");
    echo "✓ Blog categories seeded\n";

    // Seed blogs
    $pdo->exec("INSERT IGNORE INTO `blogs` (id, title, title_hi, slug, content, content_hi, excerpt, excerpt_hi, category_id, author_id, status, views, published_at) VALUES 
    (1, '10 Essential Vastu Tips for Your Home', '10 आवश्यक वास्तु टिप्स आपके घर के लिए', 'essential-vastu-tips-home', 
    '<h2>Transform Your Home with Vastu Shastra</h2><p>Vastu Shastra, the ancient Indian science of architecture and spatial arrangement, has been guiding homeowners for thousands of years. Here are 10 essential tips that can bring positive energy into your home.</p><h3>1. Main Entrance Direction</h3><p>The main entrance of your home is considered the mouth of energy. Ideally, it should face North, East, or North-East. These directions invite positive energy, prosperity, and good health into your home. Avoid South-West facing entrances as they can bring financial difficulties.</p><h3>2. Kitchen Placement</h3><p>The kitchen represents the fire element (Agni). Place your kitchen in the South-East corner of your house. The cook should face East while cooking. Never place the kitchen directly opposite or adjacent to the bathroom.</p><h3>3. Master Bedroom Location</h3><p>The master bedroom should ideally be in the South-West direction. This direction provides stability, strength, and restful sleep. Sleep with your head towards the South for better health and peace of mind.</p><h3>4. Pooja Room Direction</h3><p>The prayer room or pooja room should be in the North-East corner (Ishaan Kon). This direction is associated with water element and spiritual energy. While praying, face East or North for maximum spiritual benefits.</p><h3>5. Living Room Arrangement</h3><p>The living room should be in the North or East part of the house. Heavy furniture should be placed in the South or West walls. Keep the center of the room open and clutter-free to allow energy to flow freely.</p><h3>6. Bathroom Position</h3><p>Bathrooms should be in the North-West or West direction. Never construct a bathroom in the North-East corner as it can adversely affect health and prosperity. Ensure the toilet seat faces North-South direction.</p><h3>7. Water Elements</h3><p>Water features like fountains, aquariums, or water tanks should be placed in the North-East direction. Water represents wealth and prosperity in Vastu. A flowing water feature near the entrance can attract positive energy.</p><h3>8. Colors and Décor</h3><p>Use light, warm colors like cream, light yellow, or light green for walls. Avoid dark colors like black or dark red for bedrooms. Each direction has specific colors that enhance positive energy.</p><h3>9. Plants and Greenery</h3><p>Plant Tulsi (Holy Basil) in the North or North-East of your home. Money plants can be placed in the South-East. Avoid thorny plants like cactus inside the home as they generate negative energy.</p><h3>10. Mirrors Placement</h3><p>Mirrors should be placed on the North or East walls. Never place a mirror directly opposite the bed or the main entrance. A mirror reflecting the dining table is considered auspicious as it doubles abundance.</p><p><strong>Conclusion:</strong> Implementing these Vastu tips can create a harmonious living space that promotes health, wealth, and happiness. For personalized Vastu analysis, book a consultation with Samiksha Dubey today!</p>',
    '<h2>वास्तु शास्त्र से अपने घर को बदलें</h2><p>वास्तु शास्त्र, वास्तुकला और स्थानिक व्यवस्था का प्राचीन भारतीय विज्ञान, हजारों वर्षों से घर मालिकों का मार्गदर्शन कर रहा है। यहां 10 आवश्यक टिप्स हैं जो आपके घर में सकारात्मक ऊर्जा ला सकती हैं।</p><h3>1. मुख्य प्रवेश द्वार की दिशा</h3><p>आपके घर का मुख्य प्रवेश द्वार ऊर्जा का मुख माना जाता है। आदर्श रूप से, यह उत्तर, पूर्व या उत्तर-पूर्व की ओर होना चाहिए।</p><h3>2. रसोई की स्थिति</h3><p>रसोई अग्नि तत्व का प्रतिनिधित्व करती है। अपनी रसोई को घर के दक्षिण-पूर्व कोने में रखें।</p><h3>3. मास्टर बेडरूम</h3><p>मास्टर बेडरूम आदर्श रूप से दक्षिण-पश्चिम दिशा में होना चाहिए।</p><h3>4. पूजा कक्ष</h3><p>पूजा कक्ष उत्तर-पूर्व कोने (ईशान कोण) में होना चाहिए।</p><h3>5. लिविंग रूम</h3><p>लिविंग रूम घर के उत्तर या पूर्व भाग में होना चाहिए।</p>',
    'Discover 10 powerful Vastu Shastra tips to transform your home into a haven of positive energy, prosperity, and happiness.',
    'अपने घर को सकारात्मक ऊर्जा, समृद्धि और खुशी के स्वर्ग में बदलने के लिए 10 शक्तिशाली वास्तु शास्त्र टिप्स खोजें।',
    1, 1, 'published', 234, NOW()),

    (2, 'Understanding Your Life Path Number in Numerology', 'अंक ज्योतिष में अपना जीवन पथ अंक समझें', 'life-path-number-numerology',
    '<h2>What is Your Life Path Number?</h2><p>Your Life Path Number is the most important number in your numerology chart. It reveals your lifes purpose, natural talents, and the challenges you may face. Calculated from your date of birth, it provides deep insights into who you are at your core.</p><h3>How to Calculate Your Life Path Number</h3><p>To calculate your Life Path Number, reduce each component of your birth date to a single digit, then add them together and reduce again.</p><p><strong>Example:</strong> Born on 15th August 1995<br>Day: 1+5 = 6<br>Month: 8<br>Year: 1+9+9+5 = 24 → 2+4 = 6<br>Life Path: 6+8+6 = 20 → 2+0 = <strong>2</strong></p><h3>Life Path Number Meanings</h3><h4>Number 1 - The Leader</h4><p>Independent, ambitious, and pioneering. You are a natural born leader with strong determination. Career fields: Entrepreneurship, management, politics.</p><h4>Number 2 - The Diplomat</h4><p>Sensitive, cooperative, and peace-loving. You excel in partnerships and mediation. Career fields: Counseling, diplomacy, healing arts.</p><h4>Number 3 - The Communicator</h4><p>Creative, expressive, and social. You have a gift for communication and artistic expression. Career fields: Writing, acting, teaching, design.</p><h4>Number 4 - The Builder</h4><p>Practical, organized, and hardworking. You build strong foundations and value stability. Career fields: Engineering, accounting, architecture.</p><h4>Number 5 - The Adventurer</h4><p>Freedom-loving, versatile, and dynamic. You thrive on change and new experiences. Career fields: Travel, marketing, journalism.</p><h4>Number 6 - The Nurturer</h4><p>Loving, responsible, and caring. You are drawn to home, family, and community service. Career fields: Healthcare, education, social work.</p><h4>Number 7 - The Seeker</h4><p>Analytical, spiritual, and introspective. You seek deeper truths and inner wisdom. Career fields: Research, science, spirituality.</p><h4>Number 8 - The Powerhouse</h4><p>Ambitious, authoritative, and goal-oriented. You have strong business acumen. Career fields: Business, finance, law.</p><h4>Number 9 - The Humanitarian</h4><p>Compassionate, generous, and idealistic. You are driven to serve humanity. Career fields: Non-profit, charity, arts, healing.</p>',
    '<h2>आपका जीवन पथ अंक क्या है?</h2><p>आपकी जन्म तिथि से गणना किया गया, जीवन पथ अंक आपके जीवन के उद्देश्य, प्राकृतिक प्रतिभाओं और चुनौतियों को प्रकट करता है।</p><h3>गणना कैसे करें</h3><p>अपनी जन्म तिथि के प्रत्येक घटक को एक अंक में कम करें, फिर उन्हें जोड़ें।</p><h3>अंकों के अर्थ</h3><h4>अंक 1 - नेता</h4><p>स्वतंत्र, महत्वाकांक्षी और अग्रणी।</p><h4>अंक 2 - राजनयिक</h4><p>संवेदनशील, सहयोगी और शांतिप्रिय।</p>',
    'Learn how to calculate your Life Path Number and discover what it reveals about your personality, purpose, and destiny.',
    'अपना जीवन पथ अंक कैसे गणना करें और जानें कि यह आपके व्यक्तित्व और भाग्य के बारे में क्या बताता है।',
    2, 1, 'published', 567, NOW()),

    (3, 'Vastu Remedies Without Demolition', 'बिना तोड़-फोड़ के वास्तु उपाय', 'vastu-remedies-without-demolition',
    '<h2>Simple Vastu Corrections for Every Home</h2><p>Many people worry that correcting Vastu defects requires expensive renovations or demolition. The good news is that most Vastu doshas can be remedied with simple, non-destructive solutions.</p><h3>1. Salt Water Remedy</h3><p>Place rock salt in glass bowls at the corners of rooms with negative energy. Replace the salt every week. This absorbs negative vibrations and purifies the space.</p><h3>2. Pyramid Correction</h3><p>Vastu pyramids are powerful tools for correcting doshas. Place copper or crystal pyramids at specific locations to redirect energy flow. A pyramid at the center of your home (Brahmasthan) energizes the entire space.</p><h3>3. Color Therapy</h3><p>Use specific colors to balance directional energies. Green in the North for prosperity, Red or Orange in the South-East for the fire element, White or light Blue in the North-East for spirituality.</p><h3>4. Mirror Placement</h3><p>Strategically placed mirrors can visually expand space and redirect energy. Use mirrors to symbolically correct missing corners or extend truncated areas.</p><h3>5. Wind Chimes</h3><p>Hang metal wind chimes in the West or North-West to activate the air element. Wooden wind chimes in the East or South-East activate positive growth energy.</p><h3>6. Crystal Placement</h3><p>Clear quartz crystals in the North-East corner enhance spiritual energy. Rose quartz in the South-West strengthens relationships. Citrine in the South-East attracts wealth.</p><h3>7. Lighting Solutions</h3><p>Keep the North-East corner well-lit. Use bright lights in the South to balance the fire element. Avoid dim or flickering lights as they create stagnant energy.</p>',
    '<h2>हर घर के लिए सरल वास्तु सुधार</h2><p>बहुत से लोग चिंता करते हैं कि वास्तु दोषों को ठीक करने के लिए महंगे नवीनीकरण या तोड़-फोड़ की आवश्यकता होती है। अच्छी खबर यह है कि अधिकांश वास्तु दोषों को सरल, गैर-विध्वंसक समाधानों से ठीक किया जा सकता है।</p><h3>1. नमक पानी का उपाय</h3><p>नकारात्मक ऊर्जा वाले कमरों के कोनों में कांच के कटोरे में सेंधा नमक रखें।</p>',
    'Learn powerful Vastu remedies that can correct doshas in your home without any structural changes or demolition.',
    'जानें शक्तिशाली वास्तु उपाय जो बिना किसी संरचनात्मक बदलाव के आपके घर के दोषों को ठीक कर सकते हैं।',
    5, 1, 'published', 445, NOW()),

    (4, 'Vastu for Office: Boost Your Business Success', 'ऑफिस के लिए वास्तु: व्यापार में सफलता बढ़ाएं', 'vastu-office-business-success',
    '<h2>Vastu Guidelines for Office and Business</h2><p>The arrangement of your office space can significantly impact your business growth, employee productivity, and overall success. Vastu Shastra provides time-tested principles for creating an optimal work environment.</p><h3>Boss/Owner Cabin</h3><p>The owner or CEO cabin should be in the South-West corner of the office. Sit facing North or East for better decision-making. Keep the chair backed against a solid wall for support and stability.</p><h3>Reception Area</h3><p>Place the reception in the North-East or East zone. The receptionist should face East or North. Keep a small water fountain or aquarium here to welcome positive energy.</p><h3>Employee Seating</h3><p>Employees in the accounts department should sit in the South-East. Marketing and sales teams should face North or North-East. Avoid sitting directly under a beam as it creates pressure and stress.</p><h3>Conference Room</h3><p>The conference or meeting room should be in the North-West for quick decision-making. The team leader should sit facing East or North.</p>',
    '<h2>ऑफिस और व्यापार के लिए वास्तु दिशानिर्देश</h2><p>आपके ऑफिस स्थान की व्यवस्था आपके व्यापार विकास, कर्मचारी उत्पादकता और समग्र सफलता को महत्वपूर्ण रूप से प्रभावित कर सकती है।</p>',
    'Discover Vastu guidelines for your office to enhance business growth, productivity, and success.',
    'अपने ऑफिस के लिए वास्तु दिशानिर्देश खोजें जो व्यापार विकास और सफलता बढ़ाएं।',
    4, 1, 'published', 312, NOW()),

    (5, 'Lucky Numbers: How Numerology Can Change Your Life', 'भाग्यशाली अंक: अंक ज्योतिष कैसे बदल सकती है आपकी जिंदगी', 'lucky-numbers-numerology-life',
    '<h2>The Power of Numbers in Your Life</h2><p>Numbers are not just mathematical symbols — they carry vibrations and energies that influence every aspect of our lives. From your phone number to your house number, from your car registration to your bank account — every number matters.</p><h3>Your Lucky Number</h3><p>Your lucky number is derived from your date of birth and name. It represents the energies that naturally support you. Aligning your life decisions with your lucky number can enhance luck and reduce obstacles.</p><h3>Mobile Number Numerology</h3><p>Your mobile number creates a vibration that you carry with you 24/7. A number that aligns with your life path can attract opportunities, while a misaligned number can create unnecessary challenges. Add all digits of your mobile number and reduce to a single digit to find its vibration.</p><h3>House/Flat Number</h3><p>The number of your house or flat influences the energy of your living space. Number 1 houses are great for independent individuals. Number 2 houses promote partnerships. Number 8 houses can bring material success but require careful Vastu alignment.</p>',
    '<h2>आपके जीवन में अंकों की शक्ति</h2><p>अंक केवल गणितीय प्रतीक नहीं हैं — वे कंपन और ऊर्जाएं ले जाते हैं जो हमारे जीवन के हर पहलू को प्रभावित करती हैं।</p>',
    'Discover how numerology and lucky numbers can positively influence your career, relationships, and overall well-being.',
    'जानें कि अंक ज्योतिष और भाग्यशाली अंक कैसे आपके करियर और जीवन को सकारात्मक रूप से प्रभावित कर सकते हैं।',
    2, 1, 'published', 678, NOW())");
    echo "✓ Blog posts seeded (5 articles)\n";

    // Seed videos
    $pdo->exec("INSERT IGNORE INTO `videos` (id, title, title_hi, youtube_url, youtube_id, description, description_hi, category) VALUES 
    (1, 'Vastu Tips for Main Entrance', 'मुख्य प्रवेश द्वार के लिए वास्तु टिप्स', 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Learn the most important Vastu principles for your main entrance door.', 'अपने मुख्य प्रवेश द्वार के लिए सबसे महत्वपूर्ण वास्तु सिद्धांत सीखें।', 'vastu'),
    (2, 'Calculate Your Life Path Number', 'अपना जीवन पथ अंक गणना करें', 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Step by step guide to calculate your numerology life path number.', 'अपना अंक ज्योतिष जीवन पथ अंक गणना करने की चरण-दर-चरण मार्गदर्शिका।', 'numerology'),
    (3, 'Kitchen Vastu: Dos and Donts', 'रसोई वास्तु: क्या करें और क्या न करें', 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Complete guide to kitchen Vastu for health and prosperity.', 'स्वास्थ्य और समृद्धि के लिए रसोई वास्तु की पूरी गाइड।', 'vastu'),
    (4, 'Name Correction with Numerology', 'अंक ज्योतिष से नाम सुधार', 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'How changing spellings of your name can transform your destiny.', 'अपने नाम की स्पेलिंग बदलकर कैसे अपना भाग्य बदलें।', 'numerology'),
    (5, 'Vastu Remedies for South-Facing House', 'दक्षिणमुखी घर के वास्तु उपाय', 'https://youtube.com/watch?v=dQw4w9WgXcQ', 'dQw4w9WgXcQ', 'Effective remedies if your house faces South direction.', 'अगर आपका घर दक्षिण दिशा में है तो प्रभावी उपाय।', 'vastu')");
    echo "✓ Videos seeded (5 entries)\n";

    // Seed ebooks
    $pdo->exec("INSERT IGNORE INTO `ebooks` (id, title, title_hi, description, description_hi, file_path, pages, is_free, downloads) VALUES 
    (1, 'Complete Vastu Guide for Beginners', 'शुरुआती लोगों के लिए संपूर्ण वास्तु गाइड', 'A comprehensive guide covering all basic principles of Vastu Shastra for homes and offices.', 'घरों और कार्यालयों के लिए वास्तु शास्त्र के सभी बुनियादी सिद्धांतों को कवर करने वाली व्यापक गाइड।', 'ebooks/vastu-guide.pdf', 48, 1, 124),
    (2, 'Numerology Secrets Revealed', 'अंक ज्योतिष के रहस्य', 'Unlock the hidden power of numbers in your life. Complete numerology handbook.', 'अपने जीवन में अंकों की छिपी शक्ति खोलें। संपूर्ण अंक ज्योतिष पुस्तिका।', 'ebooks/numerology-secrets.pdf', 36, 1, 89),
    (3, 'Vastu Remedies Handbook', 'वास्तु उपाय पुस्तिका', '100+ proven Vastu remedies for common problems without any demolition.', 'बिना तोड़-फोड़ के सामान्य समस्याओं के लिए 100+ सिद्ध वास्तु उपाय।', 'ebooks/vastu-remedies.pdf', 52, 1, 67)");
    echo "✓ Ebooks seeded (3 entries)\n";

    // Seed testimonials
    $pdo->exec("INSERT IGNORE INTO `testimonials` (id, name, location, content, content_hi, rating) VALUES 
    (1, 'Rajesh Kumar', 'Delhi', 'Samiksha ji completely transformed our home. After implementing her Vastu suggestions, we noticed a significant improvement in our familys health and finances. Highly recommended!', 'समीक्षा जी ने हमारे घर को पूरी तरह बदल दिया। उनके वास्तु सुझावों को लागू करने के बाद, हमने अपने परिवार के स्वास्थ्य और वित्त में महत्वपूर्ण सुधार देखा।', 5),
    (2, 'Priya Sharma', 'Mumbai', 'The numerology name correction suggested by Samiksha ji was a game-changer. My business grew 3x within 6 months of the change. Amazing results!', 'समीक्षा जी द्वारा सुझाया गया नाम सुधार गेम-चेंजर था। बदलाव के 6 महीने के भीतर मेरा व्यापार 3 गुना बढ़ गया।', 5),
    (3, 'Amit Patel', 'Ahmedabad', 'I was skeptical at first, but the Vastu remedies for our office actually worked wonders. Employee productivity increased and we landed our biggest contract ever!', 'मुझे पहले संदेह था, लेकिन हमारे ऑफिस के लिए वास्तु उपायों ने वास्तव में चमत्कार किया। कर्मचारी उत्पादकता बढ़ी और हमें अब तक का सबसे बड़ा कॉन्ट्रैक्ट मिला!', 5),
    (4, 'Sunita Devi', 'Jaipur', 'The best Vastu consultant in India! Her remedies are practical and easy to implement. No unnecessary demolition advice. Very happy with the results.', 'भारत की सबसे अच्छी वास्तु सलाहकार! उनके उपाय व्यावहारिक और लागू करने में आसान हैं। अनावश्यक तोड़-फोड़ की सलाह नहीं देतीं।', 5),
    (5, 'Vikram Singh', 'Lucknow', 'Samiksha ji analyzed our new plot before construction. Her guidance helped us build a Vastu-compliant home that feels incredibly peaceful and positive.', 'समीक्षा जी ने निर्माण से पहले हमारे नए प्लॉट का विश्लेषण किया। उनके मार्गदर्शन से हमने एक वास्तु-अनुकूल घर बनाया जो अविश्वसनीय रूप से शांतिपूर्ण लगता है।', 5)");
    echo "✓ Testimonials seeded (5 entries)\n";

    // Seed quizzes
    $pdo->exec("INSERT IGNORE INTO `quizzes` (id, title, title_hi, description, description_hi, time_limit) VALUES 
    (1, 'Vastu Shastra Basics Quiz', 'वास्तु शास्त्र मूल प्रश्नोत्तरी', 'Test your knowledge of basic Vastu Shastra principles! 10 questions to challenge yourself.', 'अपने वास्तु शास्त्र के बुनियादी ज्ञान का परीक्षण करें! 10 प्रश्न।', 300),
    (2, 'Numerology Knowledge Test', 'अंक ज्योतिष ज्ञान परीक्षा', 'How well do you know numerology? Take this quiz to find out!', 'आप अंक ज्योतिष को कितनी अच्छी तरह जानते हैं? यह प्रश्नोत्तरी लें!', 300)");
    echo "✓ Quizzes seeded\n";

    // Seed quiz questions - Vastu Quiz
    $pdo->exec('INSERT IGNORE INTO `quiz_questions` (quiz_id, question, question_hi, options, options_hi, correct_answer, explanation, explanation_hi, points, sort_order) VALUES 
    (1, "Which direction is ideal for the main entrance of a house?", "घर के मुख्य प्रवेश द्वार के लिए कौन सी दिशा आदर्श है?", \'["South-West","North-East","South","West"]\', \'["दक्षिण-पश्चिम","उत्तर-पूर्व","दक्षिण","पश्चिम"]\', 1, "North-East is considered the most auspicious direction for the main entrance.", "उत्तर-पूर्व को मुख्य प्रवेश द्वार के लिए सबसे शुभ दिशा माना जाता है।", 10, 1),
    (1, "In which direction should the kitchen be placed?", "रसोई किस दिशा में होनी चाहिए?", \'["North-East","North-West","South-East","South-West"]\', \'["उत्तर-पूर्व","उत्तर-पश्चिम","दक्षिण-पूर्व","दक्षिण-पश्चिम"]\', 2, "South-East represents the fire element (Agni), making it ideal for the kitchen.", "दक्षिण-पूर्व अग्नि तत्व का प्रतिनिधित्व करती है।", 10, 2),
    (1, "Which corner is known as Ishaan Kon?", "कौन सा कोना ईशान कोण के रूप में जाना जाता है?", \'["South-West","South-East","North-East","North-West"]\', \'["दक्षिण-पश्चिम","दक्षिण-पूर्व","उत्तर-पूर्व","उत्तर-पश्चिम"]\', 2, "North-East corner is called Ishaan Kon, associated with water and spirituality.", "उत्तर-पूर्व कोने को ईशान कोण कहा जाता है।", 10, 3),
    (1, "What element does the South-East direction represent?", "दक्षिण-पूर्व दिशा किस तत्व का प्रतिनिधित्व करती है?", \'["Water","Earth","Fire","Air"]\', \'["जल","पृथ्वी","अग्नि","वायु"]\', 2, "South-East represents the Fire element (Agni tatva).", "दक्षिण-पूर्व अग्नि तत्व का प्रतिनिधित्व करती है।", 10, 4),
    (1, "Where should the master bedroom be located?", "मास्टर बेडरूम कहाँ होना चाहिए?", \'["North-East","South-West","North-West","South-East"]\', \'["उत्तर-पूर्व","दक्षिण-पश्चिम","उत्तर-पश्चिम","दक्षिण-पूर्व"]\', 1, "South-West provides stability and is ideal for the master bedroom.", "दक्षिण-पश्चिम स्थिरता प्रदान करता है।", 10, 5),
    (1, "In which direction should you sleep with your head?", "सोते समय सिर किस दिशा में होना चाहिए?", \'["North","South","West","Any direction"]\', \'["उत्तर","दक्षिण","पश्चिम","कोई भी दिशा"]\', 1, "Sleeping with head towards South promotes better health and restful sleep.", "दक्षिण दिशा में सिर करके सोने से बेहतर स्वास्थ्य मिलता है।", 10, 6),
    (1, "Which plant is considered auspicious in Vastu?", "वास्तु में कौन सा पौधा शुभ माना जाता है?", \'["Cactus","Tulsi (Holy Basil)","Bonsai","Rubber Plant"]\', \'["कैक्टस","तुलसी","बोन्साई","रबर प्लांट"]\', 1, "Tulsi (Holy Basil) is considered highly auspicious in Vastu.", "तुलसी को वास्तु में अत्यधिक शुभ माना जाता है।", 10, 7),
    (1, "What is Brahmasthan in Vastu?", "वास्तु में ब्रह्मस्थान क्या है?", \'["North-East corner","Center of the house","South-West corner","Main entrance"]\', \'["उत्तर-पूर्व कोना","घर का केंद्र","दक्षिण-पश्चिम कोना","मुख्य प्रवेश"]\', 1, "Brahmasthan is the center point of any structure, considered the energy nucleus.", "ब्रह्मस्थान किसी भी संरचना का केंद्र बिंदु है।", 10, 8),
    (1, "Which color is best for the North-East zone?", "उत्तर-पूर्व क्षेत्र के लिए कौन सा रंग सबसे अच्छा है?", \'["Red","Black","White/Light Blue","Dark Green"]\', \'["लाल","काला","सफेद/हल्का नीला","गहरा हरा"]\', 2, "White and light blue represent the water element associated with North-East.", "सफेद और हल्का नीला उत्तर-पूर्व के जल तत्व का प्रतिनिधित्व करता है।", 10, 9),
    (1, "Where should a water tank be placed?", "पानी की टंकी कहाँ रखनी चाहिए?", \'["South-West","South-East","North-East","Center"]\', \'["दक्षिण-पश्चिम","दक्षिण-पूर्व","उत्तर-पूर्व","केंद्र"]\', 2, "North-East is the water element zone, ideal for water storage.", "उत्तर-पूर्व जल तत्व क्षेत्र है।", 10, 10),

    (2, "What is a Life Path Number?", "जीवन पथ अंक क्या है?", \'["Your lucky lottery number","A number derived from your birth date","Your house number","Your phone number"]\', \'["आपका लॉटरी नंबर","आपकी जन्म तिथि से प्राप्त अंक","आपका घर नंबर","आपका फोन नंबर"]\', 1, "Life Path Number is calculated from your complete date of birth.", "जीवन पथ अंक आपकी पूर्ण जन्म तिथि से गणना किया जाता है।", 10, 1),
    (2, "Which is the most powerful number in numerology?", "अंक ज्योतिष में सबसे शक्तिशाली अंक कौन सा है?", \'["7","1","9","All are equal"]\', \'["7","1","9","सभी बराबर हैं"]\', 2, "Number 9 is considered the most powerful as it contains qualities of all numbers.", "अंक 9 को सबसे शक्तिशाली माना जाता है।", 10, 2),
    (2, "How many Master Numbers exist in numerology?", "अंक ज्योतिष में कितने मास्टर नंबर होते हैं?", \'["2","3","4","5"]\', \'["2","3","4","5"]\', 1, "Master Numbers are 11, 22, and 33.", "मास्टर नंबर 11, 22 और 33 हैं।", 10, 3),
    (2, "What does Life Path Number 1 signify?", "जीवन पथ अंक 1 का क्या अर्थ है?", \'["Diplomacy","Leadership","Creativity","Service"]\', \'["कूटनीति","नेतृत्व","रचनात्मकता","सेवा"]\', 1, "Number 1 represents leadership, independence, and pioneering spirit.", "अंक 1 नेतृत्व और स्वतंत्रता का प्रतिनिधित्व करता है।", 10, 4),
    (2, "Which planet rules Number 2?", "अंक 2 पर कौन सा ग्रह शासन करता है?", \'["Sun","Moon","Mars","Venus"]\', \'["सूर्य","चंद्रमा","मंगल","शुक्र"]\', 1, "Moon rules Number 2, giving it emotional and intuitive qualities.", "चंद्रमा अंक 2 पर शासन करता है।", 10, 5)');
    echo "✓ Quiz questions seeded\n";

    echo "\n=== Migration Complete! ===\n";
    echo "Consultant Login: samiksha@vastusamiksha.com / admin123\n";
    echo "Demo User Login: rahul@example.com / user123\n";

} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
