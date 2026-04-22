-- WhatsApp Tables Migration

CREATE TABLE IF NOT EXISTS whatsapp_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    category ENUM('welcome','reminder','report_ready','followup','festival','payment'),
    message_body TEXT COMMENT 'Use {name}, {date}, {amount}, {link} as placeholders',
    is_active TINYINT DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO whatsapp_templates (id, name, category, message_body) VALUES
(1, 'Welcome New Client', 'welcome', 'Namaste {name}! 🙏 Thank you for reaching out to Samiksha Vastu & Numerology. I will connect with you shortly to understand your requirements. — Samiksha'),
(2, 'Appointment Reminder', 'reminder', 'Namaste {name}! 🙏 This is a reminder for your consultation scheduled on {date}. Please keep your floor plan or date of birth details ready. — Samiksha'),
(3, 'Report Ready', 'report_ready', 'Namaste {name}! Your Vastu/Numerology report is ready. You can download it from your client dashboard: {link} — Samiksha'),
(4, 'Follow-up Check', 'followup', 'Namaste {name}! 🙏 Hope the Vastu changes are bringing positive energy to your space. How has the experience been so far? — Samiksha'),
(5, 'Festival Greeting', 'festival', 'Wishing you and your family a joyous and prosperous {date}! May your home be filled with positive energy and abundance. 🪔 — Samiksha'),
(6, 'Payment Request', 'payment', 'Namaste {name}! The consultation fee of ₹{amount} can be paid via UPI or the payment link: {link}. Thank you! — Samiksha');

CREATE TABLE IF NOT EXISTS whatsapp_message_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT,
    client_name VARCHAR(150),
    client_mobile VARCHAR(15),
    template_id INT,
    message_sent TEXT,
    opened_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (template_id) REFERENCES whatsapp_templates(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
