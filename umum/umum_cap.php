<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'umum') {
    header('Location: ../index.php');
    exit;
}
require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cap SPPD</title>
    <link rel="stylesheet" href="assets/css/umum.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showAlert(title, text, icon = 'info') {
            Swal.fire({
                title,
                text,
                icon,
                confirmButtonText: 'OK'
            });
        }
    </script>
</head>

<body>
    <?php include 'header_umum.php'; ?>
    <?php include 'sidebar_umum.php'; ?>

    <div class="main-content-umum">
        <?php
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            echo "<script>showAlert('Error', 'ID tidak valid!', 'error'); location.href='umum_dashboard.php';</script>";
            exit;
        }

        // Ambil data pengajuan
        $stmt = $pdo->prepare("SELECT * FROM pengajuan WHERE id = ? AND status = 'ttd_ketua'");
        $stmt->execute([$id]);
        $pengajuan = $stmt->fetch();

        if (!$pengajuan) {
            echo "<script>showAlert('Error', 'Pengajuan tidak ditemukan atau belum siap cap!', 'error'); location.href='umum_dashboard.php';</script>";
            exit;
        }

        // Jika form disubmit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['id'] == $id) {
            $action = $_POST['action']; // normal / setujui
            $status = 'selesai'; // cap selalu selesai

            $stmt_update = $pdo->prepare("UPDATE pengajuan SET status = ? WHERE id = ?");
            $stmt_update->execute([$status, $id]);

            // Ambil WA pemohon
            $stmt_peg = $pdo->prepare("SELECT u.wa_phone, u.nama 
                                       FROM users u 
                                       JOIN pengajuan p ON u.id = p.pegawai_id 
                                       WHERE p.id = ?");
            $stmt_peg->execute([$id]);
            $peg = $stmt_peg->fetch();

            if ($peg) {
                $message = ($action === 'setujui')
                    ? "Pengajuan ID #$id disetujui langsung & selesai!"
                    : "Pengajuan ID #$id telah dicap & selesai. Cetak surat sekarang.";
                sendWaNotification($peg['wa_phone'], $message, $fonnte_token);
            }

            echo "<script>showAlert('Sukses', 'Cap selesai & WA terkirim ke Pemohon!', 'success'); location.href='umum_dashboard.php';</script>";
            exit;
        }
        ?>

        <h1>Cap SPPD - ID #<?php echo $id; ?></h1>
        <p>Tujuan: <?php echo htmlspecialchars($pengajuan['tujuan']); ?> | Status: Siap cap.</p>

        <form method="POST">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            <button type="submit" name="action" value="normal">Cap Normal</button>
            <button type="submit" name="action" value="setujui" style="background:green;">Setujui Langsung (Skip)</button>
        </form>

        <p>Setelah selesai, pemohon bisa cetak di dashboard mereka via
            <code>generate_sppd.php?id=<?php echo $id; ?></code>
        </p>
    </div>

    <?php include 'footer.php'; ?>
</body>

</html>