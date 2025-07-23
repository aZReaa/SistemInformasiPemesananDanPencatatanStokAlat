<?php

class Alat
{
    private $id_alat;
    private $nama_alat;
    private $kategori;
    private $stok;
    private $harga;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function insertAlat($data)
    {
        $sql = "INSERT INTO alat (nama_alat, id_kategori, stok, harga, deskripsi, gambar) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['nama_alat'],
            $data['id_kategori'] ?? 1, // Default to category 1 if not provided
            $data['stok'],
            $data['harga'],
            $data['deskripsi'] ?? '',
            $data['gambar'] ?? null
        ]);
    }

    public function updateAlat($id, $data)
    {
        $sql = "UPDATE alat SET nama_alat = ?, id_kategori = ?, stok = ?, harga = ?, deskripsi = ?";
        $params = [
            $data['nama_alat'],
            $data['id_kategori'] ?? 1,
            $data['stok'],
            $data['harga'],
            $data['deskripsi'] ?? ''
        ];

        if (isset($data['gambar']) && !empty($data['gambar'])) {
            $sql .= ", gambar = ?";
            $params[] = $data['gambar'];
        }

        $sql .= ", updated_at = NOW() WHERE id_alat = ?";
        $params[] = $id;

        return $this->db->update($sql, $params);
    }

    public function deleteAlat($id)
    {
        $checkTransaksi = $this->db->fetch(
            "SELECT COUNT(*) as count FROM transaksi WHERE id_alat = ? AND status IN ('pending', 'approved', 'ongoing')",
            [$id]
        );

        if ($checkTransaksi['count'] > 0) {
            throw new Exception("Tidak dapat menghapus alat yang sedang dalam transaksi aktif");
        }

        return $this->db->delete("DELETE FROM alat WHERE id_alat = ?", [$id]);
    }

    public function getById($id)
    {
        return $this->db->fetch("
            SELECT a.*, k.nama_kategori as kategori 
            FROM alat a 
            LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
            WHERE a.id_alat = ?
        ", [$id]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("
            SELECT a.*, k.nama_kategori as kategori 
            FROM alat a 
            LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
            ORDER BY a.nama_alat
        ");
    }

    public function getByKategori($kategori_id)
    {
        return $this->db->fetchAll("
            SELECT a.*, k.nama_kategori as kategori 
            FROM alat a 
            LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
            WHERE a.id_kategori = ? 
            ORDER BY a.nama_alat
        ", [$kategori_id]);
    }

    public function getAvailable()
    {
        return $this->db->fetchAll("
            SELECT a.*, k.nama_kategori as kategori 
            FROM alat a 
            LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
            WHERE a.stok > 0 
            ORDER BY a.nama_alat
        ");
    }

    public function updateStok($id, $jumlah)
    {
        return $this->db->update(
            "UPDATE alat SET stok = stok - ? WHERE id_alat = ?",
            [$jumlah, $id]
        );
    }

    public function restoreStok($id, $jumlah)
    {
        return $this->db->update(
            "UPDATE alat SET stok = stok + ? WHERE id_alat = ?",
            [$jumlah, $id]
        );
    }

    public function searchAlat($keyword)
    {
        $sql = "
            SELECT a.*, k.nama_kategori as kategori 
            FROM alat a 
            LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
            WHERE a.nama_alat LIKE ? OR a.deskripsi LIKE ? OR k.nama_kategori LIKE ?
            ORDER BY a.nama_alat
        ";
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm, $searchTerm]);
    }
}