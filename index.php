<?php
// Primary Index - Redirect ke Login
// Sistem Penyewaan Alat Pesta Haqiqah

// Start session jika belum dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Jika user sudah login, redirect ke dashboard sesuai role
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {
    switch ($_SESSION['role']) {
        case 'admin':
            header('Location: /hakikah/dashboard');
            break;
        case 'pelanggan':
            header('Location: /hakikah/dashboard');
            break;
        case 'pemilik':
            header('Location: /hakikah/dashboard');
            break;
        default:
            // Role tidak valid, hapus session dan redirect ke login
            session_destroy();
            header('Location: /hakikah/login');
            break;
    }
    exit;
}

// Jika belum login, redirect ke halaman login
header('Location: /hakikah/login');
exit;
?>
