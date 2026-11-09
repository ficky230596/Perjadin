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
| Ambil Semua Pengajuan
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
$data_pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);



$stmt->execute();
$data_pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Pisahkan Pengajuan Aktif dan Riwayat
|--------------------------------------------------------------------------
*/
$pengajuan_aktif = [];
$riwayat_pengajuan = [];

foreach ($data_pengajuan as $item) {

    $status = strtolower(trim($item['status'] ?? ''));

    if (in_array($status, ['selesai', 'ditolak'], true)) {
        $riwayat_pengajuan[] = $item;
    } else {
        $pengajuan_aktif[] = $item;
    }
}

/*
|--------------------------------------------------------------------------
| Fungsi Tampilan Status
|--------------------------------------------------------------------------
*/
function statusLabel($status)
{
    switch (strtolower($status)) {

        case 'diajukan':
            return [
                'label' => 'Diajukan',
                'class' => 'status-diajukan',
                'icon' => 'fa-paper-plane'
            ];

        case 'draft_sppd':
            return [
                'label' => 'Draft SPPD',
                'class' => 'status-draft',
                'icon' => 'fa-file-pen'
            ];

        case 'ttd_ketua':
            return [
                'label' => 'TTD Ketua',
                'class' => 'status-ttd',
                'icon' => 'fa-signature'
            ];

        case 'selesai':
            return [
                'label' => 'Selesai',
                'class' => 'status-selesai',
                'icon' => 'fa-circle-check'
            ];

        case 'ditolak':
            return [
                'label' => 'Ditolak',
                'class' => 'status-ditolak',
                'icon' => 'fa-circle-xmark'
            ];

        default:
            return [
                'label' => ucwords(str_replace('_', ' ', $status)),
                'class' => 'status-default',
                'icon' => 'fa-circle-info'
            ];
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

    $timestamp = strtotime($tanggal);

    if ($timestamp === false) {
        return '-';
    }

    return date('d/m/Y', $timestamp);
}

/*
|--------------------------------------------------------------------------
| Hitung Jumlah Status
|--------------------------------------------------------------------------
*/
$total_aktif = count($pengajuan_aktif);
$total_riwayat = count($riwayat_pengajuan);

$total_diajukan = 0;
$total_draft = 0;
$total_ttd = 0;
$total_selesai = 0;
$total_ditolak = 0;

foreach ($data_pengajuan as $item) {

    $status = strtolower(trim($item['status'] ?? ''));

    if ($status === 'diajukan') {
        $total_diajukan++;
    } elseif ($status === 'draft_sppd') {
        $total_draft++;
    } elseif ($status === 'ttd_ketua') {
        $total_ttd++;
    } elseif ($status === 'selesai') {
        $total_selesai++;
    } elseif ($status === 'ditolak') {
        $total_ditolak++;
    }
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
                        Kelola pengajuan perjalanan dinas dan lihat riwayat
                        proses pengajuan.
                    </p>

                </div>

            </div>

            <a href="umum_dashboard.php" class="back-button">

                <i class="fa-solid fa-arrow-left"></i>

                <span>
                    Kembali
                </span>

            </a>

        </div>


        <!-- =========================================================
             STATISTIK
        ========================================================== -->

        <div class="stat-grid">

            <div class="stat-card">

                <div class="stat-icon blue">
                    <i class="fa-solid fa-folder-open"></i>
                </div>

                <div class="stat-content">

                    <span>
                        Pengajuan Aktif
                    </span>

                    <strong>
                        <?php echo $total_aktif; ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon yellow">
                    <i class="fa-solid fa-paper-plane"></i>
                </div>

                <div class="stat-content">

                    <span>
                        Perlu Diproses
                    </span>

                    <strong>
                        <?php echo $total_diajukan; ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon purple">
                    <i class="fa-solid fa-signature"></i>
                </div>

                <div class="stat-content">

                    <span>
                        Siap Cap
                    </span>

                    <strong>
                        <?php echo $total_ttd; ?>
                    </strong>

                </div>

            </div>


            <div class="stat-card">

                <div class="stat-icon green">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div class="stat-content">

                    <span>
                        Riwayat
                    </span>

                    <strong>
                        <?php echo $total_riwayat; ?>
                    </strong>

                </div>

            </div>

        </div>


        <!-- =========================================================
             TAB
        ========================================================== -->

        <div class="tab-card">

            <div class="tabs">

                <button type="button" class="tab-button active" onclick="showTab('aktif', this)">
                    <i class="fa-solid fa-list-check"></i>

                    Pengajuan Aktif

                    <span class="tab-count">
                        <?php echo $total_aktif; ?>
                    </span>
                </button>


                <button type="button" class="tab-button" onclick="showTab('riwayat', this)">
                    <i class="fa-solid fa-clock-rotate-left"></i>

                    Riwayat

                    <span class="tab-count">
                        <?php echo $total_riwayat; ?>
                    </span>
                </button>

            </div>

        </div>


        <!-- =========================================================
             FILTER
        ========================================================== -->

        <div class="filter-card">

            <div class="search-box">

                <i class="fa-solid fa-magnifying-glass"></i>

                <input type="text" id="searchInput" placeholder="Cari nama pemohon atau tujuan..."
                    onkeyup="filterPengajuan()">

            </div>


            <div class="filter-status">

                <select id="statusFilter" onchange="filterPengajuan()">

                    <option value="">
                        Semua Status
                    </option>

                    <option value="diajukan">
                        Diajukan
                    </option>

                    <option value="draft_sppd">
                        Draft SPPD
                    </option>

                    <option value="ttd_ketua">
                        TTD Ketua
                    </option>

                    <option value="selesai">
                        Selesai
                    </option>

                    <option value="ditolak">
                        Ditolak
                    </option>

                </select>

            </div>

        </div>


        <!-- =========================================================
             PENGAJUAN AKTIF
        ========================================================== -->

        <section id="tabAktif" class="tab-content active">

            <div class="section-header">

                <div>

                    <span class="section-label">
                        DAFTAR PENGAJUAN
                    </span>

                    <h2>
                        Pengajuan Aktif
                    </h2>

                </div>

                <span class="section-total">
                    <?php echo $total_aktif; ?> pengajuan
                </span>

            </div>


            <?php if (empty($pengajuan_aktif)): ?>

                <div class="empty-card">

                    <div class="empty-icon">
                        <i class="fa-solid fa-folder-open"></i>
                    </div>

                    <h3>
                        Tidak Ada Pengajuan Aktif
                    </h3>

                    <p>
                        Belum ada pengajuan perjalanan dinas yang sedang
                        diproses.
                    </p>

                </div>

            <?php else: ?>

                <div class="application-list">

                    <?php foreach ($pengajuan_aktif as $item): ?>

                        <?php

                        $status = strtolower(trim($item['status'] ?? ''));

                        $statusInfo = statusLabel($status);

                        $nama = trim($item['nama_pemohon'] ?? '');
                        $tujuan = trim($item['tujuan'] ?? '');

                        if ($nama === '') {
                            $nama = 'Pemohon';
                        }

                        if ($tujuan === '') {
                            $tujuan = '-';
                        }

                        ?>

                        <article class="application-item" data-nama="<?php echo htmlspecialchars(strtolower($nama)); ?>"
                            data-tujuan="<?php echo htmlspecialchars(strtolower($tujuan)); ?>"
                            data-status="<?php echo htmlspecialchars($status); ?>">

                            <div class="application-main">

                                <div class="application-number">

                                    <span>
                                        #<?php echo (int) $item['id']; ?>
                                    </span>

                                </div>


                                <div class="application-info">

                                    <div class="application-top">

                                        <h3>
                                            <?php echo htmlspecialchars($nama); ?>
                                        </h3>

                                        <span class="status-badge <?php echo $statusInfo['class']; ?>">
                                            <i class="fa-solid <?php echo $statusInfo['icon']; ?>"></i>

                                            <?php echo htmlspecialchars($statusInfo['label']); ?>
                                        </span>

                                    </div>


                                    <div class="destination">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <strong>
                                            <?php echo htmlspecialchars($tujuan); ?>
                                        </strong>

                                    </div>


                                    <div class="application-meta">

                                        <span>

                                            <i class="fa-solid fa-calendar-days"></i>

                                            Berangkat:
                                            <?php
                                            echo htmlspecialchars(
                                                formatTanggal(
                                                    $item['tanggal_berangkat'] ?? null
                                                )
                                            );
                                            ?>

                                        </span>


                                        <span>

                                            <i class="fa-solid fa-calendar-check"></i>

                                            Kembali:
                                            <?php
                                            echo htmlspecialchars(
                                                formatTanggal(
                                                    $item['tanggal_kembali'] ?? null
                                                )
                                            );
                                            ?>

                                        </span>


                                        <?php if (!empty($item['urgensi'])): ?>

                                            <span>

                                                <i class="fa-solid fa-triangle-exclamation"></i>

                                                Urgensi:
                                                <?php
                                                echo htmlspecialchars(
                                                    ucfirst(
                                                        strtolower(
                                                            $item['urgensi']
                                                        )
                                                    )
                                                );
                                                ?>

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <div class="application-action">

                                    <?php if ($status === 'diajukan'): ?>

                                        <a href="umum_buat.php?id=<?php echo (int) $item['id']; ?>" class="action-button primary">

                                            <i class="fa-solid fa-file-pen"></i>

                                            Buat Draft

                                        </a>


                                    <?php elseif ($status === 'ttd_ketua'): ?>

                                        <a href="umum_cap.php?id=<?php echo (int) $item['id']; ?>" class="action-button stamp">

                                            <i class="fa-solid fa-stamp"></i>

                                            Proses Cap

                                        </a>


                                    <?php elseif ($status === 'draft_sppd'): ?>

                                        <span class="waiting-button">

                                            <i class="fa-solid fa-hourglass-half"></i>

                                            Menunggu TTD

                                        </span>


                                    <?php else: ?>

                                        <span class="waiting-button">

                                            <i class="fa-solid fa-clock"></i>

                                            Dalam Proses

                                        </span>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- =========================================================
             RIWAYAT
        ========================================================== -->

        <section id="tabRiwayat" class="tab-content">

            <div class="section-header">

                <div>

                    <span class="section-label">
                        HISTORI PROSES
                    </span>

                    <h2>
                        Riwayat Pengajuan
                    </h2>

                </div>

                <span class="section-total">
                    <?php echo $total_riwayat; ?> pengajuan
                </span>

            </div>


            <?php if (empty($riwayat_pengajuan)): ?>

                <div class="empty-card">

                    <div class="empty-icon">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                    </div>

                    <h3>
                        Belum Ada Riwayat
                    </h3>

                    <p>
                        Riwayat pengajuan yang telah selesai atau ditolak
                        akan ditampilkan di sini.
                    </p>

                </div>

            <?php else: ?>

                <div class="application-list">

                    <?php foreach ($riwayat_pengajuan as $item): ?>

                        <?php

                        $status = strtolower(trim($item['status'] ?? ''));

                        $statusInfo = statusLabel($status);

                        $nama = trim($item['nama_pemohon'] ?? '');
                        $tujuan = trim($item['tujuan'] ?? '');

                        if ($nama === '') {
                            $nama = 'Pemohon';
                        }

                        if ($tujuan === '') {
                            $tujuan = '-';
                        }

                        ?>

                        <article class="application-item history-item"
                            data-nama="<?php echo htmlspecialchars(strtolower($nama)); ?>"
                            data-tujuan="<?php echo htmlspecialchars(strtolower($tujuan)); ?>"
                            data-status="<?php echo htmlspecialchars($status); ?>">

                            <div class="application-main">

                                <div class="application-number">

                                    <span>
                                        #<?php echo (int) $item['id']; ?>
                                    </span>

                                </div>


                                <div class="application-info">

                                    <div class="application-top">

                                        <h3>
                                            <?php echo htmlspecialchars($nama); ?>
                                        </h3>

                                        <span class="status-badge <?php echo $statusInfo['class']; ?>">

                                            <i class="fa-solid <?php echo $statusInfo['icon']; ?>"></i>

                                            <?php echo htmlspecialchars($statusInfo['label']); ?>

                                        </span>

                                    </div>


                                    <div class="destination">

                                        <i class="fa-solid fa-location-dot"></i>

                                        <strong>
                                            <?php echo htmlspecialchars($tujuan); ?>
                                        </strong>

                                    </div>


                                    <div class="application-meta">

                                        <span>

                                            <i class="fa-solid fa-calendar-days"></i>

                                            Berangkat:
                                            <?php
                                            echo htmlspecialchars(
                                                formatTanggal(
                                                    $item['tanggal_berangkat'] ?? null
                                                )
                                            );
                                            ?>

                                        </span>


                                        <span>

                                            <i class="fa-solid fa-calendar-check"></i>

                                            Kembali:
                                            <?php
                                            echo htmlspecialchars(
                                                formatTanggal(
                                                    $item['tanggal_kembali'] ?? null
                                                )
                                            );
                                            ?>

                                        </span>

                                    </div>


                                    <?php if ($status === 'ditolak' && !empty($item['alasan_penolakan'])): ?>

                                        <div class="rejection-info">

                                            <i class="fa-solid fa-circle-exclamation"></i>

                                            <div>

                                                <span>
                                                    Alasan Penolakan
                                                </span>

                                                <p>
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $item['alasan_penolakan']
                                                    );
                                                    ?>
                                                </p>

                                            </div>

                                        </div>

                                    <?php endif; ?>

                                </div>


                                <div class="history-status">

                                    <?php if ($status === 'selesai'): ?>

                                        <div class="history-result success">

                                            <i class="fa-solid fa-circle-check"></i>

                                            <span>
                                                Pengajuan Selesai
                                            </span>

                                        </div>

                                    <?php elseif ($status === 'ditolak'): ?>

                                        <div class="history-result rejected">

                                            <i class="fa-solid fa-circle-xmark"></i>

                                            <span>
                                                Pengajuan Ditolak
                                            </span>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </article>

                    <?php endforeach; ?>

                </div>

            <?php endif; ?>

        </section>


        <!-- =========================================================
             INFO
        ========================================================== -->

        <div class="info-card">

            <div class="info-icon">

                <i class="fa-solid fa-circle-info"></i>

            </div>

            <div>

                <h3>
                    Alur Pengajuan
                </h3>

                <p>
                    Pengajuan baru akan tampil dengan status
                    <strong>Diajukan</strong>.
                    Setelah dibuatkan draft SPPD, pengajuan menunggu
                    proses tanda tangan Ketua. Setelah berstatus
                    <strong>TTD Ketua</strong>, Bagian Umum dapat
                    melakukan proses cap sampai pengajuan menjadi
                    <strong>Selesai</strong>.
                </p>

            </div>

        </div>

    </div>

</main>


<?php include 'footer.php'; ?>


<style>
    /* =========================================================
   PAGE
========================================================= */

    .main-content-umum {
        margin-left: 250px;
        padding: 92px 25px 90px;
        min-height: 100vh;
        background: #f6f8fb;
        font-family: "Inter", "Segoe UI", Arial, sans-serif;
    }

    .pengajuan-page {
        max-width: 1180px;
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
   STATISTIK
========================================================= */

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 13px;
        margin-bottom: 18px;
    }

    .stat-card {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        border: 1px solid #e4e9f0;
        border-radius: 12px;
        background: #fff;
        box-shadow: 0 5px 18px rgba(16, 41, 77, .04);
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        min-width: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        font-size: 15px;
    }

    .stat-icon.blue {
        background: #edf4fc;
        color: #17477f;
    }

    .stat-icon.yellow {
        background: #fff7df;
        color: #a87900;
    }

    .stat-icon.purple {
        background: #f2effd;
        color: #6952a8;
    }

    .stat-icon.green {
        background: #edf8f1;
        color: #21864a;
    }

    .stat-content span {
        display: block;
        margin-bottom: 2px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 700;
    }

    .stat-content strong {
        display: block;
        color: #10294d;
        font-size: 19px;
        font-weight: 800;
    }


    /* =========================================================
   TABS
========================================================= */

    .tab-card {
        margin-bottom: 12px;
        border-bottom: 1px solid #dfe6ee;
    }

    .tabs {
        display: flex;
        gap: 4px;
    }

    .tab-button {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 11px 15px;
        border: 0;
        border-bottom: 3px solid transparent;
        background: transparent;
        color: #64748b;
        font-size: 11px;
        font-weight: 800;
        cursor: pointer;
        transition: .2s ease;
    }

    .tab-button:hover {
        color: #10294d;
    }

    .tab-button.active {
        border-bottom-color: #f2bd4b;
        color: #10294d;
    }

    .tab-count {
        min-width: 20px;
        padding: 3px 6px;
        border-radius: 12px;
        background: #edf2f7;
        color: #64748b;
        font-size: 9px;
    }

    .tab-button.active .tab-count {
        background: #fff2c9;
        color: #8b6800;
    }


    /* =========================================================
   FILTER
========================================================= */

    .filter-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin-bottom: 18px;
        padding: 12px;
        border: 1px solid #e4e9f0;
        border-radius: 11px;
        background: #fff;
    }

    .search-box {
        position: relative;
        flex: 1;
    }

    .search-box i {
        position: absolute;
        top: 50%;
        left: 13px;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 11px;
    }

    .search-box input,
    .filter-status select {
        width: 100%;
        height: 38px;
        border: 1px solid #dce4ed;
        border-radius: 8px;
        outline: none;
        background: #fff;
        color: #334155;
        font-family: inherit;
        font-size: 11px;
        transition: .2s ease;
    }

    .search-box input {
        padding: 0 12px 0 34px;
    }

    .filter-status {
        width: 190px;
    }

    .filter-status select {
        padding: 0 10px;
        cursor: pointer;
    }

    .search-box input:focus,
    .filter-status select:focus {
        border-color: #17477f;
        box-shadow: 0 0 0 3px rgba(23, 71, 127, .08);
    }


    /* =========================================================
   TAB CONTENT
========================================================= */

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }


    /* =========================================================
   SECTION HEADER
========================================================= */

    .section-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 15px;
        margin-bottom: 12px;
    }

    .section-label {
        display: block;
        margin-bottom: 3px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 800;
        letter-spacing: .8px;
    }

    .section-header h2 {
        margin: 0;
        color: #10294d;
        font-size: 16px;
        font-weight: 800;
    }

    .section-total {
        color: #94a3b8;
        font-size: 10px;
        font-weight: 700;
    }


    /* =========================================================
   APPLICATION LIST
========================================================= */

    .application-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .application-item {
        border: 1px solid #e3e9f0;
        border-radius: 13px;
        background: #fff;
        box-shadow: 0 4px 15px rgba(16, 41, 77, .04);
        overflow: hidden;
        transition: .2s ease;
    }

    .application-item:hover {
        border-color: #cbd7e4;
        box-shadow: 0 7px 20px rgba(16, 41, 77, .07);
        transform: translateY(-1px);
    }

    .application-main {
        display: grid;
        grid-template-columns: 48px minmax(0, 1fr) auto;
        align-items: center;
        gap: 15px;
        padding: 15px 17px;
    }

    .application-number {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .application-number span {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 10px;
        background: #f1f5f9;
        color: #17477f;
        font-size: 10px;
        font-weight: 800;
    }

    .application-info {
        min-width: 0;
    }

    .application-top {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 6px;
    }

    .application-top h3 {
        min-width: 0;
        margin: 0;
        color: #10294d;
        font-size: 13px;
        font-weight: 800;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        flex-shrink: 0;
        padding: 5px 8px;
        border-radius: 15px;
        font-size: 9px;
        font-weight: 800;
        white-space: nowrap;
    }

    .status-diajukan {
        background: #edf4fc;
        color: #17477f;
    }

    .status-draft {
        background: #fff7df;
        color: #967000;
    }

    .status-ttd {
        background: #f2effd;
        color: #6952a8;
    }

    .status-selesai {
        background: #edf8f1;
        color: #21864a;
    }

    .status-ditolak {
        background: #fff0f0;
        color: #bd3d3d;
    }

    .status-default {
        background: #f1f5f9;
        color: #64748b;
    }

    .destination {
        display: flex;
        align-items: center;
        gap: 7px;
        margin-bottom: 8px;
        color: #475569;
        font-size: 11px;
    }

    .destination i {
        color: #17477f;
        font-size: 10px;
    }

    .destination strong {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .application-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    .application-meta span {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        color: #94a3b8;
        font-size: 9px;
        font-weight: 600;
    }

    .application-meta i {
        color: #7890a9;
    }


    /* =========================================================
   ACTION
========================================================= */

    .application-action {
        display: flex;
        justify-content: flex-end;
    }

    .action-button,
    .waiting-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-width: 112px;
        min-height: 36px;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: 10px;
        font-weight: 800;
        text-decoration: none;
        white-space: nowrap;
        transition: .2s ease;
    }

    .action-button.primary {
        background: #10294d;
        color: #fff;
    }

    .action-button.primary:hover {
        background: #17477f;
        color: #fff;
        transform: translateY(-1px);
    }

    .action-button.stamp {
        background: #fff7df;
        color: #8d6900;
        border: 1px solid #f1df9e;
    }

    .action-button.stamp:hover {
        background: #ffefbd;
        color: #745500;
        transform: translateY(-1px);
    }

    .waiting-button {
        background: #f1f5f9;
        color: #64748b;
    }


    /* =========================================================
   HISTORY
========================================================= */

    .history-item {
        opacity: .96;
    }

    .history-status {
        display: flex;
        justify-content: flex-end;
    }

    .history-result {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        min-width: 145px;
        justify-content: center;
        padding: 9px 11px;
        border-radius: 8px;
        font-size: 9px;
        font-weight: 800;
    }

    .history-result.success {
        background: #edf8f1;
        color: #21864a;
    }

    .history-result.rejected {
        background: #fff0f0;
        color: #bd3d3d;
    }


    /* =========================================================
   REJECTION
========================================================= */

    .rejection-info {
        display: flex;
        align-items: flex-start;
        gap: 8px;
        margin-top: 10px;
        padding: 9px 11px;
        border-radius: 8px;
        background: #fff6f6;
        border: 1px solid #f2dada;
    }

    .rejection-info>i {
        margin-top: 2px;
        color: #bd3d3d;
        font-size: 11px;
    }

    .rejection-info span {
        display: block;
        margin-bottom: 2px;
        color: #bd3d3d;
        font-size: 9px;
        font-weight: 800;
    }

    .rejection-info p {
        margin: 0;
        color: #7f4b4b;
        font-size: 9px;
        line-height: 1.5;
    }


    /* =========================================================
   EMPTY
========================================================= */

    .empty-card {
        padding: 42px 20px;
        border: 1px dashed #d5dee8;
        border-radius: 13px;
        background: #fff;
        text-align: center;
    }

    .empty-icon {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 12px;
        border-radius: 13px;
        background: #f1f5f9;
        color: #94a3b8;
        font-size: 19px;
    }

    .empty-card h3 {
        margin: 0 0 5px;
        color: #10294d;
        font-size: 13px;
        font-weight: 800;
    }

    .empty-card p {
        max-width: 450px;
        margin: 0 auto;
        color: #94a3b8;
        font-size: 10px;
        line-height: 1.5;
    }


    /* =========================================================
   INFO
========================================================= */

    .info-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-top: 18px;
        padding: 14px 17px;
        border: 1px solid #d9e5f2;
        border-radius: 11px;
        background: #f4f8fc;
    }

    .info-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #10294d;
        color: #f2bd4b;
        font-size: 13px;
    }

    .info-card h3 {
        margin: 1px 0 4px;
        color: #10294d;
        font-size: 11px;
        font-weight: 800;
    }

    .info-card p {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.6;
    }


    /* =========================================================
   RESPONSIVE
========================================================= */

    @media (max-width: 1000px) {

        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .application-main {
            grid-template-columns: 42px minmax(0, 1fr);
        }

        .application-action {
            grid-column: 2;
            justify-content: flex-start;
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

        .filter-card {
            align-items: stretch;
            flex-direction: column;
        }

        .filter-status {
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

        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 8px;
        }

        .stat-card {
            padding: 12px;
        }

        .stat-icon {
            width: 35px;
            height: 35px;
            min-width: 35px;
            font-size: 13px;
        }

        .stat-content strong {
            font-size: 17px;
        }

        .tabs {
            overflow-x: auto;
        }

        .tab-button {
            padding: 10px 11px;
            white-space: nowrap;
        }

        .application-main {
            grid-template-columns: 1fr;
            gap: 10px;
            padding: 14px;
        }

        .application-number {
            justify-content: flex-start;
        }

        .application-number span {
            width: 34px;
            height: 34px;
        }

        .application-top {
            align-items: flex-start;
            flex-direction: column;
            gap: 6px;
        }

        .application-top h3 {
            white-space: normal;
        }

        .application-meta {
            flex-direction: column;
            gap: 6px;
        }

        .application-action {
            grid-column: auto;
            width: 100%;
        }

        .action-button,
        .waiting-button {
            width: 100%;
        }

        .history-status {
            justify-content: flex-start;
            width: 100%;
        }

        .history-result {
            width: 100%;
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

        .stat-grid {
            grid-template-columns: 1fr 1fr;
        }

        .stat-card {
            gap: 8px;
            padding: 10px;
        }

        .stat-content span {
            font-size: 8px;
        }

        .stat-content strong {
            font-size: 16px;
        }

    }


    /* =========================================================
   HIDE FILTERED ITEMS
========================================================= */

    .application-item.hidden {
        display: none !important;
    }
</style>


<script>

    /*
    |--------------------------------------------------------------------------
    | Tab
    |--------------------------------------------------------------------------
    */

    function showTab(tab, button) {

        const aktif = document.getElementById('tabAktif');
        const riwayat = document.getElementById('tabRiwayat');

        document.querySelectorAll('.tab-button').forEach(function (item) {
            item.classList.remove('active');
        });

        button.classList.add('active');

        if (tab === 'aktif') {

            aktif.classList.add('active');
            riwayat.classList.remove('active');

        } else {

            aktif.classList.remove('active');
            riwayat.classList.add('active');

        }

        filterPengajuan();
    }


    /*
    |--------------------------------------------------------------------------
    | Filter / Search
    |--------------------------------------------------------------------------
    */

    function filterPengajuan() {

        const searchInput =
            document.getElementById('searchInput');

        const statusFilter =
            document.getElementById('statusFilter');

        const search =
            searchInput.value.toLowerCase().trim();

        const status =
            statusFilter.value.toLowerCase().trim();

        const activeTab =
            document.querySelector('.tab-content.active');

        if (!activeTab) {
            return;
        }

        const items =
            activeTab.querySelectorAll('.application-item');

        items.forEach(function (item) {

            const nama =
                item.dataset.nama || '';

            const tujuan =
                item.dataset.tujuan || '';

            const itemStatus =
                item.dataset.status || '';

            const matchSearch =
                nama.includes(search) ||
                tujuan.includes(search);

            const matchStatus =
                status === '' ||
                itemStatus === status;

            if (matchSearch && matchStatus) {

                item.classList.remove('hidden');

            } else {

                item.classList.add('hidden');

            }

        });
    }

</script>


</body>

</html>