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

// Proses POST paraf
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['proses_id'])) {
    $id = $_POST['proses_id'];

    $stmt = $pdo->prepare("UPDATE pengajuan SET status = 'paraf_sekwan' WHERE id = ?");
    $stmt->execute([$id]);

    $stmt_ketua = $pdo->prepare("SELECT wa_phone FROM users WHERE role = 'ketua' LIMIT 1");
    $stmt_ketua->execute();
    $ketua = $stmt_ketua->fetch();

    if ($ketua && !empty($ketua['wa_phone'])) {
        sendWaNotification($ketua['wa_phone'], "SPPD ID #$id siap TTd.", $fonnte_token);
    }

    // SweetAlert di body setelah load
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paraf Sekwan</title>
    <link rel="stylesheet" href="assets/css/sekwan.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Sukses',
                text: 'Paraf & WA terkirim',
                icon: 'success'
            }).then(() => {
                window.location.href = 'sekwan_dashboard.php';
            });
        });
    </script>
    <?php endif; ?>
</head>
<body>


    <div class="main-content-sekwan">
        <h1>Antrian Paraf Sekwan</h1>

        <?php if (empty($queue)): ?>
            <p class="empty-antrian">Tidak ada draft siap paraf.</p>
        <?php else: ?>
            <?php foreach ($queue as $q): ?>
                <form method="POST">
                    <input type="hidden" name="proses_id" value="<?php echo htmlspecialchars($q['id']); ?>">
                    <button type="submit">Paraf ID <?php echo htmlspecialchars($q['id']); ?></button>
                </form>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>