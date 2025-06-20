-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2025 at 04:02 AM
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
-- Database: `perpuskita`
--

-- --------------------------------------------------------

--
-- Table structure for table `buku`
--

CREATE TABLE `buku` (
  `id_buku` varchar(10) NOT NULL,
  `judul` varchar(150) NOT NULL,
  `penulis` varchar(100) DEFAULT NULL,
  `penerbit` varchar(100) DEFAULT NULL,
  `kategori` varchar(255) DEFAULT NULL,
  `stok` int(11) DEFAULT 0,
  `gambar` varchar(255) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buku`
--

INSERT INTO `buku` (`id_buku`, `judul`, `penulis`, `penerbit`, `kategori`, `stok`, `gambar`, `deskripsi`) VALUES
('BK001', 'Harry Potter dan Batu Bertuah', 'J. K. Rowling', 'Gramedia', 'Fantasi', 25, '685466ed9af0b7.25021402.jpg', 'Harry Potter dan Batu Bertuah adalah novel fantasi karangan penulis Inggris J. K. Rowling yang merupakan novel pertama dalam seri Harry Potter dan novel debut Rowling. Novel ini mengisahkan mengenai Harry Potter, seorang anak yatim piatu yang mengetahui bahwa ia adalah penyihir pada ulang tahunnya yang kesebelas, ketika ia menerima undangan untuk menghadiri Sekolah Sihir Hogwarts. Harry menjalin pertemanan dan permusuhan pada tahun pertamanya di sekolah, dan dengan bantuan teman-temannya, ia menggagalkan upaya penyihir hitam Lord Voldemort untuk bangkit kembali.'),
('BK002', 'Harry Potter dan Kamar Rahasia', 'J. K. Rowling', 'Gramedia', 'Fantasi', 25, '6854679479c419.63855824.jpg', 'Harry Potter and the Chamber of Secrets adalah novel fantasi yang ditulis oleh penulis Inggris JK Rowling . Novel ini merupakan novel kedua dalam seri Harry Potter . Alur cerita mengikuti tahun kedua Harry di Sekolah Sihir Hogwarts , di mana serangkaian pesan di dinding koridor sekolah memperingatkan bahwa \" Kamar Rahasia \" telah dibuka dan bahwa \"ahli waris Slytherin \" akan membunuh semua murid yang tidak berasal dari keluarga penyihir. Ancaman-ancaman ini ditemukan setelah serangan yang membuat penduduk sekolah ketakutan. Sepanjang tahun, Harry dan teman-temannya Ron dan Hermione menyelidiki serangan tersebut.'),
('BK003', 'Laskar Pelangi', 'Andrea Hirata', 'Bentang Pustaka', 'Roman', 29, '6854683c017dd1.21045224.jpg', 'Laskar Pelangi adalah novel pertama karya Andrea Hirata yang diterbitkan oleh Bentang Pustaka pada tahun 2005. Novel ini mengisahkan perjalanan sepuluh anak dari keluarga miskin yang menempuh pendidikan di sebuah sekolah Muhammadiyah di Pulau Belitung dengan segala keterbatasan yang ada.'),
('BK004', 'Sang Pemimpi', 'Andrea Hirata', 'Bentang Pustaka', 'Roman', 30, '6854691d3794d4.18508332.jpg', 'Sang Pemimpi Baru adalah novel kedua dalam tetralogi Laskar Pelangi karya Andrea Hirata yang diterbitkan oleh Bentang Pustaka pada Juli 2006. Dalam novel ini, Andrea mengeksplorasi hubungan persahabatannya dengan dua anak yatim piatu, Arai Ichsanul Mahidin dan Jimbron, serta kekuatan mimpi yang dapat membawa Andrea dan Arai melanjutkan studi ke Sorbonne, Paris, Prancis.'),
('BK005', 'Edensor', 'Andrea Hirata', 'Bentang Pustaka', 'Roman', 29, '685469a6d648e.jpg', 'Berbeda dengan latar cerita dari Laskar Pelangi dan Sang Pemimpi, Edensor mengambil latar di luar negeri saat tokoh-tokoh utamanya, Ikal dan Arai mendapat beasiswa dari Uni Eropa untuk kuliah S2 di Prancis. Dalam Edensor, Andrea tetap dengan ciri khasnya, menulis kisah ironi menjadi parodi dan menertawakan kesedihan dengan balutan pandangan intelegensia tentang culture shock ketika kedua tokoh utama tersebut yang berasal dari pedalaman Melayu di Pulau Belitong tiba-tiba berada di Paris. Mimpi-mimpi untuk menjelajah Eropa sampai Afrika dan menemukan keterkaitan yang tak terduga dari peristiwa-peristiwa dari masa lalu mereka berdua. Dan pencarian akan cinta sejati menjadi motivasi yang menyemangati penjelajahan mereka dari bekunya musim dingin di daratan Rusia di Eropa sampai panas kering di gurun');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` varchar(10) NOT NULL,
  `nama_lengkap` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `telepon` varchar(20) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_daftar` date DEFAULT NULL,
  `tanggal_berakhir` date DEFAULT NULL,
  `role` enum('admin','anggota') DEFAULT 'anggota'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`, `telepon`, `alamat`, `tanggal_daftar`, `tanggal_berakhir`, `role`) VALUES
('ADM001', 'Admin Perpustakaan', 'admin@perpus.com', '$2y$10$cslsZIOvGEoiz015agST6uHYwQTamTc8QUlJe4enbl.zxpiOvQ5PO', '-', '-', '2025-06-19', '2030-06-19', 'admin'),
('ANG00001', 'Dhimas Kurniawan', 'dhimas@email.com', '$2y$10$s1rUBP59RnzZDQEqH9.RHu7wyzcUQOca85g.FOxEnch7HQ2hN5d4e', '12345678', 'jl.buntu Gg. Buntu', '2025-06-16', '2026-07-18', 'anggota');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` varchar(20) NOT NULL,
  `id_pengguna` varchar(10) DEFAULT NULL,
  `id_buku` varchar(10) DEFAULT NULL,
  `tanggal_booking` date DEFAULT NULL,
  `batas_waktu` date DEFAULT NULL,
  `status` enum('menunggu','dipinjam','dikembalikan','dibatalkan') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `id_buku`, `tanggal_booking`, `batas_waktu`, `status`) VALUES
('TRX20250001', 'ANG00001', 'BK003', '2025-06-17', '2025-06-18', 'dipinjam'),
('TRX20250002', 'ANG00001', 'BK005', '2025-06-20', '2025-06-27', 'dikembalikan');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `buku`
--
ALTER TABLE `buku`
  ADD PRIMARY KEY (`id_buku`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_buku` (`id_buku`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_buku`) REFERENCES `buku` (`id_buku`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
