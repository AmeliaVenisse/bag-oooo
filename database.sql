CREATE DATABASE IF NOT EXISTS municipal_vehicle_reservation
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE municipal_vehicle_reservation;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('admin','secretary','gso','requester') NOT NULL DEFAULT 'requester',
  address VARCHAR(255) NOT NULL DEFAULT '',
  contact_number VARCHAR(30) NOT NULL DEFAULT '',
  valid_id_type VARCHAR(80) NOT NULL DEFAULT '',
  valid_id_path VARCHAR(255) NULL,
  valid_id_back_path VARCHAR(255) NULL,
  birth_certificate_path VARCHAR(255) NULL,
  death_certificate_path VARCHAR(255) NULL,
  id_verified TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vehicles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_name VARCHAR(120) NOT NULL,
  plate_number VARCHAR(40) NOT NULL UNIQUE,
  vehicle_type VARCHAR(80) NOT NULL,
  capacity INT NOT NULL DEFAULT 4,
  status ENUM('Available','In Use','Maintenance','Inactive') NOT NULL DEFAULT 'Available',
  notes VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS drivers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(120) NOT NULL,
  phone VARCHAR(40) NULL,
  status ENUM('Available','Assigned','Unavailable') NOT NULL DEFAULT 'Available'
);

CREATE TABLE IF NOT EXISTS reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference_no VARCHAR(32) NOT NULL UNIQUE,
  requester_id INT NOT NULL,
  requester_id_document VARCHAR(255) NULL,
  requester_id_verified TINYINT(1) NOT NULL DEFAULT 0,
  purpose VARCHAR(255) NOT NULL,
  destination VARCHAR(255) NOT NULL,
  vehicle_type VARCHAR(80) NOT NULL,
  passenger_count INT NOT NULL DEFAULT 1,
  departure_at DATETIME NOT NULL,
  return_at DATETIME NOT NULL,
  is_emergency TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('Pending Admin Review','Pending Secretary Approval','Approved','Rejected','Assigned','Dispatched','Returned','Cancelled') NOT NULL DEFAULT 'Pending Admin Review',
  admin_note TEXT NULL,
  secretary_note TEXT NULL,
  vehicle_id INT NULL,
  driver_id INT NULL,
  approved_by INT NULL,
  dispatched_at DATETIME NULL,
  returned_at DATETIME NULL,
  odometer_out DECIMAL(10,1) NULL,
  odometer_in DECIMAL(10,1) NULL,
  fuel_out VARCHAR(40) NULL,
  fuel_in VARCHAR(40) NULL,
  return_condition VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_res_requester FOREIGN KEY (requester_id) REFERENCES users(id),
  CONSTRAINT fk_res_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL,
  CONSTRAINT fk_res_driver FOREIGN KEY (driver_id) REFERENCES drivers(id) ON DELETE SET NULL,
  CONSTRAINT fk_res_approver FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS activity_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  reservation_id INT NULL,
  action VARCHAR(120) NOT NULL,
  details TEXT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_log_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
  CONSTRAINT fk_log_res FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE SET NULL
);

-- Demo accounts. All demo passwords are: password
-- PHP password_hash for "password"
INSERT IGNORE INTO users (full_name,email,password_hash,role) VALUES
('System Administrator','admin@inabanga.gov.ph','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','admin'),
('Municipal Secretary','secretary@inabanga.gov.ph','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','secretary'),
('GSO Staff','gso@inabanga.gov.ph','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','gso'),
('Sample Requester','requester@example.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','requester');

INSERT IGNORE INTO vehicles (vehicle_name,plate_number,vehicle_type,capacity,status,notes) VALUES
('Municipal Van','GOV-101','Van',12,'Available','General municipal use'),
('Service SUV','GOV-202','SUV',7,'Available','Official trips'),
('Municipal Ambulance','AMB-001','Ambulance',4,'Available','Emergency priority');

INSERT INTO drivers (full_name,phone,status)
SELECT 'Juan Dela Cruz','09170000001','Available'
WHERE NOT EXISTS (SELECT 1 FROM drivers WHERE full_name='Juan Dela Cruz');
INSERT INTO drivers (full_name,phone,status)
SELECT 'Pedro Santos','09170000002','Available'
WHERE NOT EXISTS (SELECT 1 FROM drivers WHERE full_name='Pedro Santos');
