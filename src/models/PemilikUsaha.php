<?php

class PemilikUsaha
{
    private $id_pemilik;
    private $nama_pemilik;
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Method sesuai UML - Melihat laporan keuangan dan transaksi
     * Menampilkan data pendapatan, total penyewaan, dan laporan pelanggan
     */
    public function lihatLaporan()
    {
        // Sesuai UML Activity Diagram:
        // 1. Menampilkan data pendapatan yang telah diperoleh
        // 2. Menampilkan data jumlah total penyewaan yang telah terjadi  
        // 3. Menampilkan laporan yang merangkum data pelanggan dan transaksi
        
        $sql = "SELECT 
                    COUNT(t.id_transaksi) as total_transaksi,
                    SUM(CASE WHEN p.status_pembayaran = 'verified' THEN t.total_harga ELSE 0 END) as total_pendapatan,
                    COUNT(DISTINCT t.id_pelanggan) as total_pelanggan,
                    AVG(t.total_harga) as rata_rata_transaksi
                FROM transaksi t 
                LEFT JOIN pembayaran p ON t.id_transaksi = p.id_transaksi";

        $summaryData = $this->db->fetch($sql);
        
        // Detail transaksi untuk laporan
        $transaksiDetail = $this->db->fetchAll("
            SELECT 
                t.id_transaksi,
                p.nama as nama_pelanggan,
                a.nama_alat,
                t.tgl_sewa,
                t.tgl_kembali,
                t.total_harga,
                t.status,
                pb.status_pembayaran
            FROM transaksi t
            JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
            JOIN alat a ON t.id_alat = a.id_alat
            LEFT JOIN pembayaran pb ON t.id_transaksi = pb.id_transaksi
            ORDER BY t.created_at DESC
            LIMIT 20
        ");

        return [
            'summary' => $summaryData,
            'transactions' => $transaksiDetail
        ];
    }

    public function setId($id)
    {
        $this->id_pemilik = $id;
    }
}