<?php 
$title = 'Kelola Transaksi - Admin Dashboard';
include __DIR__ . '/../../layouts/header.php';
?>

<div class="dashboard">
    <aside class="sidebar">
        <div style="padding: 0 1.5rem; margin-bottom: 2rem;">
            <h3 style="color: var(--primary-color); margin: 0;">Admin Panel</h3>
            <p style="margin: 0.5rem 0 0 0; color: var(--secondary-color);">
                <?= htmlspecialchars($_SESSION['nama']) ?>
            </p>
        </div>
        <ul class="sidebar-nav">
            <li><a href="/hakikah/dashboard">Dashboard</a></li>
            <li><a href="/hakikah/admin/pelanggan">Kelola Pelanggan</a></li>
            <li><a href="/hakikah/admin/alat">Kelola Alat</a></li>
            <li><a href="/hakikah/admin/kategori">Kelola Kategori</a></li>
            <li><a href="/hakikah/admin/transaksi" class="active">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <h1 style="margin-bottom: 0.5rem;">Kelola Transaksi</h1>
            <p style="color: var(--secondary-color);">Manajemen semua transaksi penyewaan alat</p>
        </div>

        <div class="stats-grid" style="grid-template-columns: repeat(6, 1fr);">
            <?php
            $statusCounts = [
                'pending' => 0,
                'confirmed' => 0,
                'approved' => 0,
                'ongoing' => 0,
                'completed' => 0,
                'cancelled' => 0
            ];
            foreach ($transaksiList as $t) {
                if (isset($statusCounts[$t['status']])) {
                    $statusCounts[$t['status']]++;
                }
            }
            ?>
            <div class="stat-card">
                <h3 style="color: var(--warning-color);"><?= $statusCounts['pending'] ?></h3>
                <p>Pending</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--info-color);"><?= $statusCounts['confirmed'] ?></h3>
                <p>Confirmed</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--primary-color);"><?= $statusCounts['approved'] ?></h3>
                <p>Approved</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--info-color);"><?= $statusCounts['ongoing'] ?></h3>
                <p>Ongoing</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--success-color);"><?= $statusCounts['completed'] ?></h3>
                <p>Completed</p>
            </div>
            <div class="stat-card">
                <h3 style="color: var(--danger-color);"><?= $statusCounts['cancelled'] ?></h3>
                <p>Cancelled</p>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 style="margin: 0;">Daftar Transaksi</h3>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <button class="btn btn-primary" onclick="document.getElementById('tambahTransaksiModal').style.display='block'">
                        <i class="fas fa-plus"></i> Tambah Transaksi
                    </button>
                    <select id="filterStatus" class="form-control" style="width: 150px;">
                        <option value="">Semua Status</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                    <input type="text" id="searchTransaksi" placeholder="Cari transaksi..." class="form-control" style="width: 250px;">
                </div>
            </div>
            <div class="card-body">
                <?php if (empty($transaksiList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Belum ada transaksi
                    </p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table" id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Pelanggan</th>
                                    <th>Alat</th>
                                    <th>Periode Sewa</th>
                                    <th>Alamat/Pengiriman</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Pembayaran</th>
                                    <th style="width: 200px;">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksiList as $transaksi): ?>
                                    <tr data-status="<?= $transaksi['status'] ?>">
                                        <td><?= $transaksi['id_transaksi'] ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($transaksi['nama_alat']) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($transaksi['tgl_sewa'])) ?><br>
                                            <small style="color: var(--secondary-color);">
                                                s/d <?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?>
                                            </small>
                                        </td>
                                        <td>
                                            <?php if ($transaksi['metode_pengambilan'] === 'pickup'): ?>
                                                <div style="color: var(--primary-color); font-weight: 500;">
                                                    <i class="fas fa-store"></i> Pickup di Toko
                                                </div>
                                                <small style="color: var(--secondary-color);">
                                                    Pelanggan ambil sendiri
                                                </small>
                                            <?php elseif ($transaksi['metode_pengambilan'] === 'delivery_profile'): ?>
                                                <div style="font-size: 0.9rem;">
                                                    <i class="fas fa-truck" style="color: var(--success-color);"></i>
                                                    <?= htmlspecialchars(substr($transaksi['alamat_pelanggan'], 0, 50)) ?>
                                                    <?= strlen($transaksi['alamat_pelanggan']) > 50 ? '...' : '' ?>
                                                </div>
                                                <small style="color: var(--success-color); font-weight: 500;">
                                                    <i class="fas fa-user"></i> Delivery ke Alamat Profil
                                                </small>
                                            <?php elseif ($transaksi['metode_pengambilan'] === 'delivery_custom'): ?>
                                                <div style="font-size: 0.9rem;">
                                                    <i class="fas fa-truck" style="color: var(--info-color);"></i>
                                                    <?= htmlspecialchars(substr($transaksi['alamat_pengiriman'], 0, 50)) ?>
                                                    <?= strlen($transaksi['alamat_pengiriman']) > 50 ? '...' : '' ?>
                                                </div>
                                                <small style="color: var(--info-color); font-weight: 500;">
                                                    <i class="fas fa-map-marker-alt"></i> Delivery ke Alamat Custom
                                                </small>
                                            <?php else: ?>
                                                <!-- Fallback for old data -->
                                                <?php if (!empty($transaksi['alamat_pengiriman'])): ?>
                                                    <div style="font-size: 0.9rem;">
                                                        <i class="fas fa-truck" style="color: var(--success-color);"></i>
                                                        <?= htmlspecialchars(substr($transaksi['alamat_pengiriman'], 0, 50)) ?>
                                                        <?= strlen($transaksi['alamat_pengiriman']) > 50 ? '...' : '' ?>
                                                    </div>
                                                    <small style="color: var(--success-color); font-weight: 500;">
                                                        <i class="fas fa-truck"></i> Delivery
                                                    </small>
                                                <?php else: ?>
                                                    <div style="color: var(--warning-color); font-weight: 500;">
                                                        <i class="fas fa-store"></i> Pickup di Toko
                                                    </div>
                                                    <small style="color: var(--secondary-color);">
                                                        Pelanggan ambil sendiri
                                                    </small>
                                                <?php endif; ?>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-weight: 600; color: var(--primary-color);">
                                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($transaksi['status']) {
                                                'pending' => 'badge-warning',
                                                'approved' => 'badge-primary',
                                                'confirmed' => 'badge-primary',
                                                'ongoing' => 'badge-info',
                                                'completed' => 'badge-success',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($transaksi['status']) ?></span>
                                        </td>
                                        <td>
                                            <?php if ($transaksi['status_pembayaran']): ?>
                                                <?php
                                                $paymentBadge = match($transaksi['status_pembayaran']) {
                                                    'pending' => 'badge-warning',
                                                    'verified' => 'badge-success',
                                                    'rejected' => 'badge-danger',
                                                    default => 'badge-secondary'
                                                };
                                                ?>
                                                <span class="badge <?= $paymentBadge ?>"><?= ucfirst($transaksi['status_pembayaran']) ?></span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Belum bayar</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 0.25rem; flex-wrap: nowrap; align-items: center;">
                                                <?php if ($transaksi['status'] === 'pending'): ?>
                                                    <!-- Status Actions -->
                                                    <form method="POST" action="/hakikah/admin/transaksi/update-status" style="display: inline; margin: 0;">
                                                        <input type="hidden" name="id" value="<?= $transaksi['id_transaksi'] ?>">
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="btn btn-sm btn-success" title="Approve" style="padding: 2px 6px; font-size: 12px;">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                    <form method="POST" action="/hakikah/admin/transaksi/update-status" style="display: inline; margin: 0;">
                                                        <input type="hidden" name="id" value="<?= $transaksi['id_transaksi'] ?>">
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="btn btn-sm btn-warning" 
                                                                data-confirm="Yakin ingin menolak transaksi ini?" title="Tolak" style="padding: 2px 6px; font-size: 12px;">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <!-- CRUD Actions -->
                                                    <button class="btn btn-sm btn-info" onclick="openEditModal(<?= $transaksi['id_transaksi'] ?>, <?= $transaksi['id_pelanggan'] ?>, <?= $transaksi['id_alat'] ?>, <?= $transaksi['jumlah_alat'] ?>, '<?= $transaksi['tgl_sewa'] ?>', '<?= $transaksi['tgl_kembali'] ?>', '<?= $transaksi['metode_pengambilan'] ?? 'pickup' ?>', '<?= htmlspecialchars($transaksi['alamat_pengiriman'] ?? '', ENT_QUOTES) ?>')" title="Edit" style="padding: 2px 6px; font-size: 12px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 2px 6px; font-size: 12px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                <?php elseif ($transaksi['status'] === 'approved' || $transaksi['status'] === 'confirmed'): ?>
                                                    <!-- Edit & Delete buttons untuk approved/confirmed -->
                                                    <button class="btn btn-sm btn-info" onclick="openEditModal(<?= $transaksi['id_transaksi'] ?>, <?= $transaksi['id_pelanggan'] ?>, <?= $transaksi['id_alat'] ?>, <?= $transaksi['jumlah_alat'] ?>, '<?= $transaksi['tgl_sewa'] ?>', '<?= $transaksi['tgl_kembali'] ?>', '<?= $transaksi['metode_pengambilan'] ?? 'pickup' ?>', '<?= htmlspecialchars($transaksi['alamat_pengiriman'] ?? '', ENT_QUOTES) ?>')" title="Edit" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Mulai Sewa button -->
                                                    <form method="POST" action="/hakikah/admin/transaksi/update-status" style="display: inline; margin: 0;">
                                                        <input type="hidden" name="id" value="<?= $transaksi['id_transaksi'] ?>">
                                                        <input type="hidden" name="status" value="ongoing">
                                                        <button type="submit" class="btn btn-sm btn-primary" style="padding: 3px 8px; font-size: 11px;">
                                                            <i class="fas fa-play"></i> Mulai
                                                        </button>
                                                    </form>
                                                    
                                                <?php elseif ($transaksi['status'] === 'ongoing'): ?>
                                                    <!-- Edit & Delete untuk ongoing -->
                                                    <button class="btn btn-sm btn-info" onclick="openEditModal(<?= $transaksi['id_transaksi'] ?>, <?= $transaksi['id_pelanggan'] ?>, <?= $transaksi['id_alat'] ?>, <?= $transaksi['jumlah_alat'] ?>, '<?= $transaksi['tgl_sewa'] ?>', '<?= $transaksi['tgl_kembali'] ?>', '<?= $transaksi['metode_pengambilan'] ?? 'pickup' ?>', '<?= htmlspecialchars($transaksi['alamat_pengiriman'] ?? '', ENT_QUOTES) ?>')" title="Edit" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                    <!-- Link ke Pengembalian -->
                                                    <a href="/hakikah/admin/pengembalian" class="btn btn-sm btn-secondary" style="padding: 3px 8px; font-size: 11px;">
                                                        <i class="fas fa-undo"></i> Kembali
                                                    </a>
                                                    
                                                <?php elseif ($transaksi['status'] === 'cancelled'): ?>
                                                    <!-- Hanya bisa delete untuk cancelled -->
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                <?php elseif ($transaksi['status'] === 'completed'): ?>
                                                    <!-- Untuk completed hanya delete -->
                                                    <button class="btn btn-sm btn-outline-secondary" disabled style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-check"></i> Selesai
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    
                                                <?php else: ?>
                                                    <!-- Default: Edit & Delete untuk status lainnya -->
                                                    <button class="btn btn-sm btn-info" onclick="openEditModal(<?= $transaksi['id_transaksi'] ?>, <?= $transaksi['id_pelanggan'] ?>, <?= $transaksi['id_alat'] ?>, <?= $transaksi['jumlah_alat'] ?>, '<?= $transaksi['tgl_sewa'] ?>', '<?= $transaksi['tgl_kembali'] ?>', '<?= $transaksi['metode_pengambilan'] ?? 'pickup' ?>', '<?= htmlspecialchars($transaksi['alamat_pengiriman'] ?? '', ENT_QUOTES) ?>')" title="Edit" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="btn btn-sm btn-danger" onclick="deleteTransaksi(<?= $transaksi['id_transaksi'] ?>)" title="Hapus" style="padding: 3px 6px; font-size: 11px;">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                    <small style="color: var(--secondary-color);">Status: <?= $transaksi['status'] ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<style>
/* Style khusus untuk tabel transaksi */
.dashboard {
    display: flex;
    min-height: 100vh;
}

.sidebar {
    width: 280px !important;
    min-width: 280px;
    flex-shrink: 0;
    background-color: var(--white-color);
    box-shadow: var(--shadow);
    padding: 2rem 0;
}

.main-content {
    flex: 1;
    padding: 3rem 2rem;
    background: linear-gradient(135deg, #f8f9fc, #ffffff);
    min-height: 100vh;
    overflow-x: auto;
}

#transaksiTable {
    min-width: 1400px;
}

#transaksiTable th:last-child,
#transaksiTable td:last-child {
    width: 280px;
    min-width: 280px;
    white-space: nowrap;
}

