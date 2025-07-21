<?php 
$title = 'Profil Saya - Dashboard Pelanggan';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard">
    <aside class="sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin: 0;">Panel Pelanggan</h3>
            <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="/hakikah/dashboard">Dashboard</a></li>
            <li><a href="/hakikah/pelanggan/alat">Katalog Alat</a></li>
            <li><a href="/hakikah/pelanggan/pesanan">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil" class="active">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Profil Saya</h1>
            <p style="color: var(--secondary-color);">Kelola informasi profil dan data diri Anda</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Form Edit Profil -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Informasi Profil</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/hakikah/pelanggan/profil" id="profilForm">
                        <div class="form-group">
                            <label for="nama" class="form-label">Nama Lengkap</label>
                            <input type="text" id="nama" name="nama" class="form-control" required 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['nama'] ?? $pelanggan['nama'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" id="email" name="email" class="form-control" required 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? $pelanggan['email'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="no_hp" class="form-label">Nomor HP</label>
                            <input type="tel" id="no_hp" name="no_hp" class="form-control" required 
                                   value="<?= htmlspecialchars($_SESSION['old_data']['no_hp'] ?? $pelanggan['no_hp'] ?? '') ?>"
                                   placeholder="08xxxxxxxxxx">
                        </div>

                        <div class="form-group">
                            <label for="alamat" class="form-label">Alamat Lengkap</label>
                            <textarea id="alamat" name="alamat" class="form-control" rows="4" required 
                                      placeholder="Masukkan alamat lengkap termasuk kota dan kode pos"><?= htmlspecialchars($_SESSION['old_data']['alamat'] ?? $pelanggan['alamat'] ?? '') ?></textarea>
                        </div>

                        <div class="btn-actions" style="justify-content: flex-end;">
                            <button type="button" class="btn btn-outline-primary btn-cancel" onclick="resetForm()">Reset</button>
                            <button type="submit" class="btn btn-primary btn-submit">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Informasi Akun -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Informasi Akun</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">ID Pelanggan</div>
                        <div style="font-weight: 600; font-size: 1.1rem;">#<?= $pelanggan['id_pelanggan'] ?></div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Username</div>
                        <div style="font-weight: 600;"><?= htmlspecialchars($_SESSION['username']) ?></div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Tanggal Bergabung</div>
                        <div style="font-weight: 600;"><?= date('d/m/Y', strtotime($pelanggan['created_at'])) ?></div>
                    </div>

                    <div style="margin-bottom: 1.5rem;">
                        <div style="color: var(--secondary-color); font-size: 0.9rem; margin-bottom: 0.5rem;">Terakhir Diupdate</div>
                        <div style="font-weight: 600;"><?= date('d/m/Y H:i', strtotime($pelanggan['updated_at'])) ?></div>
                    </div>

                    <div style="border-top: 1px solid var(--border-color); padding-top: 1rem;">
                        <button class="btn btn-outline-primary" onclick="showChangePasswordModal()">
                            Ubah Password
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistik Transaksi -->
        <?php 
        $db = Database::getInstance();
        $stats = $db->fetch("
            SELECT 
                COUNT(*) as total_transaksi,
                COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
                SUM(total_harga) as total_spent
            FROM transaksi 
            WHERE id_pelanggan = ?
        ", [$_SESSION['pelanggan_id']]);
        ?>
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Statistik Transaksi</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    <div style="text-align: center; padding: 1rem; background: var(--light-bg); border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--primary-color);">
                            <?= $stats['total_transaksi'] ?? 0 ?>
                        </div>
                        <div style="color: var(--secondary-color);">Total Pesanan</div>
                    </div>
                    
                    <div style="text-align: center; padding: 1rem; background: var(--light-bg); border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--success-color);">
                            <?= $stats['completed'] ?? 0 ?>
                        </div>
                        <div style="color: var(--secondary-color);">Pesanan Selesai</div>
                    </div>
                    
                    <div style="text-align: center; padding: 1rem; background: var(--light-bg); border-radius: 8px;">
                        <div style="font-size: 2rem; font-weight: bold; color: var(--warning-color);">
                            Rp <?= number_format($stats['total_spent'] ?? 0, 0, ',', '.') ?>
                        </div>
                        <div style="color: var(--secondary-color);">Total Belanja</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal Ubah Password -->
<div id="changePasswordModal" class="modal" style="display: none;">
    <div class="modal-content" style="max-width: 500px;">
        <div class="modal-header">
            <h3 style="margin: 0;">Ubah Password</h3>
            <button type="button" class="modal-close" onclick="closeChangePasswordModal()">&times;</button>
        </div>
        
        <form id="changePasswordForm" method="POST" action="/hakikah/pelanggan/ubah-password">
            <div class="modal-body">
                <div class="form-group">
                    <label for="current_password" class="form-label">Password Saat Ini</label>
                    <input type="password" id="current_password" name="current_password" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="new_password" class="form-label">Password Baru</label>
                    <input type="password" id="new_password" name="new_password" class="form-control" required 
                           minlength="6" placeholder="Minimal 6 karakter">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                </div>
                
                <div class="alert alert-info" style="font-size: 0.9rem;">
                    <strong>Tips Keamanan:</strong>
                    <ul style="margin: 0.5rem 0 0 1rem; padding: 0;">
                        <li>Gunakan minimal 6 karakter</li>
                        <li>Kombinasi huruf besar, kecil, dan angka</li>
                        <li>Hindari informasi personal</li>
                    </ul>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-primary btn-cancel" onclick="closeChangePasswordModal()">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Ubah Password</button>
            </div>
        </form>
    </div>
</div>

<script>
function resetForm() {
    document.getElementById('profilForm').reset();
    // Reset to original values
    document.getElementById('nama').value = "<?= htmlspecialchars($pelanggan['nama']) ?>";
    document.getElementById('email').value = "<?= htmlspecialchars($pelanggan['email']) ?>";
    document.getElementById('no_hp').value = "<?= htmlspecialchars($pelanggan['no_hp']) ?>";
    document.getElementById('alamat').value = "<?= htmlspecialchars($pelanggan['alamat']) ?>";
}

function showChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'flex';
}

function closeChangePasswordModal() {
    document.getElementById('changePasswordModal').style.display = 'none';
    document.getElementById('changePasswordForm').reset();
}

// Password change form validation
document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
    const newPassword = document.getElementById('new_password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    
    if (newPassword !== confirmPassword) {
        e.preventDefault();
        alert('Konfirmasi password tidak cocok dengan password baru.');
        return;
    }
    
    if (newPassword.length < 6) {
        e.preventDefault();
        alert('Password baru minimal 6 karakter.');
        return;
    }
    
    // Form akan disubmit ke /hakikah/pelanggan/ubah-password
    return true;
});

// Auto-format phone number
document.getElementById('no_hp').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
    
    // Ensure it starts with 08 if user enters different format
    if (value.length > 0 && !value.startsWith('08')) {
        if (value.startsWith('8')) {
            value = '0' + value;
        } else if (value.startsWith('62')) {
            value = '0' + value.substring(2);
        }
    }
    
    e.target.value = value;
});

// Form validation
document.getElementById('profilForm').addEventListener('submit', function(e) {
    const email = document.getElementById('email').value;
    const no_hp = document.getElementById('no_hp').value;
    
    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Format email tidak valid.');
        return;
    }
    
    // Validate phone number
    if (!no_hp.startsWith('08') || no_hp.length < 10) {
        e.preventDefault();
        alert('Nomor HP harus dimulai dengan 08 dan minimal 10 digit.');
        return;
    }
});
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>