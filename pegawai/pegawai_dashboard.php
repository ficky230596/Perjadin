<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pegawai') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php'; // Pindahkan include db ke atas jika perlu

$stmt = $pdo->prepare("SELECT * FROM pengajuan WHERE pegawai_id = ? ORDER BY waktu_pengajuan DESC");
$stmt->execute([$_SESSION['user_id']]);
$pengajuan = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="assets/css/global.css">
</head>

<body>
    <?php include 'header_pegawai.php'; ?>
    <?php include 'sidebar_pegawai.php'; ?>

    <div class="main-content">
        <h1>Dashboard Pegawai: <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
        <p>Selamat datang! Kelola pengajuan perjalanan dinas Anda.</p>

        <a href="ajukan.php" class="button">Ajukan Perjadin Baru</a>

        <h2>Riwayat Pengajuan</h2>
        <?php if (empty($pengajuan)): ?>
            <p>Belum ada pengajuan.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Tujuan</th>
                    <th>Tanggal Berangkat</th>
                    <th>Urgensi</th>
                    <th>Status</th>
                    <!-- <th>Aksi</th> -->
                </tr>
                <?php foreach ($pengajuan as $p): ?>
                    <tr>
                        <td><?php echo $p['id']; ?></td>
                        <td><?php echo htmlspecialchars($p['tujuan']); ?></td>
                        <td><?php echo date('d-m-Y', strtotime($p['tanggal_berangkat'])); ?></td>
                        <td><?php echo ucfirst($p['urgensi']); ?></td>
                        <td><?php echo ucfirst(str_replace('_', ' ', $p['status'])); ?></td>
                        <!-- <td>
                            <?php if ($p['status'] === 'selesai'): ?>
                                <a href="generate_sppd.php?id=<?php echo $p['id']; ?>" target="_blank">Cetak Surat</a>
                            <?php else: ?>
                                Menunggu proses...
                            <?php endif; ?>
                        </td> -->
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

        <p class="notice">Dashboard ini otomatis refresh setiap 10 detik untuk update status.</p>
    </div>

    <?php include 'footer.php'; ?>

    <!-- Script untuk auto-refresh (opsional, tambahkan di akhir body) -->
    <script>
        setInterval(() => location.reload(), 10000);
    </script>
</body>

</html>