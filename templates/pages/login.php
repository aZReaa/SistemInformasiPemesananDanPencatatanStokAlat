<?php 
$title = 'Login - Sistem Penyewaan Alat Pesta Haqiqah';
$hideHeader = true;
include __DIR__ . '/../layouts/header.php'; 
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="card">
            <div class="card-header text-center">
                <h2 style="margin: 0; color: var(--primary-color);">Login</h2>
                <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">Masuk ke akun Anda</p>
            </div>
            <div class="card-body">
                <form action="/hakikah/login" method="POST">
                    <div class="form-group">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" id="username" name="username" class="form-control" required 
                               value="<?= htmlspecialchars($_SESSION['old_data']['username'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-primary btn-submit" style="width: 100%;">Login</button>
                </form>

                <div style="text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color);">
                    <p style="margin: 0; color: var(--secondary-color);">
                        Belum punya akun? 
                        <a href="/hakikah/register" style="color: var(--primary-color); text-decoration: none; font-weight: 500;">Daftar di sini</a>
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