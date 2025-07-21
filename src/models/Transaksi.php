<?php

class Transaksi
{
    private $id_transaksi;
    private $id_pelanggan;
    private $id_alat;
    private $tgl_sewa;
    private $tgl_kembali;
    private $status;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function insertTransaksi($data)
    {
        try {
            $this->db->getConnection()->beginTransaction();

            $alat = $this->db->fetch("SELECT * FROM alat WHERE id_alat = ?", [$data['id_alat']]);
            
            if (!$alat || $alat['stok'] < $data['jumlah_alat']) {
                throw new Exception("Stok alat tidak mencukupi");
            }

            $totalHarga = $alat['harga'] * $data['jumlah_alat'];
            
            // Handle address based on delivery method
            $alamatPengiriman = null;
            if ($data['metode_pengambilan'] === 'delivery_profile') {
                // Use customer's profile address
                $pelanggan = $this->db->fetch("SELECT alamat FROM pelanggan WHERE id_pelanggan = ?", [$data['id_pelanggan']]);
                $alamatPengiriman = $pelanggan['alamat'] ?? null;
            } elseif ($data['metode_pengambilan'] === 'delivery_custom') {
                // Use custom address provided
                $alamatPengiriman = $data['alamat_pengiriman'] ?? null;
            }
            
            $sql = "INSERT INTO transaksi (id_pelanggan, id_alat, tgl_sewa, tgl_kembali, jumlah_alat, total_harga, metode_pengambilan, alamat_pengiriman, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
            
            $transaksiId = $this->db->insert($sql, [
                $data['id_pelanggan'],
                $data['id_alat'],
                $data['tgl_sewa'],
                $data['tgl_kembali'],
                $data['jumlah_alat'],
                $totalHarga,
                $data['metode_pengambilan'] ?? 'pickup',
                $alamatPengiriman
            ]);

            $this->db->update(
                "UPDATE alat SET stok = stok - ? WHERE id_alat = ?",
                [$data['jumlah_alat'], $data['id_alat']]
            );

            $this->db->getConnection()->commit();
            
            return $transaksiId;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }

    public function updateStatus($id, $status)
    {
        $validStatuses = ['pending', 'approved', 'ongoing', 'completed', 'cancelled'];
        
        if (!in_array($status, $validStatuses)) {
            throw new Exception("Status tidak valid");
        }

        if ($status === 'cancelled') {
            $transaksi = $this->getById($id);
            if ($transaksi) {
                $this->db->update(
                    "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
                    [$transaksi['jumlah_alat'], $transaksi['id_alat']]
                );
            }
        }

        return $this->db->update(
            "UPDATE transaksi SET status = ?, updated_at = NOW() WHERE id_transaksi = ?",
            [$status, $id]
        );
    }

    public function getById($id)
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, p.alamat, p.no_hp, 
                       a.nama_alat, a.harga, k.nama_kategori as kategori
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                JOIN kategori_layanan k ON a.id_kategori = k.id_kategori
                WHERE t.id_transaksi = ?";
        
        return $this->db->fetch($sql, [$id]);
    }

