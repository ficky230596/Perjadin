
<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

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

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$sppd = null;
$errorMessage = '';

if (!$id || $id < 1) {
    $errorMessage = 'ID pengajuan tidak valid.';
} else {
    try {
        $query = "SELECT
                    p.*,
                    COALESCE(u.nama, p.nama, 'Tidak Diketahui') AS nama_pegawai,
                    COALESCE(u.jabatan, 'Staf') AS jabatan_pegawai,
                    u.wa_phone
                  FROM pengajuan p
                  LEFT JOIN users u ON p.pegawai_id = u.id
                  WHERE p.id = ?
                  LIMIT 1";

        $stmt = $pdo->prepare($query);
        $stmt->execute([$id]);
        $sppd = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$sppd) {
            $errorMessage = 'Data pengajuan SPPD tidak ditemukan.';
        }
    } catch (PDOException $e) {
        error_log('Detail SPPD Ketua: ' . $e->getMessage());
        $errorMessage = 'Terjadi kesalahan saat mengambil data pengajuan.';
    }
}

$statusColors = [
    'selesai' => '#198754',
    'ditolak' => '#dc3545',
    'diajukan' => '#c9972e',
    'paraf_sekwan' => '#0d9488',
    'ttd_ketua' => '#17477f',
];

$currentStatus = $sppd['status'] ?? '';
$badgeColor = $statusColors[$currentStatus] ?? '#64748b';

function detailEsc($value): string
{
    return htmlspecialchars(
        (string) ($value ?? '-'),
        ENT_QUOTES,
        'UTF-8'
    );
}

function detailDate($value): string
{
    if (empty($value)) {
        return '-';
    }

    $timestamp = strtotime((string) $value);

    return $timestamp !== false
        ? date('d-m-Y', $timestamp)
        : '-';
}

include 'header_ketua.php';
include 'sidebar_ketua.php';
?>

