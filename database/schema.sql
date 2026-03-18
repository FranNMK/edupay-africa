-- ==========================================
-- EduPay Africa Full System Setup
-- Version: 2.2
-- Author: Francis Kienji
-- Last Updated: 2026-03-17
-- ==========================================

-- 1. DATABASE CREATION
CREATE DATABASE IF NOT EXISTS edupay_africa;
USE edupay_africa;

-- 2. RESET (Clean slate)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS parent_student_link, student_fees, fee_structures, users, institutions;
SET FOREIGN_KEY_CHECKS = 1;

-- 3. CORE TABLES
-- ==========================================

-- Institutions Table (Schools/Universities)
CREATE TABLE institutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    short_name VARCHAR(20) UNIQUE,
    address TEXT,
    contact_email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Users Table (RBAC System)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    role ENUM('admin', 'institution', 'parent', 'student') NOT NULL,
    institution_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_user_institution FOREIGN KEY (institution_id) 
        REFERENCES institutions(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Parent-Student Link (Handles families with multiple children)
CREATE TABLE parent_student_link (
    parent_id INT,
    student_id INT,
    PRIMARY KEY (parent_id, student_id),
    FOREIGN KEY (parent_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Fee Structures (Billing Categories)
CREATE TABLE fee_structures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution_id INT,
    fee_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    academic_year VARCHAR(20),
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Student Fees (Balances and Tracking)
CREATE TABLE student_fees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    fee_structure_id INT,
    paid_amount DECIMAL(10, 2) DEFAULT 0.00,
    status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (fee_structure_id) REFERENCES fee_structures(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. SEED DATA (Testing Environment)
-- ==========================================

-- A. The Institution
INSERT INTO institutions (name, short_name, address, contact_email) 
VALUES ('EduPay Academy', 'EPA-001', '123 Nairobi Finance Way', 'info@edupay.co.ke');

-- B. The Users (All passwords are '123')
-- BCrypt Hash for '123'
SET @pass = '$2y$10$8K1p/a0P1p7Z9Z.qY6VvO.6f3J3V5f1G5H8J9K0L1M2N3O4P5Q6R7';

INSERT INTO users (full_name, email, password_hash, role, institution_id) VALUES 
('Francis Kienji', 'admin@edupay.com', @pass, 'admin', NULL),
('John Parent', 'john@gmail.com', @pass, 'parent', 1),
('Mary Parent', 'mary@gmail.com', @pass, 'parent', 1),
('Alex Student', 'alex@edupay.com', @pass, 'student', 1),
('Blessing Student', 'blessing@edupay.com', @pass, 'student', 1),
('Catherine Student', 'catherine@edupay.com', @pass, 'student', 1);

-- C. The Family Links
INSERT INTO parent_student_link (parent_id, student_id) 
VALUES 
((SELECT id FROM users WHERE email='john@gmail.com'), (SELECT id FROM users WHERE email='alex@edupay.com')),
((SELECT id FROM users WHERE email='john@gmail.com'), (SELECT id FROM users WHERE email='blessing@edupay.com')),
((SELECT id FROM users WHERE email='mary@gmail.com'), (SELECT id FROM users WHERE email='catherine@edupay.com'));

-- D. The Billing
INSERT INTO fee_structures (institution_id, fee_name, amount, academic_year) 
VALUES 
(1, 'Term 1 Tuition 2026', 45000.00, '2026'),
(1, 'Transport Fee', 15000.00, '2026');

-- E. The Initial Balances
INSERT INTO student_fees (student_id, fee_structure_id, paid_amount, status) VALUES 
((SELECT id FROM users WHERE email='alex@edupay.com'), 1, 25000.00, 'partial'),
((SELECT id FROM users WHERE email='blessing@edupay.com'), 1, 0.00, 'unpaid'),
((SELECT id FROM users WHERE email='catherine@edupay.com'), 2, 15000.00, 'paid');