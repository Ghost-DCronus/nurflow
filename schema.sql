-- NurFlow Master Database Schema
CREATE DATABASE IF NOT EXISTS `nurflow_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `nurflow_db`;

-- 1. Users Table (Core Identity & Location for Prayer Times)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `city` VARCHAR(100) DEFAULT 'Sokoto',
  `country` VARCHAR(100) DEFAULT 'Nigeria',
  `latitude` DECIMAL(9,6) DEFAULT 13.0059,
  `longitude` DECIMAL(9,6) DEFAULT 5.2476,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Daily Habits & Prayer Logs Table
CREATE TABLE IF NOT EXISTS `user_habits` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `habit_name` VARCHAR(50) NOT NULL,
  `status` TINYINT(1) DEFAULT 0,
  `log_date` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_habit_date` (`user_id`, `habit_name`, `log_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Islamic Fasting Logs Table
CREATE TABLE IF NOT EXISTS `fasting_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `fast_type` VARCHAR(50) NOT NULL,
  `fast_date` DATE NOT NULL,
  `status` TINYINT(1) DEFAULT 0,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_fast_date` (`user_id`, `fast_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Digital Tasbeeh Logs Table
CREATE TABLE IF NOT EXISTS `dhikr_logs` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `dhikr_title` VARCHAR(100) NOT NULL,
  `count_achieved` INT DEFAULT 0,
  `target_count` INT DEFAULT 33,
  `log_date` DATE NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Quran Progress & Bookmarks Table
CREATE TABLE IF NOT EXISTS `quran_progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `surah_number` INT NOT NULL,
  `ayah_number` INT NOT NULL,
  `surah_name` VARCHAR(100) DEFAULT '',
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_quran` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 6. Islamic Library & Book Reading Tracker Table
CREATE TABLE IF NOT EXISTS `library_books` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `author` VARCHAR(150) NOT NULL,
  `category` ENUM('General', 'History', 'Children', 'Fiqh') DEFAULT 'General',
  `cover_image` VARCHAR(255) DEFAULT 'default_cover.jpg',
  `pdf_file` VARCHAR(255) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 7. User Book Reading Progress Table
CREATE TABLE IF NOT EXISTS `book_progress` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `book_id` INT NOT NULL,
  `last_page` INT DEFAULT 1,
  `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`book_id`) REFERENCES `library_books`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_user_book` (`user_id`, `book_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 8. Daily Reflections & Spiritual Journal Table
CREATE TABLE IF NOT EXISTS `daily_reflections` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `mood` VARCHAR(50) DEFAULT 'Grateful',
  `journal_text` TEXT NOT NULL,
  `log_date` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 9. Zakat Calculations History Table
CREATE TABLE IF NOT EXISTS `zakat_history` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NOT NULL,
  `total_assets` DECIMAL(15,2) DEFAULT 0.00,
  `zakat_due` DECIMAL(15,2) DEFAULT 0.00,
  `calculation_date` DATE NOT NULL,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