    public function getByPelanggan($idPelanggan)
    {
        $sql = "SELECT t.*, a.nama_alat, a.harga, p.status_pembayaran 
                FROM transaksi t 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN pembayaran p ON t.id_transaksi = p.id_transaksi 
                WHERE t.id_pelanggan = ? 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$idPelanggan]);
    }

    public function getAll()
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, p.alamat as alamat_pelanggan, a.nama_alat, pb.status_pembayaran 
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN pembayaran pb ON t.id_transaksi = pb.id_transaksi 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getByStatus($status)
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, a.nama_alat 
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE t.status = ? 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$status]);
    }

    public function getLaporanByPeriode($startDate, $endDate)
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, a.nama_alat, pb.status_pembayaran 
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN pembayaran pb ON t.id_transaksi = pb.id_transaksi 
                WHERE t.created_at BETWEEN ? AND ? 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    public function getStatistik()
    {
        $sql = "SELECT 
                    COUNT(*) as total_transaksi,
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                    COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                    COUNT(CASE WHEN status = 'ongoing' THEN 1 END) as ongoing,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled,
                    SUM(total_harga) as total_pendapatan
                FROM transaksi";
        
        return $this->db->fetch($sql);
    }

    /**
     * Delete transaksi (hanya untuk status pending/cancelled)
     * @param int $id
     * @return bool
     */
    public function delete($id)
    {
        try {
            // Check status transaksi
            $transaksi = $this->getById($id);
            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan");
            }

            // Hanya bisa delete jika status pending atau cancelled
            if (!in_array($transaksi['status'], ['pending', 'cancelled'])) {
                throw new Exception("Hanya transaksi dengan status pending atau cancelled yang bisa dihapus");
            }

            $connection = $this->db->getConnection();
            
            // Check if transaction is already active
            $inTransaction = $connection->inTransaction();
            
            if (!$inTransaction) {
                $connection->beginTransaction();
            }

            try {
                // Jika status pending, kembalikan stok alat
                if ($transaksi['status'] === 'pending') {
                    $this->db->update(
                        "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
                        [$transaksi['jumlah_alat'], $transaksi['id_alat']]
                    );
                }

                // Delete transaksi
                $sql = "DELETE FROM transaksi WHERE id_transaksi = ?";
                $result = $this->db->delete($sql, [$id]);

                if (!$inTransaction) {
                    $connection->commit();
                }
                
                return $result;
            } catch (Exception $e) {
                if (!$inTransaction) {
                    $connection->rollback();
                }
                throw $e;
            }
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Update transaksi data
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, $data)
    {
        try {
            // Check if transaksi exists dan status masih bisa diubah
            $transaksi = $this->getById($id);
            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan");
            }

            if (!in_array($transaksi['status'], ['pending'])) {
                throw new Exception("Hanya transaksi pending yang bisa diubah");
            }

            $this->db->getConnection()->beginTransaction();

            // Kembalikan stok lama
            $this->db->update(
                "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
                [$transaksi['jumlah_alat'], $transaksi['id_alat']]
            );

            // Check stok baru
            $alat = $this->db->fetch("SELECT * FROM alat WHERE id_alat = ?", [$data['id_alat']]);
            if (!$alat || $alat['stok'] < $data['jumlah_alat']) {
                throw new Exception("Stok alat tidak mencukupi");
            }

            // Calculate new total
            $totalHarga = $alat['harga'] * $data['jumlah_alat'];

            // Handle address based on delivery method
            $alamatPengiriman = null;
            if ($data['metode_pengambilan'] === 'delivery_profile') {
                // Use customer's profile address
                $pelanggan = $this->db->fetch("SELECT alamat FROM pelanggan WHERE id_pelanggan = ?", [$data['id_pelanggan']]);
                $alamatPengiriman = $pelanggan['alamat'] ?? null;
            } elseif ($data['metode_pengambilan'] === 'delivery_custom') {
                // Use custom address provided
                $alamatPengiriman = $data['alamat_pengiriman'] ?? null;
            }

            // Update transaksi
            $sql = "UPDATE transaksi SET 
                        id_alat = ?, 
                        tgl_sewa = ?, 
                        tgl_kembali = ?, 
                        jumlah_alat = ?, 
                        total_harga = ?,
                        metode_pengambilan = ?,
                        alamat_pengiriman = ?,
                        updated_at = NOW()
                    WHERE id_transaksi = ?";
            
            $result = $this->db->update($sql, [
                $data['id_alat'],
                $data['tgl_sewa'],
                $data['tgl_kembali'],
                $data['jumlah_alat'],
                $totalHarga,
                $data['metode_pengambilan'] ?? 'pickup',
                $alamatPengiriman,
                $id
            ]);

            // Update stok baru
            $this->db->update(
                "UPDATE alat SET stok = stok - ? WHERE id_alat = ?",
                [$data['jumlah_alat'], $data['id_alat']]
            );

            $this->db->getConnection()->commit();
            return $result;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }

    /**
     * Search transaksi
     * @param string $keyword
     * @return array
     */
    public function search($keyword)
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, a.nama_alat, pb.status_pembayaran 
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN pembayaran pb ON t.id_transaksi = pb.id_transaksi 
                WHERE p.nama LIKE ? OR a.nama_alat LIKE ? OR t.id_transaksi LIKE ?
                ORDER BY t.created_at DESC";
        
        $keyword = "%$keyword%";
        return $this->db->fetchAll($sql, [$keyword, $keyword, $keyword]);
    }
}