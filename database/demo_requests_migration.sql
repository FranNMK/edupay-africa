-- ==========================================
-- EduPay Africa - Demo Requests Migration
-- Purpose: Keep new DB changes separate from original schema.sql
-- Run this AFTER database/schema.sql
-- ==========================================

USE edupay_africa;

CREATE TABLE IF NOT EXISTS demo_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution_name VARCHAR(150) NOT NULL,
    contact_name VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    school_type ENUM('Primary', 'Secondary', 'College', 'University', 'Other') DEFAULT 'Other',
    student_count INT DEFAULT NULL,
    preferred_contact ENUM('Phone', 'Email', 'WhatsApp') DEFAULT 'Email',
    message TEXT,
    status ENUM('new', 'contacted', 'qualified', 'closed') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
