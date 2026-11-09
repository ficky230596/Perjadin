<?php

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
   FILTER TANGGAL
===================================================== */

$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';

$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';


/* =====================================================
   QUERY DATA LAPORAN SEKWAN
   Mencakup dokumen yang sudah diparaf Sekwan
   atau sudah berada pada tahap selanjutnya.
===================================================== */

$query = "
    SELECT
        p.*,
        COALESCE(
            u.nama,
            p.nama,
            'Tidak Diketahui'
        ) AS nama_pegawai

    FROM pengajuan p

    LEFT JOIN users u
        ON p.pegawai_id = u.id

    WHERE p.status IN (
        'paraf_sekwan',
        'ttd_ketua',
        'dicap',
        'selesai'
    )
";

$params = [];


/* =====================================================
   FILTER PERIODE TANGGAL
===================================================== */

if (
    !empty($tanggal_mulai) &&
    !empty($tanggal_selesai)
) {

    $query .= "
        AND p.tanggal_berangkat BETWEEN ? AND ?
    ";

    $params = [
        $tanggal_mulai,
        $tanggal_selesai
    ];

}


$query .= "
    ORDER BY p.id DESC
";


$stmt = $pdo->prepare($query);

$stmt->execute($params);

$laporanList = $stmt->fetchAll();


/* =====================================================
   HEADER & SIDEBAR
===================================================== */

include 'header_sekwan.php';

include 'sidebar_sekwan.php';

?>


<!-- =====================================================
     SWEETALERT2
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


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
   KONTEN UTAMA
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
   HEADER HALAMAN
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

    background:
        #fff;

    padding:
        22px 24px;

    margin-bottom:
        20px;

    border:
        1px solid #e8edf4;

    border-radius:
        14px;

    box-shadow:
        0 4px 16px
        rgba(16, 41, 77, .07);

}


.page-header-content {

    min-width:
        0;

}


.page-header h1 {

    margin:
        0;

    color:
        #10294d;

    font-size:
        24px;

    font-weight:
        750;

    line-height:
        1.3;

}


.page-header p {

    margin:
        7px 0 0;

    color:
        #64748b;

    font-size:
        14px;

    line-height:
        1.6;

}


/* =====================================================
   ICON HEADER
===================================================== */

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


/* =====================================================
   TOMBOL CETAK
===================================================== */

.btn-cetak {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        8px;

    min-height:
        42px;

    padding:
        10px 16px;

    border:
        none;

    border-radius:
        8px;

    background:
        #28a745;

    color:
        #fff;

    font-family:
        inherit;

    font-size:
        13px;

    font-weight:
        650;

    cursor:
        pointer;

    text-decoration:
        none;

    box-shadow:
        0 3px 8px
        rgba(40, 167, 69, .18);

    transition:
        background .2s ease,
        transform .2s ease,
        box-shadow .2s ease;

}


.btn-cetak:hover {

    background:
        #218838;

    color:
        #fff;

    transform:
        translateY(-1px);

    box-shadow:
        0 5px 12px
        rgba(40, 167, 69, .24);

}


/* =====================================================
   FILTER CARD
===================================================== */

.filter-card {

    background:
        #fff;

    padding:
        18px 20px;

    margin-bottom:
        20px;

    border:
        1px solid #e8edf4;

    border-radius:
        13px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

}


.filter-title {

    display:
        flex;

    align-items:
        center;

    gap:
        9px;

    margin-bottom:
        15px;

    color:
        #10294d;

    font-size:
        14px;

    font-weight:
        700;

}


.filter-title i {

    color:
        #f2bd4b;

}


.filter-form {

    display:
        flex;

    align-items:
        flex-end;

    flex-wrap:
        wrap;

    gap:
        12px;

}


.filter-group {

    display:
        flex;

    flex-direction:
        column;

    gap:
        6px;

    min-width:
        190px;

}


.filter-group label {

    color:
        #475569;

    font-size:
        12px;

    font-weight:
        600;

}


.filter-group input {

    width:
        100%;

    height:
        40px;

    padding:
        8px 11px;

    border:
        1px solid #d6dee8;

    border-radius:
        7px;

    background:
        #fff;

    color:
        #334155;

    font-family:
        inherit;

    font-size:
        13px;

    outline:
        none;

    transition:
        border-color .2s ease,
        box-shadow .2s ease;

}


.filter-group input:focus {

    border-color:
        #17477f;

    box-shadow:
        0 0 0 3px
        rgba(23, 71, 127, .10);

}


/* =====================================================
   AKSI FILTER
===================================================== */

.filter-actions {

    display:
        flex;

    align-items:
        center;

    gap:
        8px;

}


