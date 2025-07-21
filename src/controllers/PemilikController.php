<?php

class PemilikController
{
    private $pemilik;
    private $dashboard;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pemilik') {
            header('Location: /hakikah/login');
            exit;
        }

        $this->pemilik = new PemilikUsaha();
        $this->pemilik->setId($_SESSION['pemilik_id']);
        $this->dashboard = new Dashboard();
    }

    /**
     * Dashboard pemilik sesuai UML - hanya laporan keuangan dan transaksi
     */
    public function dashboard()
    {
        // Sesuai UML: Menampilkan data pendapatan, total penyewaan, dan laporan pelanggan
        $laporanKeuangan = $this->pemilik->lihatLaporan();
        require_once __DIR__ . '/../../templates/pages/pemilik/dashboard.php';
    }
}