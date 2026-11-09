<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'sekwan'
) {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';


/* =====================================================
   AMBIL ID PENGAJUAN
===================================================== */

$id = $_GET['id'] ?? null;


/* =====================================================
   VALIDASI ID
===================================================== */

if (!$id || !is_numeric($id)) {

    include 'header_sekwan.php';
    include 'sidebar_sekwan.php';

    ?>

    <style>

        .main-content-sekwan {
            margin-left: 250px;
            padding: 92px 25px 85px;
            min-height: 100vh;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
        }

        .alert-error {
            background: #fff;
            border: 1px solid #f1c5c9;
            border-left: 5px solid #dc3545;
            border-radius: 13px;
            padding: 20px;
            color: #842029;
            box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
        }

        .alert-error h3 {
            margin: 0;
            font-size: 16px;
        }

        @media (max-width: 768px) {
            .main-content-sekwan {
                margin-left: 0;
                padding: 80px 12px 80px;
            }
        }

    </style>

    <main class="main-content-sekwan">

        <div class="alert-error">

            <h3>
                <i class="fa-solid fa-circle-exclamation"></i>
                Error: ID pengajuan tidak valid.
            </h3>

        </div>

    </main>

    <?php

    include 'footer.php';

    exit;
}


/* =====================================================
   AMBIL DATA SPPD
===================================================== */

try {

    $query = "
        SELECT
            p.*,

            COALESCE(
                u.nama,
                p.nama,
                'Tidak Diketahui'
            ) AS nama_pegawai,

            COALESCE(
                u.jabatan,
                'Staf'
            ) AS jabatan_pegawai,

            u.wa_phone

        FROM pengajuan p

        LEFT JOIN users u
            ON p.pegawai_id = u.id

        WHERE p.id = ?
    ";


    $stmt = $pdo->prepare($query);

    $stmt->execute([
        $id
    ]);


    $sppd = $stmt->fetch();


    /* ================================================
       DATA TIDAK DITEMUKAN
    ================================================= */

    if (!$sppd) {

        include 'header_sekwan.php';
        include 'sidebar_sekwan.php';

        ?>

        <style>

            .main-content-sekwan {
                margin-left: 250px;
                padding: 92px 25px 85px;
                min-height: 100vh;
                font-family: "Inter", "Segoe UI", Arial, sans-serif;
                background: #f4f7fb;
            }

            .alert-error {
                background: #fff;
                border: 1px solid #f1c5c9;
                border-left: 5px solid #dc3545;
                border-radius: 13px;
                padding: 20px;
                color: #842029;
                box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
            }

            .alert-error h3 {
                margin: 0 0 6px;
                font-size: 16px;
            }

            .alert-error p {
                margin: 0;
                font-size: 13px;
            }

            @media (max-width: 768px) {
                .main-content-sekwan {
                    margin-left: 0;
                    padding: 80px 12px 80px;
                }
            }

        </style>


        <main class="main-content-sekwan">

            <div class="alert-error">

                <h3>
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    Data Tidak Ditemukan
                </h3>

                <p>
                    Data pengajuan SPPD dengan ID
                    #<?php echo htmlspecialchars($id); ?>
                    tidak ditemukan.
                </p>

            </div>

        </main>


        <?php

        include 'footer.php';

        exit;
    }


} catch (PDOException $e) {

    include 'header_sekwan.php';
    include 'sidebar_sekwan.php';

    ?>

    <style>

        .main-content-sekwan {
            margin-left: 250px;
            padding: 92px 25px 85px;
            min-height: 100vh;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
            background: #f4f7fb;
        }

        .alert-error {
            background: #fff;
            border: 1px solid #f1c5c9;
            border-left: 5px solid #dc3545;
            border-radius: 13px;
            padding: 20px;
            color: #842029;
            box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
        }

        .alert-error h3 {
            margin: 0 0 8px;
            font-size: 16px;
        }

        .alert-error p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .main-content-sekwan {
                margin-left: 0;
                padding: 80px 12px 80px;
            }
        }

    </style>


    <main class="main-content-sekwan">

        <div class="alert-error">

            <h3>
                <i class="fa-solid fa-database"></i>
                Database Error
            </h3>

            <p>
                <?php
                echo htmlspecialchars(
                    $e->getMessage()
                );
                ?>
            </p>

        </div>

    </main>


    <?php

    include 'footer.php';

    exit;
}


