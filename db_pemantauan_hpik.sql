-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 02, 2026 at 02:44 AM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.1.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pemantauan_hpik`
--

-- --------------------------------------------------------

--
-- Table structure for table `evaluasis`
--

CREATE TABLE `evaluasis` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perencanaan_id` bigint(20) UNSIGNED NOT NULL,
  `kesimpulan` enum('Bebas HPIK','Waspada','Positif HPIK') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status_warna` enum('hijau','kuning','merah') COLLATE utf8mb4_unicode_ci NOT NULL,
  `rekomendasi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `catatan_evaluasi` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `evaluator` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_evaluasi` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jenis_penyakits`
--

CREATE TABLE `jenis_penyakits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `organisme_penyebab` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `singkatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `golongan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jenis_penyakits`
--

INSERT INTO `jenis_penyakits` (`id`, `nama`, `organisme_penyebab`, `singkatan`, `golongan`, `keterangan`, `aktif`, `created_at`, `updated_at`) VALUES
(2, 'Infection with ictalurid herpesvirus-1', 'Ictalurid herpesvirus-1', NULL, 'Virus', NULL, 1, '2026-02-24 20:12:56', '2026-02-24 20:12:56'),
(3, 'Infection with ictalurid herpesvirus-2', 'Ictalurid herpesvirus-2', NULL, 'Virus', NULL, 1, '2026-02-24 20:12:56', '2026-02-24 20:12:56'),
(4, 'Infection with spring viraemia of carp virus', 'Spring viraemia of carp virus (SVCV)', NULL, 'Virus', NULL, 1, '2026-02-24 20:12:56', '2026-02-24 20:12:56');

-- --------------------------------------------------------

--
-- Table structure for table `laboratoriums`
--

