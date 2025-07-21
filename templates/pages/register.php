<?php 
$title = 'Registrasi - Sistem Penyewaan Alat Pesta Haqiqah';
$hideHeader = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="auth-container">
    <div style="width: 100%; max-width: 500px; margin: 2rem;">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="margin: 0; color: var(--primary-color);">Registrasi</h2>
                <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">Buat akun baru untuk mulai menyewa</p>
            </div>
            <div class="card-body">
                <form action="/hakikah/register" method="POST">
                    <div class="form-group">
                        <label for="nama" class="form-label">Nama Lengkap</label>
                        <input type="text" id="nama" name="nama" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['old_data']['nama'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="alamat" class="form-label">Alamat</label>
                        <textarea id="alamat" name="alamat" class="form-control" rows="3" required><?= htmlspecialchars($_SESSION['old_data']['alamat'] ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['old_data']['email'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="no_hp" class="form-label">Nomor HP</label>
                        <input type="tel" id="no_hp" name="no_hp" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['old_data']['no_hp'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['old_data']['username'] ?? '') ?>">
                        <small style="color: var(--secondary-color); font-size: 0.875rem;">Minimal 3 karakter</small>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                        <small style="color: var(--secondary-color); font-size: 0.875rem;">Minimal 6 karakter</small>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Daftar</button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <p style="margin: 0; color: var(--secondary-color);">
                        Sudah punya akun? 
                        <a href="/hakikah/login" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Login di sini</a>
                    </p>
                    <p style="margin: 0.5rem 0 0 0;">
                        <a href="/hakikah/" style="color: var(--secondary-color); text-decoration: none;">
                            ← Kembali ke Beranda
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>