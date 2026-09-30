-- ============================================================
-- eMC System - Database Setup Script
-- Sistem e-Sijil Sakit & Slip Masa
-- ============================================================

CREATE DATABASE IF NOT EXISTS emc_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE emc_db;

-- ------------------------------------------------------------
-- Jadual: patients
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS patients (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    ic_number     VARCHAR(20)  UNIQUE NOT NULL,
    full_name     VARCHAR(150) NOT NULL,
    date_of_birth DATE         NOT NULL,
    gender        ENUM('Lelaki','Perempuan') NOT NULL,
    phone         VARCHAR(20)  DEFAULT NULL,
    address       TEXT         DEFAULT NULL,
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    updated_at    DATETIME     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Jadual: doctors
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS doctors (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    name           VARCHAR(150) NOT NULL,
    qualification  VARCHAR(200) DEFAULT NULL,
    mcr_number     VARCHAR(50)  DEFAULT NULL,
    department     VARCHAR(150) DEFAULT NULL,
    is_active      TINYINT(1)   DEFAULT 1,
    created_at     DATETIME     DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Jadual: medical_certificates
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS medical_certificates (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    reference_no  VARCHAR(50)  UNIQUE NOT NULL,
    patient_id    INT          NOT NULL,
    doctor_id     INT          NOT NULL,
    date_issued   DATE         NOT NULL,
    start_date    DATE         NOT NULL,
    num_days      INT          NOT NULL,
    end_date      DATE         NOT NULL,
    diagnosis     TEXT         DEFAULT NULL,
    notes         TEXT         DEFAULT NULL,
    language      ENUM('BM','EN') DEFAULT 'BM',
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ------------------------------------------------------------
-- Jadual: time_slips
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS time_slips (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    reference_no  VARCHAR(50)  UNIQUE NOT NULL,
    patient_id    INT          NOT NULL,
    doctor_id     INT          NOT NULL,
    date_issued   DATE         NOT NULL,
    time_in       TIME         NOT NULL,
    time_out      TIME         NOT NULL,
    purpose       TEXT         NOT NULL,
    notes         TEXT         DEFAULT NULL,
    language      ENUM('BM','EN') DEFAULT 'BM',
    created_at    DATETIME     DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- DATA DUMMY
-- ============================================================

-- Pesakit dummy
INSERT INTO patients (ic_number, full_name, date_of_birth, gender, phone, address) VALUES
('850312-10-5671', 'En. Hafiz bin Abdullah',     '1985-03-12', 'Lelaki',    '012-3456789', 'No. 12, Jalan Bahagia 3, Taman Bahagia, 68000 Ampang, Selangor'),
('901225-14-5432', 'Pn. Aishah binti Mahmud',    '1990-12-25', 'Perempuan', '013-9876543', 'No. 5, Lorong Damai 7, Taman Damai Perdana, 56000 Cheras, Kuala Lumpur'),
('780609-07-5891', 'En. Tan Wei Keong',           '1978-06-09', 'Lelaki',    '016-5554321', 'No. 88, Jalan Utama 15, Taman Utama, 47500 Subang Jaya, Selangor');

-- ------------------------------------------------------------
-- KEMASKINI: Tambah kolum RN (Registration Number iSyifaa)
-- Jalankan ini jika database sudah wujud (upgrade sedia ada)
-- ------------------------------------------------------------
-- ALTER TABLE patients ADD COLUMN rn VARCHAR(20) NULL UNIQUE AFTER ic_number;

-- Doktor dummy
INSERT INTO doctors (name, qualification, mcr_number, department, is_active) VALUES
('Dr. Ahmad Fauzi bin Ismail', 'MBBS (UM), FRACGP',   'MMC-12345', 'Perubatan Am',     1),
('Dr. Siti Rahimah binti Yusof','MD (UPM), MFamMed',  'MMC-23456', 'Perubatan Keluarga',1);
