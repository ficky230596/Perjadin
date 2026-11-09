<?php

session_start();

/*
|--------------------------------------------------------------------------
| Validasi Login Bagian Umum
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'umum') {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';

/*
|--------------------------------------------------------------------------
| Ambil Data Pengajuan
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        p.*,
        u.nama AS nama_pemohon,
        u.wa_phone
    FROM pengajuan p
    LEFT JOIN users u ON u.id = p.pegawai_id
    ORDER BY p.id DESC
");

$stmt->execute();

$semuaPengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Pisahkan Daftar Aktif dan Riwayat
|--------------------------------------------------------------------------
*/

$daftarPengajuan = [];
$riwayatPengajuan = [];

foreach ($semuaPengajuan as $item) {

    $status = strtolower(trim($item['status'] ?? ''));

    /*
    | Status yang dianggap sudah masuk riwayat
    */
    if (in_array($status, ['selesai', 'ditolak'], true)) {
        $riwayatPengajuan[] = $item;
    } else {
        $daftarPengajuan[] = $item;
    }
}

/*
|--------------------------------------------------------------------------
| Helper Status
|--------------------------------------------------------------------------
*/

function statusLabel($status)
{
    $status = strtolower(trim($status));

    $labels = [
        'diajukan' => 'Diajukan',
        'draft_sppd' => 'Draft SPPD',
        'ttd_ketua' => 'TTD Ketua',
        'selesai' => 'Selesai',
        'ditolak' => 'Ditolak'
    ];

    return $labels[$status] ?? ucwords(str_replace('_', ' ', $status));
}

function statusClass($status)
{
    $status = strtolower(trim($status));

    switch ($status) {
        case 'diajukan':
            return 'status-diajukan';

        case 'draft_sppd':
            return 'status-draft';

        case 'ttd_ketua':
            return 'status-ketua';

        case 'selesai':
            return 'status-selesai';

        case 'ditolak':
            return 'status-ditolak';

        default:
            return 'status-default';
    }
}

function statusIcon($status)
{
    $status = strtolower(trim($status));

    switch ($status) {
        case 'diajukan':
            return 'fa-paper-plane';

        case 'draft_sppd':
            return 'fa-file-pen';

        case 'ttd_ketua':
            return 'fa-signature';

        case 'selesai':
            return 'fa-circle-check';

        case 'ditolak':
            return 'fa-circle-xmark';

        default:
            return 'fa-circle-info';
    }
}

/*
|--------------------------------------------------------------------------
| Format Tanggal
|--------------------------------------------------------------------------
*/

function formatTanggal($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    $time = strtotime($tanggal);

    if (!$time) {
        return htmlspecialchars($tanggal);
    }

    $bulan = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des'
    ];

    return date('d', $time) . ' ' .
        $bulan[(int) date('n', $time)] . ' ' .
        date('Y', $time);
}

?>

<?php include 'header_umum.php'; ?>

<?php include 'sidebar_umum.php'; ?>

