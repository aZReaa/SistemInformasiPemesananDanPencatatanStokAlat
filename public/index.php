<?php
// Error reporting untuk development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if autoloader exists
$autoloaderPath = __DIR__ . '/../src/core/Autoloader.php';
if (!file_exists($autoloaderPath)) {
    die('Error: Autoloader tidak ditemukan. Pastikan struktur folder sudah benar.');
}

require_once $autoloaderPath;

try {
    // Register autoloader
    Autoloader::register();
    
    // Initialize router
    $router = new Router();
    
    // Public routes
    $router->get('/', function() {
        $homePath = __DIR__ . '/../templates/pages/home.php';
        if (file_exists($homePath)) {
            require_once $homePath;
        } else {
            // Fallback jika home.php tidak ada
            echo createFallbackHomePage();
        }
    });
    
    $router->get('/login', function() {
        $loginPath = __DIR__ . '/../templates/pages/login.php';
        if (file_exists($loginPath)) {
            require_once $loginPath;
        } else {
            echo createFallbackLoginPage();
        }
    });
    
    $router->post('/login', 'AuthController@login');
    
    $router->get('/register', function() {
        $registerPath = __DIR__ . '/../templates/pages/register.php';
        if (file_exists($registerPath)) {
            require_once $registerPath;
        } else {
            echo createFallbackRegisterPage();
        }
    });
    
    $router->post('/register', 'AuthController@register');
    
    $router->get('/dashboard', function() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /hakikah/login');
            exit;
        }
        
        $role = $_SESSION['role'];
        
        switch ($role) {
            case 'admin':
                $controller = new AdminController();
                $controller->dashboard();
                break;
            case 'pelanggan':
                $controller = new PelangganController();
                $controller->dashboard();
                break;
            case 'pemilik':
                $controller = new PemilikController();
                $controller->dashboard();
                break;
            default:
                header('Location: /hakikah/login');
                exit;
        }
    });
    
    $router->get('/logout', 'AuthController@logout');
    
    // Admin Routes - Tambah pengecekan role
    $router->get('/admin/pelanggan', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->pelanggan();
    });
    
    $router->post('/admin/pelanggan/tambah', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->tambahPelanggan();
    });

    $router->post('/admin/pelanggan/edit', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->editPelanggan();
    });


    $router->post('/admin/pelanggan/reset-password', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->resetPasswordPelanggan();
    });

    $router->post('/admin/pelanggan/delete', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->deletePelanggan();
    });
    
    $router->get('/admin/alat', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->alat();
    });
    $router->post('/admin/alat/tambah', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->tambahAlat();
    });
    
    $router->post('/admin/alat/edit', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->editAlat();
    });
    
    $router->post('/admin/alat/delete', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->deleteAlat();
    });
    
    $router->get('/admin/kategori', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->kategori();
    });
    
    $router->post('/admin/kategori/tambah', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->tambahKategori();
    });
    
    $router->post('/admin/kategori/edit', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->editKategori();
    });
    
    $router->post('/admin/kategori/delete', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->deleteKategori();
    });
    
    $router->get('/admin/kategori', 'AdminController@kategori');
    $router->post('/admin/kategori/tambah', 'AdminController@tambahKategori');
    $router->post('/admin/kategori/edit', 'AdminController@editKategori');
    $router->post('/admin/kategori/delete', 'AdminController@deleteKategori');
    
    $router->get('/admin/transaksi', 'AdminController@transaksi');
    $router->post('/admin/transaksi/update-status', 'AdminController@updateStatusTransaksi');
    $router->post('/admin/transaksi/tambah', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->tambahTransaksi();
    });
    $router->post('/admin/transaksi/edit', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->editTransaksi();
    });
    $router->post('/admin/transaksi/delete', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->deleteTransaksi();
    });
    
    $router->get('/admin/pembayaran', 'AdminController@pembayaran');
    $router->post('/admin/pembayaran/verifikasi', 'AdminController@verifikasiPembayaran');
    
    $router->get('/admin/pengembalian', 'AdminController@pengembalian');
    $router->post('/admin/pengembalian/tambah', 'AdminController@tambahPengembalian');
    
    $router->get('/admin/laporan', 'AdminController@laporan');
    $router->get('/admin/laporan/export', 'AdminController@exportLaporan');
    
    // Cetak Laporan (sesuai UML requirement)
    $router->get('/admin/laporan/cetak', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->cetakLaporan();
    });
    
    // Proses Kembalian (sesuai UML kelolaPengembalian)
    $router->get('/admin/pengembalian/proses/(\d+)', function($id_transaksi) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new AdminController();
        $controller->prosesKembalian($id_transaksi);
    });
    
    // Pelanggan Routes - Tambah pengecekan role
    $router->get('/alat', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->katalogAlat();
    }); // Alias untuk backward compatibility
    
    $router->get('/pesanan', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->daftarPesanan();
    }); // Alias untuk backward compatibility
    
    $router->get('/pelanggan/alat', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->katalogAlat();
    });
    $router->get('/pelanggan/alat/(\d+)', function($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->detailAlat($id);
    });
    
    $router->get('/pelanggan/pesan/(\d+)', function($id) {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->pesanAlat($id);
    });
    
    $router->post('/pelanggan/pesan', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->pesanAlat();
    });
    
    $router->get('/pelanggan/pesanan', function() {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
            header('Location: /hakikah/login');
            exit;
        }
        $controller = new PelangganController();
        $controller->daftarPesanan();
    });
    $router->get('/pelanggan/detail-pesanan/(\d+)', function($id) {
        $controller = new PelangganController();
        $controller->detailPesanan($id);
    });
    
    $router->get('/pelanggan/pembayaran/(\d+)', function($id) {
        $controller = new PelangganController();
        $controller->pembayaran($id);
    });
    $router->post('/pelanggan/pembayaran/(\d+)', function($id) {
        $controller = new PelangganController();
        $controller->pembayaran($id);
    });
    
    $router->get('/pelanggan/riwayat', 'PelangganController@riwayatTransaksi');
    $router->get('/pelanggan/profil', 'PelangganController@profil');
    $router->post('/pelanggan/profil', 'PelangganController@profil');
    $router->post('/pelanggan/ubah-password', 'PelangganController@ubahPassword');
    $router->post('/pelanggan/batalkan-pesanan', 'PelangganController@batalkanPesanan');
    
    // Pemilik Routes - Sesuai UML: hanya laporan keuangan dan transaksi
    // Tidak ada route tambahan - hanya dashboard yang menampilkan laporan
    
    // Run the router
    $router->run();
    
} catch (Exception $e) {
    // Error handling
    echo createErrorPage($e);
}

