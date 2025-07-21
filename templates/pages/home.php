<?php 
$title = 'Beranda - Sistem Penyewaan Alat Pesta Haqiqah';
include __DIR__ . '/../layouts/header.php'; 
?>

<section class="hero">
    <div class="container">
        <h1>Selamat Datang di Haqiqah Rental</h1>
        <p>Solusi lengkap untuk kebutuhan penyewaan alat pesta pernikahan dan haqiqah Anda</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <div style="gap: 1rem; display: flex; justify-content: center; flex-wrap: wrap;">
                <a href="/hakikah/register" class="btn btn-primary">Daftar Sekarang</a>
                <a href="/hakikah/login" class="btn btn-outline-primary" style="background-color: rgba(255,255,255,0.1); border-color: white; color: white;">Login</a>
            </div>
        <?php else: ?>
            <a href="/hakikah/dashboard" class="btn btn-primary">Masuk ke Dashboard</a>
        <?php endif; ?>
    </div>
</section>

<section style="padding: 4rem 0;">
    <div class="container">
        <div class="text-center mb-3">
            <h2 style="margin-bottom: 1rem;">Layanan Kami</h2>
            <p style="color: var(--secondary-color); max-width: 600px; margin: 0 auto;">
                Kami menyediakan berbagai peralatan berkualitas untuk acara pernikahan dan haqiqah Anda
            </p>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>🎪</h3>
                <h4 style="color: var(--primary-color); margin: 0.5rem 0;">Tenda & Canopy</h4>
                <p>Tenda berkualitas berbagai ukuran untuk melindungi acara Anda</p>
            </div>
            <div class="stat-card">
                <h3>🪑</h3>
                <h4 style="color: var(--primary-color); margin: 0.5rem 0;">Kursi & Meja</h4>
                <p>Kursi dan meja yang nyaman untuk tamu undangan</p>
            </div>
            <div class="stat-card">
                <h3>🎵</h3>
                <h4 style="color: var(--primary-color); margin: 0.5rem 0;">Sound System</h4>
                <p>Peralatan audio berkualitas untuk hiburan acara</p>
            </div>
            <div class="stat-card">
                <h3>🎊</h3>
                <h4 style="color: var(--primary-color); margin: 0.5rem 0;">Dekorasi</h4>
                <p>Berbagai dekorasi untuk mempercantik acara Anda</p>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
                    <div>
                        <h3 style="margin-bottom: 1rem; color: var(--primary-color);">Mengapa Memilih Kami?</h3>
                        <ul style="list-style: none; padding: 0;">
                            <li style="margin-bottom: 0.5rem;">✅ Peralatan berkualitas dan terawat</li>
                            <li style="margin-bottom: 0.5rem;">✅ Harga kompetitif dan terjangkau</li>
                            <li style="margin-bottom: 0.5rem;">✅ Layanan antar jemput</li>
                            <li style="margin-bottom: 0.5rem;">✅ Tim profesional dan berpengalaman</li>
                            <li style="margin-bottom: 0.5rem;">✅ Booking online yang mudah</li>
                        </ul>
                    </div>
                    <div style="text-align: center;">
                        <h4 style="color: var(--primary-color); margin-bottom: 1rem;">Siap Memulai?</h4>
                        <p style="margin-bottom: 1.5rem; color: var(--secondary-color);">
                            Daftar sekarang dan nikmati kemudahan booking alat pesta online
                        </p>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <a href="/hakikah/register" class="btn btn-primary">Daftar Gratis</a>
                        <?php else: ?>
                            <a href="/hakikah/dashboard" class="btn btn-primary">Lihat Katalog</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>