<main class="main-content-umum">

    <div class="pengajuan-page">

        <!-- =========================================================
             PAGE HEADER
        ========================================================== -->

        <div class="page-heading">

            <div class="page-heading-left">

                <div class="page-icon">
                    <i class="fa-solid fa-folder-open"></i>
                </div>

                <div>
                    <span class="page-kicker">
                        Bagian Umum
                    </span>

                    <h1>
                        Daftar Pengajuan
                    </h1>

                    <p>
                        Kelola pengajuan perjalanan dinas dan pantau riwayat
                        proses pengajuan.
                    </p>
                </div>

            </div>

            <a href="umum_dashboard.php" class="back-button">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>

        </div>


        <!-- =========================================================
             STATISTIK
        ========================================================== -->

        <div class="summary-grid">

            <div class="summary-card">

                <div class="summary-icon blue">
                    <i class="fa-solid fa-list-check"></i>
                </div>

                <div class="summary-info">
                    <span>Pengajuan Aktif</span>
                    <strong>
                        <?php echo count($daftarPengajuan); ?>
                    </strong>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon gold">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div class="summary-info">
                    <span>Riwayat</span>
                    <strong>
                        <?php echo count($riwayatPengajuan); ?>
                    </strong>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon green">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div class="summary-info">
                    <span>Selesai</span>
                    <strong>
                        <?php

                        $jumlahSelesai = 0;

                        foreach ($riwayatPengajuan as $item) {
                            if (($item['status'] ?? '') === 'selesai') {
                                $jumlahSelesai++;
                            }
                        }

                        echo $jumlahSelesai;

                        ?>
                    </strong>
                </div>

            </div>


            <div class="summary-card">

                <div class="summary-icon red">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>

                <div class="summary-info">
                    <span>Ditolak</span>
                    <strong>
                        <?php

                        $jumlahDitolak = 0;

                        foreach ($riwayatPengajuan as $item) {
                            if (($item['status'] ?? '') === 'ditolak') {
                                $jumlahDitolak++;
                            }
                        }

                        echo $jumlahDitolak;

                        ?>
                    </strong>
                </div>

            </div>

        </div>


        <!-- =========================================================
             TAB
        ========================================================== -->

        <div class="tab-card">

            <div class="tab-header">

                <button type="button" class="tab-button active" onclick="showTab('aktif', this)">
                    <i class="fa-solid fa-list-check"></i>
                    <span>Daftar Pengajuan</span>

                    <b>
                        <?php echo count($daftarPengajuan); ?>
                    </b>
                </button>


                <button type="button" class="tab-button" onclick="showTab('riwayat', this)">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    <span>Riwayat</span>

                    <b>
                        <?php echo count($riwayatPengajuan); ?>
                    </b>
                </button>

            </div>

        </div>


        <!-- =========================================================
             TAB AKTIF
        ========================================================== -->

        <section id="tabAktif" class="tab-content active">

            <div class="content-card">

                <div class="content-card-header">

                    <div>

                        <span class="section-label">
                            PENGAJUAN AKTIF
                        </span>

                        <h2>
                            Daftar Pengajuan
                        </h2>

                    </div>

                    <div class="header-tools">

                        <div class="search-box">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input type="text" id="searchAktif" placeholder="Cari pemohon atau tujuan..."
                                onkeyup="searchTable('aktif')">

                        </div>

                    </div>

                </div>


                <?php if (empty($daftarPengajuan)): ?>

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fa-solid fa-folder-open"></i>
                        </div>

                        <h3>
                            Tidak Ada Pengajuan Aktif
                        </h3>

                        <p>
                            Belum terdapat pengajuan yang perlu diproses
                            oleh Bagian Umum.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-wrapper">

                        <table class="pengajuan-table" id="tableAktif">

                            <thead>

                                <tr>

                                    <th width="55">
                                        No
                                    </th>

                                    <th>
                                        Pemohon
                                    </th>

                                    <th>
                                        Tujuan
                                    </th>

                                    <th>
                                        Perjalanan
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th width="150">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($daftarPengajuan as $index => $item): ?>

                                    <?php
                                    $status = strtolower(trim($item['status'] ?? ''));
                                    ?>

                                    <tr>

                                        <td class="number-cell">
                                            <?php echo $index + 1; ?>
                                        </td>


                                        <td>

                                            <div class="person-cell">

                                                <div class="person-avatar">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>

                                                <div class="person-info">

                                                    <strong>
                                                        <?php
                                                        echo htmlspecialchars(
                                                            $item['nama_pemohon'] ?? '-'
                                                        );
                                                        ?>
                                                    </strong>

                                                    <span>
                                                        ID Pengajuan #<?php echo (int) $item['id']; ?>
                                                    </span>

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            <div class="destination-cell">

                                                <i class="fa-solid fa-location-dot"></i>

                                                <span>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['tujuan'] ?? '-'
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        </td>


                                        <td>

                                            <div class="date-cell">

                                                <strong>
                                                    <?php
                                                    echo formatTanggal(
                                                        $item['tanggal_berangkat'] ?? null
                                                    );
                                                    ?>
                                                </strong>

                                                <span>
                                                    s/d
                                                    <?php
                                                    echo formatTanggal(
                                                        $item['tanggal_kembali'] ?? null
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        </td>


                                        <td>

                                            <span class="status-badge <?php echo statusClass($status); ?>">

                                                <i class="fa-solid <?php echo statusIcon($status); ?>"></i>

                                                <?php echo htmlspecialchars(statusLabel($status)); ?>

                                            </span>

                                        </td>


                                        <td>

                                            <div class="action-buttons">

                                                <?php if ($status === 'ttd_ketua'): ?>

                                                    <a href="umum_cap.php?id=<?php echo (int) $item['id']; ?>"
                                                        class="table-action primary" title="Proses Cap">
                                                        <i class="fa-solid fa-stamp"></i>
                                                        <span>Proses Cap</span>
                                                    </a>

                                                <?php elseif ($status === 'diajukan'): ?>

                                                    <a href="umum_buat.php?id=<?php echo (int) $item['id']; ?>"
                                                        class="table-action primary" title="Buat Draft">
                                                        <i class="fa-solid fa-file-pen"></i>
                                                        <span>Buat Draft</span>
                                                    </a>

                                                <?php else: ?>

                                                    <button type="button" class="table-action secondary" onclick="showDetail(
                                                            <?php echo htmlspecialchars(
                                                                json_encode(
                                                                    [
                                                                        'id' => $item['id'],
                                                                        'nama' => $item['nama_pemohon'] ?? '-',
                                                                        'tujuan' => $item['tujuan'] ?? '-',
                                                                        'status' => statusLabel($status)
                                                                    ],
                                                                    JSON_HEX_TAG |
                                                                    JSON_HEX_APOS |
                                                                    JSON_HEX_QUOT |
                                                                    JSON_HEX_AMP
                                                                )
                                                            ); ?>
                                                        )">
                                                        <i class="fa-solid fa-eye"></i>
                                                        <span>Lihat</span>
                                                    </button>

                                                <?php endif; ?>

                                            </div>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </section>


        <!-- =========================================================
             TAB RIWAYAT
        ========================================================== -->

        <section id="tabRiwayat" class="tab-content">

            <div class="content-card">

                <div class="content-card-header">

                    <div>

                        <span class="section-label">
                            RIWAYAT PROSES
                        </span>

                        <h2>
                            Riwayat Pengajuan
                        </h2>

                    </div>

                    <div class="header-tools">

                        <div class="search-box">

                            <i class="fa-solid fa-magnifying-glass"></i>

                            <input type="text" id="searchRiwayat" placeholder="Cari pemohon atau tujuan..."
                                onkeyup="searchTable('riwayat')">

                        </div>

                    </div>

                </div>


                <?php if (empty($riwayatPengajuan)): ?>

                    <div class="empty-state">

                        <div class="empty-icon">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>

                        <h3>
                            Belum Ada Riwayat
                        </h3>

                        <p>
                            Riwayat pengajuan yang telah selesai atau ditolak
                            akan muncul di sini.
                        </p>

                    </div>

                <?php else: ?>

                    <div class="table-wrapper">

                        <table class="pengajuan-table" id="tableRiwayat">

                            <thead>

                                <tr>

                                    <th width="55">
                                        No
                                    </th>

                                    <th>
                                        Pemohon
                                    </th>

                                    <th>
                                        Tujuan
                                    </th>

                                    <th>
                                        Perjalanan
                                    </th>

                                    <th>
                                        Status
                                    </th>

                                    <th width="110">
                                        Detail
                                    </th>

                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($riwayatPengajuan as $index => $item): ?>

                                    <?php
                                    $status = strtolower(trim($item['status'] ?? ''));
                                    ?>

                                    <tr>

                                        <td class="number-cell">
                                            <?php echo $index + 1; ?>
                                        </td>


                                        <td>

                                            <div class="person-cell">

                                                <div class="person-avatar">
                                                    <i class="fa-solid fa-user"></i>
                                                </div>

                                                <div class="person-info">

                                                    <strong>
                                                        <?php
                                                        echo htmlspecialchars(
                                                            $item['nama_pemohon'] ?? '-'
                                                        );
                                                        ?>
                                                    </strong>

                                                    <span>
                                                        ID Pengajuan #<?php echo (int) $item['id']; ?>
                                                    </span>

                                                </div>

                                            </div>

                                        </td>


                                        <td>

                                            <div class="destination-cell">

                                                <i class="fa-solid fa-location-dot"></i>

                                                <span>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['tujuan'] ?? '-'
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        </td>


                                        <td>

                                            <div class="date-cell">

                                                <strong>
                                                    <?php
                                                    echo formatTanggal(
                                                        $item['tanggal_berangkat'] ?? null
                                                    );
                                                    ?>
                                                </strong>

                                                <span>
                                                    s/d
                                                    <?php
                                                    echo formatTanggal(
                                                        $item['tanggal_kembali'] ?? null
                                                    );
                                                    ?>
                                                </span>

                                            </div>

                                        </td>


                                        <td>

                                            <span class="status-badge <?php echo statusClass($status); ?>">

                                                <i class="fa-solid <?php echo statusIcon($status); ?>"></i>

                                                <?php echo htmlspecialchars(statusLabel($status)); ?>

                                            </span>

                                        </td>


                                        <td>

                                            <button type="button" class="table-action secondary" onclick="showDetail(
                                                    <?php echo htmlspecialchars(
                                                        json_encode(
                                                            [
                                                                'id' => $item['id'],
                                                                'nama' => $item['nama_pemohon'] ?? '-',
                                                                'tujuan' => $item['tujuan'] ?? '-',
                                                                'tanggal_berangkat' => formatTanggal($item['tanggal_berangkat'] ?? null),
                                                                'tanggal_kembali' => formatTanggal($item['tanggal_kembali'] ?? null),
                                                                'status' => statusLabel($status)
                                                            ],
                                                            JSON_HEX_TAG |
                                                            JSON_HEX_APOS |
                                                            JSON_HEX_QUOT |
                                                            JSON_HEX_AMP
                                                        )
                                                    ); ?>
                                                )">
                                                <i class="fa-solid fa-eye"></i>
                                                <span>Lihat</span>
                                            </button>

                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

            </div>

        </section>

    </div>

