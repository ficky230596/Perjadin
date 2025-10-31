-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 31 Okt 2025 pada 21.10
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
-- Database: `db_perjadin`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `pengajuan`
--

CREATE TABLE `pengajuan` (
  `id` int(11) NOT NULL,
  `pegawai_id` int(11) NOT NULL,
  `tujuan` varchar(255) NOT NULL,
  `tanggal_berangkat` date NOT NULL,
  `tanggal_kembali` date NOT NULL,
  `alasan` text NOT NULL,
  `urgensi` enum('rendah','sedang','tinggi') NOT NULL,
  `status` enum('diajukan','draft_sppd','paraf_sekwan','ttd_ketua','dicap','selesai','ditolak') DEFAULT 'diajukan',
  `prioritas_skor` int(11) DEFAULT 0,
  `waktu_pengajuan` timestamp NOT NULL DEFAULT current_timestamp(),
  `spt_no` varchar(50) DEFAULT NULL,
  `spd_no` varchar(50) DEFAULT NULL,
  `pangkat` varchar(50) DEFAULT NULL,
  `tingkat_biaya` varchar(50) DEFAULT NULL,
  `alat_angkutan` varchar(50) DEFAULT 'Mobil',
  `pengikut` text DEFAULT NULL,
  `instansi_anggaran` varchar(100) DEFAULT 'Sekretariat DPRD Kab. Banggai Kepulauan',
  `akun_anggaran` varchar(50) DEFAULT NULL,
  `golongan` varchar(50) NOT NULL,
  `alasan_penolakan` text NOT NULL,
  `nama` varchar(100) NOT NULL,
  `fraksi` varchar(100) NOT NULL,
  `komisi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengajuan`
--

INSERT INTO `pengajuan` (`id`, `pegawai_id`, `tujuan`, `tanggal_berangkat`, `tanggal_kembali`, `alasan`, `urgensi`, `status`, `prioritas_skor`, `waktu_pengajuan`, `spt_no`, `spd_no`, `pangkat`, `tingkat_biaya`, `alat_angkutan`, `pengikut`, `instansi_anggaran`, `akun_anggaran`, `golongan`, `alasan_penolakan`, `nama`, `fraksi`, `komisi`) VALUES
(9, 1, 'Tondano', '2025-10-18', '2025-10-23', 'Kunjungan kerja buru', 'tinggi', 'selesai', 3, '2025-10-16 18:05:20', '094/009/SPT/2025', 'OO/SPD/SET.DPRD/2025', '230596', '21355456', 'Mobil', 'Ficky:', 'Sekretariat DPRD Kab. Banggai Kepulauan', '232333', '', '', '', '', ''),
(10, 1, 'Jepang', '2025-10-18', '2025-10-21', 'Studi Tour', 'tinggi', 'selesai', 3, '2025-10-16 18:20:00', '094/010/SPT/2025', 'OO/SPD/SET.DPRD/2025', '1234', '11111', 'Pesawat', 'Mario :', 'Sekretariat DPRD Kab. Banggai Kepulauan', '45445344fgg', '', '', '', '', ''),
(11, 1, 'Belanda', '2025-10-18', '2025-10-29', 'Cari pemain bola', 'sedang', 'selesai', 2, '2025-10-16 18:49:36', '094/011/SPT/2025', 'OO/SPD/SET.DPRD/2025', '230596', '24234', 'Kapal', 'Marcel :', 'Sekretariat DPRD Kab. Banggai Kepulauan', '2424234', '', '', '', '', ''),
(12, 1, 'Manado', '2025-10-21', '2025-10-31', 'Cari Cewe', 'rendah', 'selesai', 1, '2025-10-16 18:51:15', '094/012/SPT/2025', 'OO/SPD/SET.DPRD/2025', '230596', '5353535', 'Jalan kaki', 'Deljio', 'Sekretariat DPRD Kab. Banggai Kepulauan', '343434', '', '', '', '', ''),
(13, 1, 'Cobsa', '2025-10-17', '2025-10-15', 'wewrewr', 'tinggi', 'selesai', 3, '2025-10-16 18:56:58', '094/013/SPT/2025', 'OO/SPD/SET.DPRD/2025', '2424', 'wrrqwdf', 'Mobil', '3wrqr', 'Sekretariat DPRD Kab. Banggai Kepulauan', 'r3wqr', '', '', '', '', ''),
(14, 1, 'Lopana', '2025-10-21', '2025-10-22', 'Cari Ikang', 'tinggi', 'selesai', 3, '2025-10-17 05:52:55', '094/014/SPT/2025', 'OO/SPD/SET.DPRD/2025', '7C', '152525', 'Pesawat', 'Marcel :', 'Sekretariat DPRD Kab. Banggai Kepulauan', '424f424', '', '', '', '', ''),
(15, 1, 'Bitung', '2025-10-20', '2025-10-29', 'Memriksa anggota peks bitung yang melakukan pembunuhan terhadap ikan di bitung, dan mencuri lolo atas nama Durex', 'tinggi', 'selesai', 3, '2025-10-18 08:29:36', '094/015/SPT/2025', 'OO/SPD/SET.DPRD/2025', '4C', '300000', 'Pesawat', 'Friska :', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', '', '', '', '', ''),
(16, 1, 'tual', '2025-10-19', '2025-10-20', 'fwrewrrwerrwerer', 'rendah', 'selesai', 1, '2025-10-18 08:51:07', '094/016/SPT/2025', 'OO/SPD/SET.DPRD/2025', '23C', '431313', 'Kapal', 'Yanto', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', '', '', '', '', ''),
(17, 1, '3r3r', '2025-10-22', '2025-10-20', 'r3r23r', 'sedang', 'selesai', 2, '2025-10-18 08:58:50', '34423/42131/44-122', '1111/313/000', 'Super', '111111111', 'oto', 'Marko', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', '', '', '', '', ''),
(18, 1, 'Jerman', '2025-10-19', '2025-10-22', 'fqf  fdqj qm 0djap p pdajdp \r\ndaodkaqdk  a dadkmapmd p  amdpamdapm  mdfpampo mapomdpoamf\r\n modfpka akpfdakfpoamfmf', 'tinggi', '', 3, '2025-10-18 12:05:37', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'tidak penting', '', '', ''),
(19, 1, 'amrik', '2025-10-18', '2025-10-23', 'nvuowneo w0ijf wr w0fjwoif\r\ncqfqewf', 'tinggi', 'ditolak', 3, '2025-10-18 13:50:48', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'Habis Doi', '', '', ''),
(20, 1, 'Gorontalo', '2025-10-24', '2025-11-03', 'gewsge fafd sfg ', 'rendah', 'ditolak', 1, '2025-10-18 13:54:07', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'Habis Doi', '', '', ''),
(21, 1, 'Bandung', '2025-10-18', '2025-10-23', 'qdqd dqdqd', 'sedang', 'selesai', 2, '2025-10-18 14:07:22', 'g5464y34t', 't43654gete', '', '442424', 'Mobil', 'marko', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', 'III', '', '', '', ''),
(23, 8, 'Jawa', '2025-10-21', '2025-10-23', 'jalan jalan', 'tinggi', 'selesai', 3, '2025-10-18 17:30:48', '123456/9', '846/322', '', '642424', 'Mobil', 'Mario', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', '', '', '', '', ''),
(24, 1, 'Gor', '2025-10-21', '2025-10-22', 'egeggerhbdfnb bgergrg', 'rendah', 'ditolak', 1, '2025-10-19 16:12:56', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'tetet', '', '', ''),
(25, 1, 'tes', '2025-10-21', '2025-10-21', 'gwegb fwetg gewgeww gwetf wege ', 'sedang', 'ditolak', 2, '2025-10-19 16:15:34', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'trttr', '', '', ''),
(26, 1, 'doi', '2025-10-20', '2025-10-22', 'ge gewg g ergg  erggwg gwtgewg', 'tinggi', 'ditolak', 3, '2025-10-19 16:16:42', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'coba lagi', '', '', ''),
(27, 1, 'Bali', '2025-10-22', '2025-10-24', 'sjfa a sv g ffeve  fqwfu wf fw fqowf fw fqw', 'tinggi', 'selesai', 3, '2025-10-20 08:55:00', '353t/226ge', '353253', '', '123456', 'Pesawat', 'Mario', 'Sekretariat DPRD Kab. Banggai Kepulauan', '', 'III', '', '', '', ''),
(28, 1, 'rherh', '2025-10-21', '2025-10-23', 'segegse vafwe ', 'tinggi', 'ditolak', 3, '2025-10-20 12:27:38', NULL, NULL, 'Penata Muda', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', 'tes', '', '', ''),
(29, 1, 'tual', '2025-11-02', '2025-11-06', 'hugel', 'tinggi', 'selesai', 3, '2025-10-31 12:50:22', '5345gfe', 'tet', '', '3000', 'Mobil', 'egye', 'Sekretariat DPRD Kab. Banggai Kepulauan', '3t35346', 'III', '', '', '', ''),
(30, 1, 'rumah', '2025-11-01', '2025-11-02', '5u5eruy5hrhrthutr', 'sedang', 'selesai', 2, '2025-10-31 13:01:58', '5345gfe', 'tet', '', '3000', 'Mobil', 'iuoiugb', 'Sekretariat DPRD Kab. Banggai Kepulauan', '345324', 'III', '', '', '', ''),
(31, 1, 'rumah', '2025-11-01', '2025-10-29', 'fwfwefwwef', 'tinggi', 'diajukan', 3, '2025-10-31 13:08:37', NULL, NULL, '', NULL, 'Mobil', NULL, 'Sekretariat DPRD Kab. Banggai Kepulauan', NULL, 'III', '', '', '', '');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('pegawai','umum','sekwan','ketua') NOT NULL,
  `nama` varchar(100) NOT NULL,
  `jabatan` varchar(100) DEFAULT 'Staf',
  `wa_phone` varchar(20) DEFAULT NULL COMMENT 'Nomor WA format 628xxxxxxxxxx',
  `pangkat` varchar(50) NOT NULL,
  `golongan` varchar(50) NOT NULL,
  `fraksi` varchar(100) NOT NULL,
  `komisi` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `nama`, `jabatan`, `wa_phone`, `pangkat`, `golongan`, `fraksi`, `komisi`) VALUES
(1, 'pegawai1', '$2y$10$s7F0.iYqDJEemwWAupSxEe02rCzXOsg9xfGjVaq9TXwuz3mxUkJwe', 'pegawai', 'Veririanus Lamasang', 'Anggota DPRD', '6282248139051', '', 'III', '', ''),
(2, 'umum1', '$2y$10$/1PjhJc7rxcayyOD8v38LeCAton4FqNQfCha3I.Mj2KUP.y6rfRcy', 'umum', 'Staff Umum', 'Sekretaris DPRD', '6282248139051', '', '', '', 'Komisi I'),
(3, 'sekwan', '$2y$10$cYtz9Z01AY0i7Mid1764buYFYFvfldOvVU8udYyQass/fc.96Dr82', 'sekwan', 'Asgar Lalu', 'Sekwan', '6285342860104', '', '', '', ''),
(4, 'ketua', '$2y$10$Yw9y2SCDwjCwfJhFdr0ps.QXB/rxsmG2lw1xJm5ofugi8r2OKo2sO', 'ketua', 'Arkam Supu', 'Ketua DPRD', '6282248139051', '', '', '', ''),
(7, 'Petugas4', '$2y$10$kp3Ylbcrx53/Dyc2VeINX.B3YkUWj8FEoWarGtYKL9bQtIX5.UGLa', 'pegawai', 'Marko', 'Bendahara Pengeluaran', '6287873702335', 'Penata Muda (III/a)', 'IV', '', ''),
(8, 'fr2305', '$2y$10$MeSakWjeyz/XzSU4e1q7HuKo4cSDdFST6YAKYJdO7Ni1rM8/GTw2y', 'pegawai', 'FRISKA REGINA', 'Anggota DPRD', '6287873702335', '', '', 'Fraksi Golkar', 'Komisi I');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pegawai_id` (`pegawai_id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `pengajuan`
--
ALTER TABLE `pengajuan`
  ADD CONSTRAINT `pengajuan_ibfk_1` FOREIGN KEY (`pegawai_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
