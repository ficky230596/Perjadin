<?php
session_start();
require_once '../config/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'umum') {
    http_response_code(403);
    exit('Akses ditolak');
}

$action = $_GET['action'] ?? '';

/**
 * Daftar jabatan yang hanya boleh dimiliki oleh 1 orang
 */
$jabatan_unik = [
    "Ketua DPRD",
    "Wakil Ketua I",
    "Wakil Ketua II",
    "Sekretaris DPRD",
    "Kepala Bagian Umum & Keuangan",
    "Kepala Bagian Persidangan & Perundangan",
    "Kepala Bagian Fasilitasi & Pengawasan",
    "Kasubbag Keuangan",
    "Kasubbag Umum & Kepegawaian",
    "Kasubbag Rumah Tangga",
    "Kasubbag Persidangan",
    "Kasubbag Hukum",
    "Kasubbag Pengawasan",
    "Bendahara Pengeluaran",
    "Bendahara Penerimaan",
    "Bendahara Barang"
];

if ($action === 'get') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($user);
    exit;
}

if ($action === 'save') {
    $id       = $_POST['id'] ?? '';
    $nama     = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'] ?? '';
    $role     = $_POST['role'];
    $jabatan  = $_POST['jabatan'] ?? '';
    $pangkat  = $_POST['pangkat'] ?? '';
    $golongan = $_POST['golongan'] ?? '';
    $wa_phone = $_POST['wa_phone'] ?? '';
    $fraksi   = $_POST['fraksi'] ?? null;
    $komisi   = $_POST['komisi'] ?? null;

    // Validasi wajib isi
    if (empty($nama) || empty($username) || (!$id && empty($password))) {
        http_response_code(400);
        exit("Semua field wajib diisi.");
    }

    // Cegah username duplikat
    $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND id != ?");
    $check->execute([$username, $id ?: 0]);
    if ($check->fetch()) {
        http_response_code(400);
        exit("Username sudah digunakan pengguna lain.");
    }

    // Cegah jabatan unik terduplikasi
    if (in_array($jabatan, $jabatan_unik)) {
        $cek_jabatan = $pdo->prepare("SELECT id FROM users WHERE jabatan = ? AND id != ?");
        $cek_jabatan->execute([$jabatan, $id ?: 0]);
        if ($cek_jabatan->fetch()) {
            http_response_code(400);
            exit("Jabatan '$jabatan' sudah terisi oleh pengguna lain.");
        }
    }

    // Simpan data
    if ($id) {
        // Update
        if (!empty($password)) {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users 
                SET nama=?, username=?, password=?, role=?, jabatan=?, pangkat=?, golongan=?, wa_phone=?, fraksi=?, komisi=? 
                WHERE id=?");
            $sukses = $stmt->execute([$nama, $username, $hash, $role, $jabatan, $pangkat, $golongan, $wa_phone, $fraksi, $komisi, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE users 
                SET nama=?, username=?, role=?, jabatan=?, pangkat=?, golongan=?, wa_phone=?, fraksi=?, komisi=? 
                WHERE id=?");
            $sukses = $stmt->execute([$nama, $username, $role, $jabatan, $pangkat, $golongan, $wa_phone, $fraksi, $komisi, $id]);
        }
    } else {
        // Insert
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (nama, username, password, role, jabatan, pangkat, golongan, wa_phone, fraksi, komisi) 
            VALUES (?,?,?,?,?,?,?,?,?,?)");
        $sukses = $stmt->execute([$nama, $username, $hash, $role, $jabatan, $pangkat, $golongan, $wa_phone, $fraksi, $komisi]);
    }

    echo $sukses ? "OK" : "ERROR";
    exit;
}

if ($action === 'delete') {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $sukses = $stmt->execute([$id]);
    echo $sukses ? "OK" : "ERROR";
    exit;
}
?>
