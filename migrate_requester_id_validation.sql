-- Run once against an existing municipal_vehicle_reservation database.
ALTER TABLE reservations
  ADD COLUMN requester_id_document VARCHAR(255) NULL AFTER requester_id,
  ADD COLUMN requester_id_verified TINYINT(1) NOT NULL DEFAULT 0 AFTER requester_id_document;

