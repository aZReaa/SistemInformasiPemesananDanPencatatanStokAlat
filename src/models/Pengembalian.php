<?php

class Pengembalian
{
    private $id_pengembalian;
    private $id_transaksi;
    private $tanggal;
    private $kondisi_alat;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function insert($data)
    {
        try {
            $this->db->getConnection()->beginTransaction();

            // Check if this transaction has already been returned
            $existingReturn = $this->db->fetch(
                "SELECT id_pengembalian FROM pengembalian WHERE id_transaksi = ?",
                [$data['id_transaksi']]
            );

            if ($existingReturn) {
                throw new Exception("Transaksi ini sudah pernah dicatat pengembaliannya");
            }

            // Get transaction details first to validate
            $transaksi = $this->db->fetch(
                "SELECT * FROM transaksi WHERE id_transaksi = ?",
                [$data['id_transaksi']]
            );

            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan");
            }

            // Only allow return for ongoing transactions
            if (!in_array($transaksi['status'], ['ongoing', 'approved', 'aktif'])) {
                throw new Exception("Hanya transaksi yang sedang berlangsung yang bisa dikembalikan. Status saat ini: " . $transaksi['status']);
            }

            $sql = "INSERT INTO pengembalian (id_transaksi, tanggal, kondisi_alat, catatan, denda) 
                    VALUES (?, ?, ?, ?, ?)";
            
            $pengembalianId = $this->db->insert($sql, [
                $data['id_transaksi'],
                $data['tanggal'],
                $data['kondisi_alat'],
                $data['catatan'] ?? '',
                $data['denda'] ?? 0
            ]);

            // Update transaction status to completed
            $this->db->update(
                "UPDATE transaksi SET status = 'completed' WHERE id_transaksi = ?",
                [$data['id_transaksi']]
            );

            // Restore stock based on condition
            if ($data['kondisi_alat'] === 'baik') {
                // Return all items if in good condition
                $this->db->update(
                    "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
                    [$transaksi['jumlah_alat'], $transaksi['id_alat']]
                );
            } elseif ($data['kondisi_alat'] === 'rusak') {
                // Return partial items if some are damaged
                $stokKembali = max(0, $transaksi['jumlah_alat'] - ($data['rusak_count'] ?? 0));
                $this->db->update(
                    "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
                    [$stokKembali, $transaksi['id_alat']]
                );
            } elseif ($data['kondisi_alat'] === 'hilang') {
                // Don't return any stock if items are lost
                // No stock update needed
            }

            $this->db->getConnection()->commit();
            
            return $pengembalianId;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }

    public function getById($id)
    {
        $sql = "SELECT pg.*, t.id_pelanggan, t.jumlah_alat, t.total_harga,
                       p.nama as nama_pelanggan, p.alamat, p.no_hp,
                       a.nama_alat, a.harga
                FROM pengembalian pg 
                JOIN transaksi t ON pg.id_transaksi = t.id_transaksi 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE pg.id_pengembalian = ?";
        
        return $this->db->fetch($sql, [$id]);
    }

    public function getByTransaksi($idTransaksi)
    {
        return $this->db->fetch(
            "SELECT * FROM pengembalian WHERE id_transaksi = ?",
            [$idTransaksi]
        );
    }

    public function getAll()
    {
        $sql = "SELECT pg.*, t.id_pelanggan, t.jumlah_alat,
                       p.nama as nama_pelanggan, a.nama_alat, k.nama_kategori as kategori
                FROM pengembalian pg 
                JOIN transaksi t ON pg.id_transaksi = t.id_transaksi 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori
                ORDER BY pg.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function getByPelanggan($idPelanggan)
    {
        $sql = "SELECT pg.*, t.jumlah_alat, a.nama_alat
                FROM pengembalian pg 
                JOIN transaksi t ON pg.id_transaksi = t.id_transaksi 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE t.id_pelanggan = ? 
                ORDER BY pg.created_at DESC";
        
        return $this->db->fetchAll($sql, [$idPelanggan]);
    }

    public function getLaporanKondisiAlat($startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                    kondisi_alat,
                    COUNT(*) as jumlah,
                    SUM(denda) as total_denda
                FROM pengembalian";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " WHERE created_at BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $sql .= " GROUP BY kondisi_alat";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function getStatistikKondisi()
    {
        $sql = "SELECT 
                    COUNT(CASE WHEN kondisi_alat = 'baik' THEN 1 END) as baik,
                    COUNT(CASE WHEN kondisi_alat = 'rusak' THEN 1 END) as rusak,
                    COUNT(CASE WHEN kondisi_alat = 'hilang' THEN 1 END) as hilang,
                    SUM(denda) as total_denda,
                    COUNT(*) as total_pengembalian
                FROM pengembalian";
        
        return $this->db->fetch($sql);
    }

    public function getTerlambat()
    {
        $sql = "SELECT pg.*, t.tgl_kembali, t.jumlah_alat,
                       p.nama as nama_pelanggan, p.no_hp,
                       a.nama_alat,
                       DATEDIFF(pg.tanggal, t.tgl_kembali) as hari_terlambat
                FROM pengembalian pg 
                JOIN transaksi t ON pg.id_transaksi = t.id_transaksi 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                WHERE pg.tanggal > t.tgl_kembali 
                ORDER BY hari_terlambat DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function update($id, $data)
    {
        $sql = "UPDATE pengembalian SET kondisi_alat = ?, catatan = ?, denda = ? 
                WHERE id_pengembalian = ?";
        
        return $this->db->update($sql, [
            $data['kondisi_alat'],
            $data['catatan'] ?? '',
            $data['denda'] ?? 0,
            $id
        ]);
    }
}