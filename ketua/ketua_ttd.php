<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'ketua'
) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';

/*
|--------------------------------------------------------------------------
| Proses tanda tangan dan persetujuan langsung
|--------------------------------------------------------------------------
*/

$alertScript = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = 0;
    $status = '';

    if (isset($_POST['ttd_id'])) {
        $id = (int) $_POST['ttd_id'];
        $status = 'ttd_ketua';
    } elseif (isset($_POST['skip_id'])) {
        $id = (int) $_POST['skip_id'];
        $status = 'selesai';
    }

    if ($id > 0 && $status !== '') {
        try {
            $stmt = $pdo->prepare(
                "UPDATE pengajuan SET status = ? WHERE id = ?"
            );
            $stmt->execute([$status, $id]);

            if ($stmt->rowCount() > 0) {
                if ($status === 'ttd_ketua') {
                    // Notifikasi kepada pengguna dengan role umum.
                    $stmtTarget = $pdo->prepare(
                        "SELECT wa_phone FROM users
                         WHERE role = ? AND wa_phone IS NOT NULL
                         AND wa_phone <> ''
                         LIMIT 1"
                    );
                    $stmtTarget->execute(['umum']);
                    $target = $stmtTarget->fetch(PDO::FETCH_ASSOC);

                    if ($target && !empty($target['wa_phone'])) {
                        sendWaNotification(
                            $target['wa_phone'],
                            "SPPD ID #{$id} siap cap.",
                            $fonnte_token
                        );
                        $alertScript = "showAlert('Sukses', 'Tanda tangan berhasil diproses dan notifikasi WhatsApp dikirim.');";
                    } else {
                        $alertScript = "showAlert('Berhasil', 'Tanda tangan berhasil diproses, tetapi nomor WhatsApp penerima tidak ditemukan.');";
                    }
                } else {
                    // Ambil nomor WhatsApp pegawai yang mengajukan.
                    $stmtPegawai = $pdo->prepare(
                        "SELECT u.wa_phone
                         FROM users u
                         JOIN pengajuan p ON u.id = p.pegawai_id
                         WHERE p.id = ?
                         LIMIT 1"
                    );
                    $stmtPegawai->execute([$id]);
                    $target = $stmtPegawai->fetch(PDO::FETCH_ASSOC);

                    if ($target && !empty($target['wa_phone'])) {
                        sendWaNotification(
                            $target['wa_phone'],
                            "Pengajuan ID #{$id} disetujui langsung!",
                            $fonnte_token
                        );
                        $alertScript = "showAlert('Sukses', 'Pengajuan disetujui langsung dan notifikasi WhatsApp dikirim.');";
                    } else {
                        $alertScript = "showAlert('Berhasil', 'Pengajuan disetujui langsung, tetapi nomor WhatsApp pegawai tidak ditemukan.');";
                    }
                }
            } else {
                $alertScript = "showAlert('Informasi', 'Tidak ada perubahan status. Periksa ID pengajuan dan status saat ini.');";
            }
        } catch (PDOException $e) {
            error_log('Proses tanda tangan Ketua: ' . $e->getMessage());
            $alertScript = "showAlert('Gagal', 'Terjadi kesalahan saat memproses pengajuan.');";
        }
    } else {
        $alertScript = "showAlert('Peringatan', 'ID pengajuan tidak valid.');";
    }
}

$queue = getScheduledQueue($pdo, 'paraf_sekwan');

include 'header_ketua.php';
include 'sidebar_ketua.php';
?>

