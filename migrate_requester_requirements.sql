-- Run once against an existing municipal_vehicle_reservation database.
ALTER TABLE users
  ADD COLUMN address VARCHAR(255) NOT NULL DEFAULT '' AFTER role,
  ADD COLUMN contact_number VARCHAR(30) NOT NULL DEFAULT '' AFTER address,
  ADD COLUMN valid_id_type VARCHAR(80) NOT NULL DEFAULT '' AFTER contact_number,
  ADD COLUMN valid_id_path VARCHAR(255) NULL AFTER valid_id_type,
  ADD COLUMN valid_id_back_path VARCHAR(255) NULL AFTER valid_id_path,
  ADD COLUMN birth_certificate_path VARCHAR(255) NULL AFTER valid_id_back_path,
  ADD COLUMN death_certificate_path VARCHAR(255) NULL AFTER birth_certificate_path,
  ADD COLUMN id_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER death_certificate_path;