// Fallback functions untuk menampilkan halaman jika template tidak ada
function createFallbackHomePage() {
    return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Haqiqah Rental - Penyewaan Alat Pesta</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .hero { background: linear-gradient(135deg, #0d6efd, #0056b3); color: white; padding: 3rem; border-radius: 10px; text-align: center; margin-bottom: 2rem; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 1rem; }
        .btn { display: inline-block; padding: 12px 24px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; margin: 0 5px; }
        .btn:hover { background: #0056b3; }
        .services { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin: 2rem 0; }
        .service-card { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; text-align: center; }
        .alert { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 1rem; border-radius: 5px; margin: 1rem 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            ⚠️ <strong>Info:</strong> Template utama tidak ditemukan. Menggunakan halaman fallback.
        </div>
        
        <div class="hero">
            <h1>🎪 Haqiqah Rental</h1>
            <p>Solusi lengkap untuk kebutuhan penyewaan alat pesta pernikahan dan haqiqah Anda</p>
            <a href="/hakikah/login" class="btn">Login</a>
            <a href="/hakikah/register" class="btn">Daftar</a>
            <a href="/hakikah/test" class="btn">Test System</a>
        </div>
        
        <div class="services">
            <div class="service-card">
                <h3>🎪 Tenda & Canopy</h3>
                <p>Tenda berkualitas berbagai ukuran</p>
            </div>
            <div class="service-card">
                <h3>🪑 Kursi & Meja</h3>
                <p>Kursi dan meja yang nyaman</p>
            </div>
            <div class="service-card">
                <h3>🎵 Sound System</h3>
                <p>Peralatan audio berkualitas</p>
            </div>
            <div class="service-card">
                <h3>🎊 Dekorasi</h3>
                <p>Berbagai dekorasi cantik</p>
            </div>
        </div>
        
        <div style="text-align: center; margin-top: 2rem;">
            <h3>Status Sistem</h3>
            <p>✅ Router: Berfungsi</p>
            <p>✅ Session: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Aktif' : 'Tidak aktif') . '</p>
            <p>✅ PHP: ' . PHP_VERSION . '</p>
        </div>
    </div>
</body>
</html>';
}

function createFallbackLoginPage() {
    return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Haqiqah Rental</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-container { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 100%; max-width: 400px; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .text-center { text-align: center; }
        .alert { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="alert">
            ⚠️ Template login tidak ditemukan. Menggunakan form fallback.
        </div>
        
        <h2 class="text-center">🔐 Login</h2>
        <p class="text-center">Haqiqah Rental System</p>
        
        <form method="POST" action="/hakikah/login">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="text-center" style="margin-top: 1rem;">
            <p>Belum punya akun? <a href="/hakikah/register">Daftar di sini</a></p>
            <p><a href="/hakikah/">← Kembali ke beranda</a></p>
        </div>
        
        <div style="margin-top: 2rem; padding: 1rem; background: #e9ecef; border-radius: 5px; font-size: 0.9rem;">
            <strong>Demo Login:</strong><br>
            Username: <code>admin</code><br>
            Password: <code>password</code>
        </div>
    </div>
</body>
</html>';
}

function createFallbackRegisterPage() {
    return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi - Haqiqah Rental</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 500px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        input, textarea { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; box-sizing: border-box; }
        .btn { width: 100%; padding: 12px; background: #0d6efd; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #0056b3; }
        .text-center { text-align: center; }
        .alert { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            ⚠️ Template registrasi tidak ditemukan. Menggunakan form fallback.
        </div>
        
        <h2 class="text-center">📝 Registrasi Pelanggan</h2>
        <p class="text-center">Daftar untuk menggunakan layanan Haqiqah Rental</p>
        
        <form method="POST" action="/hakikah/register">
            <div class="form-group">
                <label for="nama">Nama Lengkap:</label>
                <input type="text" id="nama" name="nama" required placeholder="Masukkan nama lengkap">
            </div>
            
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required placeholder="Masukkan email">
            </div>
            
            <div class="form-group">
                <label for="no_hp">Nomor HP:</label>
                <input type="tel" id="no_hp" name="no_hp" required placeholder="08xxxxxxxxxx">
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat:</label>
                <textarea id="alamat" name="alamat" rows="3" required placeholder="Masukkan alamat lengkap"></textarea>
            </div>
            
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required placeholder="Masukkan username">
            </div>
            
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required placeholder="Masukkan password">
            </div>
            
            <button type="submit" class="btn">Daftar</button>
        </form>
        
        <div class="text-center" style="margin-top: 1rem;">
            <p>Sudah punya akun? <a href="/hakikah/login">Login di sini</a></p>
            <p><a href="/hakikah/">← Kembali ke beranda</a></p>
        </div>
    </div>
</body>
</html>';
}

function createFallbackDashboard($role, $userRole) {
    return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ' . $role . ' - Haqiqah Rental</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #0d6efd, #0056b3); color: white; padding: 2rem; border-radius: 10px; margin-bottom: 2rem; }
        .alert { background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .nav { display: flex; gap: 1rem; margin: 1rem 0; flex-wrap: wrap; }
        .nav a { padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }
        .nav a:hover { background: #0056b3; }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            ⚠️ Template dashboard tidak ditemukan. Menggunakan dashboard fallback.
        </div>
        
        <div class="header">
            <h1>🏠 Dashboard ' . $role . '</h1>
            <p>Selamat datang di sistem Haqiqah Rental</p>
            <p><strong>Login sebagai:</strong> ' . htmlspecialchars($_SESSION['nama'] ?? 'User') . ' (' . $userRole . ')</p>
        </div>
        
        <div class="nav">';
    
    // Navigation based on role
    switch($userRole) {
        case 'admin':
            $navigation = '
            <a href="/hakikah/admin/pelanggan">👥 Kelola Pelanggan</a>
            <a href="/hakikah/admin/alat">🔧 Kelola Alat</a>
            <a href="/hakikah/admin/kategori">📋 Kelola Kategori</a>
            <a href="/hakikah/admin/transaksi">💼 Transaksi</a>
            <a href="/hakikah/admin/pembayaran">💰 Pembayaran</a>
            <a href="/hakikah/admin/pengembalian">📦 Pengembalian</a>
            <a href="/hakikah/admin/laporan">📊 Laporan</a>';
            break;
        case 'pelanggan':
            $navigation = '
            <a href="/hakikah/pelanggan/alat">🛍️ Katalog Alat</a>
            <a href="/hakikah/pelanggan/pesanan">📋 Pesanan Saya</a>
            <a href="/hakikah/pelanggan/riwayat">📜 Riwayat</a>
            <a href="/hakikah/pelanggan/profil">👤 Profil</a>';
            break;
        case 'pemilik':
            $navigation = '
            <a href="/hakikah/dashboard">📊 Laporan Keuangan & Transaksi</a>';
            break;
        default:
            $navigation = '<a href="/hakikah/login">🔐 Login</a>';
    }
    
    return $navigation . '
            <a href="/hakikah/logout">🚪 Logout</a>
        </div>
        
        <div style="text-align: center; margin: 2rem 0;">
            <h3>Sistem berjalan dengan baik!</h3>
            <p>Template dashboard akan dimuat setelah file tersedia.</p>
            <p><strong>Status:</strong> ✅ Router aktif, Session: ' . (session_status() === PHP_SESSION_ACTIVE ? 'Aktif' : 'Tidak aktif') . '</p>
        </div>
    </div>
</body>
</html>';
}

function createErrorPage($exception) {
    return '<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - Haqiqah Rental</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background: #f8f9fa; }
        .error-container { max-width: 800px; margin: 0 auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .error-header { background: #dc3545; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .error-details { background: #f8f9fa; padding: 1rem; border-radius: 5px; margin: 1rem 0; font-family: monospace; }
        .btn { display: inline-block; padding: 10px 20px; background: #0d6efd; color: white; text-decoration: none; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-header">
            <h1>🚨 Error Terjadi</h1>
            <p>Terjadi kesalahan dalam sistem Haqiqah Rental</p>
        </div>
        
        <h3>Detail Error:</h3>
        <div class="error-details">
            <strong>Message:</strong> ' . htmlspecialchars($exception->getMessage()) . '<br>
            <strong>File:</strong> ' . htmlspecialchars($exception->getFile()) . '<br>
            <strong>Line:</strong> ' . $exception->getLine() . '<br>
            <strong>Time:</strong> ' . date('Y-m-d H:i:s') . '
        </div>
        
        <h3>Kemungkinan Solusi:</h3>
        <ul>
            <li>Pastikan database sudah dibuat dan terkonfigurasi dengan benar</li>
            <li>Periksa file konfigurasi database di <code>config/database.php</code></li>
            <li>Pastikan semua file model dan controller ada</li>
            <li>Cek permissions folder dan file</li>
        </ul>
        
        <div style="text-align: center; margin-top: 2rem;">
            <a href="/hakikah/" class="btn">🏠 Kembali ke Beranda</a>
            <a href="/hakikah/test" class="btn">🔧 Test System</a>
        </div>
    </div>
</body>
</html>';
}
?>