#transaksiTable .btn {
    margin: 1px;
    font-size: 11px;
    padding: 2px 6px;
}

.table-responsive {
    overflow-x: auto;
    width: 100%;
}

.card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(0, 0, 0, 0.05);
    margin-bottom: 2rem;
}

.card-header {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--border-color);
    background: rgba(13, 110, 253, 0.02);
}

.card-body {
    padding: 2rem;
}
</style>

<!-- Modal Tambah Transaksi -->
<div id="tambahTransaksiModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 50%; max-width: 600px; position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h3 style="margin: 0;">Tambah Transaksi</h3>
            <button type="button" onclick="document.getElementById('tambahTransaksiModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/hakikah/admin/transaksi/tambah">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Pelanggan</label>
                <select name="id_pelanggan" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="">Pilih Pelanggan</option>
                    <?php
                    if (isset($pelangganList) && !empty($pelangganList)) {
                        foreach ($pelangganList as $pelanggan):
                        ?>
                            <option value="<?= $pelanggan['id_pelanggan'] ?>">
                                <?= htmlspecialchars($pelanggan['nama']) ?> - <?= htmlspecialchars($pelanggan['email']) ?>
                            </option>
                        <?php endforeach;
                    } else {
                        echo '<option value="">Tidak ada pelanggan tersedia</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Alat</label>
                <select name="id_alat" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="">Pilih Alat</option>
                    <?php
                    if (isset($alatList) && !empty($alatList)) {
                        foreach ($alatList as $alat):
                        ?>
                            <option value="<?= $alat['id_alat'] ?>">
                                <?= htmlspecialchars($alat['nama_alat']) ?> - Stok: <?= $alat['stok'] ?> - Rp <?= number_format($alat['harga_sewa'] ?? $alat['harga'] ?? 0, 0, ',', '.') ?>
                            </option>
                        <?php endforeach;
                    } else {
                        echo '<option value="">Tidak ada alat tersedia</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Jumlah Alat</label>
                <input type="number" name="jumlah_alat" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" min="1" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tanggal Sewa</label>
                <input type="date" name="tgl_sewa" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Metode Pengambilan</label>
                <select name="metode_pengambilan" id="adminMetodePengambilan" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="pickup">Pickup di Toko</option>
                    <option value="delivery_profile">Delivery ke Alamat Profil</option>
                    <option value="delivery_custom">Delivery ke Alamat Custom</option>
                </select>
            </div>
            <div style="margin-bottom: 15px; display: none;" id="adminAlamatCustomGroup">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Alamat Pengiriman Custom</label>
                <textarea name="alamat_pengiriman" id="adminAlamatPengiriman" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('tambahTransaksiModal').style.display='none'" class="btn btn-outline-primary btn-cancel">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Tambah</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div id="editTransaksiModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 8px; width: 50%; max-width: 600px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
            <h3 style="margin: 0;">Edit Transaksi</h3>
            <button type="button" onclick="document.getElementById('editTransaksiModal').style.display='none'" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/hakikah/admin/transaksi/edit">
            <input type="hidden" name="id" id="editTransaksiId">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Pelanggan</label>
                <select name="id_pelanggan" id="editPelanggan" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="">Pilih Pelanggan</option>
                    <?php
                    if (isset($pelangganList) && !empty($pelangganList)) {
                        foreach ($pelangganList as $pelanggan):
                        ?>
                            <option value="<?= $pelanggan['id_pelanggan'] ?>">
                                <?= htmlspecialchars($pelanggan['nama']) ?> - <?= htmlspecialchars($pelanggan['email']) ?>
                            </option>
                        <?php endforeach;
                    } else {
                        echo '<option value="">Tidak ada pelanggan tersedia</option>';
                    }
                    ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Alat</label>
                <select name="id_alat" id="editAlat" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="">Pilih Alat</option>
                    <?php
                    if (class_exists('Alat')) {
                        $alatModel = new Alat();
                        $alatList = $alatModel->getAll(); // Changed to getAll() to include all equipment for editing
                        foreach ($alatList as $alat):
                        ?>
                            <option value="<?= $alat['id_alat'] ?>">
                                <?= htmlspecialchars($alat['nama_alat']) ?> - Stok: <?= $alat['stok'] ?> - Rp <?= number_format($alat['harga_sewa'] ?? $alat['harga'] ?? 0, 0, ',', '.') ?>
                            </option>
                        <?php endforeach;
                    }
                    ?>
                </select>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Jumlah Alat</label>
                <input type="number" name="jumlah_alat" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" min="1" required id="editJumlahAlat">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tanggal Sewa</label>
                <input type="date" name="tgl_sewa" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required id="editTglSewa">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Tanggal Kembali</label>
                <input type="date" name="tgl_kembali" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required id="editTglKembali">
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Metode Pengambilan</label>
                <select name="metode_pengambilan" id="editMetodePengambilan" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" required>
                    <option value="pickup">Pickup di Toko</option>
                    <option value="delivery_profile">Delivery ke Alamat Profil</option>
                    <option value="delivery_custom">Delivery ke Alamat Custom</option>
                </select>
            </div>
            <div style="margin-bottom: 15px; display: none;" id="editAlamatCustomGroup">
                <label style="display: block; margin-bottom: 5px; font-weight: bold;">Alamat Pengiriman Custom</label>
                <textarea name="alamat_pengiriman" id="editAlamatPengiriman" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" rows="3" placeholder="Masukkan alamat lengkap untuk pengiriman..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" onclick="document.getElementById('editTransaksiModal').style.display='none'" class="btn btn-outline-primary btn-cancel">Batal</button>
                <button type="submit" class="btn btn-primary btn-submit">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
// Filter by status
document.getElementById('filterStatus').addEventListener('change', function() {
    const selectedStatus = this.value;
    const table = document.getElementById('transaksiTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const status = row.getAttribute('data-status');
        if (selectedStatus === '' || status === selectedStatus) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Search functionality
document.getElementById('searchTransaksi').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('transaksiTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
    
    for (let row of rows) {
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Confirm delete/reject actions
document.addEventListener('click', function(e) {
    if (e.target.hasAttribute('data-confirm')) {
        if (!confirm(e.target.getAttribute('data-confirm'))) {
            e.preventDefault();
            return false;
        }
    }
});

// Open edit modal
function openEditModal(id, idPelanggan, idAlat, jumlah, tglSewa, tglKembali, metodePengambilan, alamatPengiriman) {
    document.getElementById('editTransaksiId').value = id;
    document.getElementById('editPelanggan').value = idPelanggan;
    document.getElementById('editAlat').value = idAlat;
    document.getElementById('editJumlahAlat').value = jumlah;
    document.getElementById('editTglSewa').value = tglSewa;
    document.getElementById('editTglKembali').value = tglKembali;
    document.getElementById('editMetodePengambilan').value = metodePengambilan || 'pickup';
    document.getElementById('editAlamatPengiriman').value = alamatPengiriman || '';
    
    // Update alamat custom group visibility
    updateEditAlamatCustomVisibility();
    
    document.getElementById('editTransaksiModal').style.display = 'block';
}

// Delete transaksi
function deleteTransaksi(id) {
    if (confirm('Yakin ingin menghapus transaksi ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/hakikah/admin/transaksi/delete';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = id;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}

// Handle delivery method changes
function updateAdminAlamatCustomVisibility() {
    const metodePengambilan = document.getElementById('adminMetodePengambilan').value;
    const alamatCustomGroup = document.getElementById('adminAlamatCustomGroup');
    const alamatInput = document.getElementById('adminAlamatPengiriman');
    
    if (metodePengambilan === 'delivery_custom') {
        alamatCustomGroup.style.display = 'block';
        alamatInput.required = true;
    } else {
        alamatCustomGroup.style.display = 'none';
        alamatInput.required = false;
        alamatInput.value = '';
    }
}

function updateEditAlamatCustomVisibility() {
    const metodePengambilan = document.getElementById('editMetodePengambilan').value;
    const alamatCustomGroup = document.getElementById('editAlamatCustomGroup');
    const alamatInput = document.getElementById('editAlamatPengiriman');
    
    if (metodePengambilan === 'delivery_custom') {
        alamatCustomGroup.style.display = 'block';
        alamatInput.required = true;
    } else {
        alamatCustomGroup.style.display = 'none';
        alamatInput.required = false;
        if (metodePengambilan !== 'delivery_custom') {
            alamatInput.value = '';
        }
    }
}

// Add event listeners
document.addEventListener('DOMContentLoaded', function() {
    const adminMetodePengambilan = document.getElementById('adminMetodePengambilan');
    const editMetodePengambilan = document.getElementById('editMetodePengambilan');
    
    if (adminMetodePengambilan) {
        adminMetodePengambilan.addEventListener('change', updateAdminAlamatCustomVisibility);
    }
    
    if (editMetodePengambilan) {
        editMetodePengambilan.addEventListener('change', updateEditAlamatCustomVisibility);
    }
});

// Close modals when clicking outside
window.onclick = function(event) {
    const tambahModal = document.getElementById('tambahTransaksiModal');
    const editModal = document.getElementById('editTransaksiModal');
    
    if (event.target === tambahModal) {
        tambahModal.style.display = 'none';
    }
    if (event.target === editModal) {
        editModal.style.display = 'none';
    }
}
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>