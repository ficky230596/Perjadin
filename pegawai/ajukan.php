<?php
session_start();
if ($_SESSION['role'] !== 'pegawai') {
    header('Location: pegawai_dashboard.php');
    exit;
}
include 'header_pegawai.php';
include 'sidebar_pegawai.php';
include '../config/db.php';

// ambil data pangkat & golongan pegawai dari DB
$stmt_pg = $pdo->prepare("SELECT pangkat, golongan FROM users WHERE id = ?");
$stmt_pg->execute([$_SESSION['user_id']]);
$pegawai = $stmt_pg->fetch(PDO::FETCH_ASSOC);
?>

<?php
if ($_POST) {
    $tujuan = $_POST['tujuan'];
    $tgl_berangkat = $_POST['tgl_berangkat'];
    $tgl_kembali = $_POST['tgl_kembali'];
    $alasan = $_POST['alasan'];
    $urgensi = $_POST['urgensi'];

    $skor = ($urgensi === 'tinggi' ? 3 : ($urgensi === 'sedang' ? 2 : 1));

    $stmt = $pdo->prepare("INSERT INTO pengajuan 
        (pegawai_id, tujuan, tanggal_berangkat, tanggal_kembali, alasan, urgensi, prioritas_skor, pangkat, golongan) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([
        $_SESSION['user_id'],
        $tujuan,
        $tgl_berangkat,
        $tgl_kembali,
        $alasan,
        $urgensi,
        $skor,
        $pegawai['pangkat'],
        $pegawai['golongan']
    ]);
    $id = $pdo->lastInsertId();

    // WA ke Umum
    $stmt_umum = $pdo->prepare("SELECT wa_phone FROM users WHERE role = 'umum' LIMIT 1");
    $stmt_umum->execute();
    $umum = $stmt_umum->fetch();

    sendWaNotification(
        $umum['wa_phone'],
        "Pengajuan baru ID #$id dari " . $_SESSION['nama'] . " - Urgensi: $urgensi. Tujuan: $tujuan.",
        $fonnte_token
    );

    // SweetAlert2 sukses + redirect
    echo "<script>
        Swal.fire({
            icon: 'success',
            title: 'Sukses!',
            text: 'Pengajuan berhasil & WA terkirim',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'pegawai_dashboard.php';
        });
    </script>";
}
?>
<style>
    /* Tambahkan styling untuk form */
</style>

<div class="ajukan-form-container">
    <h1>Ajukan Perjadin</h1>
    <h2 id="ajukan">Form Pengajuan Perjadin</h2>
    <form class="ajukan-form" method="POST">
        <!-- Baris 1 -->
        <div class="form-row">
            <div class="form-group">
                <label>Pangkat</label>
                <input type="text" value="<?= htmlspecialchars($pegawai['pangkat']) ?>" readonly>
            </div>
            <div class="form-group">
                <label>Golongan</label>
                <input type="text" value="<?= htmlspecialchars($pegawai['golongan']) ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tujuan">Tujuan</label>
                <input type="text" id="tujuan" name="tujuan" required>
            </div>
        </div>

        <!-- Baris 2 -->
        <div class="form-row">
            <div class="form-group">
                <label for="tgl_berangkat">Tanggal Berangkat</label>
                <input type="date" id="tgl_berangkat" name="tgl_berangkat" required>
            </div>
            <div class="form-group">
                <label for="tgl_kembali">Tanggal Kembali</label>
                <input type="date" id="tgl_kembali" name="tgl_kembali" required>
            </div>
            <div class="form-group">
                <label for="urgensi">Urgensi</label>
                <select id="urgensi" name="urgensi" required>
                    <option value="rendah">Rendah</option>
                    <option value="sedang">Sedang</option>
                    <option value="tinggi">Tinggi</option>
                </select>
            </div>
        </div>

        <!-- Baris 3 (Alasan full) -->
        <div class="form-row full-row">
            <div class="form-group">
                <label for="alasan">Alasan</label>
                <textarea id="alasan" name="alasan" required></textarea>
            </div>
        </div>

        <!-- Baris 4 (Button full) -->
        <div class="form-row full-row">
            <div class="form-group btn-group">
                <button type="submit">Ajukan</button>
            </div>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>