-- Vastu Quiz Game Migration

CREATE TABLE IF NOT EXISTS game_questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    question_hi TEXT,
    option_a VARCHAR(200),
    option_b VARCHAR(200),
    option_c VARCHAR(200),
    option_d VARCHAR(200),
    correct_option ENUM('A','B','C','D'),
    explanation TEXT,
    is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO game_questions (id, question_text, question_hi, option_a, option_b, option_c, option_d, correct_option, explanation) VALUES
(1, 'Which direction is best for the main entrance according to Vastu?', 'वास्तु के अनुसार मुख्य द्वार के लिए कौन सी दिशा सबसे अच्छी है?', 'North / East', 'South', 'South-West', 'West', 'A', 'North or East directions attract positive energy, wealth, and prosperity.'),
(2, 'Where should the kitchen ideally be located?', 'रसोईघर आदर्श रूप से कहाँ स्थित होना चाहिए?', 'North-East', 'South-East', 'South-West', 'Center', 'B', 'South-East is the Agni (Fire) corner, making it the ideal spot for a kitchen.'),
(3, 'Which number is considered the most powerful in Numerology?', 'अंक ज्योतिष में किस संख्या को सबसे शक्तिशाली माना जाता है?', '1', '4', '8', '9', 'D', '9 represents completion, wisdom, and universal love.'),
(4, 'Where should the master bedroom be placed?', 'मास्टर बेडरूम कहाँ होना चाहिए?', 'North-East', 'South-East', 'South-West', 'North-West', 'C', 'South-West brings stability and peace to the head of the family.'),
(5, 'What element is associated with the North-East direction?', 'उत्तर-पूर्व दिशा किस तत्व से जुड़ी है?', 'Fire', 'Water', 'Earth', 'Air', 'B', 'Water is the ruling element of the North-East (Ishan) corner.');

CREATE TABLE IF NOT EXISTS game_leaderboard (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    guest_name VARCHAR(100) NULL,
    score INT NOT NULL,
    total_time INT NOT NULL COMMENT 'Time taken in seconds',
    played_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