/* =====================================================
   STATUS BADGE
===================================================== */

$status_colors = [

    'selesai' =>
        '#28a745',

    'ditolak' =>
        '#dc3545',

    'diajukan' =>
        '#f0ad00',

    'paraf_sekwan' =>
        '#17a2b8',

    'ttd_ketua' =>
        '#007bff',

    'dicap' =>
        '#6f42c1'

];


$current_status =
    $sppd['status'];


$badge_color =
    $status_colors[$current_status]
    ?? '#6c757d';


/* =====================================================
   STATUS CLASS
===================================================== */

$status_class = 'status-default';

switch ($current_status) {

    case 'selesai':

        $status_class =
            'status-success';

        break;


    case 'ditolak':

        $status_class =
            'status-danger';

        break;


    case 'diajukan':

        $status_class =
            'status-warning';

        break;


    case 'paraf_sekwan':

        $status_class =
            'status-info';

        break;


    case 'ttd_ketua':

        $status_class =
            'status-primary';

        break;


    case 'dicap':

        $status_class =
            'status-purple';

        break;

}


/* =====================================================
   STATUS ICON
===================================================== */

$status_icon = 'fa-clock';

switch ($current_status) {

    case 'selesai':
        $status_icon = 'fa-circle-check';
        break;

    case 'ditolak':
        $status_icon = 'fa-circle-xmark';
        break;

    case 'diajukan':
        $status_icon = 'fa-paper-plane';
        break;

    case 'paraf_sekwan':
        $status_icon = 'fa-file-signature';
        break;

    case 'ttd_ketua':
        $status_icon = 'fa-signature';
        break;

    case 'dicap':
        $status_icon = 'fa-stamp';
        break;

}


/* =====================================================
   HEADER & SIDEBAR
===================================================== */

include 'header_sekwan.php';

include 'sidebar_sekwan.php';

?>


<style>

/* =====================================================
   RESET
===================================================== */

* {
    box-sizing: border-box;
}


/* =====================================================
   BODY
===================================================== */

body {

    margin: 0;

    padding: 0;

    background:
        #f4f7fb;

}


/* =====================================================
   MAIN CONTENT
===================================================== */

.main-content-sekwan {

    margin-left:
        250px;

    padding:
        92px 25px 85px;

    min-height:
        100vh;

    font-family:
        "Inter",
        "Segoe UI",
        Arial,
        sans-serif;

    transition:
        margin-left .25s ease;

}


/* =====================================================
   PAGE HEADER
===================================================== */

.page-header {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        20px;

    padding:
        22px 24px;

    margin-bottom:
        20px;

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-radius:
        14px;

    box-shadow:
        0 4px 16px
        rgba(16, 41, 77, .07);

}


.page-header-content {

    display:
        flex;

    align-items:
        center;

    gap:
        14px;

    min-width:
        0;

}


.page-header-icon {

    width:
        52px;

    height:
        52px;

    min-width:
        52px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        13px;

    background:
        #10294d;

    color:
        #f2bd4b;

    font-size:
        21px;

}


.page-header-text {

    min-width:
        0;

}


.page-header h1 {

    margin:
        0;

    color:
        #10294d;

    font-size:
        23px;

    font-weight:
        750;

    line-height:
        1.3;

}


.page-header p {

    margin:
        6px 0 0;

    color:
        #64748b;

    font-size:
        13px;

}


/* =====================================================
   ACTION TOP
===================================================== */

.action-top {

    display:
        flex;

    align-items:
        center;

    gap:
        8px;

    flex-shrink:
        0;

}


.btn-action {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        7px;

    min-height:
        40px;

    padding:
        9px 14px;

    border:
        none;

    border-radius:
        8px;

    font-family:
        inherit;

    font-size:
        12px;

    font-weight:
        650;

    text-decoration:
        none;

    cursor:
        pointer;

    transition:
        background .2s ease,
        transform .2s ease,
        box-shadow .2s ease;

}


.btn-print {

    background:
        #28a745;

    color:
        #fff;

    box-shadow:
        0 3px 8px
        rgba(40, 167, 69, .18);

}


.btn-print:hover {

    background:
        #218838;

    color:
        #fff;

    transform:
        translateY(-1px);

}


