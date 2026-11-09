<?php

session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'umum') {

    echo "
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
    <script>
        Swal.fire({
            title: 'Akses Ditolak!',
            text: 'Silakan login sebagai Bagian Umum.',
            icon: 'error'
        }).then(() => {
            window.location.href = '../index.php';
        });
    </script>";

    exit;
}

require_once '../config/db.php';

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Buat Draft SPPD</title>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>

        /* =====================================================
           PAGE
           ===================================================== */

        body {
            margin: 0;
            background: #f5f7fa;
            font-family: "Inter", "Segoe UI", Arial, sans-serif;
            color: #334155;
        }

        .main-content-umum {
            margin-left: 250px;
            padding: 94px 26px 88px;
            min-height: 100vh;
            box-sizing: border-box;
        }

        .draft-page {
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
        }


        /* =====================================================
           PAGE HEADER
           ===================================================== */

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 22px;
        }

        .page-header-left {
            display: flex;
            align-items: center;
            gap: 14px;
            min-width: 0;
        }

        .page-header-icon {
            width: 52px;
            height: 52px;
            min-width: 52px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 13px;
            background: #10294d;
            color: #f2bd4b;
            font-size: 21px;
            box-shadow: 0 7px 18px rgba(16, 41, 77, .14);
        }

        .page-header-text {
            min-width: 0;
        }

        .page-label {
            display: block;
            margin-bottom: 3px;
            color: #b08416;
            font-size: 9px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .page-header h1 {
            margin: 0;
            color: #10294d;
            font-size: 24px;
            font-weight: 800;
            line-height: 1.25;
        }

        .page-header p {
            margin: 4px 0 0;
            color: #64748b;
            font-size: 11px;
            line-height: 1.5;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
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
            white-space: nowrap;
            transition: .2s ease;
        }

        .back-button:hover {
            border-color: #10294d;
            color: #10294d;
            box-shadow: 0 4px 12px rgba(16, 41, 77, .08);
            transform: translateY(-1px);
        }


        /* =====================================================
           CURRENT APPLICATION
           ===================================================== */

        .application-summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 16px;
            padding: 16px 19px;
            border: 1px solid #dbe5ef;
            border-radius: 13px;
            background: #fff;
            box-shadow: 0 5px 18px rgba(16, 41, 77, .045);
        }

        .summary-left {
            display: flex;
            align-items: center;
            gap: 11px;
            min-width: 0;
        }

        .summary-icon {
            width: 38px;
            height: 38px;
            min-width: 38px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #f1f5f9;
            color: #17477f;
            font-size: 14px;
        }

        .summary-content {
            min-width: 0;
        }

        .summary-content span {
            display: block;
            margin-bottom: 3px;
            color: #94a3b8;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .summary-content strong {
            display: block;
            color: #10294d;
            font-size: 12px;
            font-weight: 800;
            overflow-wrap: anywhere;
        }

        .urgency-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 11px;
            border-radius: 20px;
            background: #fff3f3;
            color: #b4232f;
            font-size: 9px;
            font-weight: 800;
            white-space: nowrap;
        }

        .urgency-badge i {
            font-size: 8px;
        }


        /* =====================================================
           MAIN GRID
           ===================================================== */

        .content-wrapper {
            display: grid;
            grid-template-columns: minmax(0, 1.65fr) minmax(280px, .85fr);
            gap: 17px;
            align-items: start;
        }


        /* =====================================================
           CARD
           ===================================================== */

        .content-card {
            background: #fff;
            border: 1px solid #e4e9f0;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(16, 41, 77, .045);
            overflow: hidden;
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 17px 20px;
            border-bottom: 1px solid #edf1f5;
        }

        .card-header-icon {
            width: 36px;
            height: 36px;
            min-width: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #10294d;
            color: #f2bd4b;
            font-size: 13px;
        }

        .card-header-text {
            min-width: 0;
        }

        .card-header-text span {
            display: block;
            margin-bottom: 2px;
            color: #94a3b8;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: .8px;
        }

        .card-header-text h2 {
            margin: 0;
            color: #10294d;
            font-size: 15px;
            font-weight: 800;
        }

        .card-body {
            padding: 19px 20px 20px;
        }


        /* =====================================================
           FORM
           ===================================================== */

        .draft-form {
            width: 100%;
        }

        .form-section-title {
            display: flex;
            align-items: center;
            gap: 7px;
            margin: 0 0 13px;
            color: #10294d;
            font-size: 11px;
            font-weight: 800;
        }

        .form-section-title i {
            color: #f2bd4b;
            font-size: 10px;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 13px;
            margin-bottom: 13px;
        }

        .form-row.three-column {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .form-group {
            min-width: 0;
        }

        .form-group.full {
            margin-bottom: 13px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0 0 6px;
            color: #475569;
            font-size: 9px;
            font-weight: 750;
        }

        .required-mark {
            color: #dc3545;
            font-weight: 800;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 11px;
            pointer-events: none;
        }

        .form-control,
        .draft-form input,
        .draft-form textarea {
            width: 100%;
            min-height: 40px;
            padding: 9px 11px;
            border: 1px solid #d9e1ea;
            border-radius: 8px;
            outline: none;
            background: #fff;
            color: #334155;
            font-family: inherit;
            font-size: 10px;
            box-sizing: border-box;
            transition: border-color .2s ease,
                        box-shadow .2s ease,
                        background .2s ease;
        }

        .input-wrapper .form-control {
            padding-left: 33px;
        }

        .draft-form textarea {
            min-height: 76px;
            resize: vertical;
            line-height: 1.55;
        }

        .form-control::placeholder,
        .draft-form input::placeholder,
        .draft-form textarea::placeholder {
            color: #b0bac7;
        }

        .form-control:focus,
        .draft-form input:focus,
        .draft-form textarea:focus {
            border-color: #17477f;
            box-shadow: 0 0 0 3px rgba(23, 71, 127, .08);
            background: #fff;
        }

        .field-hint {
            display: block;
            margin-top: 5px;
            color: #94a3b8;
            font-size: 8px;
            line-height: 1.4;
        }


        /* =====================================================
           SUBMIT
           ===================================================== */

        .form-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 18px;
            padding-top: 16px;
            border-top: 1px solid #edf1f5;
        }

        .submit-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 9px 15px;
            border: 0;
            border-radius: 8px;
            background: #10294d;
            color: #fff;
            font-family: inherit;
            font-size: 10px;
            font-weight: 750;
            cursor: pointer;
            box-shadow: 0 5px 12px rgba(16, 41, 77, .14);
            transition: all .2s ease;
        }

        .submit-button:hover {
            background: #17477f;
            box-shadow: 0 7px 16px rgba(16, 41, 77, .18);
            transform: translateY(-1px);
        }

        .submit-button i {
            color: #f2bd4b;
        }


        /* =====================================================
           PRIORITY CARD
           ===================================================== */

        .priority-card {
            background: #fff;
            border: 1px solid #e4e9f0;
            border-radius: 14px;
            box-shadow: 0 5px 18px rgba(16, 41, 77, .045);
            overflow: hidden;
        }

        .priority-header {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 16px 17px;
            border-bottom: 1px solid #edf1f5;
        }

        .priority-header-icon {
            width: 35px;
            height: 35px;
            min-width: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 9px;
            background: #fff3f3;
            color: #c0392b;
            font-size: 13px;
        }

        .priority-header-text {
            min-width: 0;
        }

        .priority-header-text span {
            display: block;
            color: #94a3b8;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: .7px;
        }

        .priority-header-text h2 {
            margin: 2px 0 0;
            color: #10294d;
            font-size: 14px;
            font-weight: 800;
        }

        .priority-body {
            padding: 15px 17px 17px;
        }

        .priority-subtitle {
            margin: 0 0 12px;
            color: #64748b;
            font-size: 9px;
            line-height: 1.55;
        }

        .reasons-container {
            display: flex;
            flex-direction: column;
            gap: 9px;
        }

        .reason-card {
            position: relative;
            padding: 11px 11px 11px 14px;
            border: 1px solid #e6ebf0;
            border-radius: 9px;
            background: #fbfcfd;
            overflow: hidden;
        }

        .reason-card::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 3px;
            background: #f2bd4b;
        }

        .reason-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            margin-bottom: 6px;
            border-radius: 6px;
            background: #10294d;
            color: #f2bd4b;
            font-size: 8px;
            font-weight: 800;
        }

        .reason-card h3 {
            margin: 0 0 4px;
            color: #334155;
            font-size: 10px;
            font-weight: 800;
        }

        .reason-card p {
            margin: 0;
            color: #64748b;
            font-size: 9px;
            line-height: 1.55;
        }

        .empty-antrian {
            margin: 0;
            padding: 18px 10px;
            text-align: center;
            color: #94a3b8;
            font-size: 9px;
            font-style: italic;
        }


        /* =====================================================
           DETAIL PENGAJUAN
           ===================================================== */

        .detail-card {
            margin-top: 15px;
        }

        .detail-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .detail-item {
            padding: 10px 11px;
            border: 1px solid #e8edf2;
            border-radius: 8px;
            background: #fbfcfd;
        }

        .detail-item-label {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 4px;
            color: #94a3b8;
            font-size: 8px;
            font-weight: 650;
        }

        .detail-item-label i {
            color: #17477f;
            font-size: 9px;
        }

        .detail-item-value {
            color: #475569;
            font-size: 9px;
            font-weight: 700;
            line-height: 1.45;
        }

        .detail-status {
            display: inline-flex;
            align-items: center;
            padding: 4px 7px;
            border-radius: 12px;
            background: #fff7df;
            color: #997000;
            font-size: 8px;
            font-weight: 800;
        }


        /* =====================================================
           RESPONSIVE 1000
           ===================================================== */

        @media (max-width: 1000px) {

            .content-wrapper {
                grid-template-columns: 1fr;
            }

            .priority-panel {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 15px;
                align-items: start;
            }

            .priority-card {
                margin: 0;
            }

        }


        /* =====================================================
           RESPONSIVE 768
           ===================================================== */

        @media (max-width: 768px) {

            .main-content-umum {
                margin-left: 0;
                padding: 82px 14px 76px;
            }

            .page-header {
                align-items: flex-start;
            }

            .page-header-icon {
                width: 45px;
                height: 45px;
                min-width: 45px;
                font-size: 18px;
            }

            .page-header h1 {
                font-size: 20px;
            }

            .page-header p {
                font-size: 9px;
            }

            .back-button {
                min-height: 36px;
                padding: 8px 10px;
            }

            .back-button span {
                display: none;
            }

            .application-summary {
                align-items: flex-start;
            }

            .form-row.three-column {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .priority-panel {
                grid-template-columns: 1fr;
            }

        }


        /* =====================================================
           RESPONSIVE 600
           ===================================================== */

        @media (max-width: 600px) {

            .page-header {
                flex-direction: column;
                gap: 12px;
            }

            .back-button {
                align-self: flex-start;
            }

            .application-summary {
                flex-direction: column;
                align-items: flex-start;
                gap: 11px;
                padding: 14px;
            }

            .form-row,
            .form-row.three-column {
                grid-template-columns: 1fr;
            }

            .card-header {
                padding: 14px 15px;
            }

            .card-body {
                padding: 16px 15px;
            }

            .form-footer {
                justify-content: stretch;
            }

            .submit-button {
                width: 100%;
            }

        }


        /* =====================================================
           RESPONSIVE 400
           ===================================================== */

        @media (max-width: 400px) {

            .main-content-umum {
                padding-left: 10px;
                padding-right: 10px;
            }

            .page-header-left {
                gap: 10px;
            }

            .page-header-icon {
                width: 40px;
                height: 40px;
                min-width: 40px;
                font-size: 16px;
                border-radius: 10px;
            }

            .page-header h1 {
                font-size: 18px;
            }

            .page-header p {
                font-size: 8px;
            }

            .card-header-text h2 {
                font-size: 13px;
            }

            .urgency-badge {
                font-size: 8px;
            }

        }


        /* =====================================================
           PRINT
           ===================================================== */

        @media print {

            .main-content-umum {
                margin: 0;
                padding: 0;
                background: #fff;
            }

            .page-header,
            .priority-card,
            .form-footer {
                display: none !important;
            }

            .content-card {
                border: 0;
                box-shadow: none;
            }

        }

    </style>

</head>


<body>

<?php include 'header_umum.php'; ?>

<?php include 'sidebar_umum.php'; ?>


<main class="main-content-umum">

    <div class="draft-page">


        <!-- =====================================================
             PAGE HEADER
             ===================================================== -->

        <div class="page-header">

            <div class="page-header-left">

                <div class="page-header-icon">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </div>

                <div class="page-header-text">

                    <span class="page-label">
                        BAGIAN UMUM
                    </span>

                    <h1>
                        Buat Draft SPPD
                    </h1>

                    <p>
                        Lengkapi informasi perjalanan dinas untuk
                        membuat draft SPPD sebelum diteruskan ke Sekwan.
                    </p>

                </div>

            </div>


            <a href="umum_dashboard.php" class="back-button">

                <i class="fa-solid fa-arrow-left"></i>

                <span>Kembali</span>

            </a>

        </div>


<?php

$id = $_GET['id'] ?? $_POST['id'] ?? null;

if (!$id) {

    echo "
    <script>
        Swal.fire({
            title: 'Error!',
            text: 'ID pengajuan tidak valid!',
            icon: 'error'
        }).then(() => {
            window.location.href = 'umum_dashboard.php';
        });
    </script>";

    exit;
}


// Ambil data pengajuan

$stmt = $pdo->prepare(
    "SELECT * FROM pengajuan
     WHERE id = ?
     AND status = 'diajukan'"
);

$stmt->execute([$id]);

$pengajuan = $stmt->fetch();


if (!$pengajuan) {

    echo "
    <script>
        Swal.fire({
            title: 'Error!',
            text: 'Pengajuan tidak ditemukan atau status salah!',
            icon: 'error'
        }).then(() => {
            window.location.href = 'umum_dashboard.php';
        });
    </script>";

    exit;
}


// Ambil alasan prioritas

$stmt_priority = $pdo->prepare(
    "SELECT alasan
     FROM pengajuan
     WHERE urgensi = 'tinggi'
     AND status = 'diajukan'
     LIMIT 3"
);

$stmt_priority->execute();

$priority_reasons = $stmt_priority->fetchAll(
    PDO::FETCH_COLUMN
);


// Proses form submit

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $_POST['id'] == $id) {

    $spt_no = $_POST['spt_no'] ?? '';
    $spd_no = $_POST['spd_no'] ?? '';
    $pangkat = $_POST['pangkat'] ?? '';
    $tingkat_biaya = $_POST['tingkat_biaya'] ?? '';
    $alat_angkutan = $_POST['alat_angkutan'] ?? '';
    $pengikut = $_POST['pengikut'] ?? '';
    $akun_anggaran = $_POST['akun_anggaran'] ?? '';


    if (empty($tingkat_biaya) || empty($alat_angkutan)) {

        echo "
        <script>
            Swal.fire({
                title: 'Peringatan!',
                text: 'Semua field wajib diisi!',
                icon: 'warning'
            });
        </script>";

    } else {

        $stmt_update = $pdo->prepare(
            "UPDATE pengajuan
             SET status = 'draft_sppd',
                 spt_no = ?,
                 spd_no = ?,
                 pangkat = ?,
                 tingkat_biaya = ?,
                 alat_angkutan = ?,
                 pengikut = ?,
                 akun_anggaran = ?
             WHERE id = ?"
        );

        $stmt_update->execute([
            $spt_no,
            $spd_no,
            $pangkat,
            $tingkat_biaya,
            $alat_angkutan,
            $pengikut,
            $akun_anggaran,
            $id
        ]);


        echo "
        <script>
            Swal.fire({
                title: 'Sukses!',
                text: 'Draft dibuat & WA terkirim ke Sekwan!',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                window.location.href = 'umum_dashboard.php';
            });
        </script>";

        exit;
    }
}


