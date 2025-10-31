<?php
/**
 * create_db_perjadin.php
 * Jalankan di XAMPP (htdocs). Script ini:
 *  - Membuat database db_perjadin dengan charset utf8mb4
 *  - Membuat tabel users dan pengajuan
 *  - Menambahkan contoh user dengan password yang di-hash
 *
 * PENTING:
 *  - Setelah berhasil, hapus/move file ini agar tidak bisa dijalankan ulang sembarangan.
 *  - Ubah DB credentials jika Anda tidak pakai default XAMPP.
 */

// Konfigurasi koneksi MySQL (ubah bila perlu)
$host = '127.0.0.1';
$port = 3306;
$dbUser = 'root';
$dbPass = ''; // default XAMPP: kosong
$dbName = 'db_perjadin';

try {
    // Koneksi ke server (tanpa menentukan database terlebih dahulu)
    $dsnServer = "mysql:host=$host;port=$port;charset=utf8mb4";
    $opts = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    $pdo = new PDO($dsnServer, $dbUser, $dbPass, $opts);
    echo "Terhubung ke MySQL server.<br>";

    // 1) Buat database jika belum ada
    $sqlCreateDb = "CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci";
    $pdo->exec($sqlCreateDb);
    echo "Database `$dbName` dibuat/ada.<br>";

    // 2) Pilih database
    $pdo->exec("USE `$dbName`");
    echo "Menggunakan database `$dbName`.<br>";

    // 3) Buat tabel users
    $sqlUsers = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('pegawai', 'umum', 'sekwan', 'ketua') NOT NULL,
    nama VARCHAR(100) NOT NULL,
    jabatan VARCHAR(100) DEFAULT 'Staf',
    wa_phone VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
SQL;
    $pdo->exec($sqlUsers);
    echo "Tabel `users` dibuat/ada.<br>";

    // 4) Buat tabel pengajuan
    $sqlPengajuan = <<<SQL
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
SQL;
    $pdo->exec($sqlPengajuan);
    echo "Tabel `pengajuan` dibuat/ada.<br>";

    // 5) Tambah contoh data users (cek dulu apakah sudah ada)
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) AS cnt FROM users");
    $stmtCheck->execute();
    $row = $stmtCheck->fetch();
    if ($row && $row['cnt'] == 0) {
        // contoh password plaintext (untuk contoh): 12345
        $pwPlain = '12345';
        $pwHash = password_hash($pwPlain, PASSWORD_BCRYPT);

        $users = [
            ['pegawai1', $pwHash, 'pegawai', 'Veririanus Lamasang', 'Anggota DPRD', '6281111111111'],
            ['umum1', $pwHash, 'umum', 'Staff Umum', 'Bagian Umum', '6282222222222'],
            ['sekwan', $pwHash, 'sekwan', 'Asgar Lalu', 'Sekwan', '6283333333333'],
            ['ketua', $pwHash, 'ketua', 'Arkam Supu', 'Ketua DPRD', '6284444444444'],
        ];

        $ins = $pdo->prepare("INSERT INTO users (username, password, role, nama, jabatan, wa_phone) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($users as $u) {
            $ins->execute($u);
        }
        echo "Contoh data `users` berhasil ditambahkan (password contoh: 12345 — sudah di-hash).<br>";
    } else {
        echo "Tabel `users` sudah berisi data; contoh data dilewati.<br>";
    }

    echo "<br><strong>Selesai.</strong> Pastikan untuk menghapus file ini dari htdocs bila tidak perlu lagi.<br>";
    echo "Jika Anda ingin memasukkan data pengajuan contoh, beri tahu saya dan saya buatkan juga skrip insert pengajuan.";
} catch (PDOException $e) {
    echo "<strong>Terjadi kesalahan:</strong> " . htmlspecialchars($e->getMessage()) . "<br>";
    echo "Pastikan MySQL (MariaDB) di XAMPP berjalan dan kredensial benar (user/password).";
    exit;
}