<style>
    .ttd-page {
        max-width: 1200px;
        margin: 0 auto;
        padding: 10px 0 35px;
        color: #26364a;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .ttd-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .ttd-heading h1 {
        margin: 0 0 7px;
        color: #10294d;
        font-size: 27px;
        font-weight: 800;
    }

    .ttd-heading p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    .ttd-count {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 12px 17px;
        border: 1px solid #f0dba7;
        border-radius: 12px;
        background: #fff9e9;
        color: #17477f;
        font-size: 14px;
        font-weight: 700;
    }

    .ttd-card {
        overflow: hidden;
        border: 1px solid #e5eaf1;
        border-radius: 15px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 41, 77, .07);
    }

    .ttd-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 19px 22px;
        border-bottom: 1px solid #e5eaf1;
        background: linear-gradient(110deg, #10294d, #17477f);
        color: #fff;
    }

    .ttd-card-header i {
        color: #f2bd4b;
        font-size: 21px;
    }

    .ttd-card-header h2 {
        margin: 0;
        font-size: 17px;
        font-weight: 700;
    }

    .ttd-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .ttd-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 680px;
    }

    .ttd-table th {
        padding: 15px 18px;
        background: #f5f7fb;
        color: #17477f;
        text-align: left;
        font-size: 12px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .5px;
        border-bottom: 1px solid #e5eaf1;
    }

    .ttd-table td {
        padding: 17px 18px;
        border-bottom: 1px solid #edf0f5;
        color: #334155;
        font-size: 14px;
        vertical-align: middle;
    }

    .ttd-table tbody tr:hover {
        background: #fbfcff;
    }

    .ttd-table tbody tr:last-child td {
        border-bottom: none;
    }

    .ttd-id {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 7px;
        background: #edf3fb;
        color: #17477f;
        font-weight: 800;
    }

    .ttd-score {
        color: #946b12;
        font-weight: 800;
    }

    .ttd-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ttd-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 7px;
        padding: 9px 12px;
        border: none;
        border-radius: 8px;
        color: #fff;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .ttd-btn-normal {
        background: #17477f;
    }

    .ttd-btn-normal:hover {
        background: #10294d;
    }

    .ttd-btn-skip {
        background: #198754;
    }

    .ttd-btn-skip:hover {
        background: #146c43;
    }

    .ttd-empty {
        padding: 50px 20px;
        text-align: center;
    }

    .ttd-empty i {
        margin-bottom: 15px;
        color: #cbd5e1;
        font-size: 44px;
    }

    .ttd-empty h3 {
        margin: 0 0 8px;
        color: #17477f;
        font-size: 18px;
    }

    .ttd-empty p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
    }

    @media (max-width: 800px) {
        .ttd-heading h1 {
            font-size: 23px;
        }

        .ttd-count {
            padding: 10px 13px;
        }
    }
</style>

<main>
    <div class="ttd-page">

        <div class="ttd-heading">
            <div>
                <h1><i class="fa-solid fa-file-signature"></i> Antrian Tanda Tangan</h1>
                <p>Kelola tanda tangan dan persetujuan pengajuan perjalanan dinas.</p>
            </div>

            <div class="ttd-count">
                <i class="fa-solid fa-inbox"></i>
                <?= count($queue) ?> Pengajuan
            </div>
        </div>

        <section class="ttd-card">
            <div class="ttd-card-header">
                <i class="fa-solid fa-list-check"></i>
                <h2>Antrian Tanda Tangan Prioritas</h2>
            </div>

            <?php if (empty($queue)): ?>
                <div class="ttd-empty">
                    <i class="fa-regular fa-circle-check"></i>
                    <h3>Tidak Ada Antrian Tanda Tangan</h3>
                    <p>Belum ada pengajuan yang memerlukan tindakan Ketua.</p>
                </div>
            <?php else: ?>
                <div class="ttd-table-wrap">
                    <table class="ttd-table">
                        <thead>
                            <tr>
                                <th>ID Pengajuan</th>
                                <th>Tujuan</th>
                                <th>Skor Prioritas</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($queue as $q): ?>
                                <tr>
                                    <td>
                                        <span class="ttd-id">
                                            #<?= htmlspecialchars((string) $q['id'], ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars((string) ($q['tujuan'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                    </td>

                                    <td>
                                        <span class="ttd-score">
                                            <?= htmlspecialchars((string) ($q['prioritas_skor'] ?? '-'), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="ttd-actions">
                                            <form method="POST"
                                                onsubmit="return confirm('Proses tanda tangan normal untuk pengajuan ini?');">
                                                <button type="submit" name="ttd_id" value="<?= (int) $q['id'] ?>"
                                                    class="ttd-btn ttd-btn-normal">
                                                    <i class="fa-solid fa-signature"></i>
                                                    TTd Normal
                                                </button>
                                            </form>

                                            <form method="POST"
                                                onsubmit="return confirm('Setujui langsung pengajuan ini tanpa proses tanda tangan normal?');">
                                                <button type="submit" name="skip_id" value="<?= (int) $q['id'] ?>"
                                                    class="ttd-btn ttd-btn-skip">
                                                    <i class="fa-solid fa-check-double"></i>
                                                    Setujui Langsung
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </section>

    </div>
</main>

<?php if ($alertScript !== ''): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof showAlert === 'function') {
                <?= $alertScript ?>
            } else {
                alert('Proses pengajuan selesai. Periksa status dan notifikasi.');
            }
        });
    </script>
<?php endif; ?>

<?php include 'footer.php'; ?>
</summary