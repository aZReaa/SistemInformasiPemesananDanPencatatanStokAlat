<?php

class AdminController
{
    private $admin;
    private $alat;
    private $transaksi;
    private $pembayaran;
    private $pengembalian;
    private $dashboard;
    private $kategori;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }

        $this->admin = new Admin();
        $this->admin->setId($_SESSION['admin_id']);
        $this->alat = new Alat();
        $this->transaksi = new Transaksi();
        $this->pembayaran = new Pembayaran();
        $this->pengembalian = new Pengembalian();
        $this->dashboard = new Dashboard();
        $this->kategori = new KategoriLayanan();
    }

    /**
     * Dashboard admin menggunakan Dashboard class sesuai UML
     */
    public function dashboard()
    {
        $dashboardData = $this->dashboard->viewDashboard('admin', $_SESSION['admin_id']);
        require_once __DIR__ . '/../../templates/pages/admin/dashboard.php';
    }

    public function pelanggan()
    {
        // Handle filtering
        $filters = [
            'search' => $_GET['search'] ?? '',
            'date_from' => $_GET['date_from'] ?? '',
            'date_to' => $_GET['date_to'] ?? ''
        ];

        // Remove empty filters
        $filters = array_filter($filters, function($value) {
            return !empty($value);
        });

        if (!empty($filters)) {
            $pelangganList = $this->admin->filterPelanggan($filters);
        } else {
            $pelangganList = $this->admin->kelolaPelanggan();
        }
        
        require_once __DIR__ . '/../../templates/pages/admin/pelanggan.php';
    }

    public function tambahPelanggan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama' => trim($_POST['nama'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'no_hp' => trim($_POST['no_hp'] ?? ''),
                'username' => trim($_POST['username'] ?? ''),
                'password' => $_POST['password'] ?? ''
            ];

            $errors = $this->validatePelangganData($data);

            if (empty($errors)) {
                try {
                    $result = $this->admin->tambahPelanggan($data);
                    if ($result) {
                        $_SESSION['success'] = 'Pelanggan berhasil ditambahkan';
                    } else {
                        $_SESSION['error'] = 'Gagal menambahkan pelanggan';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menambahkan pelanggan: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
            }
        }
        
        header('Location: /hakikah/admin/pelanggan');
        exit;
    }

    public function editPelanggan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'nama' => trim($_POST['nama'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'no_hp' => trim($_POST['no_hp'] ?? ''),
                'username' => trim($_POST['username'] ?? '')
            ];

            $errors = $this->validatePelangganEditData($data);

            if (empty($errors) && $id) {
                try {
                    $result = $this->admin->editPelanggan($id, $data);
                    if ($result) {
                        $_SESSION['success'] = 'Data pelanggan berhasil diperbarui';
                    } else {
                        $_SESSION['error'] = 'Gagal memperbarui data pelanggan';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memperbarui data pelanggan: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        header('Location: /hakikah/admin/pelanggan');
        exit;
    }

    public function resetPasswordPelanggan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $newPassword = $_POST['new_password'] ?? '';

            if ($id && !empty($newPassword)) {
                try {
                    $result = $this->admin->resetPasswordPelanggan($id, $newPassword);
                    if ($result) {
                        $_SESSION['success'] = 'Password pelanggan berhasil direset';
                    } else {
                        $_SESSION['error'] = 'Gagal mereset password pelanggan';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal mereset password: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'ID pelanggan dan password baru harus diisi';
            }
        }
        
        header('Location: /hakikah/admin/pelanggan');
        exit;
    }

    public function deletePelanggan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                try {
                    $db = Database::getInstance();
                    $db->delete("DELETE FROM pelanggan WHERE id_pelanggan = ?", [$id]);
                    $_SESSION['success'] = 'Pelanggan berhasil dihapus';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menghapus pelanggan: ' . $e->getMessage();
                }
            }
        }
        
        header('Location: /hakikah/admin/pelanggan');
        exit;
    }

    public function alat()
    {
        $alatList = $this->admin->kelolaAlat();
        $kategoriList = $this->admin->kelolaKategori();
        require_once __DIR__ . '/../../templates/pages/admin/alat.php';
    }

    public function tambahAlat()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_alat' => trim($_POST['nama_alat'] ?? ''),
                'id_kategori' => (int)($_POST['id_kategori'] ?? 0),
                'stok' => (int)($_POST['stok'] ?? 0),
                'harga' => (float)($_POST['harga'] ?? 0),
                'deskripsi' => trim($_POST['deskripsi'] ?? '')
            ];

            // Handle image upload
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $imageResult = $this->handleImageUpload($_FILES['gambar']);
                if ($imageResult['success']) {
                    $data['gambar'] = $imageResult['filename'];
                } else {
                    $_SESSION['error'] = $imageResult['error'];
                    header('Location: /hakikah/admin/alat');
                    exit;
                }
            }

            $errors = $this->validateAlatData($data);

            if (empty($errors)) {
                try {
                    $this->admin->tambahAlat($data);
                    $_SESSION['success'] = 'Alat berhasil ditambahkan';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menambahkan alat: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
            }
        }
        
        header('Location: /hakikah/admin/alat');
        exit;
    }

    public function editAlat()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'nama_alat' => trim($_POST['nama_alat'] ?? ''),
                'id_kategori' => (int)($_POST['id_kategori'] ?? 0),
                'stok' => (int)($_POST['stok'] ?? 0),
                'harga' => (float)($_POST['harga'] ?? 0),
                'deskripsi' => trim($_POST['deskripsi'] ?? '')
            ];

            // Handle image upload
            if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
                $imageResult = $this->handleImageUpload($_FILES['gambar']);
                if ($imageResult['success']) {
                    $data['gambar'] = $imageResult['filename'];
                } else {
                    $_SESSION['error'] = $imageResult['error'];
                    header('Location: /hakikah/admin/alat');
                    exit;
                }
            }

            $errors = $this->validateAlatData($data);

            if (empty($errors) && $id) {
                try {
                    $this->admin->updateAlat($id, $data);
                    $_SESSION['success'] = 'Alat berhasil diperbarui';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memperbarui alat: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        header('Location: /hakikah/admin/alat');
        exit;
    }

    public function deleteAlat()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                try {
                    $this->admin->hapusAlat($id);
                    $_SESSION['success'] = 'Alat berhasil dihapus';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menghapus alat: ' . $e->getMessage();
                }
            }
        }
        
        header('Location: /hakikah/admin/alat');
        exit;
    }

    public function transaksi()
    {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        if (!empty($search)) {
            $transaksiList = $this->transaksi->search($search);
        } elseif (!empty($status)) {
            $transaksiList = $this->transaksi->getByStatus($status);
        } else {
            $transaksiList = $this->transaksi->getAll();
        }
        
        // Load models dan data untuk dropdown di modal
        require_once __DIR__ . '/../models/Pelanggan.php';
        require_once __DIR__ . '/../models/Alat.php';
        
        // Prepare data for dropdowns
        $pelangganModel = new Pelanggan();
        $alatModel = new Alat();
        $pelangganList = $pelangganModel->getAll();
        $alatList = $alatModel->getAll();
        
        require_once __DIR__ . '/../../templates/pages/admin/transaksi.php';
    }

    public function updateStatusTransaksi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($id && $status) {
                try {
                    $this->admin->updateStatusTransaksi($id, $status);
                    $_SESSION['success'] = 'Status transaksi berhasil diperbarui';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memperbarui status: ' . $e->getMessage();
                }
            }
        }
        
        header('Location: /hakikah/admin/transaksi');
        exit;
    }

    public function tambahTransaksi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_pelanggan' => trim($_POST['id_pelanggan'] ?? ''),
                'id_alat' => trim($_POST['id_alat'] ?? ''),
                'tgl_sewa' => trim($_POST['tgl_sewa'] ?? ''),
                'tgl_kembali' => trim($_POST['tgl_kembali'] ?? ''),
                'jumlah_alat' => (int)($_POST['jumlah_alat'] ?? 0),
                'metode_pengambilan' => $_POST['metode_pengambilan'] ?? 'pickup',
                'alamat_pengiriman' => trim($_POST['alamat_pengiriman'] ?? '')
            ];

            $errors = $this->validateTransaksiData($data);

            if (empty($errors)) {
                try {
                    $result = $this->transaksi->insertTransaksi($data);
                    if ($result) {
                        $_SESSION['success'] = 'Transaksi berhasil ditambahkan';
                    } else {
                        $_SESSION['error'] = 'Gagal menambahkan transaksi';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menambahkan transaksi: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        header('Location: /hakikah/admin/transaksi');
        exit;
    }

    public function editTransaksi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $data = [
                'id_pelanggan' => trim($_POST['id_pelanggan'] ?? ''),
                'id_alat' => trim($_POST['id_alat'] ?? ''),
                'tgl_sewa' => trim($_POST['tgl_sewa'] ?? ''),
                'tgl_kembali' => trim($_POST['tgl_kembali'] ?? ''),
                'jumlah_alat' => (int)($_POST['jumlah_alat'] ?? 0),
                'metode_pengambilan' => $_POST['metode_pengambilan'] ?? 'pickup',
                'alamat_pengiriman' => trim($_POST['alamat_pengiriman'] ?? '')
            ];

            $errors = $this->validateTransaksiEditData($data);

            if (empty($errors) && $id) {
                try {
                    $result = $this->transaksi->update($id, $data);
                    if ($result) {
                        $_SESSION['success'] = 'Transaksi berhasil diperbarui';
                    } else {
                        $_SESSION['error'] = 'Gagal memperbarui transaksi';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memperbarui transaksi: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        header('Location: /hakikah/admin/transaksi');
        exit;
    }

    public function deleteTransaksi()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if ($id) {
                try {
                    $result = $this->transaksi->delete($id);
                    if ($result) {
                        $_SESSION['success'] = 'Transaksi berhasil dihapus';
                    } else {
                        $_SESSION['error'] = 'Gagal menghapus transaksi';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menghapus transaksi: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'ID transaksi tidak valid';
            }
        } else {
            $_SESSION['error'] = 'Method tidak diizinkan';
        }
        
        header('Location: /hakikah/admin/transaksi');
        exit;
    }

    private function validateTransaksiData($data)
    {
        $errors = [];

        if (empty($data['id_pelanggan'])) {
            $errors[] = 'Pelanggan harus dipilih';
        }

        if (empty($data['id_alat'])) {
            $errors[] = 'Alat harus dipilih';
        }

        if (empty($data['tgl_sewa'])) {
            $errors[] = 'Tanggal sewa harus diisi';
        }

        if (empty($data['tgl_kembali'])) {
            $errors[] = 'Tanggal kembali harus diisi';
        }

        if ($data['jumlah_alat'] <= 0) {
            $errors[] = 'Jumlah alat harus lebih dari 0';
        }

        // Validate dates
        if (!empty($data['tgl_sewa']) && !empty($data['tgl_kembali'])) {
            if (strtotime($data['tgl_kembali']) <= strtotime($data['tgl_sewa'])) {
                $errors[] = 'Tanggal kembali harus setelah tanggal sewa';
            }
        }

        // Validate delivery method and address
        if (!empty($data['metode_pengambilan'])) {
            if ($data['metode_pengambilan'] === 'delivery_profile') {
                // Check if customer has address in profile
                $db = Database::getInstance();
                $pelanggan = $db->fetch("SELECT alamat FROM pelanggan WHERE id_pelanggan = ?", [$data['id_pelanggan']]);
                if (empty($pelanggan['alamat'])) {
                    $errors[] = 'Alamat profil pelanggan belum diisi. Pilih alamat custom atau update profil pelanggan.';
                }
            } elseif ($data['metode_pengambilan'] === 'delivery_custom') {
                if (empty($data['alamat_pengiriman'])) {
                    $errors[] = 'Alamat pengiriman harus diisi untuk delivery custom';
                } elseif (strlen($data['alamat_pengiriman']) < 10) {
                    $errors[] = 'Alamat pengiriman minimal 10 karakter';
                }
            }
        }

        return $errors;
    }

    private function validateTransaksiEditData($data)
    {
        $errors = [];

        if (empty($data['id_pelanggan'])) {
            $errors[] = 'Pelanggan harus dipilih';
        }

        if (empty($data['id_alat'])) {
            $errors[] = 'Alat harus dipilih';
        }

        if (empty($data['tgl_sewa'])) {
            $errors[] = 'Tanggal sewa harus diisi';
        }

        if (empty($data['tgl_kembali'])) {
            $errors[] = 'Tanggal kembali harus diisi';
        }

        if ($data['jumlah_alat'] <= 0) {
            $errors[] = 'Jumlah alat harus lebih dari 0';
        }

        // Validate dates
        if (!empty($data['tgl_sewa']) && !empty($data['tgl_kembali'])) {
            if (strtotime($data['tgl_kembali']) <= strtotime($data['tgl_sewa'])) {
                $errors[] = 'Tanggal kembali harus setelah tanggal sewa';
            }
        }

        // Validate delivery method and address
        if (!empty($data['metode_pengambilan'])) {
            if ($data['metode_pengambilan'] === 'delivery_profile') {
                // Check if customer has address in profile
                $db = Database::getInstance();
                $pelanggan = $db->fetch("SELECT alamat FROM pelanggan WHERE id_pelanggan = ?", [$data['id_pelanggan']]);
                if (empty($pelanggan['alamat'])) {
                    $errors[] = 'Alamat profil pelanggan belum diisi. Pilih alamat custom atau update profil pelanggan.';
                }
            } elseif ($data['metode_pengambilan'] === 'delivery_custom') {
                if (empty($data['alamat_pengiriman'])) {
                    $errors[] = 'Alamat pengiriman harus diisi untuk delivery custom';
                } elseif (strlen($data['alamat_pengiriman']) < 10) {
                    $errors[] = 'Alamat pengiriman minimal 10 karakter';
                }
            }
        }

        return $errors;
    }

    public function pembayaran()
    {
        $pembayaranList = $this->admin->kelolaPembayaran();
        require_once __DIR__ . '/../../templates/pages/admin/pembayaran.php';
    }

    public function verifikasiPembayaran()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $status = $_POST['status'] ?? null;

            if ($id && $status) {
                try {
                    $this->admin->verifikasiPembayaran($id, $status, $_SESSION['admin_id']);
                    $_SESSION['success'] = 'Pembayaran berhasil diverifikasi';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memverifikasi pembayaran: ' . $e->getMessage();
                }
            }
        }
        
        header('Location: /hakikah/admin/pembayaran');
        exit;
    }

    public function pengembalian()
    {
        $pengembalianList = $this->admin->kelolaPengembalian();
        require_once __DIR__ . '/../../templates/pages/admin/pengembalian.php';
    }

    public function tambahPengembalian()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_transaksi' => $_POST['id_transaksi'] ?? null,
                'tanggal' => $_POST['tanggal'] ?? date('Y-m-d'),
                'kondisi_alat' => $_POST['kondisi_alat'] ?? 'baik',
                'catatan' => trim($_POST['catatan'] ?? ''),
                'denda' => (float)($_POST['denda'] ?? 0)
            ];

            if ($data['id_transaksi']) {
                try {
                    $this->admin->catatPengembalian($data);
                    $_SESSION['success'] = 'Pengembalian berhasil dicatat';
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal mencatat pengembalian: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = 'ID transaksi harus diisi';
            }
        }
        
        header('Location: /hakikah/admin/pengembalian');
        exit;
    }

    public function laporan()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        
        $laporan = $this->admin->lihatLaporan($startDate, $endDate);
        $transaksiList = $this->transaksi->getLaporanByPeriode($startDate, $endDate);
        
        require_once __DIR__ . '/../../templates/pages/admin/laporan.php';
    }

    /**
     * Cetak laporan dalam format print-friendly (sesuai UML requirement)
     */
    public function cetakLaporan()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $format = $_GET['format'] ?? 'html'; // html, pdf
        
        $laporan = $this->admin->lihatLaporan($startDate, $endDate);
        $transaksiList = $this->transaksi->getLaporanByPeriode($startDate, $endDate);
        
        if ($format === 'pdf') {
            $this->generatePDFLaporan($laporan, $transaksiList, $startDate, $endDate);
        } else {
            // Print HTML version
            require_once __DIR__ . '/../../templates/pages/admin/laporan-print.php';
        }
    }

    /**
     * Generate PDF laporan (simple HTML to PDF conversion)
     */
    private function generatePDFLaporan($laporan, $transaksiList, $startDate, $endDate)
    {
        // Set headers untuk PDF download
        header('Content-Type: text/html; charset=utf-8');
        
        // Simple HTML to PDF menggunakan browser print
        echo "<!DOCTYPE html>";
        echo "<html><head>";
        echo "<meta charset='utf-8'>";
        echo "<title>Laporan Penyewaan</title>";
        echo "<style>";
        echo "body { font-family: Arial, sans-serif; margin: 20px; }";
        echo "table { width: 100%; border-collapse: collapse; margin: 20px 0; }";
        echo "th, td { border: 1px solid #ddd; padding: 8px; text-align: left; font-size: 12px; }";
        echo "th { background-color: #f2f2f2; font-weight: bold; }";
        echo ".header { text-align: center; margin-bottom: 20px; }";
        echo ".summary { margin: 20px 0; }";
        echo "@media print { .no-print { display: none; } body { margin: 0; } }";
        echo "@page { margin: 1cm; }";
        echo "</style>";
        echo "<script>window.onload = function() { window.print(); }</script>";
        echo "</head><body>";
        
        echo "<div class='header'>";
        echo "<h2>LAPORAN PENYEWAAN ALAT PESTA</h2>";
        echo "<p>Periode: " . date('d/m/Y', strtotime($startDate)) . " - " . date('d/m/Y', strtotime($endDate)) . "</p>";
        echo "<p>Dicetak pada: " . date('d/m/Y H:i:s') . "</p>";
        echo "</div>";
        
        // Summary
        echo "<div class='summary'>";
        echo "<h3>Ringkasan Laporan</h3>";
        echo "<table>";
        echo "<tr><th style='width: 200px;'>Total Transaksi</th><td>" . count($transaksiList) . " transaksi</td></tr>";
        echo "<tr><th>Total Pendapatan</th><td>Rp " . number_format($laporan['total_pendapatan'], 0, ',', '.') . "</td></tr>";
        echo "<tr><th>Rata-rata per Transaksi</th><td>Rp " . number_format(count($transaksiList) > 0 ? $laporan['total_pendapatan'] / count($transaksiList) : 0, 0, ',', '.') . "</td></tr>";
        echo "<tr><th>Status Pembayaran Lunas</th><td>" . $laporan['transaksi_lunas'] . " transaksi</td></tr>";
        echo "<tr><th>Status Pembayaran Pending</th><td>" . $laporan['transaksi_pending'] . " transaksi</td></tr>";
        echo "</table>";
        echo "</div>";
        
        // Transaksi detail
        if (!empty($transaksiList)) {
            echo "<h3>Detail Transaksi</h3>";
            echo "<table>";
            echo "<tr>";
            echo "<th style='width: 50px;'>ID</th>";
            echo "<th style='width: 80px;'>Tanggal</th>";
            echo "<th style='width: 120px;'>Pelanggan</th>";
            echo "<th style='width: 120px;'>Alat</th>";
            echo "<th style='width: 50px;'>Qty</th>";
            echo "<th style='width: 120px;'>Periode Sewa</th>";
            echo "<th style='width: 80px;'>Total</th>";
            echo "<th style='width: 80px;'>Status</th>";
            echo "</tr>";
            
            foreach ($transaksiList as $t) {
                echo "<tr>";
                echo "<td>" . $t['id_transaksi'] . "</td>";
                echo "<td>" . date('d/m/Y', strtotime($t['created_at'])) . "</td>";
                echo "<td>" . htmlspecialchars($t['nama_pelanggan']) . "</td>";
                echo "<td>" . htmlspecialchars($t['nama_alat']) . "</td>";
                echo "<td>" . $t['jumlah_alat'] . "</td>";
                echo "<td>" . date('d/m', strtotime($t['tgl_sewa'])) . " - " . date('d/m', strtotime($t['tgl_kembali'])) . "</td>";
                echo "<td>Rp " . number_format($t['total_harga'], 0, ',', '.') . "</td>";
                echo "<td>" . ucfirst($t['status']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        }
        
        echo "<div style='margin-top: 40px; text-align: right;'>";
        echo "<p>_______________________</p>";
        echo "<p>Admin</p>";
        echo "</div>";
        
        echo "</body></html>";
        exit;
    }

    public function exportLaporan()
    {
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');
        $format = $_GET['format'] ?? 'csv';
        
        $transaksiList = $this->transaksi->getLaporanByPeriode($startDate, $endDate);
        
        if ($format === 'csv') {
            $this->exportToCSV($transaksiList, $startDate, $endDate);
        }
    }

    private function exportToCSV($data, $startDate, $endDate)
    {
        $filename = "laporan_transaksi_{$startDate}_{$endDate}.csv";
        
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for proper UTF-8 encoding in Excel
        fwrite($output, "\xEF\xBB\xBF");
        
        // CSV Headers
        fputcsv($output, [
            'ID Transaksi',
            'Tanggal Dibuat',
            'Nama Pelanggan',
            'Email Pelanggan',
            'Nama Alat',
            'Kategori',
            'Tanggal Sewa',
            'Tanggal Kembali',
            'Jumlah Alat',
            'Harga Satuan',
            'Total Harga',
            'Status',
            'Status Pembayaran'
        ]);
        
        // Data rows
        foreach ($data as $row) {
            fputcsv($output, [
                $row['id_transaksi'],
                date('d/m/Y H:i', strtotime($row['created_at'])),
                $row['nama_pelanggan'],
                $row['email'] ?? '',
                $row['nama_alat'],
                $row['kategori'] ?? '',
                date('d/m/Y', strtotime($row['tgl_sewa'])),
                date('d/m/Y', strtotime($row['tgl_kembali'])),
                $row['jumlah_alat'],
                $row['harga'],
                $row['total_harga'],
                ucfirst($row['status']),
                isset($row['status_pembayaran']) ? ucfirst($row['status_pembayaran']) : 'Belum ada'
            ]);
        }
        
        fclose($output);
        exit;
    }

    /**
     * Kelola kategori layanan sesuai UML
     */
    public function kategori()
    {
        $kategoriList = $this->kategori->getAllWithAlatCount();
        
        // Debug: jika kosong, coba insert data default
        if (empty($kategoriList)) {
            $this->insertDefaultKategori();
            $kategoriList = $this->kategori->getAllWithAlatCount();
        }
        
        require_once __DIR__ . '/../../templates/pages/admin/kategori.php';
    }

    /**
     * Insert kategori default jika tidak ada data
     */
    private function insertDefaultKategori()
    {
        $defaultKategori = [
            ['nama_kategori' => 'Kursi', 'deskripsi' => 'Kursi untuk acara pesta'],
            ['nama_kategori' => 'Meja', 'deskripsi' => 'Meja untuk acara pesta'],
            ['nama_kategori' => 'Tenda', 'deskripsi' => 'Tenda untuk acara outdoor'],
            ['nama_kategori' => 'Sound System', 'deskripsi' => 'Peralatan audio untuk acara'],
            ['nama_kategori' => 'Dekorasi', 'deskripsi' => 'Dekorasi untuk mempercantik acara'],
            ['nama_kategori' => 'Peralatan Dapur', 'deskripsi' => 'Peralatan masak untuk catering'],
            ['nama_kategori' => 'Lighting', 'deskripsi' => 'Peralatan pencahayaan untuk acara']
        ];
        
        foreach ($defaultKategori as $data) {
            try {
                $this->kategori->insert($data);
            } catch (Exception $e) {
                // Silent fail - category might already exist
            }
        }
    }

    public function tambahKategori()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori'] ?? ''),
                'deskripsi' => trim($_POST['deskripsi'] ?? '')
            ];

            $errors = $this->validateKategoriData($data);

            if (empty($errors)) {
                try {
                    $result = $this->kategori->insert($data);
                    if ($result) {
                        $_SESSION['success'] = 'Kategori berhasil ditambahkan';
                    } else {
                        $_SESSION['error'] = 'Gagal menambahkan kategori';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menambahkan kategori: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
            }
        }
        
        header('Location: /hakikah/admin/kategori');
        exit;
    }

    public function editKategori()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            $data = [
                'nama_kategori' => trim($_POST['nama_kategori'] ?? ''),
                'deskripsi' => trim($_POST['deskripsi'] ?? '')
            ];

            $errors = $this->validateKategoriData($data);

            if (empty($errors) && $id > 0) {
                try {
                    $result = $this->kategori->update($id, $data);
                    if ($result) {
                        $_SESSION['success'] = 'Kategori berhasil diupdate';
                    } else {
                        $_SESSION['error'] = 'Gagal mengupdate kategori';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal mengupdate kategori: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
            }
        }
        
        header('Location: /hakikah/admin/kategori');
        exit;
    }

    public function deleteKategori()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id > 0) {
                try {
                    $result = $this->kategori->delete($id);
                    if ($result) {
                        $_SESSION['success'] = 'Kategori berhasil dihapus';
                    } else {
                        $_SESSION['error'] = 'Gagal menghapus kategori. Kategori mungkin sedang digunakan.';
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal menghapus kategori: ' . $e->getMessage();
                }
            }
        }
        
        header('Location: /hakikah/admin/kategori');
        exit;
    }

    private function validateKategoriData($data)
    {
        $errors = [];

        if (empty($data['nama_kategori'])) {
            $errors[] = 'Nama kategori harus diisi';
        } elseif (strlen($data['nama_kategori']) < 3) {
            $errors[] = 'Nama kategori minimal 3 karakter';
        }

        if (strlen($data['deskripsi']) > 500) {
            $errors[] = 'Deskripsi maksimal 500 karakter';
        }

        return $errors;
    }

    private function validateAlatData($data)
    {
        $errors = [];

        if (empty($data['nama_alat'])) {
            $errors[] = 'Nama alat harus diisi';
        }

        if (empty($data['id_kategori']) || $data['id_kategori'] <= 0) {
            $errors[] = 'Kategori harus dipilih';
        }

        if ($data['stok'] < 0) {
            $errors[] = 'Stok tidak boleh negatif';
        }

        if ($data['harga'] <= 0) {
            $errors[] = 'Harga harus lebih dari 0';
        }

        return $errors;
    }

    private function handleImageUpload($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/alat/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Validate file type
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'error' => 'Format gambar harus JPG, PNG, GIF, atau WebP'];
        }

        // Validate file size (max 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'error' => 'Ukuran gambar maksimal 5MB'];
        }

        // Generate unique filename
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = 'alat_' . time() . '_' . uniqid() . '.' . $extension;
        $filepath = $uploadDir . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'filename' => $filename];
        } else {
            return ['success' => false, 'error' => 'Gagal mengupload gambar'];
        }
    }

    /**
     * Validasi data pengembalian sesuai UML attribute requirements
     */
    private function validatePengembalianData($data)
    {
        $errors = [];

        if (empty($data['id_transaksi'])) {
            $errors[] = 'ID Transaksi harus dipilih';
        }

        if (empty($data['tanggal'])) {
            $errors[] = 'Tanggal pengembalian harus diisi';
        }

        if (empty($data['kondisi_alat'])) {
            $errors[] = 'Kondisi alat harus dipilih';
        } elseif (!in_array($data['kondisi_alat'], ['baik', 'rusak_ringan', 'rusak_berat', 'hilang'])) {
            $errors[] = 'Kondisi alat tidak valid';
        }

        if ($data['denda'] < 0) {
            $errors[] = 'Denda tidak boleh negatif';
        }

        return $errors;
    }

    private function validatePelangganData($data)
    {
        $errors = [];

        if (empty($data['nama'])) {
            $errors[] = 'Nama pelanggan harus diisi';
        } elseif (strlen($data['nama']) < 3) {
            $errors[] = 'Nama pelanggan minimal 3 karakter';
        }

        if (empty($data['alamat'])) {
            $errors[] = 'Alamat harus diisi';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email harus diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        if (empty($data['no_hp'])) {
            $errors[] = 'Nomor HP harus diisi';
        } elseif (!preg_match('/^[0-9+\-\s]+$/', $data['no_hp'])) {
            $errors[] = 'Format nomor HP tidak valid';
        }

        if (empty($data['username'])) {
            $errors[] = 'Username harus diisi';
        } elseif (strlen($data['username']) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }

        if (empty($data['password'])) {
            $errors[] = 'Password harus diisi';
        } elseif (strlen($data['password']) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }

        return $errors;
    }

    private function validatePelangganEditData($data)
    {
        $errors = [];

        if (empty($data['nama'])) {
            $errors[] = 'Nama pelanggan harus diisi';
        } elseif (strlen($data['nama']) < 3) {
            $errors[] = 'Nama pelanggan minimal 3 karakter';
        }

        if (empty($data['alamat'])) {
            $errors[] = 'Alamat harus diisi';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email harus diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        }

        if (empty($data['no_hp'])) {
            $errors[] = 'Nomor HP harus diisi';
        } elseif (!preg_match('/^[0-9+\-\s]+$/', $data['no_hp'])) {
            $errors[] = 'Format nomor HP tidak valid';
        }

        if (!empty($data['username']) && strlen($data['username']) < 3) {
            $errors[] = 'Username minimal 3 karakter';
        }

        return $errors;
    }

    /**
     * Menangani pengembalian alat dengan workflow lengkap (sesuai UML method kelolaPengembalian)
     */
    public function prosesKembalian($id_transaksi)
    {
        try {
            // Get transaksi data
            $transaksi = $this->transaksi->getById($id_transaksi);
            
            if (!$transaksi) {
                $_SESSION['error'] = 'Transaksi tidak ditemukan';
                header('Location: /hakikah/admin/pengembalian');
                exit;
            }

            // Cek apakah sudah pernah dikembalikan
            $existingReturn = $this->db->fetch(
                "SELECT * FROM pengembalian WHERE id_transaksi = ?", 
                [$id_transaksi]
            );

            if ($existingReturn) {
                $_SESSION['error'] = 'Transaksi ini sudah pernah dicatat pengembaliannya';
                header('Location: /hakikah/admin/pengembalian');
                exit;
            }

            // Auto process return dengan kondisi default
            $data = [
                'id_transaksi' => $id_transaksi,
                'tanggal' => date('Y-m-d'),
                'kondisi_alat' => 'baik',
                'catatan' => 'Pengembalian diproses otomatis',
                'denda' => 0
            ];

            $result = $this->admin->catatPengembalian($data);
            
            if ($result) {
                // Update status transaksi
                $this->transaksi->updateStatus($id_transaksi, 'selesai');
                
                // Kembalikan stok
                $this->alat->tambahStok($transaksi['id_alat'], $transaksi['jumlah_alat']);
                
                $_SESSION['success'] = 'Pengembalian berhasil diproses';
            } else {
                $_SESSION['error'] = 'Gagal memproses pengembalian';
            }

        } catch (Exception $e) {
            $_SESSION['error'] = 'Error: ' . $e->getMessage();
        }

        header('Location: /hakikah/admin/pengembalian');
        exit;
    }
}