-- Tribike database SQL for PostgreSQL
-- Converted from MySQL for Render.com

CREATE DATABASE tribike;

-- Users table
CREATE TABLE IF NOT EXISTS users (
  id SERIAL PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  is_admin BOOLEAN NOT NULL DEFAULT FALSE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bikes table
CREATE TABLE IF NOT EXISTS bikes (
  id SERIAL PRIMARY KEY,
  model VARCHAR(150) NOT NULL,
  type VARCHAR(50) NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'available',
  price_per_hour DECIMAL(8,2) NOT NULL DEFAULT 0.00,
  latitude DECIMAL(10, 8) DEFAULT NULL,
  longitude DECIMAL(11, 8) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Rentals table
CREATE TABLE IF NOT EXISTS rentals (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  bike_id INTEGER NOT NULL,
  start_time TIMESTAMP NOT NULL,
  end_time TIMESTAMP DEFAULT NULL,
  total_price DECIMAL(10,2) DEFAULT 0.00,
  status VARCHAR(20) NOT NULL DEFAULT 'ongoing',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  bike_id INTEGER NOT NULL,
  start_datetime TIMESTAMP NOT NULL,
  end_datetime TIMESTAMP NOT NULL,
  status VARCHAR(20) DEFAULT 'booked',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE
);

-- Invoices table
CREATE TABLE IF NOT EXISTS invoices (
  id SERIAL PRIMARY KEY,
  rental_id INTEGER DEFAULT NULL,
  booking_id INTEGER DEFAULT NULL,
  user_id INTEGER NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  status VARCHAR(20) DEFAULT 'unpaid',
  issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (rental_id) REFERENCES rentals(id) ON DELETE SET NULL,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE SET NULL,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
  id SERIAL PRIMARY KEY,
  invoice_id INTEGER NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  paid_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  method VARCHAR(50) DEFAULT 'cash',
  FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON DELETE CASCADE
);

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
  id SERIAL PRIMARY KEY,
  user_id INTEGER NOT NULL,
  message TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Damage reports table
CREATE TABLE IF NOT EXISTS damage_reports (
  id SERIAL PRIMARY KEY,
  bike_id INTEGER NOT NULL,
  user_id INTEGER NOT NULL,
  description TEXT NOT NULL,
  latitude DECIMAL(10, 8) DEFAULT NULL,
  longitude DECIMAL(11, 8) DEFAULT NULL,
  address TEXT DEFAULT NULL,
  status VARCHAR(20) DEFAULT 'pending',
  reported_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (bike_id) REFERENCES bikes(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Settings table
CREATE TABLE IF NOT EXISTS settings (
  id SERIAL PRIMARY KEY,
  cfg_key VARCHAR(100) NOT NULL UNIQUE,
  cfg_value TEXT,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin (password: admin123)
INSERT INTO users (name, email, password, is_admin) VALUES
  ('Admin', 'admin@example.com', 'admin123', TRUE)
ON CONFLICT (email) DO NOTHING;

-- Sample bikes
INSERT INTO bikes (model, type, status, price_per_hour) VALUES
  ('Polygon Xtrada', 'mountain', 'available', 5.00),
  ('Giant Escape 3', 'city', 'available', 3.50),
  ('RoadMaster 2.0', 'road', 'available', 6.00)
ON CONFLICT DO NOTHING;

-- Sample settings
INSERT INTO settings (cfg_key, cfg_value) VALUES ('site_name', 'Tribike')
ON CONFLICT (cfg_key) DO NOTHING;


