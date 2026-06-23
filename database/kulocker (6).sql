-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 22 Jun 2026 pada 08.47
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kulocker`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `akses_log`
--

CREATE TABLE `akses_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pemesanan_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `waktu_akses` timestamp NOT NULL DEFAULT current_timestamp(),
  `jenis` enum('buka','tutup') NOT NULL,
  `status` enum('berhasil','gagal') NOT NULL,
  `keterangan` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluhan`
--

CREATE TABLE `keluhan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `locker_id` int(10) UNSIGNED DEFAULT NULL,
  `pemesanan_id` int(10) UNSIGNED DEFAULT NULL,
  `judul` varchar(150) NOT NULL,
  `deskripsi` text NOT NULL,
  `status` enum('open','proses','selesai') NOT NULL DEFAULT 'open',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `keluhan_response`
--

CREATE TABLE `keluhan_response` (
  `id` int(10) UNSIGNED NOT NULL,
  `keluhan_id` int(10) UNSIGNED NOT NULL,
  `admin_id` int(10) UNSIGNED NOT NULL,
  `pesan` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `lockers`
--

CREATE TABLE `lockers` (
  `id` int(10) UNSIGNED NOT NULL,
  `kode_loker` varchar(10) NOT NULL COMMENT 'Contoh: A-01',
  `lokasi` varchar(100) NOT NULL COMMENT 'Contoh: Gedung A Lt.2',
  `ukuran` enum('S','M','L') NOT NULL,
  `status` enum('tersedia','terpakai','rusak') NOT NULL DEFAULT 'tersedia',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `lockers`
--

INSERT INTO `lockers` (`id`, `kode_loker`, `lokasi`, `ukuran`, `status`, `created_at`) VALUES
(1, 'A-01', 'Gedung A Lt.1', 'S', 'terpakai', '2026-06-05 22:52:35'),
(2, 'A-02', 'Gedung A Lt.1', 'S', 'terpakai', '2026-06-05 22:52:35'),
(3, 'A-03', 'Gedung A Lt.1', 'S', 'terpakai', '2026-06-05 22:52:35'),
(4, 'B-01', 'Gedung B Lt.2', 'S', 'tersedia', '2026-06-05 22:52:35'),
(5, 'B-02', 'Gedung B Lt.2', 'S', 'terpakai', '2026-06-05 22:52:35'),
(6, 'B-03', 'Gedung B Lt.2', 'S', 'terpakai', '2026-06-05 22:52:35');

-- --------------------------------------------------------

--
-- Struktur dari tabel `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `judul` varchar(150) NOT NULL,
  `pesan` text NOT NULL,
  `jenis` enum('info','peringatan','pengingat') NOT NULL DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(10) UNSIGNED NOT NULL,
  `pemesanan_id` int(10) UNSIGNED NOT NULL,
  `jumlah` decimal(10,2) NOT NULL,
  `metode` enum('transfer','qris','tunai') NOT NULL,
  `status` enum('pending','lunas','gagal') NOT NULL DEFAULT 'pending',
  `bukti` varchar(255) DEFAULT NULL COMMENT 'Path file bukti transfer',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pemesanan_id`, `jumlah`, `metode`, `status`, `bukti`, `created_at`) VALUES
(40, 40, 0.00, '', 'lunas', NULL, '2026-06-21 11:37:51'),
(41, 41, 0.00, '', 'lunas', NULL, '2026-06-21 12:28:18'),
(42, 42, 0.00, '', 'lunas', NULL, '2026-06-22 05:52:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pemesanan`
--

CREATE TABLE `pemesanan` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `locker_id` int(10) UNSIGNED NOT NULL,
  `tanggal_mulai` datetime NOT NULL,
  `tanggal_selesai` datetime NOT NULL,
  `status` enum('pending','aktif','selesai','dibatalkan') NOT NULL DEFAULT 'pending',
  `kode_akses` varchar(20) NOT NULL COMMENT 'PIN / token akses',
  `notifikasi_step` enum('belum','15_menit','5_menit','1_menit','terkunci') NOT NULL DEFAULT 'belum',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `pemesanan`
--

INSERT INTO `pemesanan` (`id`, `user_id`, `locker_id`, `tanggal_mulai`, `tanggal_selesai`, `status`, `kode_akses`, `notifikasi_step`, `created_at`) VALUES
(40, 10, 5, '2026-06-21 00:00:00', '2026-06-21 00:00:00', 'selesai', '541089', 'belum', '2026-06-21 11:37:51'),
(41, 10, 1, '2026-06-21 20:28:18', '2026-06-21 21:28:18', 'selesai', '504644', 'belum', '2026-06-21 12:28:18'),
(42, 10, 3, '2026-06-22 13:52:02', '2026-06-22 13:16:55', 'aktif', '371958', '15_menit', '2026-06-22 05:52:02');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengumuman`
--

CREATE TABLE `pengumuman` (
  `id` int(10) UNSIGNED NOT NULL,
  `judul` varchar(200) NOT NULL,
  `isi` text NOT NULL,
  `kategori` enum('info','peringatan','promo','maintenance') NOT NULL DEFAULT 'info',
  `is_aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `expired_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengumuman`
--

INSERT INTO `pengumuman` (`id`, `judul`, `isi`, `kategori`, `is_aktif`, `created_at`, `expired_at`) VALUES
(1, 'Selamat Datang di KuLocker!', 'Sistem loker pintar Universitas Mataram kini resmi beroperasi.', 'info', 1, '2026-06-18 09:51:38', NULL),
(2, 'Promo Juni 2026', 'Perpanjang sewa loker dan dapatkan diskon 20%!', 'promo', 1, '2026-06-18 09:51:38', NULL),
(3, 'Maintenance Gedung B', 'Loker Gedung B akan maintenance 10 Juni 2026.', 'maintenance', 1, '2026-06-18 09:51:38', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL COMMENT 'bcrypt hash',
  `role` enum('mahasiswa','admin') NOT NULL DEFAULT 'mahasiswa',
  `nim` varchar(20) DEFAULT NULL COMMENT 'Khusus mahasiswa',
  `no_hp` varchar(15) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `nim`, `no_hp`, `created_at`) VALUES
(1, 'Admin KuLocker', 'admin@kulocker.ac.id', '$2y$10$exampleHashedPasswordHere', 'admin', NULL, '081234567890', '2026-06-05 22:52:35'),
(10, 'Moh. Saqif Dendi Al Fayyed', 'dendi0006@gmail.com', '$2y$10$PFj6/cKJfPu30t2N7kWkN.kScVc9RP2OwhVtJjUZwY0nkZW9AbrWy', 'mahasiswa', 'F1D02410122', '', '2026-06-06 09:02:36'),
(11, 'Denduy', 'nodichannel@gmail.com', '$2y$10$VJ2YZJE3jzQkVNPWEnJNO.rEYSTrLYoIEeOQnHmaplWOS6MHDSY8q', 'mahasiswa', 'F1D02410001', '', '2026-06-07 01:30:19');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `akses_log`
--
ALTER TABLE `akses_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_akseslog_pemesanan` (`pemesanan_id`),
  ADD KEY `fk_akseslog_user` (`user_id`);

--
-- Indeks untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_keluhan_user` (`user_id`),
  ADD KEY `fk_keluhan_locker` (`locker_id`),
  ADD KEY `fk_keluhan_pemesanan` (`pemesanan_id`);

--
-- Indeks untuk tabel `keluhan_response`
--
ALTER TABLE `keluhan_response`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_keluhanresp_keluhan` (`keluhan_id`),
  ADD KEY `fk_keluhanresp_admin` (`admin_id`);

--
-- Indeks untuk tabel `lockers`
--
ALTER TABLE `lockers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_lockers_kode` (`kode_loker`);

--
-- Indeks untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifikasi_user` (`user_id`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pembayaran_pemesanan` (`pemesanan_id`);

--
-- Indeks untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pemesanan_user` (`user_id`),
  ADD KEY `fk_pemesanan_locker` (`locker_id`);

--
-- Indeks untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `akses_log`
--
ALTER TABLE `akses_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `keluhan_response`
--
ALTER TABLE `keluhan_response`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `lockers`
--
ALTER TABLE `lockers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT untuk tabel `pengumuman`
--
ALTER TABLE `pengumuman`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `akses_log`
--
ALTER TABLE `akses_log`
  ADD CONSTRAINT `fk_akseslog_pemesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_akseslog_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keluhan`
--
ALTER TABLE `keluhan`
  ADD CONSTRAINT `fk_keluhan_locker` FOREIGN KEY (`locker_id`) REFERENCES `lockers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_keluhan_pemesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_keluhan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `keluhan_response`
--
ALTER TABLE `keluhan_response`
  ADD CONSTRAINT `fk_keluhanresp_admin` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_keluhanresp_keluhan` FOREIGN KEY (`keluhan_id`) REFERENCES `keluhan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_pemesanan` FOREIGN KEY (`pemesanan_id`) REFERENCES `pemesanan` (`id`) ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pemesanan`
--
ALTER TABLE `pemesanan`
  ADD CONSTRAINT `fk_pemesanan_locker` FOREIGN KEY (`locker_id`) REFERENCES `lockers` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pemesanan_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
