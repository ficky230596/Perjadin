php
<?php

session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'umum'
) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';


/* =====================================================
   AMBIL ANTRIAN
===================================================== */

$queue_draft = getScheduledQueue($pdo, 'diajukan');

$queue_cap = getScheduledQueue($pdo, 'ttd_ketua');


/* =====================================================
   HITUNG JUMLAH ANTRIAN
===================================================== */

$total_draft = count($queue_draft);

$total_cap = count($queue_cap);

$total_antrian = $total_draft + $total_cap;

?>

<?php include 'header_umum.php'; ?>

<?php include 'sidebar_umum.php'; ?>


<style>
    /* =====================================================
   DASHBOARD BAGIAN UMUM
===================================================== */

    * {
        box-sizing: border-box;
    }


    body {
        margin: 0;
        padding: 0;
        background: #f4f7fb;
        font-family:
            "Inter",
            "Segoe UI",
            Arial,
            sans-serif;
    }


    /* =====================================================
   MAIN CONTENT
===================================================== */

    .main-content-umum {

        margin-left: 250px;

        padding:
            92px 25px 85px;

        min-height: 100vh;

        transition:
            margin-left .25s ease;

    }


    /* =====================================================
   PAGE HEADER
===================================================== */

    .page-header-umum {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 20px;

        padding:
            22px 24px;

        margin-bottom: 20px;

        background: #ffffff;

        border:
            1px solid #e7edf4;

        border-radius: 14px;

        box-shadow:
            0 4px 16px rgba(16, 41, 77, .07);

    }


    .page-header-umum-left {

        display: flex;

        align-items: center;

        gap: 14px;

        min-width: 0;

    }


    .page-header-umum-icon {

        width: 52px;

        height: 52px;

        min-width: 52px;

        display: flex;

        align-items: center;

        justify-content: center;

        background: #10294d;

        color: #f2bd4b;

        border-radius: 13px;

        font-size: 21px;

    }


    .page-header-umum-text {

        min-width: 0;

    }


    .page-header-umum h1 {

        margin: 0;

        color: #10294d;

        font-size: 23px;

        font-weight: 750;

        line-height: 1.3;

    }


    .page-header-umum p {

        margin:
            6px 0 0;

        color: #64748b;

        font-size: 13px;

        line-height: 1.5;

    }


    /* =====================================================
   USER BADGE
===================================================== */

    .user-badge-umum {

        display: inline-flex;

        align-items: center;

        gap: 7px;

        padding:
            8px 12px;

        border-radius: 8px;

        background: #f1f5f9;

        color: #475569;

        font-size: 11px;

        font-weight: 600;

        white-space: nowrap;

    }


    .user-badge-umum i {

        color: #17477f;

    }


    /* =====================================================
   STATISTIC CARDS
===================================================== */

    .stats-grid-umum {

        display: grid;

        grid-template-columns:
            repeat(3, 1fr);

        gap: 16px;

        margin-bottom: 20px;

    }


    .stat-card-umum {

        position: relative;

        overflow: hidden;

        display: flex;

        align-items: center;

        gap: 14px;

        padding: 18px;

        background: #ffffff;

        border:
            1px solid #e7edf4;

        border-radius: 13px;

        box-shadow:
            0 4px 14px rgba(16, 41, 77, .06);

    }


    .stat-card-umum::after {

        content: "";

        position: absolute;

        right: -25px;

        bottom: -30px;

        width: 90px;

        height: 90px;

        border-radius: 50%;

        background:
            rgba(16, 41, 77, .035);

    }


    .stat-icon-umum {

        width: 46px;

        height: 46px;

        min-width: 46px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 11px;

        background: #eef4fb;

        color: #17477f;

        font-size: 19px;

    }


    .stat-card-umum.warning .stat-icon-umum {

        background: #fff5d6;

        color: #a36f00;

    }


    .stat-card-umum.success .stat-icon-umum {

        background: #eaf7ee;

        color: #218838;

    }


    .stat-card-umum.primary .stat-icon-umum {

        background: #eef4fb;

        color: #17477f;

    }


    .stat-content-umum {

        min-width: 0;

    }


    .stat-content-umum span {

        display: block;

        margin-bottom: 4px;

        color: #64748b;

        font-size: 11px;

        font-weight: 600;

    }


    .stat-content-umum strong {

        display: block;

        color: #10294d;

        font-size: 24px;

        font-weight: 750;

        line-height: 1;

    }


    /* =====================================================
   SECTION CARD
===================================================== */

    .dashboard-card-umum {

        margin-bottom: 20px;

        background: #ffffff;

        border:
            1px solid #e7edf4;

        border-radius: 13px;

        overflow: hidden;

        box-shadow:
            0 4px 14px rgba(16, 41, 77, .06);

    }


    .dashboard-card-header-umum {

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 12px;

        padding:
            16px 18px;

        border-bottom:
            1px solid #edf1f5;

    }


    .dashboard-card-title-umum {

        display: flex;

        align-items: center;

        gap: 10px;

    }


    .dashboard-card-title-icon {

        width: 37px;

        height: 37px;

        min-width: 37px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 9px;

        background: #eef4fb;

        color: #17477f;

        font-size: 15px;

    }


    .dashboard-card-title-umum h2 {

        margin: 0;

        color: #10294d;

        font-size: 14px;

        font-weight: 700;

    }


    .dashboard-card-title-umum p {

        margin:
            3px 0 0;

        color: #94a3b8;

        font-size: 10px;

    }


    .queue-count-umum {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        min-width: 27px;

        height: 27px;

        padding:
            0 8px;

        border-radius: 20px;

        background: #10294d;

        color: #f2bd4b;

        font-size: 11px;

        font-weight: 750;

    }


    /* =====================================================
   TABLE WRAPPER
===================================================== */

    .table-responsive-umum {

        width: 100%;

        overflow-x: auto;

    }


    /* =====================================================
   TABLE
===================================================== */

    .umum-table {

        width: 100%;

        border-collapse: collapse;

        min-width: 760px;

    }


    .umum-table th {

        padding:
            12px 14px;

        background: #f8fafc;

        border-bottom:
            1px solid #e4e9f0;

        color: #64748b;

        font-size: 10px;

        font-weight: 750;

        text-align: left;

        text-transform: uppercase;

        letter-spacing: .3px;

        white-space: nowrap;

    }


    .umum-table td {

        padding:
            13px 14px;

        border-bottom:
            1px solid #edf1f5;

        color: #334155;

        font-size: 12px;

        vertical-align: middle;

    }


    .umum-table tbody tr {

        transition:
            background .2s ease;

    }


    .umum-table tbody tr:hover {

        background: #f8fbff;

    }


    .umum-table tbody tr:last-child td {

        border-bottom: none;

    }


    /* =====================================================
   ID
===================================================== */

    .table-id-umum {

        color: #10294d;

        font-weight: 750;

    }


    /* =====================================================
   URGENSI
===================================================== */

    .urgensi-badge-umum {

        display: inline-flex;

        align-items: center;

        gap: 5px;

        padding:
            5px 9px;

        border-radius: 20px;

        background: #fff5d6;

        color: #946800;

        font-size: 10px;

        font-weight: 700;

    }


    /* =====================================================
   PRIORITAS
===================================================== */

    .prioritas-normal {

        color: #475569;

        font-weight: 650;

    }


    .prioritas-tinggi {

        display: inline-flex;

        align-items: center;

        gap: 5px;

        padding:
            5px 8px;

        border-radius: 6px;

        background: #fff0f1;

        color: #c52d3a;

        font-weight: 750;

    }


    .prioritas-tinggi::before {

        content: "";

        width: 5px;

        height: 5px;

        border-radius: 50%;

        background: #dc3545;

    }


    /* =====================================================
   ACTION
===================================================== */

    .action-group-umum {

        display: flex;

        align-items: center;

        gap: 6px;

        flex-wrap: wrap;

    }


    .btn-umum {

        display: inline-flex;

        align-items: center;

        justify-content: center;

        gap: 6px;

        min-height: 33px;

        padding:
            7px 10px;

        border: none;

        border-radius: 7px;

        text-decoration: none;

        font-family: inherit;

        font-size: 10px;

        font-weight: 700;

        cursor: pointer;

        transition:
            background .2s ease,
            color .2s ease,
            transform .2s ease,
            box-shadow .2s ease;

    }


    .btn-umum:hover {

        transform:
            translateY(-1px);

    }


    .btn-proses-umum {

        background: #10294d;

        color: #ffffff;

        box-shadow:
            0 3px 8px rgba(16, 41, 77, .12);

    }


    .btn-proses-umum:hover {

        background: #17477f;

        color: #ffffff;

    }


    .btn-cap-umum {

        background: #f2bd4b;

        color: #10294d;

    }


    .btn-cap-umum:hover {

        background: #e4ae3e;

        color: #10294d;

    }


    .btn-tolak {

        background: #fff0f1;

        color: #c52d3a;

    }


    .btn-tolak:hover {

        background: #dc3545;

        color: #ffffff;

    }


    /* =====================================================
   EMPTY STATE
===================================================== */

    .empty-antrian {

        display: flex;

        flex-direction: column;

        align-items: center;

        justify-content: center;

        min-height: 150px;

        padding: 25px;

        color: #94a3b8;

        text-align: center;

    }


    .empty-antrian-icon {

        width: 45px;

        height: 45px;

        display: flex;

        align-items: center;

        justify-content: center;

        margin-bottom: 9px;

        border-radius: 50%;

        background: #f1f5f9;

        color: #94a3b8;

        font-size: 17px;

    }


    .empty-antrian strong {

        margin-bottom: 4px;

        color: #64748b;

        font-size: 12px;

    }


    .empty-antrian span {

        font-size: 10px;

    }


    /* =====================================================
   LOADING
===================================================== */

    .dashboard-loading {

        display: none;

        padding: 15px;

        text-align: center;

        color: #64748b;

        font-size: 11px;

    }


    /* =====================================================
   TABLET
===================================================== */

    @media (max-width: 1000px) {

        .main-content-umum {

            margin-left: 250px;

            padding:
                88px 18px 82px;

        }


        .stats-grid-umum {

            grid-template-columns:
                repeat(3, 1fr);

            gap: 12px;

        }


        .stat-card-umum {

            padding: 15px;

        }


        .stat-content-umum strong {

            font-size: 21px;

        }

    }


    /* =====================================================
   MOBILE
===================================================== */

    @media (max-width: 768px) {

        .main-content-umum {

            margin-left: 0;

            width: 100%;

            padding:
                80px 12px 80px;

        }


        .page-header-umum {

            flex-direction: column;

            align-items: stretch;

            padding: 17px;

        }


        .page-header-umum-left {

            width: 100%;

        }


        .page-header-umum-icon {

            width: 45px;

            height: 45px;

            min-width: 45px;

        }


        .page-header-umum h1 {

            font-size: 19px;

        }


        .page-header-umum p {

            font-size: 11px;

        }


        .user-badge-umum {

            width: fit-content;

        }


        .stats-grid-umum {

            grid-template-columns:
                1fr;

            gap: 10px;

        }


        .stat-card-umum {

            padding: 14px;

        }


        .stat-content-umum strong {

            font-size: 20px;

        }


        .dashboard-card-header-umum {

            padding:
                14px 15px;

        }


        .dashboard-card-title-icon {

            width: 34px;

            height: 34px;

            min-width: 34px;

        }


        .dashboard-card-title-umum h2 {

            font-size: 13px;

        }


        .dashboard-card-title-umum p {

            font-size: 9px;

        }


        .queue-count-umum {

            min-width: 25px;

            height: 25px;

            font-size: 10px;

        }

    }


    /* =====================================================
   MOBILE KECIL
===================================================== */

    @media (max-width: 480px) {

        .main-content-umum {

            padding:
                76px 10px 78px;

        }


        .page-header-umum {

            padding: 15px;

        }


        .page-header-umum-left {

            gap: 10px;

        }


        .page-header-umum-icon {

            width: 40px;

            height: 40px;

            min-width: 40px;

            font-size: 16px;

        }


        .page-header-umum h1 {

            font-size: 17px;

        }


        .page-header-umum p {

            font-size: 10px;

        }


        .user-badge-umum {

            font-size: 9px;

        }


        .stat-icon-umum {

            width: 40px;

            height: 40px;

            min-width: 40px;

            font-size: 16px;

        }


        .dashboard-card-title-umum h2 {

            font-size: 12px;

        }


        .dashboard-card-title-umum p {

            display: none;

        }

    }


    /* =====================================================
   PRINT
===================================================== */

    @media print {

        .umum-header,
        .sidebar-umum,
        .umum-sidebar-overlay,
        .footer-umum {

            display: none !important;

        }


        .main-content-umum {

            margin: 0 !important;

            padding: 0 !important;

            width: 100% !important;

        }


        .page-header-umum,
        .dashboard-card-umum,
        .stat-card-umum {

            box-shadow: none !important;

        }

    }
