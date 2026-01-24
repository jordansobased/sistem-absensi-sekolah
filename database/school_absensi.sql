-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 24, 2026 at 08:27 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `school_absensi`
--

-- --------------------------------------------------------

--
-- Table structure for table `absensi`
--

CREATE TABLE `absensi` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) DEFAULT NULL,
  `guru_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `status` enum('hadir','izin','sakit','alpha') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `absensi`
--

INSERT INTO `absensi` (`id`, `siswa_id`, `guru_id`, `tanggal`, `status`) VALUES
(12, 6, 9, '2026-01-06', 'hadir'),
(13, 6, 9, '2026-01-07', 'hadir'),
(14, 6, 11, '2026-01-08', 'sakit'),
(15, 10, 12, '2026-01-06', 'hadir'),
(16, 10, 12, '2026-01-07', 'izin'),
(17, 10, 12, '2026-01-10', 'izin'),
(18, 10, NULL, '2026-01-12', 'izin'),
(19, 10, NULL, '2026-01-13', 'izin'),
(20, 10, NULL, '2026-01-14', 'izin');

-- --------------------------------------------------------

--
-- Table structure for table `izin`
--

CREATE TABLE `izin` (
  `id` int(11) NOT NULL,
  `siswa_id` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `keterangan` varchar(255) DEFAULT NULL,
  `file` varchar(255) DEFAULT NULL,
  `status` enum('pending','disetujui','ditolak') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `izin`
--

INSERT INTO `izin` (`id`, `siswa_id`, `tanggal`, `keterangan`, `file`, `status`, `created_at`) VALUES
(1, 10, '2026-01-10', 'Izin sakit', '1767970077_Clairo - Sling album cover.jpg', 'pending', '2026-01-09 14:47:57'),
(2, 10, '2026-01-10', 'Izin sakit', '1768024948_the new album NIKI _Buzz_ out in 9th august.jpg', 'pending', '2026-01-10 06:02:28'),
(3, 10, '2026-01-12', 'upacara', '1768025049_Rockstar Nicole🤩🤩🥰✨🤘.jpg', 'pending', '2026-01-10 06:04:09'),
(4, 10, '2026-01-13', 'mati', '1768025199_Brent Faiyaz - Sonder Son.jpg', 'pending', '2026-01-10 06:06:39'),
(5, 10, '2026-01-14', 'mati', '1768320121_Ye Impala.jpg', 'pending', '2026-01-13 16:02:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('admin','guru','siswa') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'Admin Sekolah', 'admin@gmail.com', '$2y$10$.gcxVjEz1q2AQhJX5XXhmOmB.Suy87IY6jVh92wrnOmwHG715h9hy', 'admin', '2026-01-09 06:29:44'),
(5, 'admin2', 'admin2@gmail.com', '$2y$10$wOPsoHCq2UXL0EqOj7Zeou/A84BNqv7m.1syFM7iOO2Yf6JCzZRna', 'admin', '2026-01-09 07:06:20'),
(6, 'bagas', 'bagas@gmail.com', '$2y$10$Tb5d83sS4kzyNLrr7VDRku/8L3lusXFFKiP4yUNJqx6GWuyWs5KyG', 'siswa', '2026-01-09 07:07:26'),
(9, 'guru 1', 'guru1@gmail.com', '$2y$10$WQ2PwSSHrISuGCWfCVdyH.2r6tyBg2kFzuWFZ.wW4xjJEWAbehMUu', 'guru', '2026-01-09 07:23:10'),
(10, 'alfin', 'alfin@gmail.com', '$2y$10$nwq0LdpbZjoyH8uAYPXq/OdK71vp7hklb7xRY3n6v6FkiqqADzm3G', 'siswa', '2026-01-09 07:33:41'),
(11, 'pak budi', 'pakbudi@gmail.com', '$2y$10$FX0B9SRpHz4njm.kgofRI.wKZyZoyMAvxkHn4YpcEMri.PHzf4ZNa', 'guru', '2026-01-09 07:34:12'),
(12, 'bu oksa', 'buoksa@gmail.com', '$2y$10$bXjnPV6KTrP8zhGn1Kd0heHnPPKfbsXJ7BLqHdTdSZzM.GNHYFyZa', 'guru', '2026-01-09 07:34:29'),
(13, 'admin bintang', 'adminbintang@gmail.com', '$2y$10$tu.GQnhpSPhpQKvVocfao.FeeeqY0fxLY/DXv1GrueJGA.IPE7Ndq', 'admin', '2026-01-09 07:34:50'),
(14, 'admin kirk', 'adminkirk@gmail.com', '$2y$10$vYUwEaBr5ZhgXyHSyD.2e.ofbV0x4wigmY36nVkkqX/FO5yIw203.', 'admin', '2026-01-09 07:35:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `absensi`
--
ALTER TABLE `absensi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`),
  ADD KEY `guru_id` (`guru_id`);

--
-- Indexes for table `izin`
--
ALTER TABLE `izin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `siswa_id` (`siswa_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `absensi`
--
ALTER TABLE `absensi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `izin`
--
ALTER TABLE `izin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `absensi`
--
ALTER TABLE `absensi`
  ADD CONSTRAINT `absensi_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `absensi_ibfk_2` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `izin`
--
ALTER TABLE `izin`
  ADD CONSTRAINT `izin_ibfk_1` FOREIGN KEY (`siswa_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