</main>

<?php include 'footer.php'; ?>


<style>
    /* =========================================================
   UMUM PENGAJUAN
========================================================= */

    .main-content-umum {
        margin-left: 250px;
        padding: 92px 25px 90px;
        min-height: 100vh;
        background: #f6f8fb;
        font-family: "Inter", "Segoe UI", Arial, sans-serif;
    }

    .pengajuan-page {
        max-width: 1250px;
        margin: 0 auto;
    }


    /* =========================================================
   PAGE HEADING
========================================================= */

    .page-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 22px;
    }

    .page-heading-left {
        display: flex;
        align-items: center;
        gap: 14px;
        min-width: 0;
    }

    .page-icon {
        width: 50px;
        height: 50px;
        min-width: 50px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 13px;

        background: #10294d;
        color: #f2bd4b;

        font-size: 20px;

        box-shadow: 0 7px 18px rgba(16, 41, 77, .14);
    }

    .page-kicker {
        display: block;
        margin-bottom: 3px;

        color: #f2bd4b;

        font-size: 10px;
        font-weight: 800;

        letter-spacing: .8px;
        text-transform: uppercase;
    }

    .page-heading h1 {
        margin: 0;

        color: #10294d;

        font-size: 24px;
        font-weight: 800;
    }

    .page-heading p {
        margin: 4px 0 0;

        color: #64748b;

        font-size: 12px;
    }

    .back-button {
        display: inline-flex;
        align-items: center;
        gap: 8px;

        min-height: 40px;

        padding: 9px 14px;

        border: 1px solid #dbe3ec;
        border-radius: 9px;

        background: #fff;
        color: #475569;

        text-decoration: none;

        font-size: 11px;
        font-weight: 700;

        transition: .2s ease;
    }

    .back-button:hover {
        border-color: #10294d;
        color: #10294d;
        transform: translateY(-1px);
    }


    /* =========================================================
   SUMMARY
========================================================= */

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 13px;
        margin-bottom: 18px;
    }

    .summary-card {
        display: flex;
        align-items: center;
        gap: 12px;

        min-height: 82px;
        padding: 15px;

        background: #fff;

        border: 1px solid #e4e9f0;
        border-radius: 13px;

        box-shadow: 0 5px 18px rgba(16, 41, 77, .045);
    }

    .summary-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 10px;

        font-size: 15px;
    }

    .summary-icon.blue {
        background: #edf4fb;
        color: #17477f;
    }

    .summary-icon.gold {
        background: #fff7df;
        color: #a87900;
    }

    .summary-icon.green {
        background: #edf8f1;
        color: #21864a;
    }

    .summary-icon.red {
        background: #fff0f0;
        color: #c03939;
    }

    .summary-info span {
        display: block;

        margin-bottom: 3px;

        color: #94a3b8;

        font-size: 9px;
        font-weight: 700;
    }

    .summary-info strong {
        color: #10294d;

        font-size: 21px;
        font-weight: 800;
    }


    /* =========================================================
   TABS
========================================================= */

    .tab-card {
        margin-bottom: 15px;

        background: #fff;

        border: 1px solid #e4e9f0;
        border-radius: 13px;

        box-shadow: 0 5px 18px rgba(16, 41, 77, .045);
    }

    .tab-header {
        display: flex;
        align-items: center;

        padding: 6px;

        gap: 5px;
    }

    .tab-button {
        position: relative;

        display: inline-flex;
        align-items: center;
        gap: 8px;

        min-height: 40px;

        padding: 8px 14px;

        border: 0;
        border-radius: 8px;

        background: transparent;
        color: #64748b;

        font-family: inherit;
        font-size: 11px;
        font-weight: 700;

        cursor: pointer;

        transition: .2s ease;
    }

    .tab-button i {
        font-size: 11px;
    }

    .tab-button b {
        min-width: 20px;
        height: 20px;

        display: inline-flex;
        align-items: center;
        justify-content: center;

        padding: 0 5px;

        border-radius: 20px;

        background: #f1f5f9;
        color: #64748b;

        font-size: 9px;
    }

    .tab-button:hover {
        color: #10294d;
        background: #f8fafc;
    }

    .tab-button.active {
        background: #10294d;
        color: #fff;
    }

    .tab-button.active b {
        background: #f2bd4b;
        color: #10294d;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }


    /* =========================================================
   CONTENT CARD
========================================================= */

    .content-card {
        background: #fff;

        border: 1px solid #e4e9f0;
        border-radius: 14px;

        box-shadow: 0 5px 18px rgba(16, 41, 77, .05);

        overflow: hidden;
    }

    .content-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 15px;

        padding: 18px 20px;

        border-bottom: 1px solid #edf1f5;
    }

    .section-label {
        display: block;

        margin-bottom: 3px;

        color: #94a3b8;

        font-size: 9px;
        font-weight: 800;

        letter-spacing: .8px;
    }

    .content-card-header h2 {
        margin: 0;

        color: #10294d;

        font-size: 16px;
        font-weight: 800;
    }


    /* =========================================================
   SEARCH
========================================================= */

    .header-tools {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .search-box {
        position: relative;
    }

    .search-box i {
        position: absolute;

        left: 11px;
        top: 50%;

        transform: translateY(-50%);

        color: #94a3b8;

        font-size: 10px;
    }

    .search-box input {
        width: 235px;
        height: 36px;

        padding: 0 12px 0 30px;

        border: 1px solid #dbe3ec;
        border-radius: 8px;

        outline: none;

        background: #f8fafc;
        color: #334155;

        font-family: inherit;
        font-size: 10px;

        transition: .2s ease;
    }

    .search-box input:focus {
        border-color: #17477f;
        background: #fff;

        box-shadow: 0 0 0 3px rgba(23, 71, 127, .07);
    }

    .search-box input::placeholder {
        color: #a0aec0;
    }


    /* =========================================================
   TABLE
========================================================= */

    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }

    .pengajuan-table {
        width: 100%;
        border-collapse: collapse;

        min-width: 850px;
    }

    .pengajuan-table thead th {
        padding: 12px 15px;

        background: #f8fafc;

        border-bottom: 1px solid #e6ebf1;

        color: #64748b;

        font-size: 9px;
        font-weight: 800;

        letter-spacing: .3px;

        text-align: left;
        white-space: nowrap;
    }

    .pengajuan-table tbody td {
        padding: 13px 15px;

        border-bottom: 1px solid #edf1f5;

        color: #475569;

        font-size: 10px;

        vertical-align: middle;
    }

    .pengajuan-table tbody tr {
        transition: .15s ease;
    }

    .pengajuan-table tbody tr:hover {
        background: #fafcff;
    }

    .pengajuan-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .number-cell {
        color: #94a3b8 !important;
        font-weight: 700;
    }


    /* =========================================================
   PERSON
========================================================= */

    .person-cell {
        display: flex;
        align-items: center;
        gap: 10px;

        min-width: 180px;
    }

    .person-avatar {
        width: 34px;
        height: 34px;
        min-width: 34px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 9px;

        background: #edf4fb;
        color: #17477f;

        font-size: 11px;
    }

    .person-info {
        min-width: 0;
    }

    .person-info strong {
        display: block;

        margin-bottom: 3px;

        color: #334155;

        font-size: 11px;
        font-weight: 800;

        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .person-info span {
        display: block;

        color: #94a3b8;

        font-size: 8px;
    }


    /* =========================================================
   DESTINATION
========================================================= */

    .destination-cell {
        display: flex;
        align-items: flex-start;
        gap: 7px;

        max-width: 190px;
    }

    .destination-cell i {
        margin-top: 2px;

        color: #17477f;

        font-size: 10px;
    }

    .destination-cell span {
        color: #475569;

        font-size: 10px;
        line-height: 1.4;
    }


    /* =========================================================
   DATE
========================================================= */

    .date-cell strong {
        display: block;

        margin-bottom: 2px;

        color: #475569;

        font-size: 10px;
    }

    .date-cell span {
        color: #94a3b8;

        font-size: 8px;
    }


    /* =========================================================
   STATUS
========================================================= */

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;

        padding: 6px 9px;

        border-radius: 20px;

        font-size: 8px;
        font-weight: 800;

        white-space: nowrap;
    }

    .status-badge i {
        font-size: 8px;
    }

    .status-diajukan {
        background: #eef5ff;
        color: #2563a6;
    }

    .status-draft {
        background: #f3efff;
        color: #6d4bb1;
    }

    .status-ketua {
        background: #fff7df;
        color: #996f00;
    }

    .status-selesai {
        background: #edf8f1;
        color: #21864a;
    }

    .status-ditolak {
        background: #fff0f0;
        color: #c03939;
    }

    .status-default {
        background: #f1f5f9;
        color: #64748b;
    }


    /* =========================================================
   ACTION
========================================================= */

    .action-buttons {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .table-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;

        min-height: 31px;

        padding: 6px 9px;

        border-radius: 7px;

        font-family: inherit;
        font-size: 9px;
        font-weight: 700;

        text-decoration: none;

        cursor: pointer;

        transition: .2s ease;
    }

    .table-action.primary {
        border: 1px solid #10294d;

        background: #10294d;
        color: #fff;
    }

    .table-action.primary:hover {
        background: #17477f;
        border-color: #17477f;

        transform: translateY(-1px);
    }

    .table-action.secondary {
        border: 1px solid #dbe3ec;

        background: #fff;
        color: #475569;
    }

    .table-action.secondary:hover {
        border-color: #10294d;
        color: #10294d;

        background: #f8fafc;
    }


    /* =========================================================
   EMPTY STATE
========================================================= */

    .empty-state {
        padding: 65px 20px;

        text-align: center;
    }

    .empty-icon {
        width: 58px;
        height: 58px;

        margin: 0 auto 13px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 14px;

        background: #f1f5f9;
        color: #94a3b8;

        font-size: 21px;
    }

    .empty-state h3 {
        margin: 0 0 5px;

        color: #334155;

        font-size: 13px;
        font-weight: 800;
    }

    .empty-state p {
        max-width: 420px;

        margin: 0 auto;

        color: #94a3b8;

        font-size: 10px;
        line-height: 1.6;
    }


    /* =========================================================
   RESPONSIVE
========================================================= */

    @media (max-width: 1050px) {

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
        }

    }

    @media (max-width: 768px) {

        .main-content-umum {
            margin-left: 0;
            padding: 82px 14px 78px;
        }

        .page-heading {
            align-items: flex-start;
        }

        .page-heading h1 {
            font-size: 20px;
        }

        .page-heading p {
            font-size: 10px;
        }

        .page-icon {
            width: 44px;
            height: 44px;
            min-width: 44px;

            font-size: 17px;
        }

        .back-button {
            min-height: 36px;
            padding: 8px 10px;
        }

        .back-button span {
            display: none;
        }

        .content-card-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .header-tools {
            width: 100%;
        }

        .search-box {
            width: 100%;
        }

        .search-box input {
            width: 100%;
        }

    }

    @media (max-width: 600px) {

        .page-heading {
            flex-direction: column;
            gap: 12px;
        }

        .back-button {
            align-self: flex-start;
        }

        .summary-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 9px;
        }

        .summary-card {
            min-height: 72px;
            padding: 11px;
        }

        .summary-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
        }

        .summary-info strong {
            font-size: 18px;
        }

        .tab-button {
            flex: 1;
            justify-content: center;

            padding-left: 8px;
            padding-right: 8px;
        }

        .tab-button span {
            font-size: 9px;
        }

        .content-card-header {
            padding: 15px;
        }

    }

    @media (max-width: 400px) {

        .main-content-umum {
            padding-left: 10px;
            padding-right: 10px;
        }

        .page-heading-left {
            gap: 10px;
        }

        .page-icon {
            width: 40px;
            height: 40px;
            min-width: 40px;

            font-size: 15px;
        }

        .page-heading h1 {
            font-size: 18px;
        }

        .summary-grid {
            gap: 7px;
        }

        .summary-card {
            gap: 7px;
            padding: 9px;
        }

        .summary-icon {
            width: 32px;
            height: 32px;
            min-width: 32px;

            font-size: 12px;
        }

        .summary-info span {
            font-size: 8px;
        }

        .summary-info strong {
            font-size: 16px;
        }

        .tab-button {
            gap: 5px;
        }

        .tab-button i {
            font-size: 9px;
        }

    }


    /* =========================================================
   PRINT
========================================================= */

    @media print {

        .main-content-umum {
            margin-left: 0;
            padding: 0;
            background: #fff;
        }

        .page-heading,
        .summary-grid,
        .tab-card,
        .header-tools,
        .action-buttons {
            display: none !important;
        }

        .content-card {
            border: 0;
            box-shadow: none;
        }

    }