</style>


<main class="main-content-umum">


    <!-- =================================================
         PAGE HEADER
    ================================================= -->

    <section class="page-header-umum">

        <div class="page-header-umum-left">

            <div class="page-header-umum-icon">

                <i class="fa-solid fa-gauge-high"></i>

            </div>


            <div class="page-header-umum-text">

                <h1>
                    Dashboard Bagian Umum
                </h1>

                <p>
                    Kelola draft dan cap SPPD berdasarkan prioritas.
                </p>

            </div>

        </div>


        <div class="user-badge-umum">

            <i class="fa-solid fa-user"></i>

            <span>
                <?php
                echo htmlspecialchars(
                    $_SESSION['nama'] ?? 'Bagian Umum'
                );
                ?>
            </span>

        </div>

    </section>


    <!-- =================================================
         STATISTICS
    ================================================= -->

    <section class="stats-grid-umum">


        <!-- Draft -->

        <div class="stat-card-umum warning">

            <div class="stat-icon-umum">

                <i class="fa-solid fa-file-circle-plus"></i>

            </div>


            <div class="stat-content-umum">

                <span>
                    Antrian Buat Draft
                </span>

                <strong>
                    <?php echo $total_draft; ?>
                </strong>

            </div>

        </div>


        <!-- Cap -->

        <div class="stat-card-umum success">

            <div class="stat-icon-umum">

                <i class="fa-solid fa-stamp"></i>

            </div>


            <div class="stat-content-umum">

                <span>
                    Antrian Cap SPPD
                </span>

                <strong>
                    <?php echo $total_cap; ?>
                </strong>

            </div>

        </div>


        <!-- Total -->

        <div class="stat-card-umum primary">

            <div class="stat-icon-umum">

                <i class="fa-solid fa-layer-group"></i>

            </div>


            <div class="stat-content-umum">

                <span>
                    Total Antrian
                </span>

                <strong>
                    <?php echo $total_antrian; ?>
                </strong>

            </div>

        </div>


    </section>


    <!-- =================================================
         ANTRIAN BUAT DRAFT
    ================================================= -->

    <section class="dashboard-card-umum">


        <div class="dashboard-card-header-umum">

            <div class="dashboard-card-title-umum">

                <div class="dashboard-card-title-icon">

                    <i class="fa-solid fa-file-circle-plus"></i>

                </div>


                <div>

                    <h2>
                        Antrian Buat Draft
                    </h2>

                    <p>
                        Pengajuan perjalanan dinas dari Pegawai
                    </p>

                </div>

            </div>


            <span class="queue-count-umum">

                <?php echo $total_draft; ?>

            </span>

        </div>


        <?php if (empty($queue_draft)): ?>


            <div class="empty-antrian">

                <div class="empty-antrian-icon">

                    <i class="fa-solid fa-inbox"></i>

                </div>

                <strong>
                    Tidak ada antrian draft
                </strong>

                <span>
                    Belum ada pengajuan yang menunggu proses pembuatan draft.
                </span>

            </div>


        <?php else: ?>


            <div class="table-responsive-umum">

                <table class="umum-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Pemohon</th>

                            <th>Tujuan</th>

                            <th>Urgensi</th>

                            <th>Skor Prioritas</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($queue_draft as $q): ?>


                            <?php

                            $stmt_pemohon = $pdo->prepare(
                                "SELECT nama, wa_phone
                             FROM users
                             WHERE id = ?"
                            );

                            $stmt_pemohon->execute([
                                $q['pegawai_id']
                            ]);

                            $userData =
                                $stmt_pemohon->fetch(
                                    PDO::FETCH_ASSOC
                                );

                            $pemohon =
                                $userData['nama']
                                ?? 'Tidak Diketahui';

                            $hp =
                                $userData['wa_phone']
                                ?? '';

                            $prioritas_skor =
                                (float) ($q['prioritas_skor'] ?? 0);

                            $prioritasClass =
                                ($prioritas_skor > 5)
                                ? 'prioritas-tinggi'
                                : 'prioritas-normal';

                            ?>


                            <tr>


                                <td class="table-id-umum">

                                    #<?php
                                    echo htmlspecialchars(
                                        $q['id']
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $pemohon
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $q['tujuan']
                                    );
                                    ?>

                                </td>


                                <td>

                                    <span class="urgensi-badge-umum">

                                        <i class="fa-solid fa-bolt"></i>

                                        <?php
                                        echo htmlspecialchars(
                                            ucfirst(
                                                $q['urgensi'] ?? '-'
                                            )
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <span class="<?php
                                    echo $prioritasClass;
                                    ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            (string) 
                                            $prioritas_skor
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>
                                    <div class="action-group-umum">

                                        <a href="umum_buat.php?id=<?php echo urlencode($q['id']); ?>"
                                            class="btn-umum btn-proses-umum" title="Proses draft">
                                            <i class="fa-solid fa-file-pen"></i>
                                            Proses Draft
                                        </a>

                                        <button type="button" class="btn-umum btn-tolak" onclick='tolakPengajuan(
                                            <?php echo json_encode((string) $q["id"]); ?>,
                                            <?php echo json_encode($pemohon, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>,
                                            <?php echo json_encode($hp, JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>,
                                            <?php echo json_encode($q["tujuan"], JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP); ?>
                                        )' title="Tolak pengajuan">
                                            <i class="fa-solid fa-xmark"></i>
                                            Tolak
                                        </button>

                                    </div>
                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php endif; ?>


    </section>


    <!-- =================================================
         ANTRIAN CAP SPPD
    ================================================= -->

    <section class="dashboard-card-umum">


        <div class="dashboard-card-header-umum">

            <div class="dashboard-card-title-umum">

                <div class="dashboard-card-title-icon">

                    <i class="fa-solid fa-stamp"></i>

                </div>


                <div>

                    <h2>
                        Antrian Cap SPPD
                    </h2>

                    <p>
                        Pengajuan yang telah ditandatangani Ketua
                    </p>

                </div>

            </div>


            <span class="queue-count-umum">

                <?php echo $total_cap; ?>

            </span>

        </div>


        <?php if (empty($queue_cap)): ?>


            <div class="empty-antrian">

                <div class="empty-antrian-icon">

                    <i class="fa-solid fa-stamp"></i>

                </div>

                <strong>
                    Tidak ada antrian cap
                </strong>

                <span>
                    Belum ada SPPD yang menunggu proses cap.
                </span>

            </div>


        <?php else: ?>


            <div class="table-responsive-umum">

                <table class="umum-table">

                    <thead>

                        <tr>

                            <th>ID</th>

                            <th>Tujuan</th>

                            <th>Skor Prioritas</th>

                            <th>Aksi</th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($queue_cap as $q): ?>


                            <?php

                            $prioritas_skor =
                                (float) ($q['prioritas_skor'] ?? 0);

                            ?>


                            <tr>


                                <td class="table-id-umum">

                                    #<?php
                                    echo htmlspecialchars(
                                        $q['id']
                                    );
                                    ?>

                                </td>


                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $q['tujuan']
                                    );
                                    ?>

                                </td>


                                <td>

                                    <span class="<?php
                                    echo (
                                        $prioritas_skor > 5
                                    )
                                        ? 'prioritas-tinggi'
                                        : 'prioritas-normal';
                                    ?>">

                                        <?php
                                        echo htmlspecialchars(
                                            (string) 
                                            $prioritas_skor
                                        );
                                        ?>

                                    </span>

                                </td>


                                <td>

                                    <div class="action-group-umum">

                                        <a href="umum_cap.php?id=<?php echo urlencode($q['id']); ?>"
                                            class="btn-umum btn-cap-umum" title="Proses cap">

                                            <i class="fa-solid fa-stamp"></i>

                                            Proses Cap

                                        </a>

                                    </div>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>


        <?php endif; ?>


    </section>


    <div class="dashboard-loading" id="dashboardLoading">

        <i class="fa-solid fa-spinner fa-spin"></i>

        Memuat data...

    </div>


</main>


<?php include 'footer.php'; ?>


<script>

    /* =====================================================
       TOLAK PENGAJUAN
    ===================================================== */

    function tolakPengajuan(
        id,
        nama,
        hp,
        tujuan
    ) {

        Swal.fire({

            title:
                'Tolak Pengajuan?',

            html:
                '<div style="text-align:left;">' +

                '<div style="' +
                'margin-bottom:10px;' +
                'padding:10px;' +
                'background:#f8fafc;' +
                'border-radius:8px;' +
                'font-size:12px;' +
                'color:#475569;' +
                '">' +

                '<strong>Pemohon:</strong> ' +
                escapeHtml(nama) +

                '<br>' +

                '<strong>Tujuan:</strong> ' +
                escapeHtml(tujuan) +

                '</div>' +

                '<label style="' +
                'display:block;' +
                'margin-bottom:6px;' +
                'font-size:12px;' +
                'font-weight:600;' +
                'color:#334155;' +
                '">' +

                'Alasan Penolakan' +

                '</label>' +

                '</div>',

            input:
                'textarea',

            inputPlaceholder:
                'Tuliskan alasan penolakan...',

            inputAttributes: {

                'aria-label':
                    'Alasan penolakan'

            },

            inputAutoTrim:
                true,

            showCancelButton:
                true,

            confirmButtonText:
                '<i class="fa-solid fa-xmark"></i> Tolak',

            cancelButtonText:
                'Batal',

            confirmButtonColor:
                '#dc3545',

            cancelButtonColor:
                '#6c757d',

            reverseButtons:
                true,

            focusCancel:
                true,

            preConfirm:
                function (alasan) {

                    alasan =
                        (alasan || '').trim();


                    if (!alasan) {

                        Swal.showValidationMessage(
                            'Alasan penolakan wajib diisi!'
                        );

                        return false;

                    }


                    return alasan;

                }

        }).then(
            function (result) {

                if (!result.isConfirmed) {
                    return;
                }


                Swal.fire({

                    title:
                        'Memproses...',

                    text:
                        'Sedang menolak pengajuan.',

                    allowOutsideClick:
                        false,

                    allowEscapeKey:
                        false,

                    didOpen:
                        function () {

                            Swal.showLoading();

                        }

                });


                fetch(
                    'tolak_pengajuan.php',
                    {

                        method:
                            'POST',

                        headers: {

                            'Content-Type':
                                'application/x-www-form-urlencoded; charset=UTF-8'

                        },

                        body:
                            'id=' +
                            encodeURIComponent(id) +

                            '&alasan=' +
                            encodeURIComponent(result.value) +

                            '&nama=' +
                            encodeURIComponent(nama) +

                            '&hp=' +
                            encodeURIComponent(hp) +

                            '&tujuan=' +
                            encodeURIComponent(tujuan)

                    }
                )

                    .then(
                        function (res) {

                            return res.text();

                        }
                    )

                    .then(
                        function (res) {

                            const response =
                                res.trim();


                            if (response === 'OK') {

                                Swal.fire({

                                    icon:
                                        'success',

                                    title:
                                        'Ditolak!',

                                    text:
                                        'Pengajuan berhasil ditolak dan notifikasi WA dikirim.',

                                    confirmButtonColor:
                                        '#10294d'

                                }).then(
                                    function () {

                                        window.location.href =
                                            'umum_dashboard.php';

                                    }
                                );


                            } else {

                                Swal.fire({

                                    icon:
                                        'error',

                                    title:
                                        'Gagal!',

                                    text:
                                        'Terjadi kesalahan saat menolak pengajuan.',

                                    confirmButtonColor:
                                        '#10294d'

                                });

                            }

                        }
                    )

                    .catch(
                        function () {

                            Swal.fire({

                                icon:
                                    'error',

                                title:
                                    'Koneksi Gagal!',

                                text:
                                    'Tidak dapat terhubung ke server.',

                                confirmButtonColor:
                                    '#10294d'

                            });

                        }
                    );

            }
        );

    }


    /* =====================================================
       ESCAPE HTML
    ===================================================== */

    function escapeHtml(value) {

        return String(value ?? '')

            .replace(
                /&/g,
                '&amp;'
            )

            .replace(
                /</g,
                '&lt;'
            )

            .replace(
                />/g,
                '&gt;'
            )

            .replace(
                /"/g,
                '&quot;'
            )

            .replace(
                /'/g,
                '&#039;'
            );

    }

</script>