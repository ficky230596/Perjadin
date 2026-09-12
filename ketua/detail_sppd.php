<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'ketua') {
    header('Location: ../index.php');
    exit;
}

include 'header_ketua.php';
include 'sidebar_ketua.php';
include '../config/db.php';

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

<div class="main-content-container">
    <div class="page-header">
        <div>
            <h1>Detail Pengajuan SPPD</h1>
            <p>Informasi lengkap perjalanan dinas #<?php echo $sppd['id']; ?></p>
        </div>
        <div class="action-top">
            <button onclick="window.print()" class="btn btn-print"><i class="fas fa-print"></i> Cetak Dokumen</button>
            <a href="laporan_ketua.php" class="btn btn-back">Kembali</a>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="status-banner" style="border-left-color: <?php echo $badge_color; ?>">
        <div>
            <span class="status-label">Status Saat Ini:</span>
            <span class="status-badge" style="background-color: <?php echo $badge_color; ?>">
                <?php echo strtoupper(str_replace('_', ' ', $sppd['status'])); ?>
            </span>
        </div>
        <div>
            <span class="status-label">Urgensi:</span>
            <strong><?php echo ucfirst($sppd['urgensi']); ?></strong>
        </div>
    </div>

    <!-- Grid Informasi -->
    <div class="grid-container">
        <!-- Kolom 1: Informasi Pegawai -->
        <div class="card">
            <h3><i class="fas fa-user"></i> Informasi Pegawai</h3>
            <table class="table-info">
                <tr>
                    <th>Nama Pemohon</th>
                    <td><?php echo htmlspecialchars($sppd['nama_pegawai']); ?></td>
                </tr>
                <tr>
                    <th>Jabatan</th>
                    <td><?php echo htmlspecialchars($sppd['jabatan_pegawai']); ?></td>
                </tr>
                <tr>
                    <th>Pangkat / Golongan</th>
                    <td><?php echo htmlspecialchars($sppd['pangkat'] ?? '-'); ?> /
                        <?php echo htmlspecialchars($sppd['golongan'] ?? '-'); ?></td>
                </tr>
                <tr>
                    <th>Fraksi / Komisi</th>
                    <td><?php echo htmlspecialchars($sppd['fraksi'] ?: '-'); ?> /
                        <?php echo htmlspecialchars($sppd['komisi'] ?: '-'); ?></td>
                </tr>
                <tr>
                    <th>No. WhatsApp</th>
                    <td><?php echo htmlspecialchars($sppd['wa_phone'] ?? '-'); ?></td>
                </tr>
            </table>
        </div>

        <!-- Kolom 2: Detail Perjalanan -->
        <div class="card">
            <h3><i class="fas fa-map-marked-alt"></i> Detail Perjalanan Dinas</h3>
            <table class="table-info">
                <tr>
                    <th>Tujuan</th>
                    <td><strong><?php echo htmlspecialchars($sppd['tujuan']); ?></strong></td>
                </tr>
                <tr>
                    <th>Tanggal Berangkat</th>
                    <td><?php echo date('d-m-Y', strtotime($sppd['tanggal_berangkat'])); ?></td>
                </tr>
                <tr>
                    <th>Tanggal Kembali</th>
                    <td><?php echo date('d-m-Y', strtotime($sppd['tanggal_kembali'])); ?></td>
                </tr>
                <tr>
                    <th>Alat Angkutan</th>
                    <td><?php echo htmlspecialchars($sppd['alat_angkutan']); ?></td>
                </tr>
                <tr>
                    <th>Instansi Anggaran</th>
                    <td><?php echo htmlspecialchars($sppd['instansi_anggaran']); ?></td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Card Lebar: Maksud & Dokumen -->
    <div class="card full-width">
        <h3><i class="fas fa-file-alt"></i> Maksud & Administrasi Surat</h3>
        <table class="table-info">
            <tr>
                <th style="width: 20%;">Maksud Perjalanan</th>
                <td><?php echo nl2br(htmlspecialchars($sppd['alasan'])); ?></td>
            </tr>
            <tr>
                <th>No. SPT / SPD</th>
                <td>
                    SPT: <strong><?php echo htmlspecialchars($sppd['spt_no'] ?? 'Belum ada'); ?></strong><br>
                    SPD: <strong><?php echo htmlspecialchars($sppd['spd_no'] ?? 'Belum ada'); ?></strong>
                </td>
            </tr>
            <tr>
                <th>Pengikut</th>
                <td><?php echo htmlspecialchars($sppd['pengikut'] ?? 'Tidak ada pengikut'); ?></td>
            </tr>
            <?php if (!empty($sppd['alasan_penolakan'])): ?>
                <tr>
                    <th>Alasan Penolakan</th>
                    <td style="color: #dc3545; font-weight: bold;">
                        <?php echo htmlspecialchars($sppd['alasan_penolakan']); ?></td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</div>

<style>
    .main-content-container {
        width: 92%;
        margin: 20px auto;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }

    .page-header h1 {
        margin: 0;
        font-size: 24px;
        color: #333;
    }

    .page-header p {
        margin: 5px 0 0 0;
        color: #666;
        font-size: 14px;
    }

    .action-top {
        display: flex;
        gap: 10px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        border: none;
        transition: opacity 0.2s;
    }

    .btn:hover {
        opacity: 0.85;
    }

    .btn-print {
        background: #28a745;
        color: #fff;
    }

    .btn-back {
        background: #6c757d;
        color: #fff;
        display: inline-flex;
        align-items: center;
    }

    .status-banner {
        background: #fff;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-left: 6px solid #007bff;
    }

    .status-label {
        color: #666;
        margin-right: 10px;
        font-size: 14px;
    }

    .status-badge {
        color: #fff;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
    }

    .grid-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 20px;
    }

    .card {
        background: #fff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .card.full-width {
        grid-column: span 2;
    }

    .card h3 {
        margin-top: 0;
        font-size: 16px;
        color: #007bff;
        border-bottom: 2px solid #f1f1f1;
        padding-bottom: 10px;
        margin-bottom: 15px;
    }

    .table-info {
        width: 100%;
        border-collapse: collapse;
    }

    .table-info th,
    .table-info td {
        padding: 10px 12px;
        text-align: left;
        font-size: 14px;
        border-bottom: 1px solid #f8f9fa;
    }

    .table-info th {
        color: #555;
        width: 35%;
        font-weight: 600;
        background: #fafafa;
        border-radius: 4px 0 0 4px;
    }

    .table-info td {
        color: #333;
    }

    .alert-error {
        width: 90%;
        margin: 30px auto;
        padding: 20px;
        background: #f8d7da;
        color: #721c24;
        border-radius: 8px;
        border: 1px solid #f5c6cb;
    }

    /* Print Optimization */
    @media print {

        .action-top,
        .sidebar,
        header,
        .page-header p {
            display: none !important;
        }

        .main-content-container {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
            margin-bottom: 15px;
        }

        .page-header {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
        }

        .page-header h1 {
            font-size: 20px;
            color: #000;
        }
    }

    @media (max-width: 768px) {
        .grid-container {
            grid-template-columns: 1fr;
        }

        .card.full-width {
            grid-column: span 1;
        }

        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
    }
</style>

<?php include 'footer.php'; ?>