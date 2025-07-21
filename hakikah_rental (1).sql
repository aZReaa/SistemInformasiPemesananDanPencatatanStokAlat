-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 21, 2025 at 03:01 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hakikah_rental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id_admin` int NOT NULL,
  `nama_admin` varchar(100) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id_admin`, `nama_admin`, `user_id`, `created_at`) VALUES
(1, 'Administrator', 1, '2025-07-20 07:16:56'),
(2, 'Administrator System', 1, '2025-07-20 09:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `alat`
--

CREATE TABLE `alat` (
  `id_alat` int NOT NULL,
  `nama_alat` varchar(100) NOT NULL,
  `kategori` varchar(50) NOT NULL,
  `stok` int NOT NULL DEFAULT '0',
  `harga` decimal(10,2) NOT NULL,
  `deskripsi` text,
  `gambar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_kategori` int DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alat`
--

INSERT INTO `alat` (`id_alat`, `nama_alat`, `kategori`, `stok`, `harga`, `deskripsi`, `gambar`, `created_at`, `updated_at`, `id_kategori`) VALUES
(1, 'Tenda 3x3', 'Pernikahan', 10, '150000.00', 'Tenda ukuran 3x3 meter untuk acara pernikahan', NULL, '2025-07-20 07:16:56', '2025-07-20 10:58:28', 3),
(2, 'Kursi Plastik', 'Pernikahan', 87, '5000.00', 'Kursi plastik warna putih', NULL, '2025-07-20 07:16:56', '2025-07-21 02:36:05', 1),
(3, 'Meja Bundar', 'Pernikahan', 18, '25000.00', 'Meja bundar untuk 8 orang', NULL, '2025-07-20 07:16:56', '2025-07-21 01:54:30', 2),
(4, 'Sound System', 'Pernikahan', 4, '200000.00', 'Sound system lengkap dengan microphone', NULL, '2025-07-20 07:16:56', '2025-07-21 01:56:26', 4),
(5, 'Tenda 2x2', 'Haqiqah', 15, '100000.00', 'Tenda ukuran 2x2 meter untuk acara haqiqah', NULL, '2025-07-20 07:16:56', '2025-07-20 10:58:28', 3),
(6, 'Kursi Anak', 'Haqiqah', 0, '3000.00', 'Kursi plastik ukuran anak-anak', NULL, '2025-07-20 07:16:56', '2025-07-20 10:31:42', 1),
(7, 'test', 'Sound System', 100, '40000.00', 'fjaos', NULL, '2025-07-20 14:08:37', '2025-07-20 14:08:37', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kategori_layanan`
--

CREATE TABLE `kategori_layanan` (
  `id_kategori` int NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `kategori_layanan`
--

INSERT INTO `kategori_layanan` (`id_kategori`, `nama_kategori`, `deskripsi`, `created_at`) VALUES
(1, 'Kursi', 'Kursi untuk acara pesta', '2025-07-20 14:47:51'),
(2, 'Meja', 'Meja untuk acara pesta', '2025-07-20 14:47:51'),
(3, 'Tenda', 'Tenda untuk acara outdoor', '2025-07-20 14:47:51'),
(4, 'Sound System', 'Peralatan audio untuk acara', '2025-07-20 14:47:51'),
(5, 'Dekorasi', 'Dekorasi untuk mempercantik acara', '2025-07-20 14:47:51'),
(6, 'Peralatan Dapur', 'Peralatan masak untuk catering', '2025-07-20 14:47:51'),
(7, 'Lighting', 'Peralatan pencahayaan untuk acara', '2025-07-20 14:47:51'),
(8, 'testing', 'fafk', '2025-07-20 14:48:11');

-- --------------------------------------------------------

--
-- Table structure for table `pelanggan`
--

CREATE TABLE `pelanggan` (
  `id_pelanggan` int NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` text NOT NULL,
  `email` varchar(100) NOT NULL,
  `no_hp` varchar(20) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pelanggan`
--

INSERT INTO `pelanggan` (`id_pelanggan`, `nama`, `alamat`, `email`, `no_hp`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Reza', 'Jl. Merdeka No. 13 Kota Palopo Provinsi Sulawesi Selatan', 'reza@gmail.com', '08123456789', 2, '2025-07-20 09:11:37', '2025-07-20 11:15:16'),
(2, 'aZReaa', 'tandipau', 'mynameisreza07@gmail.com', '013981931938', 6, '2025-07-20 14:06:58', '2025-07-20 14:06:58');

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `status_pembayaran` enum('pending','verified','rejected') DEFAULT 'pending',
  `tanggal_bayar` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `verified_by` int DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_transaksi`, `jumlah`, `bukti_transfer`, `status_pembayaran`, `tanggal_bayar`, `verified_by`, `verified_at`) VALUES
(5, 1, '150000.00', 'uploads/bukti_transfer/bukti_1753009931_687ccf0b7abf3.png', 'verified', '2025-07-20 11:12:11', 1, '2025-07-20 15:26:24'),
(6, 2, '50000.00', 'uploads/bukti_transfer/bukti_1753062877_687d9ddd9f2b1.png', 'verified', '2025-07-21 01:54:37', 1, '2025-07-21 01:54:56'),
(7, 3, '200000.00', 'uploads/bukti_transfer/bukti_1753062993_687d9e51ccadb.png', 'verified', '2025-07-21 01:56:33', 1, '2025-07-21 02:16:02'),
(8, 4, '5000.00', 'uploads/bukti_transfer/bukti_1753063617_687da0c1ad394.png', 'verified', '2025-07-21 02:06:57', 1, '2025-07-21 02:16:00');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik_usaha`
--

CREATE TABLE `pemilik_usaha` (
  `id_pemilik` int NOT NULL,
  `nama_pemilik` varchar(100) NOT NULL,
  `user_id` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pemilik_usaha`
--

INSERT INTO `pemilik_usaha` (`id_pemilik`, `nama_pemilik`, `user_id`, `created_at`) VALUES
(1, 'Ahmad Pemilik', 3, '2025-07-20 09:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `pengembalian`
--

CREATE TABLE `pengembalian` (
  `id_pengembalian` int NOT NULL,
  `id_transaksi` int NOT NULL,
  `tanggal` date NOT NULL,
  `kondisi_alat` enum('baik','rusak','hilang') DEFAULT 'baik',
  `catatan` text,
  `denda` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pengembalian`
--

INSERT INTO `pengembalian` (`id_pengembalian`, `id_transaksi`, `tanggal`, `kondisi_alat`, `catatan`, `denda`, `created_at`) VALUES
(1, 1, '2025-07-20', 'baik', '', '0.00', '2025-07-20 16:01:47'),
(2, 4, '2025-07-21', 'baik', '', '1000.00', '2025-07-21 02:14:54');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int NOT NULL,
  `id_pelanggan` int NOT NULL,
  `id_alat` int NOT NULL,
  `tgl_sewa` date NOT NULL,
  `tgl_kembali` date NOT NULL,
  `status` enum('pending','confirmed','approved','ongoing','completed','selesai','cancelled','rejected','payment_pending','aktif') DEFAULT 'pending',
  `jumlah_alat` int NOT NULL DEFAULT '1',
  `total_harga` decimal(10,2) NOT NULL,
  `metode_pengambilan` enum('pickup','delivery') DEFAULT 'pickup',
  `alamat_pengiriman` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pelanggan`, `id_alat`, `tgl_sewa`, `tgl_kembali`, `status`, `jumlah_alat`, `total_harga`, `metode_pengambilan`, `alamat_pengiriman`, `created_at`, `updated_at`) VALUES
(1, 1, 6, '2025-07-22', '2025-07-25', 'ongoing', 50, '150000.00', 'pickup', NULL, '2025-07-20 10:31:42', '2025-07-20 15:39:39'),
(2, 2, 3, '2025-07-21', '2025-07-22', 'confirmed', 2, '50000.00', 'pickup', NULL, '2025-07-21 01:54:30', '2025-07-21 01:54:37'),
(3, 2, 4, '2025-07-21', '2025-07-22', 'confirmed', 1, '200000.00', 'pickup', NULL, '2025-07-21 01:56:26', '2025-07-21 01:56:33'),
(4, 1, 2, '2025-07-21', '2025-07-22', 'ongoing', 1, '5000.00', 'delivery', 'jalan datuk sulaiman', '2025-07-21 02:06:46', '2025-07-21 02:14:08'),
(5, 2, 2, '2025-07-21', '2025-07-23', 'approved', 12, '60000.00', 'pickup', NULL, '2025-07-21 02:35:21', '2025-07-21 02:35:27');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','pelanggan','pemilik') NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', '2025-07-20 07:16:56', '2025-07-20 07:16:56'),
(2, 'pelanggan1', '$2y$10$.Ro/tpx.fROquC84wROyN.Zn3xtLYX3YlECYSTJa9amo3vjC0OlEa', 'pelanggan', '2025-07-20 09:11:37', '2025-07-20 11:18:54'),
(3, 'pemilik1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pemilik', '2025-07-20 09:11:37', '2025-07-20 09:11:37'),
(6, 'azreaaa', '$2y$10$bf8OA4JUnUURfB67uZ4qhe.K/9NeQpoogD32WMIAzpR.icEVxNL7W', 'pelanggan', '2025-07-20 14:06:58', '2025-07-20 14:06:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `alat`
--
ALTER TABLE `alat`
  ADD PRIMARY KEY (`id_alat`);

--
-- Indexes for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD PRIMARY KEY (`id_pelanggan`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `pemilik_usaha`
--
ALTER TABLE `pemilik_usaha`
  ADD PRIMARY KEY (`id_pemilik`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD PRIMARY KEY (`id_pengembalian`),
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pelanggan` (`id_pelanggan`),
  ADD KEY `id_alat` (`id_alat`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `alat`
--
ALTER TABLE `alat`
  MODIFY `id_alat` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `kategori_layanan`
--
ALTER TABLE `kategori_layanan`
  MODIFY `id_kategori` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pelanggan`
--
ALTER TABLE `pelanggan`
  MODIFY `id_pelanggan` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `pemilik_usaha`
--
ALTER TABLE `pemilik_usaha`
  MODIFY `id_pemilik` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `pengembalian`
--
ALTER TABLE `pengembalian`
  MODIFY `id_pengembalian` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pelanggan`
--
ALTER TABLE `pelanggan`
  ADD CONSTRAINT `pelanggan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE,
  ADD CONSTRAINT `pembayaran_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL;

--
-- Constraints for table `pemilik_usaha`
--
ALTER TABLE `pemilik_usaha`
  ADD CONSTRAINT `pemilik_usaha_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pengembalian`
--
ALTER TABLE `pengembalian`
  ADD CONSTRAINT `pengembalian_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id_transaksi`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pelanggan`) REFERENCES `pelanggan` (`id_pelanggan`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_alat`) REFERENCES `alat` (`id_alat`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
