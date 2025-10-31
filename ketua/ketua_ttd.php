<?php
session_start();
if ($_SESSION['role'] !== 'ketua') header('Location: dashboard.php');
include 'header_ketua.php';
include 'sidebar_ketua.php';
include '../config/db.php';
$queue = getScheduledQueue($pdo, 'paraf_sekwan');
?>
<h1>Antrian TTd</h1>
<?php
if ($_POST) {
    $id = $_POST['ttd_id'] ?? $_POST['skip_id'];
    $status = isset($_POST['ttd_id']) ? 'ttd_ketua' : 'selesai';
    $stmt = $pdo->prepare("UPDATE pengajuan SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);

    // WA target
    if ($status === 'ttd_ketua') {
        $target_role = 'umum';
    } else {
        // Ambil WA pegawai dari pengajuan
        $stmt_peg = $pdo->prepare("SELECT u.wa_phone FROM users u JOIN pengajuan p ON u.id = p.pegawai_id WHERE p.id = ?");
        $stmt_peg->execute([$id]);
        $target = $stmt_peg->fetch();
        sendWaNotification($target['wa_phone'], "Pengajuan ID #$id disetujui langsung!", $fonnte_token);
        echo "<script>showAlert('Sukses', 'Skip & WA terkirim');</script>";
        return;
    }
    $stmt_target = $pdo->prepare("SELECT wa_phone FROM users WHERE role = ? LIMIT 1");
    $stmt_target->execute([$target_role]);
    $target = $stmt_target->fetch();
    sendWaNotification($target['wa_phone'], "SPPD ID #$id siap cap.", $fonnte_token);

    echo "<script>showAlert('Sukses', 'TTd & WA terkirim');</script>";
}
foreach ($queue as $q): ?>
    <form method="POST">
        ID <?php echo $q['id']; ?>:
        <button name="ttd_id" value="<?php echo $q['id']; ?>">TTd Normal</button>
        <button name="skip_id" value="<?php echo $q['id']; ?>" style="background:green;">Setujui Langsung</button>
    </form>
<?php endforeach; ?>
<?php include 'footer.php'; ?>