.btn-filter {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        7px;

    height:
        40px;

    padding:
        8px 15px;

    border:
        none;

    border-radius:
        7px;

    background:
        #17477f;

    color:
        #fff;

    font-family:
        inherit;

    font-size:
        13px;

    font-weight:
        650;

    cursor:
        pointer;

    text-decoration:
        none;

    transition:
        background .2s ease,
        transform .2s ease;

}


.btn-filter:hover {

    background:
        #10294d;

    color:
        #fff;

    transform:
        translateY(-1px);

}


.btn-reset {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        7px;

    height:
        40px;

    padding:
        8px 14px;

    border:
        1px solid #d6dee8;

    border-radius:
        7px;

    background:
        #fff;

    color:
        #64748b;

    font-size:
        13px;

    font-weight:
        600;

    text-decoration:
        none;

    transition:
        background .2s ease,
        border-color .2s ease,
        color .2s ease;

}


.btn-reset:hover {

    background:
        #f8fafc;

    border-color:
        #b8c4d2;

    color:
        #10294d;

}


/* =====================================================
   RINGKASAN LAPORAN
===================================================== */

.report-summary {

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        15px;

    margin-bottom:
        14px;

}


.report-summary-title {

    display:
        flex;

    align-items:
        center;

    gap:
        8px;

    color:
        #10294d;

    font-size:
        15px;

    font-weight:
        700;

}


.report-summary-title i {

    color:
        #f2bd4b;

}


.report-count {

    padding:
        5px 10px;

    border-radius:
        20px;

    background:
        #eef4fb;

    color:
        #17477f;

    font-size:
        11px;

    font-weight:
        700;

}


/* =====================================================
   TABLE CONTAINER
===================================================== */

.table-card {

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-radius:
        13px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

    overflow:
        hidden;

}


.table-scroll {

    width:
        100%;

    overflow-x:
        auto;

    -webkit-overflow-scrolling:
        touch;

}


/* =====================================================
   TABLE
===================================================== */

.table-laporan {

    width:
        100%;

    min-width:
        850px;

    border-collapse:
        collapse;

}


.table-laporan thead tr {

    background:
        linear-gradient(
            135deg,
            #10294d 0%,
            #17477f 100%
        );

    color:
        #fff;

}


.table-laporan th {

    padding:
        13px 16px;

    text-align:
        left;

    font-size:
        12px;

    font-weight:
        700;

    white-space:
        nowrap;

}


.table-laporan td {

    padding:
        13px 16px;

    color:
        #475569;

    font-size:
        13px;

    border-bottom:
        1px solid #edf1f5;

    vertical-align:
        middle;

}


.table-laporan tbody tr {

    transition:
        background .2s ease;

}


.table-laporan tbody tr:hover {

    background:
        #f8fafc;

}


.table-laporan tbody tr:last-child td {

    border-bottom:
        none;

}


/* =====================================================
   ID PENGAJUAN
===================================================== */

.id-badge {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        4px;

    color:
        #10294d;

    font-weight:
        750;

}


/* =====================================================
   NAMA PEGAWAI
===================================================== */

.pegawai-name {

    color:
        #10294d;

    font-weight:
        650;

}


/* =====================================================
   STATUS
===================================================== */

.status-badge {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        6px;

    padding:
        5px 10px;

    border-radius:
        20px;

    font-size:
        11px;

    font-weight:
        650;

    white-space:
        nowrap;

}


.status-paraf {

    background:
        #fff5d6;

    color:
        #9a6b00;

}


.status-ketua {

    background:
        #e8f0ff;

    color:
        #2456a6;

}


.status-dicap {

    background:
        #eeeafa;

    color:
        #62529a;

}


.status-selesai {

    background:
        #e2f5e9;

    color:
        #26733e;

}


/* =====================================================
   TOMBOL DETAIL
===================================================== */

.btn-detail {

    display:
        inline-flex;

    align-items:
        center;

    justify-content:
        center;

    gap:
        6px;

    padding:
        8px 12px;

    border:
        1px solid #d6e3f2;

    border-radius:
        7px;

    background:
        #f4f8fd;

    color:
        #17477f;

    text-decoration:
        none;

    font-size:
        12px;

    font-weight:
        650;

    transition:
        background .2s ease,
        border-color .2s ease,
        color .2s ease,
        transform .2s ease;

}


.btn-detail:hover {

    background:
        #17477f;

    border-color:
        #17477f;

    color:
        #fff;

    transform:
        translateY(-1px);

}


/* =====================================================
   EMPTY STATE
===================================================== */