<style>
    .detail-page {
        max-width: 1250px;
        margin: 0 auto;
        padding: 10px 0 35px;
        color: #26364a;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .detail-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        margin-bottom: 22px;
    }

    .detail-heading h1 {
        margin: 0 0 8px;
        color: #10294d;
        font-size: 27px;
        font-weight: 800;
    }

    .detail-heading p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .detail-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 9px;
    }

    .detail-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        padding: 11px 16px;
        border: none;
        border-radius: 8px;
        font-family: inherit;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: background .2s ease;
    }

    .detail-btn-print {
        background: #17477f;
        color: #fff;
    }

    .detail-btn-print:hover {
        background: #10294d;
        color: #fff;
    }

    .detail-btn-back {
        background: #fff8e6;
        border: 1px solid #f0dba7;
        color: #8a6415;
    }

    .detail-btn-back:hover {
        background: #fdf0c9;
        color: #70500e;
    }

    .detail-status-banner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 18px;
        padding: 20px 23px;
        margin-bottom: 22px;
        border: 1px solid #e5eaf1;
        border-left: 5px solid var(--status-color);
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(16, 41, 77, .05);
    }

    .detail-status-group {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 10px;
    }

    .detail-status-label {
        color: #64748b;
        font-size: 13px;
    }

    .detail-status-badge {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 7px 12px;
        border-radius: 20px;
        background: var(--status-color);
        color: #fff;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .4px;
    }

    .detail-urgency {
        color: #17477f;
        font-size: 14px;
        font-weight: 800;
    }

    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .detail-card {
        min-width: 0;
        overflow: hidden;
        border: 1px solid #e5eaf1;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 6px 20px rgba(16, 41, 77, .05);
    }

    .detail-card.full-width {
        grid-column: 1 / -1;
    }

    .detail-card-heading {
        display: flex;
        align-items: center;
        gap: 11px;
        padding: 17px 20px;
        border-bottom: 1px solid #e5eaf1;
        background: linear-gradient(110deg, #10294d, #17477f);
        color: #fff;
    }

    .detail-card-heading i {
        color: #f2bd4b;
        font-size: 18px;
    }

    .detail-card-heading h2 {
        margin: 0;
        font-size: 15px;
        font-weight: 700;
    }

    .detail-card-body {
        padding: 16px 20px;
    }

    .detail-info-table {
        width: 100%;
        border-collapse: collapse;
    }

    .detail-info-table th,
    .detail-info-table td {
        padding: 13px 11px;
        border-bottom: 1px solid #edf0f5;
        text-align: left;
        vertical-align: top;
        font-size: 13px;
        line-height: 1.7;
        overflow-wrap: anywhere;
    }

    .detail-info-table tr:last-child th,
    .detail-info-table tr:last-child td {
        border-bottom: none;
    }

    .detail-info-table th {
        width: 39%;
        background: #f8fafc;
        color: #475569;
        font-weight: 700;
    }

    .detail-info-table td {
        color: #26364a;
    }

    .detail-emphasis {
        color: #17477f;
        font-weight: 800;
    }

    .detail-rejection {
        color: #b91c1c;
        font-weight: 700;
    }

    .detail-error {
        padding: 30px 22px;
        margin: 15px 0 30px;
        border: 1px solid #fecaca;
        border-left: 5px solid #dc3545;
        border-radius: 12px;
        background: #fff5f5;
        color: #991b1b;
    }

    .detail-error h2 {
        margin: 0 0 8px;
        font-size: 19px;
    }

    .detail-error p {
        margin: 0 0 18px;
        font-size: 14px;
        line-height: 1.7;
    }

    @media (max-width: 950px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .detail-card.full-width {
            grid-column: auto;
        }
    }

    @media (max-width: 600px) {
        .detail-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .detail-heading h1 {
            font-size: 23px;
        }

        .detail-actions {
            width: 100%;
        }

        .detail-actions .detail-btn {
            flex: 1;
        }

        .detail-status-banner {
            align-items: flex-start;
            flex-direction: column;
            padding: 17px;
        }

        .detail-card-heading {
            padding: 15px;
        }

        .detail-card-body {
            padding: 10px;
        }

        .detail-info-table th,
        .detail-info-table td {
            padding: 10px 8px;
            font-size: 12px;
        }

        .detail-info-table th {
            width: 40%;
        }
    }

    @media print {
        @page {
            size: A4;
            margin: 12mm;
        }

        html,
        body {
            min-height: auto !important;
            height: auto !important;
            display: block !important;
            background: #fff !important;
            color: #000 !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .main-header,
        .sidebar-ketua,
        #sidebarOverlayKetua,
        #sidebarToggle,
        .footer-ketua,
        .detail-actions {
            display: none !important;
        }

        main {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .detail-page {
            max-width: none;
            margin: 0;
            padding: 0;
        }

        .detail-heading h1 {
            font-size: 21px;
            color: #10294d !important;
        }

        .detail-status-banner,
        .detail-card {
            box-shadow: none !important;
            break-inside: avoid;
        }

        .detail-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
        }

        .detail-card-heading {
            background: #10294d !important;
        }

        .detail-info-table th,
        .detail-info-table td {
            padding: 8px;
            font-size: 10px;
        }

        .detail-card-body {
            padding: 8px;
        }
    }
</style>

<main>
    <div class="detail-page">

        <?php if ($errorMessage !== ''): ?>

                <div class="detail-error">
                    <h2>
                        <i class="fa-solid fa-circle-exclamation"></i>
                        Data Tidak Dapat Ditampilkan
                    </h2>

                    <p>
                        <?= detailEsc($errorMessage) ?>
                    </p>

                    <a href="laporan_ketua.php" class="detail-btn detail-btn-back">
                        <i class="fa-solid fa-arrow-left"></i>
                        Kembali ke Laporan
                    </a>
                </div>

        <?php else: ?>

                <div class="detail-heading">
                    <div>
                        <h1>
                            <i class="fa-solid fa-file-lines"></i>
                            Detail Pengajuan SPPD
                        </h1>

                        <p>
                            Informasi lengkap perjalanan dinas
                            <strong>#<?= (int) $sppd['id'] ?></strong>
                        </p>
                    </div>

                    <div class="detail-actions">
                        <button
                            type="button"
                            onclick="window.print()"
                            class="detail-btn detail-btn-print">
                            <i class="fa-solid fa-print"></i>
                            Cetak Dokumen
                        </button>

                        <a
                            href="laporan_ketua.php"
                            class="detail-btn detail-btn-back">
                            <i class="fa-solid fa-arrow-left"></i>
                            Kembali
                        </a>
                    </div>
                </div>

                <section
                    class="detail-status-banner"
                    style="--status-color: <?= detailEsc($badgeColor) ?>;">

                    <div class="detail-status-group">
                        <span class="detail-status-label">Status Saat Ini:</span>

                        <span class="detail-status-badge">
                            <i class="fa-solid fa-circle-check"></i>
                            <?= detailEsc(strtoupper(str_replace('_', ' ', $currentStatus))) ?>
                        </span>
                    </div>

                    <div class="detail-status-group">
                        <span class="detail-status-label">Urgensi:</span>

                        <span class="detail-urgency">
                            <?= detailEsc(ucfirst((string) ($sppd['urgensi'] ?? '-'))) ?>
                        </span>
                    </div>
                </section>

                <div class="detail-grid">

                    <!-- Informasi Pegawai -->
                    <section class="detail-card">
                        <div class="detail-card-heading">
                            <i class="fa-solid fa-user-tie"></i>
                            <h2>Informasi Pegawai</h2>
                        </div>

                        <div class="detail-card-body">
                            <table class="detail-info-table">
                                <tr>
                                    <th>Nama Pemohon</th>
                                    <td><?= detailEsc($sppd['nama_pegawai']) ?></td>
                                </tr>

                                <tr>
                                    <th>Jabatan</th>
                                    <td><?= detailEsc($sppd['jabatan_pegawai']) ?></td>
                                </tr>

                                <tr>
                                    <th>Pangkat / Golongan</th>
                                    <td>
                                        <?= detailEsc($sppd['pangkat'] ?? '-') ?>
                                        /
                                        <?= detailEsc($sppd['golongan'] ?? '-') ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Fraksi / Komisi</th>
                                    <td>
                                        <?= detailEsc($sppd['fraksi'] ?? '-') ?>
                                        /
                                        <?= detailEsc($sppd['komisi'] ?? '-') ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>No. WhatsApp</th>
                                    <td><?= detailEsc($sppd['wa_phone'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </section>

                    <!-- Detail Perjalanan -->
                    <section class="detail-card">
                        <div class="detail-card-heading">
                            <i class="fa-solid fa-route"></i>
                            <h2>Detail Perjalanan Dinas</h2>
                        </div>

                        <div class="detail-card-body">
                            <table class="detail-info-table">
                                <tr>
                                    <th>Tujuan</th>
                                    <td class="detail-emphasis">
                                        <?= detailEsc($sppd['tujuan'] ?? '-') ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Tanggal Berangkat</th>
                                    <td>
                                        <?= detailDate($sppd['tanggal_berangkat'] ?? null) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Tanggal Kembali</th>
                                    <td>
                                        <?= detailDate($sppd['tanggal_kembali'] ?? null) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Alat Angkutan</th>
                                    <td><?= detailEsc($sppd['alat_angkutan'] ?? '-') ?></td>
                                </tr>

                                <tr>
                                    <th>Instansi Anggaran</th>
                                    <td><?= detailEsc($sppd['instansi_anggaran'] ?? '-') ?></td>
                                </tr>
                            </table>
                        </div>
                    </section>

                    <!-- Maksud dan Administrasi -->
                    <section class="detail-card full-width">
                        <div class="detail-card-heading">
                            <i class="fa-solid fa-file-circle-check"></i>
                            <h2>Maksud dan Administrasi Surat</h2>
                        </div>

                        <div class="detail-card-body">
                            <table class="detail-info-table">
                                <tr>
                                    <th style="width: 25%;">Maksud Perjalanan</th>
                                    <td>
                                        <?= nl2br(detailEsc($sppd['alasan'] ?? '-')) ?>
                                    </td>
                                </tr>

                                <tr>
                                    <th>No. SPT / SPD</th>
                                    <td>
                                        SPT:
                                        <strong>
                                            <?= detailEsc($sppd['spt_no'] ?? 'Belum ada') ?>
                                        </strong>
                                        <br>

                                        SPD:
                                        <strong>
                                            <?= detailEsc($sppd['spd_no'] ?? 'Belum ada') ?>
                                        </strong>
                                    </td>
                                </tr>

                                <tr>
                                    <th>Pengikut</th>
                                    <td>
                                        <?= detailEsc($sppd['pengikut'] ?? 'Tidak ada pengikut') ?>
                                    </td>
                                </tr>

                                <?php if (!empty($sppd['alasan_penolakan'])): ?>
                                        <tr>
                                            <th>Alasan Penolakan</th>
                                            <td class="detail-rejection">
                                                <?= nl2br(detailEsc($sppd['alasan_penolakan'])) ?>
                                            </td>
                                        </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </section>

                </div>

        <?php endif; ?>

    </div>
</main>

<?php include 'footer.php'; ?>