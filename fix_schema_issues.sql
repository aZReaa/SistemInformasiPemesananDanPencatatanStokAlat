-- Fix database schema issues
-- This script addresses the following problems:
-- 1. metode_pengambilan enum values issue
-- 2. kategori field default value issue

USE hakikah_rental;

-- Fix 1: Update metode_pengambilan enum to include all possible values
ALTER TABLE transaksi 
MODIFY COLUMN metode_pengambilan ENUM('pickup','delivery','delivery_profile','delivery_custom') DEFAULT 'pickup';

-- Fix 2: Add default value to kategori field in alat table
ALTER TABLE alat 
MODIFY COLUMN kategori VARCHAR(50) NOT NULL DEFAULT 'Umum';

-- Fix 3: Ensure id_kategori has proper default value
ALTER TABLE alat 
MODIFY COLUMN id_kategori INT DEFAULT 1;

-- Optional: Update existing NULL kategori values if any
UPDATE alat SET kategori = 'Umum' WHERE kategori IS NULL OR kategori = '';

-- Verify the changes
SHOW CREATE TABLE transaksi;
SHOW CREATE TABLE alat;
