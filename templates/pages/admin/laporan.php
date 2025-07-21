<?php 
$title = 'Laporan - Admin Dashboard';
include __DIR__ . '/../../layouts/header.php';

// Get additional statistics
$db = Database::getInstance();
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-t');

// Get detailed statistics
$kategoriStats = $db->fetchAll("
    SELECT 
        a.kategori,
        COUNT(t.id_transaksi) as total_transaksi,
        SUM(t.total_harga) as total_pendapatan,
        SUM(t.jumlah_alat) as total_alat_disewa
    FROM transaksi t
    JOIN alat a ON t.id_alat = a.id_alat
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY a.kategori
    ORDER BY total_pendapatan DESC
", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

$alatPopuler = $db->fetchAll("
    SELECT 
        a.nama_alat,
        a.kategori,
        COUNT(t.id_transaksi) as jumlah_disewa,
        SUM(t.jumlah_alat) as total_unit,
        SUM(t.total_harga) as total_pendapatan
    FROM transaksi t
    JOIN alat a ON t.id_alat = a.id_alat
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY t.id_alat
    ORDER BY jumlah_disewa DESC
    LIMIT 10
", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

$pelangganAktif = $db->fetchAll("
    SELECT 
        p.nama,
        p.email,
        COUNT(t.id_transaksi) as total_transaksi,
        SUM(t.total_harga) as total_belanja
    FROM transaksi t
    JOIN pelanggan p ON t.id_pelanggan = p.id_pelanggan
    WHERE t.created_at BETWEEN ? AND ?
    GROUP BY t.id_pelanggan
    ORDER BY total_belanja DESC
    LIMIT 10
", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

$statusStats = $db->fetchAll("
    SELECT 
        status,
        COUNT(*) as jumlah,
        SUM(total_harga) as total_nilai
    FROM transaksi
    WHERE created_at BETWEEN ? AND ?
    GROUP BY status
", [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
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
            <li><a href="/hakikah/admin/transaksi">Transaksi</a></li>
            <li><a href="/hakikah/admin/pembayaran">Pembayaran</a></li>
            <li><a href="/hakikah/admin/pengembalian">Pengembalian</a></li>
            <li><a href="/hakikah/admin/laporan" class="active">Laporan</a></li>
        </ul>
    </aside>

    <main class="main-content">
        <div style="margin-bottom: 2rem;">
            <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                <h1 style="margin: 0;">Laporan Transaksi</h1>
                <div style="display: flex; gap: 1rem;">
                    <button class="btn btn-outline-primary" onclick="window.print()">
                        📄 Cetak Laporan
                    </button>
                    <a href="/hakikah/admin/laporan/export?format=csv&start_date=<?= urlencode($startDate) ?>&end_date=<?= urlencode($endDate) ?>" 
                       class="btn btn-primary">
                        📊 Export CSV
                    </a>
                </div>
            </div>
            <p style="color: var(--secondary-color);">Analisis transaksi dan pendapatan berdasarkan periode</p>
        </div>

        <!-- Filter Periode -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Filter Periode</h3>
            </div>
            <div class="card-body">
                <form method="GET" action="/hakikah/admin/laporan" style="display: flex; gap: 1rem; align-items: end;">
                    <div class="form-group" style="margin: 0;">
                        <label for="start_date" class="form-label">Tanggal Mulai</label>
                        <input type="date" id="start_date" name="start_date" class="form-control" 
                               value="<?= htmlspecialchars($startDate) ?>">
                    </div>
                    <div class="form-group" style="margin: 0;">
                        <label for="end_date" class="form-label">Tanggal Akhir</label>
                        <input type="date" id="end_date" name="end_date" class="form-control" 
                               value="<?= htmlspecialchars($endDate) ?>">
                    </div>
                    <button type="submit" class="btn btn-primary btn-submit">Filter</button>
                    <button type="button" class="btn btn-outline-primary" onclick="setQuickFilter('today')">Hari Ini</button>
                    <button type="button" class="btn btn-outline-primary" onclick="setQuickFilter('month')">Bulan Ini</button>
                    <button type="button" class="btn btn-outline-primary" onclick="setQuickFilter('year')">Tahun Ini</button>
                    
                    <!-- Tombol Cetak (sesuai UML requirement) -->
                    <a href="/hakikah/admin/laporan/cetak?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" 
                       class="btn btn-success" target="_blank">🖨️ Cetak Laporan</a>
                    <a href="/hakikah/admin/laporan/export?start_date=<?= $startDate ?>&end_date=<?= $endDate ?>" 
                       class="btn btn-info">📊 Export CSV</a>
                </form>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div class="stats-card">
                <div class="stats-number" style="color: var(--primary-color);"><?= $laporan['total_transaksi'] ?? 0 ?></div>
                <div class="stats-label">Total Transaksi</div>
                <div class="stats-period"><?= date('d/m/Y', strtotime($startDate)) ?> - <?= date('d/m/Y', strtotime($endDate)) ?></div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--success-color);">Rp <?= number_format($laporan['total_pendapatan'] ?? 0, 0, ',', '.') ?></div>
                <div class="stats-label">Total Pendapatan</div>
                <div class="stats-period">Periode saat ini</div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--warning-color);"><?= $laporan['total_pelanggan_aktif'] ?? 0 ?></div>
                <div class="stats-label">Pelanggan Aktif</div>
                <div class="stats-period">Unik dalam periode</div>
            </div>
            <div class="stats-card">
                <div class="stats-number" style="color: var(--info-color);">
                    <?= $laporan['total_transaksi'] > 0 ? number_format($laporan['total_pendapatan'] / $laporan['total_transaksi'], 0, ',', '.') : 0 ?>
                </div>
                <div class="stats-label">Rata-rata Transaksi</div>
                <div class="stats-period">Per transaksi</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 2rem;">
            <!-- Statistik per Kategori -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Pendapatan per Kategori</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($kategoriStats)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Tidak ada data dalam periode ini
                        </p>
                    <?php else: ?>
                        <?php foreach ($kategoriStats as $kategori): ?>
                            <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                                    <span style="font-weight: 600;"><?= htmlspecialchars($kategori['kategori']) ?></span>
                                    <span style="color: var(--primary-color); font-weight: 600;">
                                        Rp <?= number_format($kategori['total_pendapatan'], 0, ',', '.') ?>
                                    </span>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.9rem; color: var(--secondary-color);">
                                    <div><?= $kategori['total_transaksi'] ?> transaksi</div>
                                    <div><?= $kategori['total_alat_disewa'] ?> unit disewa</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Status Transaksi -->
            <div class="card">
                <div class="card-header">
                    <h3 style="margin: 0;">Status Transaksi</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($statusStats)): ?>
                        <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                            Tidak ada data dalam periode ini
                        </p>
                    <?php else: ?>
                        <?php 
                        $statusLabels = [
                            'pending' => ['label' => 'Menunggu Pembayaran', 'color' => 'warning'],
                            'approved' => ['label' => 'Disetujui', 'color' => 'primary'],
                            'ongoing' => ['label' => 'Sedang Berlangsung', 'color' => 'info'],
                            'completed' => ['label' => 'Selesai', 'color' => 'success'],
                            'cancelled' => ['label' => 'Dibatalkan', 'color' => 'danger']
                        ];
                        ?>
                        <?php foreach ($statusStats as $status): ?>
                            <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color);">
                                <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                                    <span class="badge badge-<?= $statusLabels[$status['status']]['color'] ?? 'secondary' ?>">
                                        <?= $statusLabels[$status['status']]['label'] ?? ucfirst($status['status']) ?>
                                    </span>
                                    <span style="font-weight: 600;"><?= $status['jumlah'] ?> transaksi</span>
                                </div>
                                <div style="font-size: 0.9rem; color: var(--secondary-color);">
                                    Total nilai: Rp <?= number_format($status['total_nilai'], 0, ',', '.') ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Alat Paling Populer -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Top 10 Alat Paling Populer</h3>
            </div>
            <div class="card-body">
                <?php if (empty($alatPopuler)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Tidak ada data dalam periode ini
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama Alat</th>
                                    <th>Kategori</th>
                                    <th>Disewa</th>
                                    <th>Total Unit</th>
                                    <th>Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alatPopuler as $index => $alat): ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: bold; color: var(--primary-color);">#<?= $index + 1 ?></span>
                                        </td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($alat['nama_alat']) ?></td>
                                        <td>
                                            <span class="badge badge-secondary"><?= htmlspecialchars($alat['kategori']) ?></span>
                                        </td>
                                        <td><?= $alat['jumlah_disewa'] ?>x</td>
                                        <td><?= $alat['total_unit'] ?> unit</td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($alat['total_pendapatan'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Top Pelanggan -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3 style="margin: 0;">Top 10 Pelanggan Terbaik</h3>
            </div>
            <div class="card-body">
                <?php if (empty($pelangganAktif)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Tidak ada data dalam periode ini
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Rank</th>
                                    <th>Nama Pelanggan</th>
                                    <th>Email</th>
                                    <th>Transaksi</th>
                                    <th>Total Belanja</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pelangganAktif as $index => $pelanggan): ?>
                                    <tr>
                                        <td>
                                            <span style="font-weight: bold; color: var(--primary-color);">#<?= $index + 1 ?></span>
                                        </td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($pelanggan['nama']) ?></td>
                                        <td><?= htmlspecialchars($pelanggan['email']) ?></td>
                                        <td><?= $pelanggan['total_transaksi'] ?>x</td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($pelanggan['total_belanja'], 0, ',', '.') ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Detail Transaksi -->
        <div class="card">
            <div class="card-header">
                <h3 style="margin: 0;">Detail Transaksi Periode</h3>
            </div>
            <div class="card-body">
                <?php if (empty($transaksiList)): ?>
                    <p style="text-align: center; color: var(--secondary-color); margin: 2rem 0;">
                        Tidak ada transaksi dalam periode ini
                    </p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table class="table" id="transaksiTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Tanggal</th>
                                    <th>Pelanggan</th>
                                    <th>Alat</th>
                                    <th>Periode Sewa</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($transaksiList as $transaksi): ?>
                                    <tr>
                                        <td><?= $transaksi['id_transaksi'] ?></td>
                                        <td><?= date('d/m/Y', strtotime($transaksi['created_at'])) ?></td>
                                        <td style="font-weight: 600;"><?= htmlspecialchars($transaksi['nama_pelanggan']) ?></td>
                                        <td><?= htmlspecialchars($transaksi['nama_alat']) ?></td>
                                        <td>
                                            <?= date('d/m/Y', strtotime($transaksi['tgl_sewa'])) ?> - 
                                            <?= date('d/m/Y', strtotime($transaksi['tgl_kembali'])) ?>
                                        </td>
                                        <td style="color: var(--success-color); font-weight: 600;">
                                            Rp <?= number_format($transaksi['total_harga'], 0, ',', '.') ?>
                                        </td>
                                        <td>
                                            <?php
                                            $badgeClass = match($transaksi['status']) {
                                                'pending' => 'badge-warning',
                                                'approved' => 'badge-primary',
                                                'ongoing' => 'badge-info',
                                                'completed' => 'badge-success',
                                                'cancelled' => 'badge-danger',
                                                default => 'badge-secondary'
                                            };
                                            ?>
                                            <span class="badge <?= $badgeClass ?>"><?= ucfirst($transaksi['status']) ?></span>
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

<script>
// Quick filter functions
function setQuickFilter(type) {
    const startInput = document.getElementById('start_date');
    const endInput = document.getElementById('end_date');
    const today = new Date();
    
    switch(type) {
        case 'today':
            const todayStr = today.toISOString().split('T')[0];
            startInput.value = todayStr;
            endInput.value = todayStr;
            break;
        case 'month':
            const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
            const monthEnd = new Date(today.getFullYear(), today.getMonth() + 1, 0);
            startInput.value = monthStart.toISOString().split('T')[0];
            endInput.value = monthEnd.toISOString().split('T')[0];
            break;
        case 'year':
            const yearStart = new Date(today.getFullYear(), 0, 1);
            const yearEnd = new Date(today.getFullYear(), 11, 31);
            startInput.value = yearStart.toISOString().split('T')[0];
            endInput.value = yearEnd.toISOString().split('T')[0];
            break;
    }
    
    // Auto submit form
    document.querySelector('form').submit();
}

// Export to CSV function
function exportToCSV() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    // Create CSV data
    let csvContent = "data:text/csv;charset=utf-8,";
    
    // Add headers
    csvContent += "ID Transaksi,Tanggal,Pelanggan,Alat,Tanggal Sewa,Tanggal Kembali,Total Harga,Status\n";
    
    // Add data from table
    const table = document.getElementById('transaksiTable');
    if (table) {
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        for (let row of rows) {
            const cells = row.getElementsByTagName('td');
            const rowData = [];
            for (let i = 0; i < cells.length - 1; i++) { // Skip last column (status badge)
                let cellText = cells[i].textContent.trim();
                // Clean up currency formatting for CSV
                if (cellText.includes('Rp ')) {
                    cellText = cellText.replace('Rp ', '').replace(/\./g, '');
                }
                rowData.push('"' + cellText + '"');
            }
            // Get status text
            const statusBadge = cells[cells.length - 1].querySelector('.badge');
            rowData.push('"' + (statusBadge ? statusBadge.textContent.trim() : '') + '"');
            
            csvContent += rowData.join(',') + '\n';
        }
    }
    
    // Create download link
    const encodedUri = encodeURI(csvContent);
    const link = document.createElement('a');
    link.setAttribute('href', encodedUri);
    link.setAttribute('download', `laporan_transaksi_${startDate}_${endDate}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Print styles
const printStyles = `
    <style media="print">
        .sidebar, .btn, nav, footer { display: none !important; }
        .main-content { margin-left: 0 !important; }
        .card { break-inside: avoid; page-break-inside: avoid; }
        body { font-size: 12px; }
        h1, h2, h3, h4 { color: #000 !important; }
        .stats-card { border: 1px solid #ddd; }
        table { font-size: 10px; }
        @page { margin: 1cm; }
    </style>
`;
document.head.insertAdjacentHTML('beforeend', printStyles);
</script>

<?php include __DIR__ . '/../../layouts/footer.php'; ?>