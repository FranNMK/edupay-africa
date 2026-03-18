-- ==========================================
-- EduPay Africa - Demo Requests Approval Flow Migration
-- Purpose: Add approval + onboarding scaffolding fields
-- Run this AFTER database/schema.sql and database/demo_requests_migration.sql
-- ==========================================

USE edupay_africa;

ALTER TABLE demo_requests
    ADD COLUMN approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending' AFTER status,
    ADD COLUMN approval_notes TEXT NULL AFTER approval_status,
    ADD COLUMN institution_id INT NULL AFTER approval_notes,
    ADD COLUMN approved_by INT NULL AFTER institution_id,
    ADD COLUMN approved_at TIMESTAMP NULL AFTER approved_by,
    ADD COLUMN onboarding_token VARCHAR(100) NULL AFTER approved_at,
    ADD COLUMN onboarding_expires_at DATETIME NULL AFTER onboarding_token,
    ADD COLUMN onboarding_email_sent_at TIMESTAMP NULL AFTER onboarding_expires_at,
    ADD INDEX idx_demo_approval_status (approval_status),
    ADD INDEX idx_demo_institution_id (institution_id),
    ADD INDEX idx_demo_onboarding_token (onboarding_token),
    ADD CONSTRAINT fk_demo_institution FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE SET NULL,
    ADD CONSTRAINT fk_demo_approved_by_user FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL;