</style>


<script>

    /*
    |--------------------------------------------------------------------------
    | Tab
    |--------------------------------------------------------------------------
    */

    function showTab(tab, button) {

        document.querySelectorAll('.tab-button').forEach(function (item) {
            item.classList.remove('active');
        });

        document.querySelectorAll('.tab-content').forEach(function (item) {
            item.classList.remove('active');
        });

        button.classList.add('active');

        if (tab === 'aktif') {
            document.getElementById('tabAktif').classList.add('active');
        }

        if (tab === 'riwayat') {
            document.getElementById('tabRiwayat').classList.add('active');
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Search Table
    |--------------------------------------------------------------------------
    */

    function searchTable(type) {

        const input = document.getElementById(
            type === 'aktif'
                ? 'searchAktif'
                : 'searchRiwayat'
        );

        const table = document.getElementById(
            type === 'aktif'
                ? 'tableAktif'
                : 'tableRiwayat'
        );

        if (!input || !table) {
            return;
        }

        const keyword = input.value.toLowerCase().trim();

        const rows = table.querySelectorAll('tbody tr');

        rows.forEach(function (row) {

            const text = row.textContent.toLowerCase();

            row.style.display = text.includes(keyword)
                ? ''
                : 'none';

        });
    }


    /*
    |--------------------------------------------------------------------------
    | Detail Pengajuan
    |--------------------------------------------------------------------------
    */

    function showDetail(data) {

        if (typeof Swal === 'undefined') {
            alert(
                'Pengajuan #' +
                data.id +
                '\n\n' +
                'Pemohon: ' +
                data.nama +
                '\n' +
                'Tujuan: ' +
                data.tujuan +
                '\n' +
                'Status: ' +
                data.status
            );

            return;
        }

        let html = `
        <div style="
            text-align:left;
            font-size:12px;
            line-height:1.7;
        ">

            <div style="
                display:flex;
                align-items:center;
                gap:10px;
                padding:12px;
                margin-bottom:12px;
                background:#f4f8fc;
                border-radius:10px;
            ">

                <div style="
                    width:36px;
                    height:36px;
                    display:flex;
                    align-items:center;
                    justify-content:center;
                    border-radius:9px;
                    background:#10294d;
                    color:#f2bd4b;
                ">
                    <i class="fa-solid fa-file-lines"></i>
                </div>

                <div>
                    <strong style="
                        display:block;
                        color:#10294d;
                    ">
                        Pengajuan #${escapeHtml(String(data.id))}
                    </strong>

                    <span style="
                        color:#94a3b8;
                        font-size:10px;
                    ">
                        Detail pengajuan perjalanan dinas
                    </span>
                </div>

            </div>


            <div style="
                display:grid;
                gap:9px;
            ">

                <div>
                    <small style="color:#94a3b8;">
                        Pemohon
                    </small>

                    <div style="
                        color:#334155;
                        font-weight:700;
                    ">
                        ${escapeHtml(data.nama || '-')}
                    </div>
                </div>


                <div>
                    <small style="color:#94a3b8;">
                        Tujuan
                    </small>

                    <div style="
                        color:#334155;
                        font-weight:700;
                    ">
                        ${escapeHtml(data.tujuan || '-')}
                    </div>
                </div>


                ${data.tanggal_berangkat
                ? `
                        <div>
                            <small style="color:#94a3b8;">
                                Perjalanan
                            </small>

                            <div style="
                                color:#334155;
                                font-weight:700;
                            ">
                                ${escapeHtml(data.tanggal_berangkat)}
                                s/d
                                ${escapeHtml(data.tanggal_kembali || '-')}
                            </div>
                        </div>
                        `
                : ''
            }


                <div>
                    <small style="color:#94a3b8;">
                        Status
                    </small>

                    <div style="
                        color:#334155;
                        font-weight:700;
                    ">
                        ${escapeHtml(data.status || '-')}
                    </div>
                </div>

            </div>

        </div>
    `;

        Swal.fire({

            title: 'Detail Pengajuan',

            html: html,

            confirmButtonText: 'Tutup',

            confirmButtonColor: '#10294d',

            width: 470

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');

    }

</script>

</body>

</html>