<?php

class PelangganController
{
    private $pelanggan;
    private $alat;
    private $transaksi;
    private $pembayaran;
    private $dashboard;

    public function __construct()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }

        $this->pelanggan = new Pelanggan();
        $this->pelanggan->setId($_SESSION['pelanggan_id']);
        $this->alat = new Alat();
        $this->transaksi = new Transaksi();
        $this->pembayaran = new Pembayaran();
        $this->dashboard = new Dashboard();
    }

    /**
     * Dashboard pelanggan menggunakan Dashboard class sesuai UML
     */
    public function dashboard()
    {
        $dashboardData = $this->dashboard->viewDashboard('pelanggan', $_SESSION['pelanggan_id']);
        require_once __DIR__ . '/../../templates/pages/pelanggan/dashboard.php';
    }

    public function katalogAlat()
    {
        $search = $_GET['search'] ?? '';
        $kategori = $_GET['kategori'] ?? '';
        
        if (!empty($search)) {
            $alatList = $this->alat->searchAlat($search);
        } elseif (!empty($kategori)) {
            $alatList = $this->alat->getByKategori($kategori);
        } else {
            $alatList = $this->alat->getAvailable();
        }

        $db = Database::getInstance();
        $kategoriList = $db->fetchAll("SELECT DISTINCT kategori FROM alat WHERE stok > 0");
        
        require_once __DIR__ . '/../../templates/pages/pelanggan/katalog.php';
    }

    public function detailAlat($id)
    {
        $alat = $this->alat->getById($id);
        
        if (!$alat || $alat['stok'] <= 0) {
            $_SESSION['error'] = 'Alat tidak ditemukan atau stok habis';
            header('Location: /hakikah/pelanggan/alat');
            exit;
        }

        require_once __DIR__ . '/../../templates/pages/pelanggan/detail-alat.php';
    }

    public function pesanAlat($id = null)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $id) {
            $alat = $this->alat->getById($id);
            
            if (!$alat || $alat['stok'] <= 0) {
                $_SESSION['error'] = 'Alat tidak ditemukan atau stok habis';
                header('Location: /hakikah/pelanggan/alat');
                exit;
            }

            // Get customer data for address
            $pelangganData = $this->pelanggan->getById($_SESSION['pelanggan_id']);

            require_once __DIR__ . '/../../templates/pages/pelanggan/pesan-alat.php';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'id_pelanggan' => $_SESSION['pelanggan_id'],
                'id_alat' => $_POST['id_alat'] ?? null,
                'tgl_sewa' => $_POST['tgl_sewa'] ?? null,
                'tgl_kembali' => $_POST['tgl_kembali'] ?? null,
                'jumlah_alat' => (int)($_POST['jumlah_alat'] ?? 1),
                'metode_pengambilan' => $_POST['metode_pengambilan'] ?? 'pickup',
                'alamat_pengiriman' => trim($_POST['alamat_pengiriman'] ?? '')
            ];

            $errors = $this->validatePesananData($data);

            if (empty($errors)) {
                try {
                    $transaksiId = $this->transaksi->insertTransaksi($data);
                    $_SESSION['success'] = 'Pesanan berhasil dibuat. Silakan lakukan pembayaran.';
                    header('Location: /hakikah/pelanggan/pembayaran/' . $transaksiId);
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal membuat pesanan: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
            }

            header('Location: /hakikah/pelanggan/pesan/' . ($data['id_alat'] ?? ''));
            exit;
        }
    }

    public function daftarPesanan()
    {
        $status = $_GET['status'] ?? '';
        $pesananList = $this->transaksi->getByPelanggan($_SESSION['pelanggan_id']);
        
        if (!empty($status)) {
            $pesananList = array_filter($pesananList, fn($p) => $p['status'] === $status);
        }

        require_once __DIR__ . '/../../templates/pages/pelanggan/pesanan.php';
    }

    public function detailPesanan($id)
    {
        $pesanan = $this->transaksi->getById($id);
        
        if (!$pesanan || $pesanan['id_pelanggan'] != $_SESSION['pelanggan_id']) {
            $_SESSION['error'] = 'Pesanan tidak ditemukan';
            header('Location: /hakikah/pelanggan/pesanan');
            exit;
        }

        $pembayaran = $this->pembayaran->getByTransaksi($id);
        require_once __DIR__ . '/../../templates/pages/pelanggan/detail-pesanan.php';
    }

    public function riwayatTransaksi()
    {
        $riwayat = $this->transaksi->getByPelanggan($_SESSION['pelanggan_id']);
        $riwayat = array_filter($riwayat, fn($r) => $r['status'] === 'completed');
        
        require_once __DIR__ . '/../../templates/pages/pelanggan/riwayat.php';
    }

    public function profil()
    {
        $pelanggan = $this->pelanggan->getById($_SESSION['pelanggan_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'nama' => trim($_POST['nama'] ?? ''),
                'alamat' => trim($_POST['alamat'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'no_hp' => trim($_POST['no_hp'] ?? '')
            ];

            $errors = $this->validateProfilData($data, $_SESSION['pelanggan_id']);

            if (empty($errors)) {
                try {
                    $db = Database::getInstance();
                    $db->update(
                        "UPDATE pelanggan SET nama = ?, alamat = ?, email = ?, no_hp = ? WHERE id_pelanggan = ?",
                        [$data['nama'], $data['alamat'], $data['email'], $data['no_hp'], $_SESSION['pelanggan_id']]
                    );
                    
                    $_SESSION['nama'] = $data['nama'];
                    $_SESSION['success'] = 'Profil berhasil diperbarui';
                    
                    header('Location: /hakikah/pelanggan/profil');
                    exit;
                } catch (Exception $e) {
                    $_SESSION['error'] = 'Gagal memperbarui profil: ' . $e->getMessage();
                }
            } else {
                $_SESSION['errors'] = $errors;
                $_SESSION['old_data'] = $data;
            }
        }

        require_once __DIR__ . '/../../templates/pages/pelanggan/profil.php';
    }

    public function batalkanPesanan()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            
            if ($id) {
                $pesanan = $this->transaksi->getById($id);
                
                if ($pesanan && $pesanan['id_pelanggan'] == $_SESSION['pelanggan_id'] && 
                    $pesanan['status'] === 'pending') {
                    
                    try {
                        $this->transaksi->updateStatus($id, 'cancelled');
                        $_SESSION['success'] = 'Pesanan berhasil dibatalkan';
                    } catch (Exception $e) {
                        $_SESSION['error'] = 'Gagal membatalkan pesanan: ' . $e->getMessage();
                    }
                } else {
                    $_SESSION['error'] = 'Pesanan tidak dapat dibatalkan';
                }
            }
        }
        
        header('Location: /hakikah/pelanggan/pesanan');
        exit;
    }

    private function validatePesananData($data)
    {
        $errors = [];

        if (empty($data['id_alat'])) {
            $errors[] = 'Alat harus dipilih';
        }

        if (empty($data['tgl_sewa'])) {
            $errors[] = 'Tanggal sewa harus diisi';
        } elseif (strtotime($data['tgl_sewa']) < strtotime(date('Y-m-d'))) {
            $errors[] = 'Tanggal sewa tidak boleh di masa lalu';
        }

        if (empty($data['tgl_kembali'])) {
            $errors[] = 'Tanggal kembali harus diisi';
        } elseif (strtotime($data['tgl_kembali']) <= strtotime($data['tgl_sewa'])) {
            $errors[] = 'Tanggal kembali harus setelah tanggal sewa';
        }

        if ($data['jumlah_alat'] <= 0) {
            $errors[] = 'Jumlah alat harus lebih dari 0';
        }

        if ($data['id_alat']) {
            $alat = $this->alat->getById($data['id_alat']);
            if (!$alat || $alat['stok'] < $data['jumlah_alat']) {
                $errors[] = 'Stok alat tidak mencukupi';
            }
        }

        // Validate delivery method and address
        if (!empty($data['metode_pengambilan'])) {
            if ($data['metode_pengambilan'] === 'delivery_profile') {
                // Check if customer has address in profile
                $pelanggan = $this->pelanggan->getById($_SESSION['pelanggan_id']);
                if (empty($pelanggan['alamat'])) {
                    $errors[] = 'Alamat profil belum diisi. Silakan isi alamat di profil atau pilih alamat custom.';
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

    private function validateProfilData($data, $currentId)
    {
        $errors = [];

        if (empty($data['nama'])) {
            $errors[] = 'Nama harus diisi';
        }

        if (empty($data['alamat'])) {
            $errors[] = 'Alamat harus diisi';
        }

        if (empty($data['email'])) {
            $errors[] = 'Email harus diisi';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid';
        } else {
            $db = Database::getInstance();
            $existing = $db->fetch("SELECT id_pelanggan FROM pelanggan WHERE email = ? AND id_pelanggan != ?", 
                                  [$data['email'], $currentId]);
            if ($existing) {
                $errors[] = 'Email sudah digunakan oleh pelanggan lain';
            }
        }

        if (empty($data['no_hp'])) {
            $errors[] = 'Nomor HP harus diisi';
        }

        return $errors;
    }

    /**
     * Pembayaran dan konfirmasi pembayaran sesuai UML
     */
    public function pembayaran($idTransaksi)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            return $this->prosesKonfirmasiPembayaran($idTransaksi);
        }

        // Tampilkan form pembayaran
        $transaksi = $this->transaksi->getById($idTransaksi);
        
        if (!$transaksi || $transaksi['id_pelanggan'] != $_SESSION['pelanggan_id']) {
            $_SESSION['error'] = 'Transaksi tidak ditemukan';
            header('Location: /hakikah/pelanggan/pesanan');
            exit;
        }

        require_once __DIR__ . '/../../templates/pages/pelanggan/pembayaran.php';
    }

    private function prosesKonfirmasiPembayaran($idTransaksi)
    {
        try {
            $jumlah = (float)($_POST['jumlah'] ?? 0);
            $buktiTransfer = null;

            // Handle file upload jika ada
            if (isset($_FILES['bukti_transfer']) && $_FILES['bukti_transfer']['error'] === UPLOAD_ERR_OK) {
                $buktiTransfer = $this->uploadBuktiTransfer($_FILES['bukti_transfer']);
            }

            if ($jumlah <= 0) {
                throw new Exception('Jumlah pembayaran harus lebih dari 0');
            }

            // Gunakan method konfirmasiPembayaran sesuai UML
            $result = $this->pelanggan->konfirmasiPembayaran($idTransaksi, $jumlah, $buktiTransfer);

            if ($result) {
                $_SESSION['success'] = 'Konfirmasi pembayaran berhasil. Menunggu verifikasi admin.';
            } else {
                $_SESSION['error'] = 'Gagal mengkonfirmasi pembayaran';
            }

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
        }

        header('Location: /hakikah/pelanggan/pesanan');
        exit;
    }

    private function uploadBuktiTransfer($file)
    {
        $uploadDir = __DIR__ . '/../../public/uploads/bukti_transfer/';
        
        // Create directory if not exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Format file harus JPG, JPEG, atau PNG');
        }

        if ($file['size'] > $maxSize) {
            throw new Exception('Ukuran file maksimal 5MB');
        }

        $filename = 'bukti_' . time() . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $filepath = $uploadDir . $filename;

        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return 'uploads/bukti_transfer/' . $filename;
        }

        throw new Exception('Gagal mengupload file');
    }

    /**
     * Ubah password pelanggan
     */
    public function ubahPassword()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            // Validasi input
            $errors = $this->validatePasswordData($currentPassword, $newPassword, $confirmPassword);

            if (empty($errors)) {
                try {
                    // Ambil data user dari database
                    $db = Database::getInstance();
                    $user = $db->fetch(
                        "SELECT u.*, p.nama FROM users u JOIN pelanggan p ON u.id = p.user_id WHERE u.id = ?", 
                        [$_SESSION['user_id']]
                    );

                    // Verifikasi password saat ini
                    if (!password_verify($currentPassword, $user['password'])) {
                        $_SESSION['error'] = 'Password saat ini tidak benar';
                        header('Location: /hakikah/pelanggan/profil');
                        exit;
                    }

                    // Hash password baru
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update password di database
                    $result = $db->update(
                        "UPDATE users SET password = ? WHERE id = ?",
                        [$hashedPassword, $_SESSION['user_id']]
                    );

                    if ($result) {
                        $_SESSION['success'] = 'Password berhasil diubah';
                    } else {
                        $_SESSION['error'] = 'Gagal mengubah password';
                    }

                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error: ' . $e->getMessage();
                }
            } else {
                $_SESSION['error'] = implode(', ', $errors);
            }
        }

        header('Location: /hakikah/pelanggan/profil');
        exit;
    }

    /**
     * Validasi data password
     */
    private function validatePasswordData($currentPassword, $newPassword, $confirmPassword)
    {
        $errors = [];

        if (empty($currentPassword)) {
            $errors[] = 'Password saat ini harus diisi';
        }

        if (empty($newPassword)) {
            $errors[] = 'Password baru harus diisi';
        } elseif (strlen($newPassword) < 6) {
            $errors[] = 'Password baru minimal 6 karakter';
        }

        if (empty($confirmPassword)) {
            $errors[] = 'Konfirmasi password harus diisi';
        } elseif ($newPassword !== $confirmPassword) {
            $errors[] = 'Konfirmasi password tidak cocok';
        }

        if ($currentPassword === $newPassword) {
            $errors[] = 'Password baru harus berbeda dengan password saat ini';
        }

        return $errors;
    }
}