.empty-laporan {

    background:
        #fff;

    min-height:
        240px;

    padding:
        35px 25px;

    display:
        flex;

    flex-direction:
        column;

    align-items:
        center;

    justify-content:
        center;

    text-align:
        center;

    border:
        1px solid #e8edf4;

    border-radius:
        13px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

}


.empty-icon {

    width:
        60px;

    height:
        60px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    margin-bottom:
        14px;

    border-radius:
        50%;

    background:
        #eef4fb;

    color:
        #17477f;

    font-size:
        24px;

}


.empty-laporan h3 {

    margin:
        0;

    color:
        #10294d;

    font-size:
        16px;

}


.empty-laporan p {

    margin:
        7px 0 0;

    color:
        #64748b;

    font-size:
        13px;

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


    .filter-form {

        align-items:
            stretch;

    }


    .filter-group {

        flex:
            1;

        min-width:
            160px;

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

        align-items:
            flex-start;

        padding:
            18px;

    }


    .page-header h1 {

        font-size:
            19px;

    }


    .page-header p {

        font-size:
            12px;

    }


    .page-header-icon {

        width:
            45px;

        height:
            45px;

        min-width:
            45px;

        font-size:
            18px;

    }


    .filter-card {

        padding:
            16px;

    }


    .filter-form {

        flex-direction:
            column;

        align-items:
            stretch;

    }


    .filter-group {

        width:
            100%;

        min-width:
            0;

    }


    .filter-actions {

        width:
            100%;

    }


    .btn-filter,
    .btn-reset {

        flex:
            1;

    }


    .report-summary {

        align-items:
            flex-start;

    }


    .table-card {

        border-radius:
            10px;

    }


    .table-scroll {

        overflow-x:
            auto;

    }


    .btn-cetak {

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


    .filter-title {

        font-size:
            13px;

    }


    .report-summary-title {

        font-size:
            14px;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    @page {

        size:
            landscape;

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


    header,
    .sekwan-header,
    .sidebar-sekwan,
    .sidebar-mobile-overlay,
    .filter-card,
    .btn-cetak,
    .btn-detail,
    .report-count {

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
            12px;

    }


    .page-header-icon {

        display:
            none;

    }


    .report-summary {

        margin-bottom:
            10px;

    }


    .report-summary-title {

        color:
            #000;

    }


    .table-card {

        border:
            none;

        box-shadow:
            none;

        overflow:
            visible;

    }


    .table-scroll {

        overflow:
            visible;

    }


    .table-laporan {

        width:
            100%;

        min-width:
            0;

        border:
            1px solid #aaa;

    }


    .table-laporan th {

        background:
            #eee !important;

        color:
            #000 !important;

        border:
            1px solid #aaa;

        padding:
            8px;

    }


    .table-laporan td {

        color:
            #000 !important;

        border:
            1px solid #aaa;

        padding:
            8px;

    }


    .status-badge {

        background:
            transparent !important;

        color:
            #000 !important;

        padding:
            0;

    }


    footer {

        display:
            none !important;

    }

}

</style>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<main class="main-content-sekwan">


    <!-- =================================================
         HEADER LAPORAN
    ================================================== -->

    <section class="page-header">

        <div class="page-header-content">

            <h1>
                Laporan Pengajuan SPPD - Sekwan
            </h1>

            <p>
                Rekapitulasi data perjalanan dinas yang telah
                melalui proses paraf Sekwan.
            </p>

        </div>


        <div class="page-header-icon">

            <i
                class="fa-solid fa-file-lines"
            ></i>

        </div>


        <button
            type="button"
            class="btn-cetak"
            onclick="window.print()"
            title="Cetak laporan"
        >

            <i
                class="fa-solid fa-print"
            ></i>

            Cetak Laporan

        </button>

    </section>


    <!-- =================================================
         FILTER TANGGAL
    ================================================== -->

    <section class="filter-card">

        <div class="filter-title">

            <i
                class="fa-solid fa-filter"
            ></i>

            Filter Berdasarkan Tanggal

        </div>


        <form
            method="GET"
            action=""
            class="filter-form"
        >


            <!-- =========================================
                 TANGGAL MULAI
            ========================================== -->

            <div class="filter-group">

                <label for="tanggal_mulai">

                    Dari Tanggal

                </label>


                <input
                    type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="<?php
                    echo htmlspecialchars(
                        $tanggal_mulai
                    );
                    ?>"
                >

            </div>


            <!-- =========================================
                 TANGGAL SELESAI
            ========================================== -->

            <div class="filter-group">

                <label for="tanggal_selesai">

                    Sampai Tanggal

                </label>


                <input
                    type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="<?php
                    echo htmlspecialchars(
                        $tanggal_selesai
                    );
                    ?>"
                >

            </div>


            <!-- =========================================
                 AKSI FILTER
            ========================================== -->

            <div class="filter-actions">

                <button
                    type="submit"
                    class="btn-filter"
                >

                    <i
                        class="fa-solid fa-filter"
                    ></i>

                    Filter

                </button>


                <a
                    href="laporan_sekwan.php"
                    class="btn-reset"
                >

                    <i
                        class="fa-solid fa-rotate-left"
                    ></i>

                    Reset

                </a>

            </div>


        </form>

    </section>


    <!-- =================================================
         RINGKASAN
    ================================================== -->

    <div class="report-summary">

        <div class="report-summary-title">

            <i
                class="fa-solid fa-list-check"
            ></i>

            Data Laporan

        </div>


        <?php if (!empty($laporanList)): ?>

            <span class="report-count">

                <?php
                echo count($laporanList);
                ?>

                Dokumen

            </span>

        <?php endif; ?>

    </div>


    <!-- =================================================
         DATA LAPORAN
    ================================================== -->

    <?php if (empty($laporanList)): ?>


        <!-- =============================================
             EMPTY STATE
        ============================================== -->

        <div class="empty-laporan">

            <div class="empty-icon">

                <i
                    class="fa-regular fa-folder-open"
                ></i>

            </div>


            <h3>
                Tidak Ada Data Laporan
            </h3>


            <p>
                Tidak ada data laporan untuk periode yang dipilih.
            </p>

        </div>


    <?php else: ?>


        <!-- =============================================
             TABLE LAPORAN
        ============================================== -->

        <section class="table-card">

            <div class="table-scroll">

                <table class="table-laporan">

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Pemohon
                            </th>

                            <th>
                                Tujuan
                            </th>

                            <th>
                                Tanggal Berangkat
                            </th>

                            <th>
                                Status
                            </th>

                            <th style="text-align:center;">
                                Aksi
                            </th>

                        </tr>

                    </thead>


                    <tbody>


                        <?php foreach ($laporanList as $row): ?>


                            <?php

                            /* =================================
                               CLASS STATUS
                            ================================= */

                            $statusClass =
                                'status-paraf';

                            $statusIcon =
                                'fa-file-signature';

                            switch ($row['status']) {

                                case 'ttd_ketua':

                                    $statusClass =
                                        'status-ketua';

                                    $statusIcon =
                                        'fa-signature';

                                    break;


                                case 'dicap':

                                    $statusClass =
                                        'status-dicap';

                                    $statusIcon =
                                        'fa-stamp';

                                    break;


                                case 'selesai':

                                    $statusClass =
                                        'status-selesai';

                                    $statusIcon =
                                        'fa-circle-check';

                                    break;

                            }

                            ?>


                            <tr>


                                <!-- =========================
                                     ID
                                ========================== -->

                                <td>

                                    <span class="id-badge">

                                        #

                                        <?php
                                        echo htmlspecialchars(
                                            $row['id']
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     PEMOHON
                                ========================== -->

                                <td>

                                    <span class="pegawai-name">

                                        <?php
                                        echo htmlspecialchars(
                                            $row['nama_pegawai']
                                        );
                                        ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     TUJUAN
                                ========================== -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['tujuan']
                                    );
                                    ?>

                                </td>


                                <!-- =========================
                                     TANGGAL
                                ========================== -->

                                <td>

                                    <?php
                                    echo htmlspecialchars(
                                        $row['tanggal_berangkat']
                                    );
                                    ?>

                                </td>


                                <!-- =========================
                                     STATUS
                                ========================== -->

                                <td>

                                    <span
                                        class="status-badge
                                        <?php echo $statusClass; ?>"
                                    >

                                        <i
                                            class="fa-solid
                                            <?php echo $statusIcon; ?>"
                                        ></i>


                                        <?php

                                        echo strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $row['status']
                                            )
                                        );

                                        ?>

                                    </span>

                                </td>


                                <!-- =========================
                                     AKSI
                                ========================== -->

                                <td
                                    style="text-align:center;"
                                >

                                    <a
                                        href="detail_sppd.php?id=<?php
                                        echo urlencode(
                                            $row['id']
                                        );
                                        ?>"
                                        class="btn-detail"
                                        title="Lihat detail pengajuan"
                                    >

                                        <i
                                            class="fa-solid fa-eye"
                                        ></i>

                                        Detail

                                    </a>

                                </td>


                            </tr>


                        <?php endforeach; ?>


                    </tbody>

                </table>

            </div>

        </section>


    <?php endif; ?>


</main>


<?php include 'footer.php'; ?>
