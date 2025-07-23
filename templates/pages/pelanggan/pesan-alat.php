<?php 
$title = 'Pesan Alat - Dashboard Pelanggan';
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
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Form Pemesanan</h1>
            <p style="color: var(--secondary-color);">Isi detail pemesanan alat pesta Anda</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Detail Alat -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Detail Alat</h3>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1.5rem;">
                        <div style="height: 200px; background: linear-gradient(135deg, var(--light-color), #e3f2fd); 
                                    display: flex; align-items: center; justify-content: center; border-radius: 0.375rem; margin-bottom: 1rem;">
                            <?php if ($alat['gambar']): ?>
                                <img src="/hakikah/public/uploads/alat/<?= htmlspecialchars($alat['gambar']) ?>" 
                                     alt="<?= htmlspecialchars($alat['nama_alat']) ?>"
                                     style="max-width: 100%; max-height: 100%; object-fit: cover; border-radius: 0.375rem;">
                            <?php else: ?>
                                <div style="text-align: center; color: var(--secondary-color);">
                                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">📦</div>
                                    <p style="margin: 0;">Tidak ada gambar</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">
                            <?= htmlspecialchars($alat['nama_alat']) ?>
                        </h4>
                        <span class="badge badge-secondary"><?= htmlspecialchars($alat['kategori']) ?></span>
                    </div>
                    
                    <div style="margin-bottom: 1rem;">
                        <strong>Deskripsi:</strong>
                        <p style="margin: 0.5rem 0; color: var(--secondary-color);">
                            <?= htmlspecialchars($alat['deskripsi']) ?>
                        </p>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <strong>Harga per hari:</strong>
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color);">
                                Rp <?= number_format($alat['harga'], 0, ',', '.') ?>
                            </div>
                        </div>
                        <div>
                            <strong>Stok tersedia:</strong>
                            <div style="font-size: 1.25rem; font-weight: 700; color: var(--success-color);">
                                <?= $alat['stok'] ?> unit
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Pemesanan -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Detail Pemesanan</h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="/hakikah/pelanggan/pesan" id="pesanForm">
                        <input type="hidden" name="id_alat" value="<?= $alat['id_alat'] ?>">
                        
                        <div class="form-group">
                            <label for="tgl_sewa" class="form-label">Tanggal Mulai Sewa</label>
                            <input type="date" id="tgl_sewa" name="tgl_sewa" class="form-control" required
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?= htmlspecialchars($_SESSION['old_data']['tgl_sewa'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="tgl_kembali" class="form-label">Tanggal Pengembalian</label>
                            <input type="date" id="tgl_kembali" name="tgl_kembali" class="form-control" required
                                   min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                   value="<?= htmlspecialchars($_SESSION['old_data']['tgl_kembali'] ?? '') ?>">
                        </div>

                        <div class="form-group">
                            <label for="jumlah_alat" class="form-label">Jumlah Alat</label>
                            <input type="number" id="jumlah_alat" name="jumlah_alat" class="form-control" 
                                   min="1" max="<?= $alat['stok'] ?>" required
                                   value="<?= htmlspecialchars($_SESSION['old_data']['jumlah_alat'] ?? '1') ?>">
                            <small style="color: var(--secondary-color);">
                                Maksimal <?= $alat['stok'] ?> unit
                            </small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Metode Pengambilan</label>
                            <div style="display: flex; flex-direction: column; gap: 0.75rem; margin-top: 0.5rem;">
                                <!-- Pickup Option -->
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; transition: all 0.2s ease;" id="pickupOption">
                                    <input type="radio" name="metode_pengambilan" value="pickup" checked style="margin-right: 0.75rem;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: var(--primary-color);">
                                            <i class="fas fa-store"></i> Pickup di Toko
                                        </div>
                                        <small style="color: var(--secondary-color);">Ambil sendiri di lokasi toko</small>
                                    </div>
                                </label>

                                <!-- Delivery to Profile Address -->
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; transition: all 0.2s ease;" id="deliveryProfileOption">
                                    <input type="radio" name="metode_pengambilan" value="delivery_profile" style="margin-right: 0.75rem;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: var(--success-color);">
                                            <i class="fas fa-truck"></i> Delivery ke Alamat Profil
                                        </div>
                                        <small style="color: var(--secondary-color);">
                                            <?php if (!empty($pelangganData['alamat'])): ?>
                                                <?= htmlspecialchars(substr($pelangganData['alamat'], 0, 60)) ?><?= strlen($pelangganData['alamat']) > 60 ? '...' : '' ?>
                                            <?php else: ?>
                                                <span style="color: var(--danger-color);">Alamat profil belum diisi</span>
                                            <?php endif; ?>
                                        </small>
                                    </div>
                                </label>

                                <!-- Delivery to Custom Address -->
                                <label style="display: flex; align-items: center; cursor: pointer; padding: 0.75rem; border: 2px solid var(--border-color); border-radius: 8px; transition: all 0.2s ease;" id="deliveryCustomOption">
                                    <input type="radio" name="metode_pengambilan" value="delivery_custom" style="margin-right: 0.75rem;">
                                    <div style="flex: 1;">
                                        <div style="font-weight: 600; color: var(--info-color);">
                                            <i class="fas fa-map-marker-alt"></i> Delivery ke Alamat Lain
                                        </div>
                                        <small style="color: var(--secondary-color);">Kirim ke alamat berbeda dari profil</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="alamatCustomGroup" style="display: none;">
                            <label for="alamat_pengiriman" class="form-label">Alamat Pengiriman Custom</label>
                            <textarea id="alamat_pengiriman" name="alamat_pengiriman" class="form-control" rows="3" 
                                      placeholder="Masukkan alamat lengkap untuk pengiriman..."><?= htmlspecialchars($_SESSION['old_data']['alamat_pengiriman'] ?? '') ?></textarea>
                            <small style="color: var(--warning-color);">
                                <i class="fas fa-info-circle"></i> Biaya pengiriman akan ditambahkan sesuai jarak
                            </small>
                        </div>

                        <div class="card" style="background-color: var(--light-color); border: 1px solid var(--border-color); margin: 1.5rem 0;">
                            <div class="card-body">
                                <h4 style="margin: 0 0 1rem 0; color: var(--primary-color);">Ringkasan Biaya</h4>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Harga per hari:</span>
                                    <span>Rp <?= number_format($alat['harga'], 0, ',', '.') ?></span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Jumlah hari:</span>
                                    <span id="jumlahHari">0 hari</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                    <span>Jumlah alat:</span>
                                    <span id="displayJumlahAlat">1 unit</span>
                                </div>
                                <hr>
                                <div style="display: flex; justify-content: space-between; font-weight: 700; color: var(--primary-color); font-size: 1.1rem;">
                                    <span>Total Biaya:</span>
                                    <span id="totalBiaya">Rp 0</span>
                                </div>
                            </div>
                        </div>

                        <div class="btn-actions">
                            <a href="/hakikah/pelanggan/alat" class="btn btn-outline-primary btn-cancel" style="flex: 1;">
                                Kembali
                            </a>
                            <button type="submit" class="btn btn-primary btn-submit" style="flex: 1;">
                                Pesan Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const hargaPerHari = <?= $alat['harga'] ?>;

function hitungTotal() {
    const tglSewa = document.getElementById('tgl_sewa').value;
    const tglKembali = document.getElementById('tgl_kembali').value;
    const jumlahAlat = parseInt(document.getElementById('jumlah_alat').value) || 1;
    
    if (tglSewa && tglKembali) {
        const startDate = new Date(tglSewa);
        const endDate = new Date(tglKembali);
        const diffTime = endDate - startDate;
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays > 0) {
            const total = hargaPerHari * diffDays * jumlahAlat;
            
            document.getElementById('jumlahHari').textContent = diffDays + ' hari';
            document.getElementById('displayJumlahAlat').textContent = jumlahAlat + ' unit';
            document.getElementById('totalBiaya').textContent = 'Rp ' + total.toLocaleString('id-ID');
        } else {
            document.getElementById('jumlahHari').textContent = '0 hari';
            document.getElementById('totalBiaya').textContent = 'Rp 0';
        }
    }
}

// Event listeners
document.getElementById('tgl_sewa').addEventListener('change', function() {
    const tglSewa = this.value;
    const tglKembaliInput = document.getElementById('tgl_kembali');
    
    if (tglSewa) {
        const minKembali = new Date(tglSewa);
        minKembali.setDate(minKembali.getDate() + 1);
        tglKembaliInput.min = minKembali.toISOString().split('T')[0];
        
        if (tglKembaliInput.value && tglKembaliInput.value <= tglSewa) {
            tglKembaliInput.value = '';
        }
    }
    
    hitungTotal();
});

document.getElementById('tgl_kembali').addEventListener('change', hitungTotal);
document.getElementById('jumlah_alat').addEventListener('input', hitungTotal);

// Handle delivery method selection
const pickupRadio = document.querySelector('input[name="metode_pengambilan"][value="pickup"]');
const deliveryProfileRadio = document.querySelector('input[name="metode_pengambilan"][value="delivery_profile"]');
const deliveryCustomRadio = document.querySelector('input[name="metode_pengambilan"][value="delivery_custom"]');
const alamatCustomGroup = document.getElementById('alamatCustomGroup');
const alamatInput = document.getElementById('alamat_pengiriman');
const pickupOption = document.getElementById('pickupOption');
const deliveryProfileOption = document.getElementById('deliveryProfileOption');
const deliveryCustomOption = document.getElementById('deliveryCustomOption');

function updateDeliveryMethod() {
    // Reset all styles
    [pickupOption, deliveryProfileOption, deliveryCustomOption].forEach(option => {
        option.style.borderColor = 'var(--border-color)';
        option.style.backgroundColor = 'transparent';
    });

    if (pickupRadio.checked) {
        alamatCustomGroup.style.display = 'none';
        alamatInput.required = false;
        alamatInput.value = '';
        pickupOption.style.borderColor = 'var(--primary-color)';
        pickupOption.style.backgroundColor = 'rgba(13, 110, 253, 0.1)';
    } else if (deliveryProfileRadio.checked) {
        alamatCustomGroup.style.display = 'none';
        alamatInput.required = false;
        alamatInput.value = '';
        deliveryProfileOption.style.borderColor = 'var(--success-color)';
        deliveryProfileOption.style.backgroundColor = 'rgba(25, 135, 84, 0.1)';
    } else if (deliveryCustomRadio.checked) {
        alamatCustomGroup.style.display = 'block';
        alamatInput.required = true;
        deliveryCustomOption.style.borderColor = 'var(--info-color)';
        deliveryCustomOption.style.backgroundColor = 'rgba(13, 202, 240, 0.1)';
    }
}

pickupRadio.addEventListener('change', updateDeliveryMethod);
deliveryProfileRadio.addEventListener('change', updateDeliveryMethod);
deliveryCustomRadio.addEventListener('change', updateDeliveryMethod);

// Initial method setup
updateDeliveryMethod();

// Initial calculation
hitungTotal();
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>