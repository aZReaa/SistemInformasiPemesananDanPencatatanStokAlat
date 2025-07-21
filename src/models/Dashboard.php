<?php

class Dashboard
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * View dashboard sesuai UML requirement
     * @param string $role - admin/pelanggan/pemilik
     * @param int $userId - ID user yang sedang login
     * @return array
     */
    public function viewDashboard($role, $userId)
    {
        switch ($role) {
            case 'admin':
                return $this->getAdminDashboard();
            case 'pelanggan':
                return $this->getPelangganDashboard($userId);
            case 'pemilik':
                return $this->getPemilikDashboard();
            default:
                return [];
        }
    }
    
    private function getAdminDashboard()
    {
        $data = [];
        
        // Total pelanggan
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM pelanggan");
        $data['total_pelanggan'] = $stmt->fetch()['total'];
        
        // Total alat
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM alat");
        $data['total_alat'] = $stmt->fetch()['total'];
        
        // Total transaksi
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM transaksi");
        $data['total_transaksi'] = $stmt->fetch()['total'];
        
        // Transaksi hari ini
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM transaksi WHERE DATE(created_at) = CURDATE()");
        $data['transaksi_hari_ini'] = $stmt->fetch()['total'];
        
        // Pendapatan total (include approved, ongoing, and completed transactions)
        $stmt = $this->db->query("SELECT SUM(total_harga) as total FROM transaksi WHERE status IN ('approved', 'ongoing', 'completed')");
        $data['total_pendapatan'] = $stmt->fetch()['total'] ?? 0;
        
        // Transaksi terbaru
        $stmt = $this->db->query("
            SELECT t.*, p.nama as nama_pelanggan, a.nama_alat 
            FROM transaksi t 
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
            JOIN alat a ON t.id_alat = a.id_alat 
            ORDER BY t.created_at DESC 
            LIMIT 5
        ");
        $data['transaksi_terbaru'] = $stmt->fetchAll();
        
        return $data;
    }
    
    private function getPelangganDashboard($pelangganId)
    {
        $data = [];
        
        // Total pesanan
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_pelanggan = ?");
        $stmt->execute([$pelangganId]);
        $data['total_pesanan'] = $stmt->fetch()['total'];
        
        // Pesanan aktif
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_pelanggan = ? AND status IN ('pending', 'confirmed', 'ongoing')");
        $stmt->execute([$pelangganId]);
        $data['pesanan_aktif'] = $stmt->fetch()['total'];
        
        // Pesanan selesai
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM transaksi WHERE id_pelanggan = ? AND status = 'completed'");
        $stmt->execute([$pelangganId]);
        $data['pesanan_selesai'] = $stmt->fetch()['total'];
        
        // Total pengeluaran
        $stmt = $this->db->prepare("SELECT SUM(total_harga) as total FROM transaksi WHERE id_pelanggan = ? AND status IN ('approved', 'ongoing', 'completed')");
        $stmt->execute([$pelangganId]);
        $data['total_pengeluaran'] = $stmt->fetch()['total'] ?? 0;
        
        // Riwayat transaksi terbaru
        $stmt = $this->db->prepare("
            SELECT t.*, a.nama_alat, a.harga 
            FROM transaksi t 
            JOIN alat a ON t.id_alat = a.id_alat 
            WHERE t.id_pelanggan = ? 
            ORDER BY t.created_at DESC 
            LIMIT 5
        ");
        $stmt->execute([$pelangganId]);
        $data['riwayat_terbaru'] = $stmt->fetchAll();
        
        return $data;
    }
    
    private function getPemilikDashboard()
    {
        $data = [];
        
        // Pendapatan bulan ini
        $stmt = $this->db->query("
            SELECT SUM(total_harga) as total 
            FROM transaksi 
            WHERE status IN ('approved', 'ongoing', 'completed') 
            AND MONTH(created_at) = MONTH(CURDATE()) 
            AND YEAR(created_at) = YEAR(CURDATE())
        ");
        $data['pendapatan_bulan_ini'] = $stmt->fetch()['total'] ?? 0;
        
        // Pendapatan tahun ini
        $stmt = $this->db->query("
            SELECT SUM(total_harga) as total 
            FROM transaksi 
            WHERE status IN ('approved', 'ongoing', 'completed') 
            AND YEAR(created_at) = YEAR(CURDATE())
        ");
        $data['pendapatan_tahun_ini'] = $stmt->fetch()['total'] ?? 0;
        
        // Total pelanggan aktif
        $stmt = $this->db->query("
            SELECT COUNT(DISTINCT id_pelanggan) as total 
            FROM transaksi 
            WHERE YEAR(created_at) = YEAR(CURDATE())
        ");
        $data['pelanggan_aktif'] = $stmt->fetch()['total'];
        
        // Alat paling populer
        $stmt = $this->db->query("
            SELECT a.nama_alat, COUNT(*) as jumlah_sewa 
            FROM transaksi t 
            JOIN alat a ON t.id_alat = a.id_alat 
            WHERE YEAR(t.created_at) = YEAR(CURDATE()) 
            GROUP BY a.id_alat 
            ORDER BY jumlah_sewa DESC 
            LIMIT 5
        ");
        $data['alat_populer'] = $stmt->fetchAll();
        
        // Statistik bulanan (6 bulan terakhir)
        $stmt = $this->db->query("
            SELECT 
                MONTH(created_at) as bulan,
                YEAR(created_at) as tahun,
                COUNT(*) as total_transaksi,
                SUM(total_harga) as total_pendapatan
            FROM transaksi 
            WHERE status IN ('approved', 'ongoing', 'completed')
            AND created_at >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
            GROUP BY YEAR(created_at), MONTH(created_at)
            ORDER BY tahun DESC, bulan DESC
        ");
        $data['statistik_bulanan'] = $stmt->fetchAll();
        
        return $data;
    }
}
