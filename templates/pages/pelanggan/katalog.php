<?php 
$title = 'Katalog Alat Pesta - Dashboard Pelanggan';
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
            <li><a href="/hakikah/pelanggan/alat" class="active">Katalog Alat</a></li>
            <li><a href="/hakikah/pelanggan/pesanan">Pesanan Saya</a></li>
            <li><a href="/hakikah/pelanggan/riwayat">Riwayat Transaksi</a></li>
            <li><a href="/hakikah/pelanggan/profil">Profil Saya</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Katalog Alat Pesta</h1>
            <p style="color: var(--secondary-color);">Pilih alat pesta yang Anda butuhkan untuk acara spesial Anda</p>
        </div>

        <div class="card" style="margin-bottom: 1.5rem;">
            <div class="card-body">
                <form method="GET" action="/hakikah/pelanggan/alat" style="display: flex; gap: 1rem; align-items: end; flex-wrap: wrap;">
                    <div class="form-group" style="margin: 0; min-width: 200px;">
                        <label for="search" class="form-label">Cari Alat</label>
                        <input type="text" id="search" name="search" class="form-control" 
                               placeholder="Nama alat..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div class="form-group" style="margin: 0; min-width: 150px;">
                        <label for="kategori" class="form-label">Kategori</label>
                        <select id="kategori" name="kategori" class="form-control">
                            <option value="">Semua Kategori</option>
                            <?php foreach ($kategoriList as $kat): ?>
                                <option value="<?= htmlspecialchars($kat['kategori']) ?>" 
                                        <?= $kategori === $kat['kategori'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($kat['kategori']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="btn-actions">
                        <button type="submit" class="btn btn-primary btn-submit">Cari</button>
                        <a href="/hakikah/pelanggan/alat" class="btn btn-outline-primary btn-cancel">Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($alatList)): ?>
            <div class="card">
                <div class="card-body">
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        <?php if (!empty($search) || !empty($kategori)): ?>
                            Tidak ada alat yang sesuai dengan pencarian Anda
                        <?php else: ?>
                            Belum ada alat tersedia saat ini
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem;">
                <?php foreach ($alatList as $alat): ?>
                    <div class="card" style="overflow: hidden;">
                        <div style="height: 200px; background: linear-gradient(135deg, var(--light-color), #e3f2fd); display: flex; align-items: center; justify-content: center;">
                            <?php if ($alat['gambar']): ?>
                                <img src="/hakikah/public/images/alat/<?= htmlspecialchars($alat['gambar']) ?>" 
                                     alt="<?= htmlspecialchars($alat['nama_alat']) ?>"
                                     style="max-width: 100%; max-height: 100%; object-fit: cover;">
                            <?php else: ?>
                                <div style="text-align: center; color: var(--secondary-color);">
                                    <div style="font-size: 3rem; margin-bottom: 0.5rem;">📦</div>
                                    <p style="margin: 0;">Tidak ada gambar</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <div style="margin-bottom: 1rem;">
                                <h4 style="margin: 0 0 0.5rem 0; color: var(--primary-color);">
                                    <?= htmlspecialchars($alat['nama_alat']) ?>
                                </h4>
                                <span class="badge badge-secondary"><?= htmlspecialchars($alat['kategori']) ?></span>
                            </div>
                            
                            <p style="margin-bottom: 1rem; color: var(--secondary-color); font-size: 0.875rem;">
                                <?= htmlspecialchars($alat['deskripsi']) ?>
                            </p>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                <div>
                                    <div style="font-size: 1.25rem; font-weight: 700; color: var(--primary-color);">
                                        Rp <?= number_format($alat['harga'], 0, ',', '.') ?>
                                    </div>
                                    <small style="color: var(--secondary-color);">per hari</small>
                                </div>
                                <div style="text-align: right;">
                                    <div style="font-weight: 600; color: <?= $alat['stok'] > 0 ? 'var(--success-color)' : 'var(--danger-color)' ?>;">
                                        <?= $alat['stok'] ?> unit
                                    </div>
                                    <small style="color: var(--secondary-color);">tersedia</small>
                                </div>
                            </div>
                            
                            <div class="btn-actions">
                                <a href="/hakikah/pelanggan/alat/<?= $alat['id_alat'] ?>" 
                                   class="btn btn-outline-primary btn-cancel" style="flex: 1;">
                                    Detail
                                </a>
                                <?php if ($alat['stok'] > 0): ?>
                                    <a href="/hakikah/pelanggan/pesan/<?= $alat['id_alat'] ?>" 
                                       class="btn btn-primary btn-submit" style="flex: 1;">
                                        Pesan Sekarang
                                    </a>
                                <?php else: ?>
                                    <button class="btn btn-outline-primary" style="flex: 1;" disabled>
                                        Stok Habis
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <p style="color: var(--secondary-color);">
                    Menampilkan <?= count($alatList) ?> alat dari total yang tersedia
                </p>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>