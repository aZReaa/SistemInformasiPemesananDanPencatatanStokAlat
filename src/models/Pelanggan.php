<?php

class Pelanggan
{
    private $id_pelanggan;
    private $nama;
    private $alamat;
    private $email;
    private $no_hp;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    public function register($data)
    {
        try {
            $this->db->getConnection()->beginTransaction();

            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
            
            $userSql = "INSERT INTO users (username, password, role) VALUES (?, ?, 'pelanggan')";
            $userId = $this->db->insert($userSql, [$data['username'], $hashedPassword]);

            $pelangganSql = "INSERT INTO pelanggan (nama, alamat, email, no_hp, user_id) VALUES (?, ?, ?, ?, ?)";
            $pelangganId = $this->db->insert($pelangganSql, [
                $data['nama'],
                $data['alamat'],
                $data['email'],
                $data['no_hp'],
                $userId
            ]);

            $this->db->getConnection()->commit();
            
            return $pelangganId;
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            throw $e;
        }
    }

    public function pesanAlat($idAlat, $tglSewa, $tglKembali, $jumlahAlat)
    {
        $alat = $this->db->fetch("SELECT * FROM alat WHERE id_alat = ?", [$idAlat]);
        
        if (!$alat || $alat['stok'] < $jumlahAlat) {
            throw new Exception("Stok alat tidak mencukupi");
        }

        $totalHarga = $alat['harga'] * $jumlahAlat;
        
        $sql = "INSERT INTO transaksi (id_pelanggan, id_alat, tgl_sewa, tgl_kembali, jumlah_alat, total_harga) 
                VALUES (?, ?, ?, ?, ?, ?)";
        
        return $this->db->insert($sql, [
            $this->id_pelanggan,
            $idAlat,
            $tglSewa,
            $tglKembali,
            $jumlahAlat,
            $totalHarga
        ]);
    }

    /**
     * Konfirmasi pembayaran sesuai UML requirement
     * @param int $idTransaksi
     * @param float $jumlah
     * @param string|null $buktiTransfer - path file bukti transfer
     * @return bool|int
     */
    public function konfirmasiPembayaran($idTransaksi, $jumlah, $buktiTransfer = null)
    {
        try {
            $this->db->getConnection()->beginTransaction();
            
            // Validasi transaksi exists dan belongs to this pelanggan
            $transaksi = $this->db->fetch(
                "SELECT * FROM transaksi WHERE id_transaksi = ? AND id_pelanggan = ?", 
                [$idTransaksi, $this->id_pelanggan]
            );
            
            if (!$transaksi) {
                throw new Exception("Transaksi tidak ditemukan atau bukan milik Anda");
            }
            
            // Check if payment already exists
            $existingPayment = $this->db->fetch(
                "SELECT * FROM pembayaran WHERE id_transaksi = ?", 
                [$idTransaksi]
            );
            
            if ($existingPayment) {
                throw new Exception("Pembayaran sudah pernah dikonfirmasi");
            }
            
            // Insert payment record - sesuai schema yang benar
            $sql = "INSERT INTO pembayaran (id_transaksi, jumlah, bukti_transfer, status_pembayaran, tanggal_bayar) 
                    VALUES (?, ?, ?, 'pending', NOW())";
            $paymentId = $this->db->insert($sql, [$idTransaksi, $jumlah, $buktiTransfer]);
            
            // Update transaksi status ke 'confirmed' setelah upload bukti pembayaran
            $updateSql = "UPDATE transaksi SET status = 'confirmed' WHERE id_transaksi = ?";
            $this->db->update($updateSql, [$idTransaksi]);
            
            $this->db->getConnection()->commit();
            return $paymentId;
            
        } catch (Exception $e) {
            $this->db->getConnection()->rollback();
            error_log("Error in konfirmasiPembayaran: " . $e->getMessage());
            throw $e;
        }
    }

    public function lihatStatusPenyewaan($idPelanggan)
    {
        $sql = "SELECT t.*, a.nama_alat, a.harga, p.status_pembayaran 
                FROM transaksi t 
                JOIN alat a ON t.id_alat = a.id_alat 
                LEFT JOIN pembayaran p ON t.id_transaksi = p.id_transaksi 
                WHERE t.id_pelanggan = ? 
                ORDER BY t.created_at DESC";
        
        return $this->db->fetchAll($sql, [$idPelanggan]);
    }

    public function getById($id)
    {
        $sql = "SELECT * FROM pelanggan WHERE id_pelanggan = ?";
        return $this->db->fetch($sql, [$id]);
    }

    public function getAllAlat()
    {
        $sql = "SELECT * FROM alat WHERE stok > 0 ORDER BY nama_alat";
        return $this->db->fetchAll($sql);
    }

    public function getAll()
    {
        $sql = "SELECT id_pelanggan, nama, email, no_hp, alamat FROM pelanggan ORDER BY nama";
        return $this->db->fetchAll($sql);
    }

    public function setId($id)
    {
        $this->id_pelanggan = $id;
    }
}