.btn-back {

    background:
        #eef1f5;

    color:
        #475569;

}


.btn-back:hover {

    background:
        #dfe5ec;

    color:
        #10294d;

    transform:
        translateY(-1px);

}


/* =====================================================
   STATUS BANNER
===================================================== */

.status-banner {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        20px;

    padding:
        17px 20px;

    margin-bottom:
        20px;

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-left:
        5px solid
        <?php echo $badge_color; ?>;

    border-radius:
        12px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

}


.status-current {

    display:
        flex;

    align-items:
        center;

    gap:
        10px;

}


.status-label {

    color:
        #64748b;

    font-size:
        13px;

}


.status-badge {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        6px;

    padding:
        6px 11px;

    border-radius:
        20px;

    color:
        #fff;

    background:
        <?php echo $badge_color; ?>;

    font-size:
        11px;

    font-weight:
        700;

    white-space:
        nowrap;

}


.urgency-box {

    display:
        flex;

    align-items:
        center;

    gap:
        9px;

}


.urgency-label {

    color:
        #64748b;

    font-size:
        13px;

}


.urgency-value {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        5px;

    padding:
        6px 10px;

    border-radius:
        7px;

    background:
        #fff5d6;

    color:
        #9a6b00;

    font-size:
        12px;

    font-weight:
        700;

}


/* =====================================================
   INFORMATION GRID
===================================================== */

.info-grid {

    display:
        grid;

    grid-template-columns:
        1fr 1fr;

    gap:
        20px;

    margin-bottom:
        20px;

}


/* =====================================================
   INFO CARD
===================================================== */

.info-card {

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-radius:
        13px;

    overflow:
        hidden;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

}


.info-card-header {

    display:
        flex;

    align-items:
        center;

    gap:
        10px;

    padding:
        16px 18px;

    border-bottom:
        1px solid #edf1f5;

}


.info-card-header-icon {

    width:
        37px;

    height:
        37px;

    min-width:
        37px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        9px;

    background:
        #eef4fb;

    color:
        #17477f;

    font-size:
        15px;

}


.info-card-header h3 {

    margin:
        0;

    color:
        #10294d;

    font-size:
        14px;

    font-weight:
        700;

}


.info-card-body {

    padding:
        5px 18px 14px;

}


/* =====================================================
   INFO TABLE
===================================================== */

.table-info {

    width:
        100%;

    border-collapse:
        collapse;

}


.table-info tr {

    border-bottom:
        1px solid #f0f3f6;

}


.table-info tr:last-child {

    border-bottom:
        none;

}


.table-info th {

    width:
        38%;

    padding:
        11px 10px 11px 0;

    text-align:
        left;

    vertical-align:
        top;

    color:
        #64748b;

    font-size:
        12px;

    font-weight:
        650;

}


.table-info td {

    padding:
        11px 0;

    color:
        #334155;

    font-size:
        13px;

    line-height:
        1.5;

}


.table-info td strong {

    color:
        #10294d;

    font-weight:
        700;

}


/* =====================================================
   FULL WIDTH CARD
===================================================== */

.full-width-card {

    margin-bottom:
        20px;

}


/* =====================================================
   ALASAN PERJALANAN
===================================================== */

.reason-box {

    padding:
        13px 15px;

    margin:
        5px 0 12px;

    background:
        #f8fafc;

    border:
        1px solid #e7edf4;

    border-radius:
        9px;

    color:
        #334155;

    font-size:
        13px;

    line-height:
        1.7;

}


/* =====================================================
   NOMOR SURAT
===================================================== */

.document-number {

    display:
        flex;

    flex-wrap:
        wrap;

    gap:
        8px;

}


.document-item {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        6px;

    padding:
        7px 10px;

    border:
        1px solid #e0e7ef;

    border-radius:
        7px;

    background:
        #f8fafc;

    color:
        #475569;

    font-size:
        12px;

}


.document-item strong {

    color:
        #10294d;

}


/* =====================================================
   FOLLOWER
===================================================== */

.follower-box {

    white-space:
        pre-line;

}


/* =====================================================
   REJECTION
===================================================== */

.rejection-card {

    background:
        #fff;

    border:
        1px solid #f1c5c9;

    border-left:
        5px solid #dc3545;

    border-radius:
        13px;

    padding:
        17px 19px;

    margin-bottom:
        20px;

    box-shadow:
        0 4px 14px
        rgba(220, 53, 69, .06);

}


