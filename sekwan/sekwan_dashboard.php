<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekwan') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';
include 'header_sekwan.php';
include 'sidebar_sekwan.php';

$queue = getScheduledQueue($pdo, 'draft_sppd');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sekwan</title>
    <link rel="stylesheet" href="assets/css/sekwan.css">
    <script>
        setTimeout(() => location.reload(), 10000);
    </script>
</head>
<body>


    <div class="main-content-sekwan">
        <h1>Dashboard Sekwan: <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
        <p>Berikan paraf pada draft SPPD berdasarkan prioritas.</p>

        <h2>Antrian Paraf (Prioritas)</h2>
        <?php if (empty($queue)): ?>
            <p class="empty-antrian">Tidak ada draft siap paraf.</p>
        <?php else: ?>
            <table class="sekwan-table">
                <tr>
                    <th>ID</th>
                    <th>Tujuan</th>
                    <th>Skor</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($queue as $q): 
                    $skorClass = ($q['prioritas_skor'] > 5) ? 'prioritas-tinggi' : ''; // Sesuaikan threshold
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($q['id']); ?></td>
                        <td><?php echo htmlspecialchars($q['tujuan']); ?></td>
                        <td class="<?php echo $skorClass; ?>"><?php echo htmlspecialchars($q['prioritas_skor']); ?></td>
                        <td>
                            <a href="sekwan_paraf.php?proses_id=<?php echo $q['id']; ?>"
                               onclick="return confirm('Yakin ingin memparaf SPPD ini?');">
                               Paraf Sekarang
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>