CREATE TABLE `laboratoriums` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pelaksanaan_id` bigint(20) UNSIGNED NOT NULL,
  `kode_sampel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_uji` enum('PCR Konvensional','qPCR','Kultur','Histopatologi','Lainnya') COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_hpik_diuji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hasil_uji` enum('Positif','Negatif','Inkonklusif') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Inkonklusif',
  `hasil_parasit` enum('+','-','NT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NT',
  `hasil_bakteri` enum('+','-','NT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NT',
  `hasil_virus` enum('+','-','NT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NT',
  `hasil_jamur` enum('+','-','NT') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NT',
  `prevalensi` decimal(5,2) DEFAULT NULL,
  `insidensi` decimal(5,2) DEFAULT NULL,
  `jumlah_ikan_terinfeksi` int(11) DEFAULT NULL,
  `jumlah_sampel_diperiksa` int(11) DEFAULT NULL,
  `jumlah_kolam_uji` int(11) DEFAULT NULL,
  `periode_pengamatan` int(11) DEFAULT NULL,
  `diagnosis_akhir` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `lab_penguji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tanggal_uji` date NOT NULL,
  `tanggal_hasil` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media_pembawas`
--

CREATE TABLE `media_pembawas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nama_latin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `keterangan` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `media_pembawas`
--

INSERT INTO `media_pembawas` (`id`, `nama`, `nama_latin`, `keterangan`, `aktif`, `created_at`, `updated_at`) VALUES
(9, 'Bulldog (Marcusenius macrolepidotus)', NULL, NULL, 1, '2026-02-24 20:02:45', '2026-02-24 20:02:45'),
(10, 'Catla (Catla catla)', NULL, NULL, 1, '2026-02-24 20:02:45', '2026-02-24 20:02:45'),
(11, 'Ceylon snakehead (Channa orientalis)', NULL, NULL, 1, '2026-02-24 20:02:45', '2026-02-24 20:02:45'),
(12, 'Bony bream (Nematalosa erebi)', NULL, NULL, 1, '2026-02-24 20:02:45', '2026-02-24 20:02:45');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(7, '2014_10_12_000000_create_users_table', 1),
(8, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(9, '2019_08_19_000000_create_failed_jobs_table', 1),
(10, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(11, '2026_02_12_074842_create_perencanaans_table', 1),
(12, '2026_02_12_083603_create_pelaksanaans_table', 1),
(13, '2014_10_12_100000_create_password_resets_table', 2),
(14, '2026_02_19_100000_add_role_to_users_table', 2),
(15, '2026_02_19_110000_create_laboratoriums_table', 3),
(16, '2026_02_19_110001_create_evaluasis_table', 3),
(17, '2026_02_23_093000_rename_upt_role_to_bkhit', 4),
(18, '2026_02_23_110000_add_missing_fields_to_pelaksanaans', 5),
(19, '2026_02_23_110001_add_missing_fields_to_laboratoriums', 5),
(20, '2026_02_23_120000_add_user_id_to_perencanaans', 6),
(21, '2026_02_23_130000_add_kalkulasi_fields_to_laboratoriums', 7),
(22, '2026_02_24_000001_add_pengambil_sampel_to_pelaksanaans', 8),
(23, '2026_02_24_030000_create_media_pembawas_table', 9),
(24, '2026_02_24_030001_create_jenis_penyakits_table', 10),
(25, '2026_02_25_024855_add_organisme_penyebab_to_jenis_penyakits', 11),
(26, '2026_02_25_034449_add_parent_id_to_users_table', 12),
(27, '2026_02_27_025941_add_plans_details_to_perencanaans_table', 13);

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pelaksanaans`
--

CREATE TABLE `pelaksanaans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `perencanaan_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal_pemantauan` date DEFAULT NULL,
  `jenis_ikan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nama_latin` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `panjang_cm` decimal(5,2) DEFAULT NULL,
  `berat_gram` decimal(8,2) DEFAULT NULL,
  `asal_benih_induk` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `padat_tebar` int(11) DEFAULT NULL,
  `gejala_klinis` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jumlah_kematian` int(11) DEFAULT 0,
  `pengambil_sampel` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`pengambil_sampel`)),
  `lokasi_pengambilan_sampel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jumlah_sampel` int(11) NOT NULL,
  `metode_pengambilan_sampel` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `perencanaans`
--

CREATE TABLE `perencanaans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `provinsi` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `kab_kota` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_mp` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `jenis_hpik` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `kemampuan_uji_upt` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metode_pengujian` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `lab_uji` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `target_uji` int(11) NOT NULL,
  `tw1` int(11) NOT NULL DEFAULT 0,
  `tw2` int(11) NOT NULL DEFAULT 0,
  `tw3` int(11) NOT NULL DEFAULT 0,
  `tw4` int(11) NOT NULL DEFAULT 0,
  `total_pengujian` int(11) NOT NULL,
  `rencana_lokasi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rencana_jumlah_sampel` int(11) NOT NULL DEFAULT 0,
  `rencana_metode_sampling` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('draft','waiting','approved') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('bkhit','bbkhit','pusat') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'bkhit',
  `upt_asal` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `parent_id`, `name`, `email`, `role`, `upt_asal`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Admin Pusat', 'pusat@fds.go.id', 'pusat', 'Deputi Karantina Ikan', NULL, '$2y$12$WfFyCfcKs3mmHXxUkzzPKufAZURb4nG6sQdaMbDwXdGqEzqMluADi', 'UGOWIT02qPFb2Fg9j0sT4KfIIdt2g0e4VS2JO7L6czuNl8BpPYwn3cG56tQ6', '2026-02-18 20:32:46', '2026-02-22 20:00:55'),
(5, NULL, 'BBKHIT Sumatera Utara', 'bbkhit_sumatera_utara@fds.go.id', 'bbkhit', 'BBKHIT Sumatera Utara', NULL, '$2y$12$OaGKX.SMdVNdSmT5IrcfW.7MuEyss/ywXc77I4t5PPZvT7MHki4LC', NULL, '2026-02-24 20:42:31', '2026-02-24 20:42:31'),
(6, 5, 'BKHIT Nangroe Aceh Darussalam', 'bkhit_nangroe_aceh_darussalam@fds.go.id', 'bkhit', 'BKHIT Nangroe Aceh Darussalam', NULL, '$2y$12$gz7H5tSE3B/OLIBKwFUJK.DyVZxWcvGPQbIBct4qoO7wMSsYEecDu', NULL, '2026-02-24 20:42:32', '2026-02-24 20:55:28'),
(7, 5, 'BKHIT Kepulauan Riau', 'bkhit_kepulauan_riau@fds.go.id', 'bkhit', 'BKHIT Kepulauan Riau', NULL, '$2y$12$r1Kt1AZyJHC/AubruzmSzubl.Pn0U7OyInP7/qB4R/sIrRFjVCKdy', NULL, '2026-02-24 20:42:32', '2026-02-24 20:55:17'),
(8, 5, 'BKHIT Sumatera Barat', 'bkhit_sumatera_barat@fds.go.id', 'bkhit', 'BKHIT Sumatera Barat', NULL, '$2y$12$3jJcLQDImk/k6aITc3dSTe0WxzLpi6KICbDPloqdhq6dwH2iFvU2q', NULL, '2026-02-24 20:42:33', '2026-02-24 20:55:44'),
(9, 5, 'BKHIT Riau', 'bkhit_riau@fds.go.id', 'bkhit', 'BKHIT Riau', NULL, '$2y$12$tA2tJLRYJTBE5UhnGYQ6tusrYNz8qJjmwkRM0FHk6ihrthXT5pke2', NULL, '2026-02-24 20:42:33', '2026-02-24 20:55:50'),
(10, 5, 'BKHIT Jambi', 'bkhit_jambi@fds.go.id', 'bkhit', 'BKHIT Jambi', NULL, '$2y$12$dVrmj/nsnW.HBJy7vqgDlucVebXho8d7KuHr23r0R08WT92ypdvGS', NULL, '2026-02-24 20:42:34', '2026-02-24 20:55:05'),
(11, 5, 'BKHIT Bengkulu', 'bkhit_bengkulu@fds.go.id', 'bkhit', 'BKHIT Bengkulu', NULL, '$2y$12$P03oHGvT8xxVIj37PfBH/et.pP71i8XqNM/8c9BiNB8RkqMW4AYNC', NULL, '2026-02-24 20:42:34', '2026-02-24 20:54:52'),
(12, 5, 'BKHIT Kepulauan Bangka Belitung', 'bkhit_kepulauan_bangka_belitung@fds.go.id', 'bkhit', 'BKHIT Kepulauan Bangka Belitung', NULL, '$2y$12$JeKWFKiV5tQgsU1HyP8P9.JtMKrZZOx0pYJ/0/m2pbUgOWCFRVg/C', NULL, '2026-02-24 20:42:35', '2026-02-24 20:55:11'),
(13, 5, 'BKHIT Sumatera Selatan', 'bkhit_sumatera_selatan@fds.go.id', 'bkhit', 'BKHIT Sumatera Selatan', NULL, '$2y$12$xUj9HAW7xK1Rc54Ws42GW.8bQpN9bVuZrg98Uy626HjHzJpnI1c9i', NULL, '2026-02-24 20:42:35', '2026-02-24 20:55:36'),
(14, 5, 'BKHIT Lampung', 'bkhit_lampung@fds.go.id', 'bkhit', 'BKHIT Lampung', NULL, '$2y$12$RHBR/ms26uzs9aJ9.q.MjeFyAnYtxN78caYRcXDk3fNZFvHDa2re6', NULL, '2026-02-24 20:42:36', '2026-02-24 20:55:23'),
(15, NULL, 'Admin Pusat Test', 'admin@pusat.go.id', 'bkhit', NULL, NULL, '$2y$12$uWT1y8.qNcYNYfoGFowl1.yC967OOMlGUOu0V44G3qie.zAh2C.Ka', NULL, '2026-02-26 20:41:17', '2026-02-26 20:41:17');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `evaluasis`
--
ALTER TABLE `evaluasis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluasis_perencanaan_id_foreign` (`perencanaan_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jenis_penyakits`
--
ALTER TABLE `jenis_penyakits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `laboratoriums`
--
ALTER TABLE `laboratoriums`
  ADD PRIMARY KEY (`id`),
  ADD KEY `laboratoriums_pelaksanaan_id_foreign` (`pelaksanaan_id`);

--
-- Indexes for table `media_pembawas`
--
ALTER TABLE `media_pembawas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pelaksanaans`
--
ALTER TABLE `pelaksanaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pelaksanaans_perencanaan_id_foreign` (`perencanaan_id`);

--
-- Indexes for table `perencanaans`
--
ALTER TABLE `perencanaans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `perencanaans_user_id_foreign` (`user_id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_parent_id_foreign` (`parent_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `evaluasis`
--
ALTER TABLE `evaluasis`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jenis_penyakits`
--
ALTER TABLE `jenis_penyakits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `laboratoriums`
--
ALTER TABLE `laboratoriums`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `media_pembawas`
--
ALTER TABLE `media_pembawas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pelaksanaans`
--
ALTER TABLE `pelaksanaans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `perencanaans`
--
ALTER TABLE `perencanaans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `evaluasis`
--
ALTER TABLE `evaluasis`
  ADD CONSTRAINT `evaluasis_perencanaan_id_foreign` FOREIGN KEY (`perencanaan_id`) REFERENCES `perencanaans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `laboratoriums`
--
ALTER TABLE `laboratoriums`
  ADD CONSTRAINT `laboratoriums_pelaksanaan_id_foreign` FOREIGN KEY (`pelaksanaan_id`) REFERENCES `pelaksanaans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pelaksanaans`
--
ALTER TABLE `pelaksanaans`
  ADD CONSTRAINT `pelaksanaans_perencanaan_id_foreign` FOREIGN KEY (`perencanaan_id`) REFERENCES `perencanaans` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `perencanaans`
--
ALTER TABLE `perencanaans`
  ADD CONSTRAINT `perencanaans_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
