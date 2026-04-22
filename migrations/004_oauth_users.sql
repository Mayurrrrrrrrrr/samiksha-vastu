-- OAuth Users Migration
-- If starting fresh, uncomment the CREATE TABLE below.
-- Since the users table already exists, we use ALTER TABLE to add the new columns.

-- CREATE TABLE IF NOT EXISTS users (
--     id INT AUTO_INCREMENT PRIMARY KEY,
--     google_id VARCHAR(100) UNIQUE,
--     name VARCHAR(150),
--     email VARCHAR(200) UNIQUE NOT NULL,
--     avatar_url TEXT,
--     mobile VARCHAR(15),
--     role ENUM('user','consultant') DEFAULT 'user', -- Mapped: user=client, consultant=admin
--     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
--     last_login TIMESTAMP NULL
-- );

-- ALTER approach (Safe for existing databases)
ALTER TABLE `users`
ADD COLUMN `google_id` VARCHAR(100) UNIQUE NULL AFTER `id`,
ADD COLUMN `avatar_url` TEXT NULL AFTER `email`,
ADD COLUMN `last_login` TIMESTAMP NULL DEFAULT NULL AFTER `created_at`;
