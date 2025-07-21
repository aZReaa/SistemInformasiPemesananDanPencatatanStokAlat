<?php
require_once __DIR__ . '/../../layouts/header.php';
?>

<main class="flex-grow-1">
<div class="container mt-4">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <?php if (!empty($alat['gambar'])): ?>
                                <img src="/hakikah/public/uploads/alat/<?= htmlspecialchars($alat['gambar']) ?>" 
                                     class="img-fluid rounded" alt="<?= htmlspecialchars($alat['nama_alat']) ?>">
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                                    <i class="fas fa-image fa-3x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-6">
                            <h3><?= htmlspecialchars($alat['nama_alat']) ?></h3>
                            <p class="text-muted mb-3"><?= htmlspecialchars($alat['kategori']) ?></p>
                            
                            <div class="mb-3">
                                <h5 class="text-primary">Rp <?= number_format($alat['harga_sewa'] ?? $alat['harga'] ?? 0, 0, ',', '.') ?> / hari</h5>
                            </div>
                            
                            <div class="mb-3">
                                <span class="badge <?= $alat['stok'] > 0 ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $alat['stok'] > 0 ? 'Tersedia (' . $alat['stok'] . ' unit)' : 'Stok Habis' ?>
                                </span>
                            </div>
                            
                            <?php if ($alat['stok'] > 0): ?>
                                <a href="/hakikah/pelanggan/pesan/<?= $alat['id_alat'] ?>" 
                                   class="btn btn-primary btn-lg">
                                    <i class="fas fa-shopping-cart me-2"></i>Sewa Sekarang
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Spesifikasi</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($alat['spesifikasi'])): ?>
                        <p><?= nl2br(htmlspecialchars($alat['spesifikasi'])) ?></p>
                    <?php else: ?>
                        <p class="text-muted">Spesifikasi tidak tersedia</p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">Deskripsi</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($alat['deskripsi'])): ?>
                        <p><?= nl2br(htmlspecialchars($alat['deskripsi'])) ?></p>
                    <?php else: ?>
                        <p class="text-muted">Deskripsi tidak tersedia</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>