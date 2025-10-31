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
    <title>Dashboard Umum</title>
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>
    <?php include 'header_umum.php'; ?>
    <?php include 'sidebar_umum.php'; ?>

    <div class="main-content-umum">
        <h1>Dashboard Bagian Umum: <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
        <p>Kelola draft dan cap SPPD berdasarkan prioritas.</p>

        <h2>Antrian Buat Draft (dari Pegawai)</h2>
        <?php
        $queue_draft = getScheduledQueue($pdo, 'diajukan');
        if (empty($queue_draft)) {
            echo "<p class='empty-antrian'>Tidak ada antrian draft.</p>";
        } else {
        ?>
            <table class="umum-table" border="1" cellpadding="6" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>Pemohon</th>
                    <th>Tujuan</th>
                    <th>Urgensi</th>
                    <th>Skor Prioritas</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($queue_draft as $q):
                    $stmt_pemohon = $pdo->prepare("SELECT nama, wa_phone FROM users WHERE id = ?");
                    $stmt_pemohon->execute([$q['pegawai_id']]);
                    $userData = $stmt_pemohon->fetch(PDO::FETCH_ASSOC);
                    $pemohon = $userData['nama'];
                    $hp      = $userData['wa_phone'];

                    $prioritasClass = ($q['prioritas_skor'] > 5) ? 'prioritas-tinggi' : '';
                ?>
                    <tr>
                        <td><?php echo $q['id']; ?></td>
                        <td><?php echo htmlspecialchars($pemohon); ?></td>
                        <td><?php echo htmlspecialchars($q['tujuan']); ?></td>
                        <td><?php echo ucfirst($q['urgensi']); ?></td>
                        <td class="<?php echo $prioritasClass; ?>"><?php echo $q['prioritas_skor']; ?></td>
                        <td>
                            <a href="umum_buat.php?id=<?php echo $q['id']; ?>">Proses Draft</a> |
                            <button class="btn-tolak" onclick="tolakPengajuan('<?php echo $q['id']; ?>','<?php echo addslashes($pemohon); ?>','<?php echo $hp; ?>','<?php echo addslashes($q['tujuan']); ?>')">
                                Tolak
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php } ?>

        <h2>Antrian Cap SPPD (dari Ketua)</h2>
        <?php
        $queue_cap = getScheduledQueue($pdo, 'ttd_ketua');
        if (empty($queue_cap)) {
            echo "<p class='empty-antrian'>Tidak ada antrian cap.</p>";
        } else {
        ?>
            <table class="umum-table" border="1" cellpadding="6" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <th>Tujuan</th>
                    <th>Skor Prioritas</th>
                    <th>Aksi</th>
                </tr>
                <?php foreach ($queue_cap as $q): ?>
                    <tr>
                        <td><?php echo $q['id']; ?></td>
                        <td><?php echo htmlspecialchars($q['tujuan']); ?></td>
                        <td><?php echo $q['prioritas_skor']; ?></td>
                        <td><a href="umum_cap.php?id=<?php echo $q['id']; ?>">Proses Cap</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php } ?>
    </div>

    <?php include 'footer.php'; ?>

    <script>
        function tolakPengajuan(id, nama, hp, tujuan) {
            Swal.fire({
                title: 'Tolak Pengajuan?',
                text: "Tuliskan alasan penolakan",
                input: 'text',
                inputPlaceholder: 'Alasan penolakan...',
                showCancelButton: true,
                confirmButtonText: 'Tolak',
                cancelButtonText: 'Batal',
                preConfirm: (alasan) => {
                    if (!alasan) {
                        Swal.showValidationMessage('Alasan wajib diisi!');
                    }
                    return alasan;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('tolak_pengajuan.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded'
                            },
                            body: `id=${id}&alasan=${encodeURIComponent(result.value)}&nama=${encodeURIComponent(nama)}&hp=${encodeURIComponent(hp)}&tujuan=${encodeURIComponent(tujuan)}`
                        })
                        .then(res => res.text())
                        .then(res => {
                            if (res === "OK") {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Ditolak!',
                                    text: 'Pengajuan berhasil ditolak dan notifikasi WA dikirim.'
                                }).then(() => {
                                    window.location.href = 'umum_dashboard.php';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal!',
                                    text: 'Terjadi kesalahan saat menolak pengajuan.'
                                });
                            }
                        });
                }
            });
        }
    </script>
</body>

</html>