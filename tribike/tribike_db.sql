-- Tribike database SQL
CREATE DATABASE IF NOT EXISTS `tribike_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tribike_db`;

-- users table
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- bikes table
CREATE TABLE IF NOT EXISTS bikes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  model VARCHAR(150) NOT NULL,
  type VARCHAR(50) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'available',
  price_per_hour DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  latitude DECIMAL(10, 8) DEFAULT NULL,
  longitude DECIMAL(11, 8) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- rentals table
CREATE TABLE IF NOT EXISTS rentals (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bike_id INT NOT NULL,
  start_time DATETIME NOT NULL,
  end_time DATETIME DEFAULT NULL,
  total_price DECIMAL(10,2) DEFAULT 0.00,
  status VARCHAR(20) NOT NULL DEFAULT 'ongoing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- bookings table
CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  bike_id INT NOT NULL,
  start_datetime DATETIME NOT NULL,
  end_datetime DATETIME NOT NULL,
  status VARCHAR(20) DEFAULT 'booked',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- invoices table
CREATE TABLE IF NOT EXISTS invoices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  rental_id INT DEFAULT NULL,
  booking_id INT DEFAULT NULL,
  user_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(20) DEFAULT 'unpaid',
  issued_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE SET NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- payments table
CREATE TABLE IF NOT EXISTS payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  invoice_id INT NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  method VARCHAR(50) DEFAULT 'cash',
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- feedback table
CREATE TABLE IF NOT EXISTS feedback (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- damage_reports table
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

-- settings table
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cfg_key VARCHAR(100) NOT NULL UNIQUE,
  cfg_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- default admin (password: admin123)
-- WARNING: Password stored in plain text - NOT SECURE for production!
INSERT INTO users (name,email,password,is_admin) VALUES
  ('Admin','admin@example.com','admin123',1);

-- sample bikes
INSERT INTO bikes (model,type,status,price_per_hour) VALUES
  ('Polygon Xtrada','mountain','available',5.00),
  ('Giant Escape 3','city','available',3.50),
  ('RoadMaster 2.0','road','available',6.00);

-- sample settings
INSERT INTO settings (cfg_key, cfg_value) VALUES ('site_name','Tribike');
