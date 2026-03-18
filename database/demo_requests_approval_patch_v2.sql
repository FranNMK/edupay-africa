-- ==========================================
-- EduPay Africa - Demo Requests Approval Patch v2
-- Purpose: Safely patch existing databases that already ran older approval migration
-- ==========================================

USE edupay_africa;

-- 1) Ensure institution_id exists
ALTER TABLE demo_requests
    ADD COLUMN IF NOT EXISTS institution_id INT NULL AFTER approval_notes;

-- 2) Ensure index on institution_id exists
SET @idx_exists := (
    SELECT COUNT(*)
    FROM information_schema.STATISTICS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'demo_requests'
      AND INDEX_NAME = 'idx_demo_institution_id'
);
SET @sql := IF(
    @idx_exists = 0,
    'CREATE INDEX idx_demo_institution_id ON demo_requests (institution_id)',
    'SELECT "idx_demo_institution_id already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3) Ensure foreign key to institutions exists
SET @fk_exists := (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE TABLE_SCHEMA = DATABASE()
      AND TABLE_NAME = 'demo_requests'
      AND CONSTRAINT_NAME = 'fk_demo_institution'
      AND CONSTRAINT_TYPE = 'FOREIGN KEY'
);
SET @sql := IF(
    @fk_exists = 0,
    'ALTER TABLE demo_requests ADD CONSTRAINT fk_demo_institution FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE SET NULL',
    'SELECT "fk_demo_institution already exists"'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
