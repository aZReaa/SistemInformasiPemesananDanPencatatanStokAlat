<?php
// Run database schema fixes
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Starting database schema fixes...\n";
    
    // Fix 1: Update metode_pengambilan enum
    echo "Fixing metode_pengambilan enum values...\n";
    $pdo->exec("ALTER TABLE transaksi 
                MODIFY COLUMN metode_pengambilan ENUM('pickup','delivery','delivery_profile','delivery_custom') DEFAULT 'pickup'");
    
    // Fix 2: Add default value to kategori field
    echo "Adding default value to kategori field...\n";
    $pdo->exec("ALTER TABLE alat 
                MODIFY COLUMN kategori VARCHAR(50) NOT NULL DEFAULT 'Umum'");
    
    // Fix 3: Ensure id_kategori has proper default value
    echo "Setting default value for id_kategori...\n";
    $pdo->exec("ALTER TABLE alat 
                MODIFY COLUMN id_kategori INT DEFAULT 1");
    
    // Fix 4: Update existing NULL kategori values
    echo "Updating existing NULL kategori values...\n";
    $pdo->exec("UPDATE alat SET kategori = 'Umum' WHERE kategori IS NULL OR kategori = ''");
    
    echo "Database schema fixes completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error fixing database schema: " . $e->getMessage() . "\n";
}
