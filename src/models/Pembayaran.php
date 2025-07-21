<?php

class Pembayaran
{
    private $id_pembayaran;
    private $id_transaksi;
    private $jumlah;
    private $bukti_transfer;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function insert($data)
    {
        $sql = "INSERT INTO pembayaran (id_transaksi, jumlah, bukti_transfer, status_pembayaran) 
                VALUES (?, ?, ?, 'pending')";
        
        return $this->db->insert($sql, [
            $data['id_transaksi'],
            $data['jumlah'],
            $data['bukti_transfer'] ?? null
        ]);
    }

    public function verifikasi($id, $status, $adminId)
    {
        $validStatuses = ['verified', 'rejected'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Status verifikasi tidak valid");
        }

        $sql = "UPDATE pembayaran SET status_pembayaran = ?, verified_by = ?, verified_at = NOW() 
                WHERE id_pembayaran = ?";
        
        $result = $this->db->update($sql, [$status, $adminId, $id]);

        if ($status === 'verified') {
            $pembayaran = $this->getById($id);
            if ($pembayaran) {
                $this->db->update(
                    "UPDATE transaksi SET status = 'approved' WHERE id_transaksi = ?",
                    [$pembayaran['id_transaksi']]
                );
            }
        }

        return $result;
    }

    public function getById($id)
    {
        $sql = "SELECT p.*, t.total_harga, t.status as status_transaksi, 
                       pel.nama as nama_pelanggan, a.nama_alat 
                FROM pembayaran p 
                JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE p.id_pembayaran = ?";
        
        return $this->db->fetch($sql, [$id]);
    }

    public function getByTransaksi($idTransaksi)
    {
        return $this->db->fetch(
            "SELECT * FROM pembayaran WHERE id_transaksi = ?",
            [$idTransaksi]
        );
    }

    public function getAll()
    {
        $sql = "SELECT p.*, t.total_harga, pel.nama as nama_pelanggan, a.nama_alat,
                       admin.nama_admin as verified_by_name
                FROM pembayaran p 
                JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN admin ON p.verified_by = admin.id_admin 
                ORDER BY p.tanggal_bayar DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getPending()
    {
        $sql = "SELECT p.*, t.total_harga, pel.nama as nama_pelanggan, a.nama_alat 
                FROM pembayaran p 
                JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE p.status_pembayaran = 'pending' 
                ORDER BY p.tanggal_bayar ASC";
        
        return $this->db->fetchAll($sql);
    }

    public function getVerified()
    {
        $sql = "SELECT p.*, t.total_harga, pel.nama as nama_pelanggan, a.nama_alat,
                       admin.nama_admin as verified_by_name
                FROM pembayaran p 
                JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN admin ON p.verified_by = admin.id_admin 
                WHERE p.status_pembayaran = 'verified' 
                ORDER BY p.verified_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getTotalPendapatan($startDate = null, $endDate = null)
    {
        $sql = "SELECT SUM(p.jumlah) as total_pendapatan 
                FROM pembayaran p 
                WHERE p.status_pembayaran = 'verified'";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " AND p.verified_at BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['total_pendapatan'] ?? 0;
    }

    public function uploadBuktiTransfer($id, $filename)
    {
        return $this->db->update(
            "UPDATE pembayaran SET bukti_transfer = ? WHERE id_pembayaran = ?",
            [$filename, $id]
        );
    }
}