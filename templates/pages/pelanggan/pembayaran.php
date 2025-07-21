<?php 
$title = 'Konfirmasi Pembayaran - Dashboard Pelanggan';
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
            <li><a href="/hakikah/pelanggan/pesanan" class="active">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Konfirmasi Pembayaran</h1>
            <p style="color: var(--secondary-color);">
                Lakukan pembayaran dan upload bukti transfer untuk menyelesaikan pesanan
            </p>
        </div>

        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem; max-width: 1200px;">
            <!-- Detail Pesanan -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 1.2rem;">Detail Pesanan #<?= $transaksi['id_transaksi'] ?></h3>
                </div>
                <div class="card-body" style="padding: 1rem;">
                    <div style="margin-bottom: 1rem;">
                        <strong style="color: var(--primary-color); font-size: 1rem;"><?= htmlspecialchars($transaksi['nama_alat']) ?></strong>
                        <div style="margin: 0.3rem 0;">
                            <span class="badge badge-secondary" style="font-size: 0.8rem;"><?= htmlspecialchars($transaksi['kategori']) ?></span>
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 0.8rem; font-size: 0.9rem;">
                        <div>
                            <div style="color: var(--secondary-color); font-size: 0.8rem;">Tanggal Sewa</div>
                            <div style="font-weight: 600;"><?= date('d/m/Y', strtotime($transaksi['tgl_sewa'])) ?></div>
                        </div>
                        <div>
                            <div style="color: var(--secondary-color); font-size: 0.8rem;">Tanggal Kembali</div>
                            <div style="font-weight: 600;"><?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?></div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.8rem; margin-bottom: 0.8rem; font-size: 0.9rem;">
                        <div>
                            <div style="color: var(--secondary-color); font-size: 0.8rem;">Jumlah</div>
                            <div style="font-weight: 600;"><?= $transaksi['jumlah_alat'] ?> unit</div>
                        </div>
                        <div>
                            <div style="color: var(--secondary-color); font-size: 0.8rem;">Harga Satuan</div>
                            <div style="font-weight: 600;">Rp <?= number_format($transaksi['harga'], 0, ',', '.') ?></div>
                        </div>
                    </div>

                    <?php 
                    $durasi = (strtotime($transaksi['tgl_kembali']) - strtotime($transaksi['tgl_sewa'])) / (60 * 60 * 24) + 1;
                    ?>
                    <div style="border-top: 1px solid var(--border-color); padding-top: 0.8rem; margin-top: 0.8rem; font-size: 0.9rem;">
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span>Durasi Sewa:</span>
                            <span style="font-weight: 600;"><?= $durasi ?> hari</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; margin-bottom: 0.3rem;">
                            <span>Subtotal:</span>
                            <span>Rp <?= number_format($transaksi['harga'] * $transaksi['jumlah_alat'], 0, ',', '.') ?></span>
                        </div>
                        <div style="display: flex; justify-content: space-between; font-size: 1rem; font-weight: 700; color: var(--primary-color);">
                            <span>Total Pembayaran:</span>
                            <span>Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?></span>
                        </div>
                    </div>

                    <div style="background: var(--light-bg); padding: 0.8rem; border-radius: 6px; margin-top: 0.8rem;">
                        <div style="font-size: 0.8rem; color: var(--secondary-color); margin-bottom: 0.3rem;">Status:</div>
                        <span class="badge badge-<?= $transaksi['status'] === 'pending' ? 'warning' : 'primary' ?>">
                            <?= ucfirst($transaksi['status']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Form Pembayaran -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0; font-size: 1.1rem;">Informasi Pembayaran</h3>
                </div>
                <div class="card-body" style="padding: 1rem;">
                    <!-- Bank Information -->
                    <div style="background: var(--light-bg); padding: 1rem; border-radius: 6px; margin-bottom: 1rem;">
                        <h4 style="margin: 0 0 0.8rem 0; color: var(--primary-color); font-size: 1rem;">Rekening Pembayaran</h4>
                        
                        <div style="display: grid; gap: 0.8rem;">
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div style="background: white; padding: 0.4rem 0.6rem; border-radius: 4px; font-weight: bold; min-width: 60px; text-align: center; font-size: 0.9rem;">
                                    BCA
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 1rem;">1234567890</div>
                                    <div style="color: var(--secondary-color); font-size: 0.8rem;">a.n. Rental Alat Pesta Haqiqah</div>
                                </div>
                            </div>
                            
                            <div style="display: flex; align-items: center; gap: 0.8rem;">
                                <div style="background: white; padding: 0.4rem 0.6rem; border-radius: 4px; font-weight: bold; min-width: 60px; text-align: center; font-size: 0.9rem;">
                                    BRI
                                </div>
                                <div>
                                    <div style="font-weight: 600; font-size: 1rem;">0987654321</div>
                                    <div style="color: var(--secondary-color); font-size: 0.8rem;">a.n. Rental Alat Pesta Haqiqah</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form method="POST" action="/hakikah/pelanggan/pembayaran/<?= $transaksi['id_transaksi'] ?>" 
                          enctype="multipart/form-data" id="paymentForm">
                        
                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="jumlah" class="form-label" style="font-size: 0.9rem;">Jumlah Pembayaran (Rp)</label>
                            <input type="number" id="jumlah" name="jumlah" class="form-control" 
                                   value="<?= $transaksi['total_harga'] ?>" readonly
                                   style="background-color: var(--light-bg); font-size: 0.9rem;">
                            <small class="form-text" style="font-size: 0.8rem;">Jumlah pembayaran harus sesuai dengan total tagihan</small>
                        </div>

                        <div class="form-group" style="margin-bottom: 1rem;">
                            <label for="bukti_transfer" class="form-label" style="font-size: 0.9rem;">Bukti Transfer</label>
                            <input type="file" id="bukti_transfer" name="bukti_transfer" class="form-control" 
                                   accept="image/jpeg,image/jpg,image/png,application/pdf" required style="font-size: 0.9rem;">
                            <small class="form-text" style="font-size: 0.8rem;">
                                Upload bukti transfer. Format: JPG, PNG, PDF. Maks 5MB.
                            </small>
                        </div>

                        <!-- Preview uploaded image -->
                        <div id="imagePreview" style="display: none; margin-top: 0.8rem;">
                            <label class="form-label" style="font-size: 0.9rem;">Preview Bukti Transfer:</label>
                            <div style="border: 1px solid var(--border-color); border-radius: 6px; padding: 0.8rem; text-align: center;">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                            </div>
                        </div>

                        <div class="btn-actions" style="justify-content: flex-end; margin-top: 1.5rem;">
                            <a href="/hakikah/pelanggan/pesanan" class="btn btn-outline-primary btn-cancel" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Kembali</a>
                            <button type="submit" class="btn btn-primary btn-submit" id="submitBtn" style="font-size: 0.9rem; padding: 0.5rem 1rem;">
                                Konfirmasi Pembayaran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Payment Instructions -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Petunjuk Pembayaran</h3>
            </div>
            <div class="card-body">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">1</div>
                            <strong>Transfer ke Rekening</strong>
                        </div>
                        <p style="margin: 0; color: var(--secondary-color); font-size: 0.9rem;">
                            Lakukan transfer sesuai dengan total pembayaran ke salah satu rekening yang tertera di atas.
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">2</div>
                            <strong>Upload Bukti Transfer</strong>
                        </div>
                        <p style="margin: 0; color: var(--secondary-color); font-size: 0.9rem;">
                            Ambil foto atau screenshot bukti transfer dan upload melalui form di atas.
                        </p>
                    </div>
                    
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <div style="background: var(--primary-color); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: bold;">3</div>
                            <strong>Menunggu Verifikasi</strong>
                        </div>
                        <p style="margin: 0; color: var(--secondary-color); font-size: 0.9rem;">
                            Admin akan memverifikasi pembayaran Anda dalam waktu 1x24 jam. Status pesanan akan diperbarui setelah verifikasi.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// Image preview functionality
document.getElementById('bukti_transfer').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    
    if (file) {
        // Validate file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak valid. Gunakan JPG, JPEG, atau PNG.');
            this.value = '';
            preview.style.display = 'none';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Form validation
document.getElementById('paymentForm').addEventListener('submit', function(e) {
    const buktiTransfer = document.getElementById('bukti_transfer');
    
    if (!buktiTransfer.files.length) {
        e.preventDefault();
        alert('Harap upload bukti transfer terlebih dahulu.');
        buktiTransfer.focus();
        return false;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Memproses...';
    
    return true;
});

// Auto-calculate payment based on duration (if needed)
document.addEventListener('DOMContentLoaded', function() {
    // Focus on file input when page loads
    setTimeout(() => {
        document.getElementById('bukti_transfer').focus();
    }, 500);
});
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>