// Fungsi detail pengajuan prioritas

function getPriorityDetails($pdo)
{
    $stmt = $pdo->prepare(
        "SELECT tujuan,
                tanggal_berangkat,
                tanggal_kembali,
                alasan,
                urgensi,
                status
         FROM pengajuan
         WHERE urgensi = 'tinggi'
         AND status = 'diajukan'
         LIMIT 3"
    );

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$priority_details = getPriorityDetails($pdo);

?>


        <!-- =====================================================
             RINGKASAN PENGAJUAN
             ===================================================== -->

        <div class="application-summary">

            <div class="summary-left">

                <div class="summary-icon">
                    <i class="fa-solid fa-file-lines"></i>
                </div>

                <div class="summary-content">

                    <span>
                        Pengajuan yang sedang diproses
                    </span>

                    <strong>
                        Tujuan:
                        <?php echo htmlspecialchars(
                            $pengajuan['tujuan']
                        ); ?>
                    </strong>

                </div>

            </div>


            <div class="urgency-badge">

                <i class="fa-solid fa-triangle-exclamation"></i>

                Urgensi:
                <?php echo ucfirst(
                    htmlspecialchars($pengajuan['urgensi'])
                ); ?>

            </div>

        </div>


        <!-- =====================================================
             CONTENT
             ===================================================== -->

        <div class="content-wrapper">


            <!-- =================================================
                 FORM DRAFT
                 ================================================= -->

            <section class="content-card">

                <div class="card-header">

                    <div class="card-header-icon">
                        <i class="fa-solid fa-file-pen"></i>
                    </div>

                    <div class="card-header-text">

                        <span>
                            FORM DOKUMEN
                        </span>

                        <h2>
                            Informasi Draft SPPD
                        </h2>

                    </div>

                </div>


                <div class="card-body">

                    <form
                        method="POST"
                        class="draft-form"
                    >

                        <input
                            type="hidden"
                            name="id"
                            value="<?php echo $id; ?>"
                        >


                        <!-- TINGKAT BIAYA -->

                        <h3 class="form-section-title">

                            <i class="fa-solid fa-money-bill-wave"></i>

                            Informasi Perjalanan

                        </h3>


                        <div class="form-row">

                            <div class="form-group">

                                <label>
                                    Tingkat Biaya Perjadin
                                    <span class="required-mark">*</span>
                                </label>

                                <div class="input-wrapper">

                                    <i class="fa-solid fa-money-bill input-icon"></i>

                                    <input
                                        type="text"
                                        name="tingkat_biaya"
                                        class="form-control"
                                        placeholder="Masukkan tingkat biaya"
                                        required
                                    >

                                </div>

                            </div>


                            <div class="form-group">

                                <label>
                                    Alat Angkutan
                                    <span class="required-mark">*</span>
                                </label>

                                <div class="input-wrapper">

                                    <i class="fa-solid fa-car input-icon"></i>

                                    <input
                                        type="text"
                                        name="alat_angkutan"
                                        class="form-control"
                                        value="Mobil"
                                        placeholder="Contoh: Mobil"
                                        required
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- PENGIKUT -->

                        <div class="form-group full">

                            <label>
                                Pengikut
                            </label>

                            <textarea
                                name="pengikut"
                                placeholder="Masukkan nama pengikut dan pisahkan dengan koma..."
                            ></textarea>

                            <span class="field-hint">
                                Jika terdapat lebih dari satu pengikut,
                                pisahkan setiap nama menggunakan tanda koma.
                            </span>

                        </div>


                        <!-- NOMOR DOKUMEN -->

                        <h3 class="form-section-title">

                            <i class="fa-solid fa-file-signature"></i>

                            Nomor Dokumen & Anggaran

                        </h3>


                        <div class="form-row three-column">

                            <div class="form-group">

                                <label>
                                    Nomor SPT
                                </label>

                                <div class="input-wrapper">

                                    <i class="fa-solid fa-file-lines input-icon"></i>

                                    <input
                                        type="text"
                                        name="spt_no"
                                        class="form-control"
                                        placeholder="Nomor SPT"
                                    >

                                </div>

                            </div>


                            <div class="form-group">

                                <label>
                                    Nomor SPD
                                </label>

                                <div class="input-wrapper">

                                    <i class="fa-solid fa-file-contract input-icon"></i>

                                    <input
                                        type="text"
                                        name="spd_no"
                                        class="form-control"
                                        placeholder="Nomor SPD"
                                    >

                                </div>

                            </div>


                            <div class="form-group">

                                <label>
                                    Akun Anggaran
                                </label>

                                <div class="input-wrapper">

                                    <i class="fa-solid fa-wallet input-icon"></i>

                                    <input
                                        type="text"
                                        name="akun_anggaran"
                                        class="form-control"
                                        placeholder="Kode akun"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- SUBMIT -->

                        <div class="form-footer">

                            <button
                                type="submit"
                                class="submit-button"
                            >

                                <i class="fa-solid fa-paper-plane"></i>

                                Simpan Draft & Kirim ke Sekwan

                            </button>

                        </div>

                    </form>

                </div>

            </section>


            <!-- =================================================
                 SIDEBAR PRIORITAS
                 ================================================= -->

            <div class="priority-panel">


                <!-- PRIORITAS TINGGI -->

                <section class="priority-card">

                    <div class="priority-header">

                        <div class="priority-header-icon">
                            <i class="fa-solid fa-triangle-exclamation"></i>
                        </div>

                        <div class="priority-header-text">

                            <span>
                                INFORMASI ANTRIAN
                            </span>

                            <h2>
                                Prioritas Tinggi
                            </h2>

                        </div>

                    </div>


                    <div class="priority-body">

                        <p class="priority-subtitle">
                            Berikut alasan dari pengajuan yang
                            memiliki tingkat urgensi tinggi.
                        </p>


                        <?php if (empty($priority_reasons)): ?>

                            <p class="empty-antrian">
                                Tidak ada pengajuan dengan urgensi
                                tinggi saat ini.
                            </p>

                        <?php else: ?>

                            <div class="reasons-container">

                                <?php foreach (
                                    $priority_reasons
                                    as $index => $reason
                                ): ?>

                                    <div class="reason-card">

                                        <span class="reason-number">
                                            <?php echo $index + 1; ?>
                                        </span>

                                        <h3>
                                            Alasan Prioritas
                                        </h3>

                                        <p>
                                            <?php echo htmlspecialchars(
                                                $reason
                                            ); ?>
                                        </p>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </section>


                <!-- DETAIL PENGAJUAN -->

                <section class="priority-card detail-card">

                    <div class="priority-header">

                        <div class="priority-header-icon">

                            <i class="fa-solid fa-circle-info"></i>

                        </div>

                        <div class="priority-header-text">

                            <span>
                                DATA PENGAJUAN
                            </span>

                            <h2>
                                Detail Pengajuan
                            </h2>

                        </div>

                    </div>


                    <div class="priority-body">

                        <?php if (empty($priority_details)): ?>

                            <p class="empty-antrian">
                                Tidak ada pengajuan dengan urgensi
                                tinggi saat ini.
                            </p>

                        <?php else: ?>

                            <div class="detail-list">

                                <?php foreach (
                                    $priority_details
                                    as $index => $data
                                ): ?>

                                    <div class="detail-item">

                                        <div class="detail-item-label">

                                            <i class="fa-solid fa-location-dot"></i>

                                            Tujuan

                                        </div>

                                        <div class="detail-item-value">

                                            <?php echo htmlspecialchars(
                                                $data['tujuan']
                                            ); ?>

                                        </div>

                                    </div>


                                    <div class="detail-item">

                                        <div class="detail-item-label">

                                            <i class="fa-solid fa-calendar-days"></i>

                                            Tanggal Berangkat

                                        </div>

                                        <div class="detail-item-value">

                                            <?php echo date(
                                                'd-m-Y',
                                                strtotime(
                                                    $data['tanggal_berangkat']
                                                )
                                            ); ?>

                                        </div>

                                    </div>


                                    <div class="detail-item">

                                        <div class="detail-item-label">

                                            <i class="fa-solid fa-calendar-check"></i>

                                            Tanggal Kembali

                                        </div>

                                        <div class="detail-item-value">

                                            <?php echo date(
                                                'd-m-Y',
                                                strtotime(
                                                    $data['tanggal_kembali']
                                                )
                                            ); ?>

                                        </div>

                                    </div>


                                    <div class="detail-item">

                                        <div class="detail-item-label">

                                            <i class="fa-solid fa-triangle-exclamation"></i>

                                            Urgensi

                                        </div>

                                        <div class="detail-item-value">

                                            <?php echo ucfirst(
                                                $data['urgensi']
                                            ); ?>

                                        </div>

                                    </div>


                                    <div class="detail-item">

                                        <div class="detail-item-label">

                                            <i class="fa-solid fa-circle-check"></i>

                                            Status

                                        </div>

                                        <div>

                                            <span class="detail-status">

                                                <?php echo ucfirst(
                                                    str_replace(
                                                        '_',
                                                        ' ',
                                                        $data['status']
                                                    )
                                                ); ?>

                                            </span>

                                        </div>

                                    </div>

                                <?php endforeach; ?>

                            </div>

                        <?php endif; ?>

                    </div>

                </section>

            </div>

        </div>

    </div>

</main>


<?php include 'footer.php'; ?>


</body>

</html>
