<?php

class Admin
{
    private $id_admin;
    private $nama_admin;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function kelolaPelanggan()
    {
        $sql = "SELECT p.*, u.username 
                FROM pelanggan p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.nama";
        return $this->db->fetchAll($sql);
    }

    public function tambahPelanggan($data)
    {
        // First create user account
        $userSql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'pelanggan')";
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
        $userId = $this->db->insert($userSql, [$data['username'], $hashedPassword]);
        
        if ($userId) {
            // Then create pelanggan record
            $pelangganSql = "INSERT INTO pelanggan (nama, alamat, email, no_hp, user_id) 
                            VALUES (?, ?, ?, ?, ?)";
            
            return $this->db->insert($pelangganSql, [
                $data['nama'],
                $data['alamat'],
                $data['email'],
                $data['no_hp'],
                $userId
            ]);
        }
        
        return false;
    }

    public function editPelanggan($id, $data)
    {
        $sql = "UPDATE pelanggan SET nama = ?, alamat = ?, email = ?, no_hp = ? WHERE id_pelanggan = ?";
        
        $result = $this->db->update($sql, [
            $data['nama'],
            $data['alamat'],
            $data['email'],
            $data['no_hp'],
            $id
        ]);

        // Update username if provided
        if (isset($data['username']) && !empty($data['username'])) {
            $pelanggan = $this->getPelangganById($id);
            if ($pelanggan && $pelanggan['user_id']) {
                $this->db->update("UPDATE users SET username = ? WHERE id = ?", 
                    [$data['username'], $pelanggan['user_id']]);
            }
        }

        return $result;
    }

    public function getPelangganById($id)
    {
        $sql = "SELECT p.*, u.username 
                FROM pelanggan p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.id_pelanggan = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function resetPasswordPelanggan($id, $newPassword)
    {
        $pelanggan = $this->getPelangganById($id);
        if ($pelanggan && $pelanggan['user_id']) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            return $this->db->update("UPDATE users SET password = ? WHERE id = ?", 
                [$hashedPassword, $pelanggan['user_id']]);
        }
        return false;
    }

    public function filterPelanggan($filters = [])
    {
        $sql = "SELECT p.*, u.username 
                FROM pelanggan p 
                LEFT JOIN users u ON p.user_id = u.id WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (p.nama LIKE ? OR p.email LIKE ? OR p.no_hp LIKE ? OR u.username LIKE ?)";
            $searchTerm = '%' . $filters['search'] . '%';
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if (!empty($filters['date_from'])) {
            $sql .= " AND p.created_at >= ?";
            $params[] = $filters['date_from'];
        }

        if (!empty($filters['date_to'])) {
            $sql .= " AND p.created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        $sql .= " ORDER BY p.nama";
        
        return $this->db->fetchAll($sql, $params);
    }

    public function kelolaAlat()
    {
        $sql = "SELECT a.*, k.nama_kategori 
                FROM alat a 
                LEFT JOIN kategori_layanan k ON a.id_kategori = k.id_kategori 
                ORDER BY a.nama_alat";
        return $this->db->fetchAll($sql);
    }

    public function kelolaKategori()
    {
        return $this->db->fetchAll("SELECT * FROM kategori_layanan ORDER BY nama_kategori");
    }

    public function tambahKategori($data)
    {
        $sql = "INSERT INTO kategori_layanan (nama_kategori, deskripsi) VALUES (?, ?)";
        
        return $this->db->insert($sql, [
            $data['nama_kategori'],
            $data['deskripsi']
        ]);
    }

    public function updateKategori($id, $data)
    {
        $sql = "UPDATE kategori_layanan SET nama_kategori = ?, deskripsi = ? WHERE id_kategori = ?";
        
        return $this->db->update($sql, [
            $data['nama_kategori'],
            $data['deskripsi'],
            $id
        ]);
    }

    public function hapusKategori($id)
    {
        return $this->db->delete("DELETE FROM kategori_layanan WHERE id_kategori = ?", [$id]);
    }

    public function getKategoriById($id)
    {
        return $this->db->fetch("SELECT * FROM kategori_layanan WHERE id_kategori = ?", [$id]);
    }

    public function tambahAlat($data)
    {
        $sql = "INSERT INTO alat (nama_alat, id_kategori, stok, harga, deskripsi, gambar) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['nama_alat'],
            $data['id_kategori'],
            $data['stok'],
            $data['harga'],
            $data['deskripsi'],
            $data['gambar'] ?? null
        ]);
    }

    public function updateAlat($id, $data)
    {
        $sql = "UPDATE alat SET nama_alat = ?, id_kategori = ?, stok = ?, harga = ?, deskripsi = ?";
        $params = [
            $data['nama_alat'],
            $data['id_kategori'],
            $data['stok'],
            $data['harga'],
            $data['deskripsi']
        ];

        if (isset($data['gambar'])) {
            $sql .= ", gambar = ?";
            $params[] = $data['gambar'];
        }

        $sql .= " WHERE id_alat = ?";
        $params[] = $id;

        return $this->db->update($sql, $params);
    }

    public function hapusAlat($id)
    {
        return $this->db->delete("DELETE FROM alat WHERE id_alat = ?", [$id]);
    }

    public function catatTransaksi()
    {
        $sql = "SELECT t.*, p.nama as nama_pelanggan, a.nama_alat 
                FROM transaksi t 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function updateStatusTransaksi($idTransaksi, $status)
    {
        return $this->db->update(
            "UPDATE transaksi SET status = ? WHERE id_transaksi = ?",
            [$status, $idTransaksi]
        );
    }

    public function kelolaPembayaran()
    {
        $sql = "SELECT p.*, t.id_pelanggan, pel.nama as nama_pelanggan, t.total_harga,
                       a.nama_alat, adm.nama_admin as verified_by_name
                FROM pembayaran p 
                JOIN transaksi t ON p.id_transaksi = t.id_transaksi 
                JOIN pelanggan pel ON t.id_pelanggan = pel.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat
                LEFT JOIN admin adm ON p.verified_by = adm.id_admin
                ORDER BY p.tanggal_bayar DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function verifikasiPembayaran($idPembayaran, $status, $adminId)
    {
        $sql = "UPDATE pembayaran SET status_pembayaran = ?, verified_by = ?, verified_at = NOW() 
                WHERE id_pembayaran = ?";
        
        return $this->db->update($sql, [$status, $adminId, $idPembayaran]);
    }

    public function kelolaPengembalian()
    {
        $sql = "SELECT pg.*, t.id_pelanggan, p.nama as nama_pelanggan, a.nama_alat 
                FROM pengembalian pg 
                JOIN transaksi t ON pg.id_transaksi = t.id_transaksi 
                JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan 
                JOIN alat a ON t.id_alat = a.id_alat 
                ORDER BY pg.created_at DESC";
        
        return $this->db->fetchAll($sql);
    }

    public function catatPengembalian($data)
    {
        $sql = "INSERT INTO pengembalian (id_transaksi, tanggal, kondisi_alat, catatan, denda) 
                VALUES (?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $data['id_transaksi'],
            $data['tanggal'],
            $data['kondisi_alat'],
            $data['catatan'],
            $data['denda']
        ]);
    }

    public function lihatLaporan($startDate = null, $endDate = null)
    {
        $sql = "SELECT 
                    COUNT(t.id_transaksi) as total_transaksi,
                    SUM(t.total_harga) as total_pendapatan,
                    COUNT(DISTINCT t.id_pelanggan) as total_pelanggan_aktif
                FROM transaksi t";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " WHERE t.created_at BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        return $this->db->fetch($sql, $params);
    }

    public function setId($id)
    {
        $this->id_admin = $id;
    }
}