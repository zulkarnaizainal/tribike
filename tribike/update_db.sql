-- Migration script to add location and damage report features
-- Run this if you already have an existing database
-- Note: Change 'tribike_db' to your database name if different (e.g., 'tribike')

USE `tribike`;

-- Create damage_reports table if it doesn't exist
CREATE TABLE IF NOT EXISTS damage_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  bike_id INT NOT NULL,
  user_id INT NOT NULL,
  description TEXT NOT NULL,
  latitude DECIMAL(10, 8) DEFAULT NULL,
  longitude DECIMAL(11, 8) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add location fields to bikes table (remove these lines if columns already exist)
-- If you get "Duplicate column name" error, skip those ALTER TABLE commands
ALTER TABLE bikes ADD COLUMN latitude DECIMAL(10, 8) DEFAULT NULL;
ALTER TABLE bikes ADD COLUMN longitude DECIMAL(11, 8) DEFAULT NULL;
ALTER TABLE bikes ADD COLUMN address TEXT DEFAULT NULL;

-- Update admin password to plain text (password: admin123)
-- WARNING: Plain text password - NOT SECURE for production!
UPDATE users SET password = 'admin123' WHERE email = 'admin@example.com' AND is_admin = 1;

