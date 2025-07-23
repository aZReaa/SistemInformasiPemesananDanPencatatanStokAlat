<?php
// Fix existing returns that didn't restore stock properly
require_once 'config/database.php';

try {
    $db = Database::getInstance();
    $pdo = $db->getConnection();
    
    echo "Checking and fixing existing returns...\n";
    
    // Get all existing returns
    $returns = $pdo->query("
        SELECT p.*, t.jumlah_alat, t.id_alat, t.status as transaksi_status
        FROM pengembalian p 
        JOIN transaksi t ON p.id_transaksi = t.id_transaksi
        ORDER BY p.created_at DESC
    ")->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($returns as $return) {
        echo "Processing return ID: {$return['id_pengembalian']} for transaction ID: {$return['id_transaksi']}\n";
        
        // Check if transaction is completed (which means return was processed)
        if ($return['transaksi_status'] === 'completed') {
            echo "- Transaction already marked as completed, stock should be restored\n";
            
            // If condition is 'baik', stock should have been restored
            if ($return['kondisi_alat'] === 'baik') {
                echo "- Items returned in good condition, stock should be +{$return['jumlah_alat']}\n";
            } elseif ($return['kondisi_alat'] === 'rusak') {
                echo "- Items returned damaged, partial stock restored\n";
            } elseif ($return['kondisi_alat'] === 'hilang') {
                echo "- Items lost, no stock restored\n";
            }
        } else {
            echo "- Transaction not marked as completed, fixing...\n";
            
            // Fix the transaction status
            $pdo->prepare("UPDATE transaksi SET status = 'completed' WHERE id_transaksi = ?")
                ->execute([$return['id_transaksi']]);
            
            // Restore stock based on condition
            if ($return['kondisi_alat'] === 'baik') {
                $pdo->prepare("UPDATE alat SET stok = stok + ? WHERE id_alat = ?")
                    ->execute([$return['jumlah_alat'], $return['id_alat']]);
                echo "- Fixed: Restored {$return['jumlah_alat']} items to stock\n";
            } elseif ($return['kondisi_alat'] === 'rusak') {
                // Assume half items are damaged if no specific count
                $stokKembali = max(0, floor($return['jumlah_alat'] / 2));
                $pdo->prepare("UPDATE alat SET stok = stok + ? WHERE id_alat = ?")
                    ->execute([$stokKembali, $return['id_alat']]);
                echo "- Fixed: Restored {$stokKembali} items to stock (damaged items)\n";
            }
        }
        echo "\n";
    }
    
    echo "Stock restoration check completed!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
