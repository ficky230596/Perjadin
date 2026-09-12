<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekwan') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';
include 'header_sekwan.php';
include 'sidebar_sekwan.php';

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "<div class='alert-error'><h3>Error: ID pengajuan tidak valid.</h3></div>";
    include 'footer.php';
    exit;
}

try {
    $query = "SELECT p.*, 
                     COALESCE(u.nama, p.nama, 'Tidak Diketahui') AS nama_pegawai, 
                     COALESCE(u.jabatan, 'Staf') AS jabatan_pegawai,
                     u.wa_phone 
              FROM pengajuan p 
              LEFT JOIN users u ON p.pegawai_id = u.id 
              WHERE p.id = ?";

    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $sppd = $stmt->fetch();

    if (!$sppd) {
        echo "<div class='alert-error'><h3>Error: Data pengajuan SPPD dengan ID $id tidak ditemukan.</h3></div>";
        include 'footer.php';
        exit;
    }

} catch (PDOException $e) {
    echo "<div class='alert-error'><h3>Database Error:</h3><p>" . htmlspecialchars($e->getMessage()) . "</p></div>";
    include 'footer.php';
    exit;
}

// Format warna status badge
$status_colors = [
    'selesai' => '#28a745',
    'ditolak' => '#dc3545',
    'diajukan' => '#ffc107',
    'paraf_sekwan' => '#17a2b8',
    'ttd_ketua' => '#007bff'
];
$current_status = $sppd['status'];
$badge_color = $status_colors[$current_status] ?? '#6c757d';
?>

<div class="main-content-sekwan"
    style="width: 92%; margin: 20px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="page-header"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div>
            <h1>Detail Pengajuan SPPD - Sekwan</h1>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Informasi lengkap perjalanan dinas
                #<?php echo $sppd['id']; ?></p>
        </div>
        <div class="action-top" style="display: flex; gap: 10px;">
            <button onclick="window.print()"
                style="padding: 8px 16px; background: #28a745; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">Cetak
                Dokumen</button>
            <a href="laporan_sekwan.php"
                style="padding: 8px 16px; background: #6c757d; color: #fff; border-radius: 6px; text-decoration: none; font-weight: 500;">Kembali</a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner"
        style="background: #fff; padding: 15px 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; justify-content: space-between; align-items: center; border-left: 6px solid <?php echo $badge_color; ?>;">
        <div>
            <span style="color: #666; margin-right: 10px; font-size: 14px;">Status Saat Ini:</span>
            <span
                style="color: #fff; padding: 5px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; background-color: <?php echo $badge_color; ?>;">
                <?php echo strtoupper(str_replace('_', ' ', $sppd['status'])); ?>
            </span>
        </div>
        <div>
            <span style="color: #666; margin-right: 10px; font-size: 14px;">Urgensi:</span>
            <strong><?php echo ucfirst($sppd['urgensi']); ?></strong>
        </div>
    </div>

    <!-- Grid Informasi -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        <!-- Kolom 1: Informasi Pegawai -->
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3
                style="margin-top: 0; font-size: 16px; color: #007bff; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; margin-bottom: 15px;">
                Informasi Pegawai</h3>
            <table class="table-info" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; width: 35%; font-weight: 600; background: #fafafa;">
                        Nama Pemohon</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['nama_pegawai']); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Jabatan</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['jabatan_pegawai']); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Pangkat / Golongan</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['pangkat'] ?? '-'); ?> /
                        <?php echo htmlspecialchars($sppd['golongan'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Fraksi / Komisi</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['fraksi'] ?: '-'); ?> /
                        <?php echo htmlspecialchars($sppd['komisi'] ?: '-'); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        No. WhatsApp</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['wa_phone'] ?? '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Kolom 2: Detail Perjalanan -->
        <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3
                style="margin-top: 0; font-size: 16px; color: #007bff; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; margin-bottom: 15px;">
                Detail Perjalanan Dinas</h3>
            <table class="table-info" style="width: 100%; border-collapse: collapse;">
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; width: 35%; font-weight: 600; background: #fafafa;">
                        Tujuan</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <strong><?php echo htmlspecialchars($sppd['tujuan']); ?></strong></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Tanggal Berangkat</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['tanggal_berangkat']); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Tanggal Kembali</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['tanggal_kembali']); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Alat Angkutan</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['alat_angkutan']); ?></td>
                </tr>
                <tr>
                    <th
                        style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Instansi Anggaran</th>
                    <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['instansi_anggaran']); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Card Lebar: Maksud & Dokumen -->
    <div style="background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <h3
            style="margin-top: 0; font-size: 16px; color: #007bff; border-bottom: 2px solid #f1f1f1; padding-bottom: 10px; margin-bottom: 15px;">
            Maksud & Administrasi Surat</h3>
        <table class="table-info" style="width: 100%; border-collapse: collapse;">
            <tr>
                <th
                    style="padding: 10px 12px; text-align: left; color: #555; width: 18%; font-weight: 600; background: #fafafa;">
                    Maksud Perjalanan</th>
                <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                    <?php echo nl2br(htmlspecialchars($sppd['alasan'])); ?></td>
            </tr>
            <tr>
                <th style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                    No. SPT / SPD</th>
                <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                    SPT: <strong><?php echo htmlspecialchars($sppd['spt_no'] ?? 'Belum ada'); ?></strong><br>
                    SPD: <strong><?php echo htmlspecialchars($sppd['spd_no'] ?? 'Belum ada'); ?></strong>
                </td>
            </tr>
            <tr>
                <th style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                    Pengikut</th>
                <td style="padding: 10px 12px; color: #333; border-bottom: 1px solid #f8f9fa;">
                    <?php echo htmlspecialchars($sppd['pengikut'] ?? 'Tidak ada pengikut'); ?></td>
            </tr>
            <?php if (!empty($sppd['alasan_penolakan'])): ?>
                <tr>
                    <th style="padding: 10px 12px; text-align: left; color: #555; font-weight: 600; background: #fafafa;">
                        Alasan Penolakan</th>
                    <td style="padding: 10px 12px; color: #dc3545; font-weight: bold; border-bottom: 1px solid #f8f9fa;">
                        <?php echo htmlspecialchars($sppd['alasan_penolakan']); ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<style>
    .alert-error {
        width: 90%;
        margin: 30px auto;
        padding: 20px;
        background: #f8d7da;
        color: #721c24;
        border-radius: 8px;
        border: 1px solid #f5c6cb;
    }

    @media print {

        .action-top,
        .sidebar,
        header,
        .page-header p {
            display: none !important;
        }

        .main-content-sekwan {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        div[style*="background: #fff"] {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            margin-bottom: 15px !important;
        }

        .page-header {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-bottom: 2px solid #000;
            margin-bottom: 15px !important;
        }

        .page-header h1 {
            color: #000 !important;
            font-size: 20px;
        }
    }

    @media (max-width: 768px) {
        div[style*="grid-template-columns"] {
            grid-template-columns: 1fr !important;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }
</style>

<?php include 'footer.php'; ?>