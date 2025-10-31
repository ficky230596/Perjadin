-- Buat database
CREATE DATABASE IF NOT EXISTS db_perjadin 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_general_ci;

USE db_perjadin;

-- Tabel users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('pegawai', 'umum', 'sekwan', 'ketua') NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) DEFAULT 'Staf',
    wa_phone VARCHAR(20) COMMENT 'Nomor WA format 628xxxxxxxxxx'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tabel pengajuan
CREATE TABLE IF NOT EXISTS pengajuan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pegawai_id INT NOT NULL,
    tujuan VARCHAR(255) NOT NULL,
    tanggal_berangkat DATE NOT NULL,
    tanggal_kembali DATE NOT NULL,
    alasan TEXT NOT NULL,
    urgensi ENUM('rendah', 'sedang', 'tinggi') NOT NULL,
    status ENUM('diajukan', 'draft_sppd', 'paraf_sekwan', 'ttd_ketua', 'dicap', 'selesai') DEFAULT 'diajukan',
    prioritas_skor INT DEFAULT 0,
    waktu_pengajuan TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    spt_no VARCHAR(50),
    spd_no VARCHAR(50),
    pangkat VARCHAR(50),
    tingkat_biaya VARCHAR(50),
    alat_angkutan VARCHAR(50) DEFAULT 'Mobil',
    pengikut TEXT,
    instansi_anggaran VARCHAR(100) DEFAULT 'Sekretariat DPRD Kab. Banggai Kepulauan',
    akun_anggaran VARCHAR(50),
    FOREIGN KEY (pegawai_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert user contoh
INSERT INTO users (username, password, role, nama, jabatan, wa_phone) VALUES
('pegawai1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pegawai', 'Veririanus Lamasang', 'Anggota DPRD', '6281111111111'),
('umum1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'umum', 'Staff Umum', 'Bagian Umum', '6282222222222'),
('sekwan', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'sekwan', 'Asgar Lalu', 'Sekwan', '6283333333333'),
('ketua', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'ketua', 'Arkam Supu', 'Ketua DPRD', '6284444444444');