.rejection-title {

    display:
        flex;

    align-items:
        center;

    gap:
        8px;

    margin-bottom:
        8px;

    color:
        #b02a37;

    font-size:
        13px;

    font-weight:
        700;

}


.rejection-text {

    margin:
        0;

    color:
        #842029;

    font-size:
        13px;

    line-height:
        1.6;

}


/* =====================================================
   ALERT ERROR
===================================================== */

.alert-error {

    background:
        #fff;

    border:
        1px solid #f1c5c9;

    border-left:
        5px solid #dc3545;

    border-radius:
        13px;

    padding:
        20px;

    color:
        #842029;

}


.alert-error h3 {

    margin:
        0 0 8px;

    font-size:
        16px;

}


.alert-error p {

    margin:
        0;

    font-size:
        13px;

    line-height:
        1.6;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 1000px) {

    .main-content-sekwan {

        margin-left:
            250px;

        padding:
            88px 18px 82px;

    }


    .page-header {

        align-items:
            flex-start;

    }


    .info-grid {

        gap:
            15px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .main-content-sekwan {

        margin-left:
            0;

        width:
            100%;

        padding:
            80px 12px 80px;

    }


    .page-header {

        flex-direction:
            column;

        align-items:
            stretch;

        padding:
            17px;

    }


    .page-header-content {

        width:
            100%;

    }


    .page-header-icon {

        width:
            45px;

        height:
            45px;

        min-width:
            45px;

    }


    .page-header h1 {

        font-size:
            19px;

    }


    .page-header p {

        font-size:
            12px;

    }


    .action-top {

        width:
            100%;

    }


    .btn-action {

        flex:
            1;

    }


    .status-banner {

        flex-direction:
            column;

        align-items:
            flex-start;

        gap:
            12px;

        padding:
            15px;

    }


    .status-current,
    .urgency-box {

        width:
            100%;

        justify-content:
            space-between;

    }


    .info-grid {

        grid-template-columns:
            1fr;

    }


    .info-card-body {

        padding:
            4px 15px 12px;

    }


    .table-info th {

        width:
            40%;

        font-size:
            11px;

    }


    .table-info td {

        font-size:
            12px;

    }


    .document-number {

        flex-direction:
            column;

        align-items:
            stretch;

    }


    .document-item {

        width:
            100%;

    }

}


/* =====================================================
   MOBILE KECIL
===================================================== */

@media (max-width: 480px) {

    .main-content-sekwan {

        padding:
            76px 10px 78px;

    }


    .page-header {

        padding:
            15px;

    }


    .page-header-content {

        gap:
            10px;

    }


    .page-header-icon {

        width:
            40px;

        height:
            40px;

        min-width:
            40px;

        font-size:
            16px;

    }


    .page-header h1 {

        font-size:
            17px;

    }


    .page-header p {

        font-size:
            11px;

    }


    .btn-action {

        padding:
            9px 10px;

        font-size:
            11px;

    }


    .status-current,
    .urgency-box {

        align-items:
            flex-start;

        flex-direction:
            column;

        gap:
            6px;

    }


    .status-badge,
    .urgency-value {

        width:
            fit-content;

    }


    .info-card-header {

        padding:
            14px;

    }


    .info-card-body {

        padding:
            4px 13px 10px;

    }


    .table-info th {

        width:
            42%;

        padding-right:
            8px;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    @page {

        size:
            A4;

        margin:
            12mm;

    }


    body {

        background:
            #fff !important;

    }


    .main-content-sekwan {

        margin:
            0 !important;

        padding:
            0 !important;

        width:
            100% !important;

        min-height:
            auto !important;

    }


    .sekwan-header,
    .sidebar-sekwan,
    .sidebar-mobile-overlay,
    .action-top,
    footer {

        display:
            none !important;

    }


    .page-header {

        display:
            block;

        margin:
            0 0 15px;

        padding:
            0 0 10px;

        background:
            none !important;

        border:
            none;

        border-bottom:
            2px solid #000;

        border-radius:
            0;

        box-shadow:
            none;

    }


    .page-header-content {

        display:
            block;

    }


    .page-header-icon {

        display:
            none;

    }


    .page-header h1 {

        color:
            #000 !important;

        font-size:
            20px;

    }


    .page-header p {

        display:
            block;

        color:
            #444 !important;

        font-size:
            11px;

    }


    .status-banner {

        box-shadow:
            none;

        border:
            1px solid #999;

        border-left:
            4px solid #000;

        margin-bottom:
            15px;

    }


    .status-badge {

        background:
            #eee !important;

        color:
            #000 !important;

        border:
            1px solid #888;

    }


    .urgency-value {

        background:
            #eee !important;

        color:
            #000 !important;

        border:
            1px solid #aaa;

    }


    .info-grid {

        grid-template-columns:
            1fr 1fr;

        gap:
            12px;

        margin-bottom:
            12px;

    }


    .info-card,
    .rejection-card {

        box-shadow:
            none;

        border:
            1px solid #999;

        break-inside:
            avoid;

    }


    .info-card-header {

        background:
            #eee !important;

    }


    .info-card-header-icon {

        display:
            none;

    }


    .table-info th {

        background:
            #f2f2f2;

        color:
            #000 !important;

    }


    .table-info td {

        color:
            #000 !important;

    }


    .reason-box,
    .document-item {

        background:
            #fff !important;

        color:
            #000 !important;

        border:
            1px solid #bbb;

    }


    .document-item strong {

        color:
            #000 !important;

    }


    .rejection-card {

        border-left:
            4px solid #000;

    }


    .rejection-title,
    .rejection-text {

        color:
            #000 !important;

    }

}

</style>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<main class="main-content-sekwan">


    <!-- =================================================
         HEADER DETAIL
    ================================================== -->

    <section class="page-header">


        <div class="page-header-content">


            <div class="page-header-icon">

                <i
                    class="fa-solid fa-file-lines"
                ></i>

            </div>


            <div class="page-header-text">

                <h1>
                    Detail Pengajuan SPPD - Sekwan
                </h1>

                <p>

                    Informasi lengkap perjalanan dinas
                    #

                    <?php
                    echo htmlspecialchars(
                        $sppd['id']
                    );
                    ?>

                </p>

            </div>


        </div>


        <!-- =============================================
             AKSI
        ============================================== -->

        <div class="action-top">


            <button
                type="button"
                class="btn-action btn-print"
                onclick="window.print()"
                title="Cetak dokumen"
            >

                <i
                    class="fa-solid fa-print"
                ></i>

                Cetak

            </button>


            <a
                href="laporan_sekwan.php"
                class="btn-action btn-back"
                title="Kembali ke laporan"
            >

                <i
                    class="fa-solid fa-arrow-left"
                ></i>

                Kembali

            </a>


        </div>


    </section>


    <!-- =================================================
         STATUS
    ================================================== -->

    <section
        class="status-banner"
        style="border-left-color:
        <?php echo htmlspecialchars($badge_color); ?>;"
    >


        <div class="status-current">

            <span class="status-label">

                Status Saat Ini

            </span>


            <span
                class="status-badge"
                style="background:
                <?php echo htmlspecialchars($badge_color); ?>;"
            >

                <i
                    class="fa-solid
                    <?php echo htmlspecialchars($status_icon); ?>"
                ></i>


                <?php

                echo strtoupper(
                    str_replace(
                        '_',
                        ' ',
                        $sppd['status']
                    )
                );

                ?>

            </span>

        </div>


        <div class="urgency-box">

            <span class="urgency-label">

                Urgensi

            </span>


            <span class="urgency-value">

                <i
                    class="fa-solid fa-bolt"
                ></i>

                <?php
                echo htmlspecialchars(
                    ucfirst(
                        $sppd['urgensi']
                    )
                );
                ?>

            </span>

        </div>


    </section>


    <!-- =================================================
         GRID INFORMASI
    ================================================== -->

    <div class="info-grid">


        <!-- =============================================
             INFORMASI PEGAWAI
        ============================================== -->

        <section class="info-card">


            <div class="info-card-header">

                <div class="info-card-header-icon">

                    <i
                        class="fa-solid fa-user"
                    ></i>

                </div>


                <h3>
                    Informasi Pegawai
                </h3>

            </div>


            <div class="info-card-body">


                <table class="table-info">


                    <tr>

                        <th>
                            Nama Pemohon
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['nama_pegawai']
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Jabatan
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['jabatan_pegawai']
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Pangkat / Golongan
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['pangkat'] ?? '-'
                            );
                            ?>

                            /

                            <?php
                            echo htmlspecialchars(
                                $sppd['golongan'] ?? '-'
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Fraksi / Komisi
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['fraksi'] ?: '-'
                            );
                            ?>

                            /

                            <?php
                            echo htmlspecialchars(
                                $sppd['komisi'] ?: '-'
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            No. WhatsApp
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['wa_phone'] ?? '-'
                            );
                            ?>

                        </td>

                    </tr>


                </table>


            </div>


        </section>


        <!-- =============================================
             DETAIL PERJALANAN
        ============================================== -->

        <section class="info-card">


            <div class="info-card-header">

                <div class="info-card-header-icon">

                    <i
                        class="fa-solid fa-route"
                    ></i>

                </div>


                <h3>
                    Detail Perjalanan Dinas
                </h3>

            </div>


            <div class="info-card-body">


                <table class="table-info">


                    <tr>

                        <th>
                            Tujuan
                        </th>

                        <td>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $sppd['tujuan']
                                );
                                ?>

                            </strong>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Tanggal Berangkat
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['tanggal_berangkat']
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Tanggal Kembali
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['tanggal_kembali']
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Alat Angkutan
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['alat_angkutan']
                            );
                            ?>

                        </td>

                    </tr>


                    <tr>

                        <th>
                            Instansi Anggaran
                        </th>

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $sppd['instansi_anggaran']
                            );
                            ?>

                        </td>

                    </tr>


                </table>


            </div>


        </section>


    </div>


    <!-- =================================================
         MAKSUD & ADMINISTRASI
    ================================================== -->

    <section class="info-card full-width-card">


        <div class="info-card-header">

            <div class="info-card-header-icon">

                <i
                    class="fa-solid fa-folder-open"
                ></i>

            </div>


            <h3>
                Maksud & Administrasi Surat
            </h3>

        </div>


        <div class="info-card-body">


            <table class="table-info">


                <!-- =====================================
                     MAKSUD
                ====================================== -->

                <tr>

                    <th>
                        Maksud Perjalanan
                    </th>

                    <td>

                        <div class="reason-box">

                            <?php

                            echo nl2br(
                                htmlspecialchars(
                                    $sppd['alasan']
                                )
                            );

                            ?>

                        </div>

                    </td>

                </tr>


                <!-- =====================================
                     NOMOR SPT / SPD
                ====================================== -->

                <tr>

                    <th>
                        No. SPT / SPD
                    </th>

                    <td>

                        <div class="document-number">


                            <div class="document-item">

                                <i
                                    class="fa-solid fa-file-signature"
                                ></i>

                                SPT:

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $sppd['spt_no']
                                        ?? 'Belum ada'
                                    );
                                    ?>

                                </strong>

                            </div>


                            <div class="document-item">

                                <i
                                    class="fa-solid fa-file-lines"
                                ></i>

                                SPD:

                                <strong>

                                    <?php
                                    echo htmlspecialchars(
                                        $sppd['spd_no']
                                        ?? 'Belum ada'
                                    );
                                    ?>

                                </strong>

                            </div>


                        </div>

                    </td>

                </tr>


                <!-- =====================================
                     PENGIKUT
                ====================================== -->

                <tr>

                    <th>
                        Pengikut
                    </th>

                    <td>

                        <div class="follower-box">

                            <?php
                            echo htmlspecialchars(
                                $sppd['pengikut']
                                ?? 'Tidak ada pengikut'
                            );
                            ?>

                        </div>

                    </td>

                </tr>


            </table>


        </div>


    </section>


    <!-- =================================================
         ALASAN PENOLAKAN
    ================================================== -->

    <?php if (!empty($sppd['alasan_penolakan'])): ?>


        <section class="rejection-card">


            <div class="rejection-title">

                <i
                    class="fa-solid fa-circle-exclamation"
                ></i>

                Alasan Penolakan

            </div>


            <p class="rejection-text">

                <?php
                echo htmlspecialchars(
                    $sppd['alasan_penolakan']
                );
                ?>

            </p>


        </section>


    <?php endif; ?>


</main>


<?php include 'footer.php'; ?>
