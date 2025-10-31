<?php
session_start();
require_once '../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id     = $_POST['id'];
    $alasan = $_POST['alasan'];
    $nama   = $_POST['nama'];
    $hp     = $_POST['hp'];
    $tujuan = $_POST['tujuan'];

    // update status jadi ditolak + simpan alasan penolakan
    $stmt = $pdo->prepare("UPDATE pengajuan SET status = 'ditolak', alasan_penolakan = ? WHERE id = ?");
    $sukses = $stmt->execute([$alasan, $id]);

    if ($sukses && !empty($hp)) {
        global $fonnte_token;
        $pesan = "Halo *$nama*,\n\n" .
            "Pengajuan perjalanan dinas dengan tujuan *$tujuan* telah *DITOLAK* Oleh Bagian Umum.\n\n" .
            "Alasan penolakan: _{$alasan}_\n\n" .
            "Silakan perbaiki atau hubungi bagian terkait.";
        sendWaNotification($hp, $pesan, $fonnte_token);
    }

    echo $sukses ? "OK" : "ERROR";
}
?>
