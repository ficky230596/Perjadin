
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

// Ambil filter tanggal.
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Validasi format tanggal.
$validTanggal = static function ($tanggal) {
    if ($tanggal === '') {
        return true;
    }

    $date = DateTime::createFromFormat('!Y-m-d', $tanggal);

    return $date && $date->format('Y-m-d') === $tanggal;
};

$filterError = '';

if (
    !$validTanggal($tanggal_mulai) ||
    !$validTanggal($tanggal_selesai)
) {
    $filterError = 'Format tanggal tidak valid.';
} elseif (
    $tanggal_mulai !== '' &&
    $tanggal_selesai !== '' &&
    $tanggal_mulai > $tanggal_selesai
) {
    $filterError = 'Tanggal mulai tidak boleh melebihi tanggal selesai.';
}

// Ambil laporan SPPD yang telah selesai.
$laporanList = [];

if ($filterError === '') {
    $query = "SELECT * FROM pengajuan WHERE status = 'selesai'";
    $params = [];

    if ($tanggal_mulai !== '') {
        $query .= " AND tanggal_berangkat >= ?";
        $params[] = $tanggal_mulai;
    }

    if ($tanggal_selesai !== '') {
        $query .= " AND tanggal_berangkat <= ?";
        $params[] = $tanggal_selesai;
    }

    $query .= " ORDER BY id DESC";

    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $laporanList = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

include 'header_ketua.php';
include 'sidebar_ketua.php';
?>

<style>
    .laporan-page {
        max-width: 1250px;
        margin: 0 auto;
        padding: 10px 0 35px;
        color: #26364a;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
    }

    .laporan-heading {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 25px;
    }

    .laporan-heading h1 {
        margin: 0 0 8px;
        color: #10294d;
        font-size: 27px;
        font-weight: 800;
    }

    .laporan-heading p {
        margin: 0;
        color: #64748b;
        font-size: 14px;
        line-height: 1.7;
    }

    .laporan-count {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 12px 16px;
        border: 1px solid #f0dba7;
        border-radius: 10px;
        background: #fff9e9;
        color: #17477f;
        font-size: 13px;
        font-weight: 700;
    }

    .laporan-card {
        margin-bottom: 22px;
        overflow: hidden;
        border: 1px solid #e5eaf1;
        border-radius: 14px;
        background: #fff;
        box-shadow: 0 8px 24px rgba(16, 41, 77, .06);
    }

    .laporan-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 17px 22px;
        background: linear-gradient(110deg, #10294d, #17477f);
        color: #fff;
    }

    .laporan-card-header i {
        color: #f2bd4b;
        font-size: 19px;
    }

    .laporan-card-header h2 {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
    }

    .laporan-filter {
        display: flex;
        align-items: flex-end;
        flex-wrap: wrap;
        gap: 16px;
        padding: 22px;
    }

    .filter-field {
        display: flex;
        flex-direction: column;
        flex: 1 1 190px;
        gap: 8px;
    }

    .filter-field label {
        color: #334155;
        font-size: 13px;
        font-weight: 700;
    }

    .filter-field input {
        width: 100%;
        min-height: 42px;
        box-sizing: border-box;
        padding: 10px 12px;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        background: #fff;
        color: #26364a;
        font: inherit;
        font-size: 13px;
    }

    .filter-field input:focus {
        outline: none;
        border-color: #17477f;
        box-shadow: 0 0 0 3px rgba(23, 71, 127, .1);
    }

    .laporan-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        min-height: 42px;
        box-sizing: border-box;
        padding: 10px 16px;
        border: none;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 700;
        text-decoration: none;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn-filter {
        background: #17477f;
        color: #fff;
    }

    .btn-filter:hover {
        background: #10294d;
        color: #fff;
    }

    .btn-reset {
        border: 1px solid #cbd5e1;
        background: #fff;
        color: #475569;
    }

    .btn-reset:hover {
        background: #f1f5f9;
    }

    .btn-print {
        background: #198754;
        color: #fff;
    }

    .btn-print:hover {
        background: #146c43;
        color: #fff;
    }

    .laporan-error {
        margin: 20px 22px 0;
        padding: 13px 15px;
        border: 1px solid #fecaca;
        border-radius: 8px;
        background: #fef2f2;
        color: #b91c1c;
        font-size: 13px;
    }

    .laporan-toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
        padding: 18px 22px;
        border-bottom: 1px solid #e5eaf1;
    }

    .laporan-toolbar p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    .laporan-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .laporan-table {
        width: 100%;
        min-width: 760px;
        margin: 0;
        border-collapse: collapse;
    }

    .laporan-table th {
        padding: 15px 16px;
        background: #f5f7fb;
        color: #17477f;
        text-align: left;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: .4px;
        border-bottom: 1px solid #e5eaf1;
    }

    .laporan-table td {
        padding: 15px 16px;
        color: #334155;
        font-size: 13px;
        border-bottom: 1px solid #edf0f5;
        vertical-align: middle;
    }

    .laporan-table tbody tr:nth-child(even) {
        background: #fafbfd;
    }

    .laporan-table tbody tr:hover {
        background: #f0f6ff;
    }

    .laporan-table tbody tr:last-child td {
        border-bottom: none;
    }

    .laporan-id {
        color: #17477f;
        font-weight: 800;
    }

    .status-selesai {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 20px;
        background: #dcfce7;
        color: #166534;
        font-size: 11px;
        font-weight: 800;
    }

    .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 8px 11px;
        border: 1px solid #d8e4f4;
        border-radius: 7px;
        background: #edf4ff;
        color: #17477f;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-detail:hover {
        background: #dceaff;
        color: #10294d;
    }

    .laporan-empty {
        padding: 45px 20px;
        text-align: center;
    }

    .laporan-empty i {
        margin-bottom: 15px;
        color: #cbd5e1;
        font-size: 42px;
    }

    .laporan-empty h3 {
        margin: 0 0 8px;
        color: #17477f;
        font-size: 17px;
    }

    .laporan-empty p {
        margin: 0;
        color: #64748b;
        font-size: 13px;
    }

    @media (max-width: 800px) {
        .laporan-heading h1 {
            font-size: 23px;
        }

        .laporan-filter {
            padding: 17px;
        }

        .laporan-toolbar {
            padding: 15px 17px;
        }
    }

    @media print {
        @page {
            size: landscape;
            margin: 12mm;
        }

        body {
            display: block !important;
            min-height: auto !important;
            background: #fff !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .main-header,
        .sidebar-ketua,
        #sidebarOverlayKetua,
        #sidebarToggle,
        .laporan-filter-card,
        .laporan-toolbar .btn-print,
        .btn-detail,
        .footer-ketua {
            display: none !important;
        }

        main {
            display: block !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .laporan-page {
            max-width: none;
            padding: 0;
        }

        .laporan-card {
            border: none;
            box-shadow: none;
            overflow: visible;
        }

        .laporan-card-header {
            background: #10294d !important;
        }

        .laporan-table-wrap {
            overflow: visible;
        }

        .laporan-table {
            min-width: 0;
            width: 100%;
        }

        .laporan-table th,
        .laporan-table td {
            padding: 8px;
            font-size: 10px;
        }

        .laporan-table tr {
            break-inside: avoid;
        }

        .laporan-heading h1 {
            font-size: 20px;
        }
    }
</style>

<main>
    <div class="laporan-page">

        <div class="laporan-heading">
            <div>
                <h1>
                    <i class="fa-solid fa-chart-column"></i>
                    Laporan SPPD
                </h1>
                <p>
                    Rekapitulasi pengajuan perjalanan dinas yang telah selesai.
                </p>
            </div>

            <div class="laporan-count">
                <i class="fa-solid fa-file-circle-check"></i>
                <?= count($laporanList) ?> Data Laporan
            </div>
        </div>

        <section class="laporan-card laporan-filter-card">
            <div class="laporan-card-header">
                <i class="fa-solid fa-filter"></i>
                <h2>Filter Periode Laporan</h2>
            </div>

            <?php if ($filterError !== ''): ?>
                <div class="laporan-error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <?= htmlspecialchars($filterError, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>

            <form method="GET" action="laporan_ketua.php" class="laporan-filter">
                <div class="filter-field">
                    <label for="tanggal_mulai">Dari Tanggal</label>
                    <input
                        type="date"
                        id="tanggal_mulai"
                        name="tanggal_mulai"
                        value="<?= htmlspecialchars($tanggal_mulai, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <div class="filter-field">
                    <label for="tanggal_selesai">Sampai Tanggal</label>
                    <input
                        type="date"
                        id="tanggal_selesai"
                        name="tanggal_selesai"
                        value="<?= htmlspecialchars($tanggal_selesai, ENT_QUOTES, 'UTF-8') ?>"
                    >
                </div>

                <button type="submit" class="laporan-btn btn-filter">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Filter
                </button>

                <a href="laporan_ketua.php" class="laporan-btn btn-reset">
                    <i class="fa-solid fa-rotate-left"></i>
                    Reset
                </a>
            </form>
        </section>

        <section class="laporan-card">
            <div class="laporan-card-header">
                <i class="fa-solid fa-table-list"></i>
                <h2>Rekapitulasi Laporan Perjalanan Dinas</h2>
            </div>

            <?php if ($filterError === ''): ?>
                <div class="laporan-toolbar">
                    <p>
                        Menampilkan <?= count($laporanList) ?> pengajuan
                        berstatus selesai.
                    </p>

                    <?php if (!empty($laporanList)): ?>
                        <button
                            type="button"
                            class="laporan-btn btn-print"
                            onclick="window.print()">
                            <i class="fa-solid fa-print"></i>
                            Cetak Laporan
                        </button>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if ($filterError !== ''): ?>

                <div class="laporan-empty">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <h3>Filter Tidak Valid</h3>
                    <p>Periksa kembali rentang tanggal yang dipilih.</p>
                </div>

            <?php elseif (empty($laporanList)): ?>

                <div class="laporan-empty">
                    <i class="fa-regular fa-folder-open"></i>
                    <h3>Tidak Ada Data Laporan</h3>
                    <p>Tidak ditemukan SPPD yang selesai pada periode tersebut.</p>
                </div>

            <?php else: ?>

                <div class="laporan-table-wrap">
                    <table class="laporan-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tujuan</th>
                                <th>Tanggal Berangkat</th>
                                <th>Tanggal Kembali</th>
                                <th>Status</th>
                                <th class="no-print">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($laporanList as $row): ?>
                                <tr>
                                    <td>
                                        <span class="laporan-id">
                                            #<?= (int) $row['id'] ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($row['tujuan'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($row['tanggal_berangkat'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <?= htmlspecialchars(
                                            (string) ($row['tanggal_kembali'] ?? '-'),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>
                                    </td>

                                    <td>
                                        <span class="status-selesai">
                                            <i class="fa-solid fa-circle-check"></i>
                                            <?= htmlspecialchars(
                                                ucfirst((string) $row['status']),
                                                ENT_QUOTES,
                                                'UTF-8'
                                            ) ?>
                                        </span>
                                    </td>

                                    <td class="no-print">
                                        <a
                                            class="btn-detail"
                                            href="detail_sppd.php?id=<?= (int) $row['id'] ?>">
                                            <i class="fa-solid fa-eye"></i>
                                            Detail
                                        </a>
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

<?php include 'footer.php'; ?>