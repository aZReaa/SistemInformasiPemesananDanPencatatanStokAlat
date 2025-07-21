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
        $sql = "INSERT INTO alat (nama_alat, kategori, stok, harga, deskripsi, gambar) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['nama_alat'],
            $data['kategori'],
            $data['stok'],
            $data['harga'],
            $data['deskripsi'] ?? '',
            $data['gambar'] ?? null
        ]);
    }

    public function updateAlat($id, $data)
    {
        $sql = "UPDATE alat SET nama_alat = ?, kategori = ?, stok = ?, harga = ?, deskripsi = ?";
        $params = [
            $data['nama_alat'],
            $data['kategori'],
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
        return $this->db->fetch("SELECT * FROM alat WHERE id_alat = ?", [$id]);
    }

    public function getAll()
    {
        return $this->db->fetchAll("SELECT * FROM alat ORDER BY nama_alat");
    }

    public function getByKategori($kategori)
    {
        return $this->db->fetchAll("SELECT * FROM alat WHERE kategori = ? ORDER BY nama_alat", [$kategori]);
    }

    public function getAvailable()
    {
        return $this->db->fetchAll("SELECT * FROM alat WHERE stok > 0 ORDER BY nama_alat");
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
        $sql = "SELECT * FROM alat WHERE nama_alat LIKE ? OR deskripsi LIKE ? ORDER BY nama_alat";
        $searchTerm = "%{$keyword}%";
        return $this->db->fetchAll($sql, [$searchTerm, $searchTerm]);
    }
}