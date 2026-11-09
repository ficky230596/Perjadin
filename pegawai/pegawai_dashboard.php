<?php

session_start();

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'pegawai'
) {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';

$stmt = $pdo->prepare("
    SELECT *
    FROM pengajuan
    WHERE pegawai_id = ?
    ORDER BY waktu_pengajuan DESC
");

$stmt->execute([$_SESSION['user_id']]);

$pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Dashboard Pegawai</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <!-- Font Inter -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

    <!-- Global CSS jika masih digunakan -->
    <link
        rel="stylesheet"
        href="assets/css/global.css"
    >

    <style>

        /* =====================================================
           RESET
           ===================================================== */

        * {
            box-sizing: border-box;
        }

        html {
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
        }

        body {
            margin: 0;
            padding: 0;

            width: 100%;
            min-height: 100vh;

            font-family: 'Inter', Arial, sans-serif;

            background: #f4f7fc;
            color: #34445a;

            overflow-x: hidden;
        }


        /* =====================================================
           VARIABEL
           ===================================================== */

        :root {
            --navy: #10294d;
            --blue: #17477f;
            --gold: #f2bd4b;

            --background: #f4f7fc;
            --white: #ffffff;

            --text: #34445a;
            --muted: #7c8899;

            --border: #e5eaf2;

            --sidebar-width: 250px;

            --header-height: 89px;

            --footer-height: 73px;
        }


        /* =====================================================
           MAIN CONTENT
           ===================================================== */

        .main-content {

            /*
             * Header fixed berada di atas.
             * Sidebar fixed berada di kiri.
             */

            margin-left: var(--sidebar-width);

            padding-top: calc(var(--header-height) + 25px);

            padding-left: 30px;
            padding-right: 30px;

            /*
             * Memberikan ruang agar konten terakhir
             * tidak tertutup footer fixed.
             */
            padding-bottom: calc(var(--footer-height) + 35px);

            width: calc(100% - var(--sidebar-width));

            min-height: 100vh;

            overflow-x: hidden;
        }


        /* =====================================================
           CONTENT CONTAINER
           ===================================================== */

        .dashboard-container {

            width: 100%;

            max-width: 1400px;

            margin: 0 auto;
        }


        /* =====================================================
           HEADER CONTENT
           ===================================================== */

        .dashboard-heading {

            margin-bottom: 22px;
        }

        .dashboard-heading h1 {

            margin: 0;

            color: var(--navy);

            font-size: 25px;

            font-weight: 800;

            line-height: 1.3;
        }

        .dashboard-heading h1 span {

            color: var(--blue);
        }

        .dashboard-heading p {

            margin: 7px 0 0;

            color: var(--muted);

            font-size: 13px;

            line-height: 1.6;
        }


        /* =====================================================
           TOP ACTION
           ===================================================== */

        .dashboard-action {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            margin-bottom: 25px;

            padding: 18px 20px;

            background: #ffffff;

            border: 1px solid var(--border);

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(16, 41, 77, 0.05);
        }

        .dashboard-action-text {

            min-width: 0;
        }

        .dashboard-action-title {

            margin: 0;

            color: var(--navy);

            font-size: 14px;

            font-weight: 700;
        }

        .dashboard-action-description {

            margin: 4px 0 0;

            color: var(--muted);

            font-size: 11px;

            line-height: 1.5;
        }


        /* =====================================================
           BUTTON
           ===================================================== */

        .btn-ajukan {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 8px;

            flex-shrink: 0;

            padding: 11px 17px;

            background: linear-gradient(
                135deg,
                var(--navy),
                var(--blue)
            );

            color: #ffffff;

            text-decoration: none;

            border-radius: 8px;

            font-size: 12px;

            font-weight: 700;

            border: 0;

            box-shadow: 0 4px 10px rgba(16, 41, 77, 0.15);

            transition: all 0.2s ease;
        }

        .btn-ajukan i {

            color: var(--gold);
        }

        .btn-ajukan:hover {

            color: #ffffff;

            transform: translateY(-1px);

            box-shadow: 0 6px 14px rgba(16, 41, 77, 0.2);
        }


        /* =====================================================
           SECTION CARD
           ===================================================== */

        .history-card {

            width: 100%;

            background: #ffffff;

            border: 1px solid var(--border);

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(16, 41, 77, 0.05);

            overflow: hidden;
        }


        /* =====================================================
           SECTION HEADER
           ===================================================== */

        .history-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 15px;

            padding: 18px 20px;

            border-bottom: 1px solid var(--border);
        }

        .history-title {

            display: flex;

            align-items: center;

            gap: 10px;
        }

        .history-title-icon {

            width: 34px;
            height: 34px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            background: #fff5df;

            color: #d99d20;

            font-size: 14px;
        }

        .history-title h2 {

            margin: 0;

            color: var(--navy);

            font-size: 15px;

            font-weight: 800;
        }

        .history-count {

            padding: 5px 9px;

            background: #f1f5fa;

            color: var(--muted);

            border-radius: 6px;

            font-size: 10px;

            font-weight: 600;
        }


        /* =====================================================
           TABLE WRAPPER
           ===================================================== */

        .table-scroll {

            width: 100%;

            /*
             * Scroll horizontal untuk mobile
             * dan scroll vertikal untuk data yang banyak.
             */
            overflow-x: auto;

            overflow-y: auto;

            max-height: 55vh;

            -webkit-overflow-scrolling: touch;

            scrollbar-width: thin;
        }

        /*
         * Lebar minimum membuat tabel tidak dipaksa
         * mengecil sampai tulisan berantakan.
         */
        .table-scroll table {

            width: 100%;

            min-width: 680px;

            border-collapse: collapse;

            border-spacing: 0;
        }


        /* =====================================================
           TABLE HEADER
           ===================================================== */

        .table-scroll thead th {

            position: sticky;

            top: 0;

            z-index: 5;

            padding: 13px 15px;

            background: #f7f9fc;

            color: var(--navy);

            border-bottom: 1px solid var(--border);

            text-align: left;

            font-size: 11px;

            font-weight: 800;

            white-space: nowrap;
        }


        /* =====================================================
           TABLE BODY
           ===================================================== */

        .table-scroll tbody td {

            padding: 13px 15px;

            color: #526174;

            border-bottom: 1px solid #edf0f5;

            font-size: 11px;

            vertical-align: middle;
        }

        .table-scroll tbody tr {

            transition: background 0.15s ease;
        }

        .table-scroll tbody tr:hover {

            background: #fafbfd;
        }

        .table-scroll tbody tr:last-child td {

            border-bottom: none;
        }


        /* =====================================================
           ID
           ===================================================== */

        .table-id {

            color: var(--navy);

            font-weight: 800;

            white-space: nowrap;
        }


        /* =====================================================
           TUJUAN
           ===================================================== */

        .table-purpose {

            min-width: 180px;

            color: var(--navy);

            font-weight: 600;
        }


        /* =====================================================
           TANGGAL
           ===================================================== */

        .table-date {

            white-space: nowrap;
        }


        /* =====================================================
           URGENSI
           ===================================================== */

        .urgency {

            display: inline-flex;

            align-items: center;

            gap: 5px;

            padding: 5px 8px;

            border-radius: 6px;

            font-size: 10px;

            font-weight: 700;

            white-space: nowrap;
        }

        .urgency i {

            font-size: 8px;
        }

        .urgency-high {

            background: #fff0ef;

            color: #c0392b;
        }

        .urgency-medium {

            background: #fff7df;

            color: #b27b10;
        }

        .urgency-low {

            background: #edf8f1;

            color: #26834a;
        }


        /* =====================================================
           STATUS
           ===================================================== */

        .status-badge {

            display: inline-flex;

            align-items: center;

            gap: 6px;

            padding: 5px 9px;

            border-radius: 6px;

            font-size: 10px;

            font-weight: 700;

            white-space: nowrap;
        }

        .status-badge i {

            font-size: 8px;
        }

        .status-menunggu {

            background: #fff7df;

            color: #a87508;
        }

        .status-diproses {

            background: #edf4ff;

            color: #2863a5;
        }

        .status-disetujui {

            background: #edf8f1;

            color: #278047;
        }

        .status-ditolak {

            background: #fff0ef;

            color: #c0392b;
        }

        .status-selesai {

            background: #eaf7f7;

            color: #217a7a;
        }

        .status-default {

            background: #f1f3f6;

            color: #697586;
        }


        /* =====================================================
           EMPTY DATA
           ===================================================== */

        .empty-state {

            padding: 45px 20px;

            text-align: center;
        }

        .empty-icon {

            width: 55px;
            height: 55px;

            margin: 0 auto 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            background: #f1f5fa;

            color: #8a96a7;

            font-size: 21px;
        }

        .empty-state h3 {

            margin: 0;

            color: var(--navy);

            font-size: 14px;

            font-weight: 700;
        }

        .empty-state p {

            margin: 6px 0 0;

            color: var(--muted);

            font-size: 11px;
        }


        /* =====================================================
           NOTICE
           ===================================================== */

        .notice {

            display: flex;

            align-items: center;

            gap: 8px;

            margin: 15px 0 0;

            padding: 10px 13px;

            background: #edf4ff;

            border: 1px solid #dce9f8;

            border-radius: 8px;

            color: #58708c;

            font-size: 10px;

            line-height: 1.5;
        }

        .notice i {

            color: var(--blue);

            flex-shrink: 0;
        }


        /* =====================================================
           TABLE SCROLLBAR
           ===================================================== */

        .table-scroll::-webkit-scrollbar {

            width: 7px;
            height: 7px;
        }

        .table-scroll::-webkit-scrollbar-track {

            background: #f1f3f6;
        }

        .table-scroll::-webkit-scrollbar-thumb {

            background: #c7cfda;

            border-radius: 10px;
        }

        .table-scroll::-webkit-scrollbar-thumb:hover {

            background: #9faaba;
        }


        /* =====================================================
           TABLET
           ===================================================== */

        @media (max-width: 900px) {

            .main-content {

                padding-left: 20px;
                padding-right: 20px;
            }

            .dashboard-heading h1 {

                font-size: 22px;
            }

            .dashboard-action {

                padding: 15px;
            }
        }


        /* =====================================================
           MOBILE
           ===================================================== */

        @media (max-width: 768px) {

            :root {

                --header-height: 112px;

                --footer-height: 62px;
            }

            .main-content {

                margin-left: 0;

                width: 100%;

                /*
                 * Header mobile = 112px.
                 */
                padding-top: calc(var(--header-height) + 68px);

                padding-left: 12px;
                padding-right: 12px;

                padding-bottom: calc(
                    var(--footer-height) + 30px
                );
            }

            .dashboard-heading {

                margin-bottom: 15px;
            }

            .dashboard-heading h1 {

                font-size: 19px;

                line-height: 1.4;
            }

            .dashboard-heading p {

                font-size: 11px;
            }

            .dashboard-action {

                flex-direction: column;

                align-items: stretch;

                padding: 14px;

                margin-bottom: 18px;

                gap: 12px;
            }

            .btn-ajukan {

                width: 100%;
            }

            .history-header {

                padding: 14px;
            }

            .history-title h2 {

                font-size: 13px;
            }

            .history-title-icon {

                width: 30px;
                height: 30px;

                font-size: 12px;
            }

            .history-count {

                font-size: 9px;
            }

            .table-scroll {

                max-height: 52vh;
            }

            .table-scroll table {

                min-width: 650px;
            }

            .table-scroll thead th {

                padding: 11px 12px;

                font-size: 10px;
            }

            .table-scroll tbody td {

                padding: 11px 12px;

                font-size: 10px;
            }

            .notice {

                margin-top: 12px;

                font-size: 9px;
            }
        }


        /* =====================================================
           MOBILE KECIL
           ===================================================== */

        @media (max-width: 480px) {

            .main-content {

                padding-left: 9px;
                padding-right: 9px;

                padding-top: calc(
                    var(--header-height) + 65px
                );
            }

            .dashboard-heading h1 {

                font-size: 17px;
            }

            .dashboard-heading p {

                font-size: 10px;
            }

            .history-card {

                border-radius: 10px;
            }

            .history-header {

                padding: 12px;
            }

            .history-title {

                gap: 7px;
            }

            .history-title h2 {

                font-size: 12px;
            }

            .history-count {

                display: none;
            }

            .table-scroll {

                max-height: 50vh;
            }

            .table-scroll table {

                min-width: 620px;
            }

            .notice {

                align-items: flex-start;

                padding: 9px 10px;

                line-height: 1.5;
            }
        }

    </style>

</head>


<body>

    <?php include 'header_pegawai.php'; ?>

    <?php include 'sidebar_pegawai.php'; ?>


    <!-- =====================================================
         MAIN CONTENT
         ===================================================== -->

    <main class="main-content">

        <div class="dashboard-container">


            <!-- =================================================
                 JUDUL
                 ================================================= -->

            <div class="dashboard-heading">

                <h1>
                    Dashboard Pegawai:
                    <span>
                        <?php echo htmlspecialchars($_SESSION['nama'] ?? 'Pegawai'); ?>
                    </span>
                </h1>

                <p>
                    Selamat datang! Kelola dan pantau pengajuan
                    perjalanan dinas Anda.
                </p>

            </div>


            <!-- =================================================
                 ACTION
                 ================================================= -->

            <div class="dashboard-action">

                <div class="dashboard-action-text">

                    <p class="dashboard-action-title">
                        Pengajuan Perjalanan Dinas
                    </p>

                    <p class="dashboard-action-description">
                        Buat pengajuan perjalanan dinas baru
                        melalui sistem.
                    </p>

                </div>

                <a
                    href="ajukan.php"
                    class="btn-ajukan"
                >
                    <i class="fa-solid fa-file-circle-plus"></i>
                    Ajukan Perjadin Baru
                </a>

            </div>


            <!-- =================================================
                 RIWAYAT PENGAJUAN
                 ================================================= -->

            <section class="history-card">

                <div class="history-header">

                    <div class="history-title">

                        <div class="history-title-icon">
                            <i class="fa-solid fa-clock-rotate-left"></i>
                        </div>

                        <h2>
                            Riwayat Pengajuan
                        </h2>

                    </div>

                    <?php if (!empty($pengajuan)): ?>

                            <span class="history-count">
                                <?php echo count($pengajuan); ?> Pengajuan
                            </span>

                    <?php endif; ?>

                </div>


                <?php if (empty($pengajuan)): ?>

                        <!-- EMPTY STATE -->

                        <div class="empty-state">

                            <div class="empty-icon">
                                <i class="fa-regular fa-folder-open"></i>
                            </div>

                            <h3>
                                Belum Ada Pengajuan
                            </h3>

                            <p>
                                Anda belum memiliki pengajuan perjalanan dinas.
                            </p>

                        </div>


                <?php else: ?>

                        <!-- =================================================
                         TABLE SCROLL
                         ================================================= -->

                        <div class="table-scroll">

                            <table>

                                <thead>

                                    <tr>

                                        <th>ID</th>

                                        <th>Tujuan</th>

                                        <th>Tanggal Berangkat</th>

                                        <th>Urgensi</th>

                                        <th>Status</th>

                                    </tr>

                                </thead>

                                <tbody>

                                    <?php foreach ($pengajuan as $p): ?>

                                            <?php

                                            /*
                                             * Format status
                                             */
                                            $statusRaw = strtolower(
                                                trim($p['status'] ?? '')
                                            );

                                            $statusText = ucfirst(
                                                str_replace(
                                                    '_',
                                                    ' ',
                                                    $statusRaw
                                                )
                                            );


                                            /*
                                             * Class status
                                             */
                                            $statusClass = 'status-default';

                                            $statusIcon = 'fa-circle-info';

                                            if (
                                                in_array(
                                                    $statusRaw,
                                                    [
                                                        'menunggu',
                                                        'pending',
                                                        'diajukan',
                                                        'belum_diproses'
                                                    ]
                                                )
                                            ) {
                                                $statusClass = 'status-menunggu';
                                                $statusIcon = 'fa-clock';
                                            } elseif (
                                                in_array(
                                                    $statusRaw,
                                                    [
                                                        'diproses',
                                                        'proses'
                                                    ]
                                                )
                                            ) {
                                                $statusClass = 'status-diproses';
                                                $statusIcon = 'fa-spinner';
                                            } elseif (
                                                in_array(
                                                    $statusRaw,
                                                    [
                                                        'disetujui',
                                                        'disetujui_ketua',
                                                        'approved'
                                                    ]
                                                )
                                            ) {
                                                $statusClass = 'status-disetujui';
                                                $statusIcon = 'fa-circle-check';
                                            } elseif (
                                                in_array(
                                                    $statusRaw,
                                                    [
                                                        'ditolak',
                                                        'rejected'
                                                    ]
                                                )
                                            ) {
                                                $statusClass = 'status-ditolak';
                                                $statusIcon = 'fa-circle-xmark';
                                            } elseif (
                                                $statusRaw === 'selesai'
                                            ) {
                                                $statusClass = 'status-selesai';
                                                $statusIcon = 'fa-check-double';
                                            }


                                            /*
                                             * Format urgensi
                                             */
                                            $urgensiRaw = strtolower(
                                                trim($p['urgensi'] ?? '')
                                            );

                                            $urgensiClass = 'urgency-low';

                                            if (
                                                in_array(
                                                    $urgensiRaw,
                                                    [
                                                        'tinggi',
                                                        'high',
                                                        'mendesak'
                                                    ]
                                                )
                                            ) {
                                                $urgensiClass = 'urgency-high';
                                            } elseif (
                                                in_array(
                                                    $urgensiRaw,
                                                    [
                                                        'sedang',
                                                        'medium'
                                                    ]
                                                )
                                            ) {
                                                $urgensiClass = 'urgency-medium';
                                            }

                                            ?>

                                            <tr>

                                                <td class="table-id">
                                                    #<?php echo (int) $p['id']; ?>
                                                </td>

                                                <td class="table-purpose">
                                                    <?php
                                                    echo htmlspecialchars(
                                                        $p['tujuan'] ?? '-'
                                                    );
                                                    ?>
                                                </td>

                                                <td class="table-date">

                                                    <?php

                                                    if (
                                                        !empty(
                                                        $p['tanggal_berangkat']
                                                    )
                                                    ) {

                                                        echo date(
                                                            'd-m-Y',
                                                            strtotime(
                                                                $p['tanggal_berangkat']
                                                            )
                                                        );

                                                    } else {

                                                        echo '-';

                                                    }

                                                    ?>

                                                </td>

                                                <td>

                                                    <span
                                                        class="urgency <?php echo $urgensiClass; ?>"
                                                    >

                                                        <i class="fa-solid fa-circle"></i>

                                                        <?php
                                                        echo htmlspecialchars(
                                                            ucfirst(
                                                                $urgensiRaw ?: '-'
                                                            )
                                                        );
                                                        ?>

                                                    </span>

                                                </td>

                                                <td>

                                                    <span
                                                        class="status-badge <?php echo $statusClass; ?>"
                                                    >

                                                        <i
                                                            class="fa-solid <?php echo $statusIcon; ?>"
                                                        ></i>

                                                        <?php
                                                        echo htmlspecialchars(
                                                            $statusText ?: '-'
                                                        );
                                                        ?>

                                                    </span>

                                                </td>

                                            </tr>

                                    <?php endforeach; ?>

                                </tbody>

                            </table>

                        </div>

                <?php endif; ?>

            </section>


            <!-- =================================================
                 NOTICE
                 ================================================= -->

            <p class="notice">

                <i class="fa-solid fa-circle-info"></i>

                Dashboard ini otomatis diperbarui setiap
                10 detik untuk menampilkan perubahan status terbaru.

            </p>


        </div>

    </main>


    <!-- =====================================================
         AUTO REFRESH
         
         Diletakkan SEBELUM footer.php karena footer.php
         Anda sudah menutup </body></html>.
         ===================================================== -->

    <script>

        setInterval(function () {

            location.reload();

        }, 10000);

    </script>


    <!-- Footer paling terakhir -->
    <?php include 'footer.php'; ?>


</body>
</html>