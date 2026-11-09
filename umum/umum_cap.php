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
| Ambil ID Pengajuan
|--------------------------------------------------------------------------
*/
$id = $_GET['id'] ?? $_POST['id'] ?? null;
$id = (int) $id;

if ($id <= 0) {
    echo "
    <script>
        window.location.href = 'umum_dashboard.php';
    </script>";
    exit;
}

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
    WHERE p.id = ?
      AND p.status = 'ttd_ketua'
    LIMIT 1
");

$stmt->execute([$id]);
$pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$pengajuan) {
    echo "
    <script>
        window.location.href = 'umum_dashboard.php';
    </script>";
    exit;
}

/*
|--------------------------------------------------------------------------
| Proses Cap / Selesai
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (int) ($_POST['id'] ?? 0) === $id) {

    $action = $_POST['action'] ?? '';

    /*
    |--------------------------------------------------------------------------
    | Validasi Action
    |--------------------------------------------------------------------------
    */
    if (!in_array($action, ['normal', 'setujui'], true)) {
        echo "
        <script>
            Swal.fire({
                title: 'Aksi Tidak Valid',
                text: 'Pilihan proses tidak dikenali.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10294d'
            }).then(function () {
                window.location.href = 'umum_cap.php?id={$id}';
            });
        </script>";
        exit;
    }

    try {

        $pdo->beginTransaction();

        /*
        |--------------------------------------------------------------------------
        | Update Status
        |--------------------------------------------------------------------------
        */
        $stmt_update = $pdo->prepare("
            UPDATE pengajuan
            SET status = 'selesai'
            WHERE id = ?
              AND status = 'ttd_ketua'
        ");

        $stmt_update->execute([$id]);

        if ($stmt_update->rowCount() < 1) {
            $pdo->rollBack();

            echo "
            <script>
                Swal.fire({
                    title: 'Gagal',
                    text: 'Pengajuan sudah diproses atau statusnya telah berubah.',
                    icon: 'error',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10294d'
                }).then(function () {
                    window.location.href = 'umum_dashboard.php';
                });
            </script>";
            exit;
        }

        $pdo->commit();

        /*
        |--------------------------------------------------------------------------
        | Kirim WhatsApp
        |--------------------------------------------------------------------------
        */
        $nama = trim($pengajuan['nama_pemohon'] ?? 'Pemohon');
        $hp = trim($pengajuan['wa_phone'] ?? '');
        $tujuan = trim($pengajuan['tujuan'] ?? '-');

        if (
            $hp !== '' &&
            isset($fonnte_token) &&
            $fonnte_token !== ''
        ) {

            if ($action === 'setujui') {

                $message =
                    "Halo *{$nama}*,\n\n" .
                    "Pengajuan perjalanan dinas dengan tujuan *{$tujuan}* " .
                    "telah *DISETUJUI LANGSUNG* dan dinyatakan *SELESAI*.\n\n" .
                    "Silakan cek dashboard untuk melanjutkan proses pencetakan surat.";

            } else {

                $message =
                    "Halo *{$nama}*,\n\n" .
                    "Pengajuan perjalanan dinas dengan tujuan *{$tujuan}* " .
                    "telah *DICAP* dan dinyatakan *SELESAI*.\n\n" .
                    "Silakan cek dashboard untuk melanjutkan proses pencetakan surat.";
            }

            sendWaNotification(
                $hp,
                $message,
                $fonnte_token
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Berhasil
        |--------------------------------------------------------------------------
        */
        echo "
        <script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Proses cap selesai. Pengajuan sekarang berstatus selesai.',
                icon: 'success',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10294d'
            }).then(function () {
                window.location.href = 'umum_dashboard.php';
            });
        </script>";

        exit;

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log(
            'Gagal memproses cap pengajuan ID ' .
            $id . ': ' . $e->getMessage()
        );

        echo "
        <script>
            Swal.fire({
                title: 'Terjadi Kesalahan',
                text: 'Pengajuan gagal diproses. Silakan coba lagi.',
                icon: 'error',
                confirmButtonText: 'OK',
                confirmButtonColor: '#10294d'
            });
        </script>";

        exit;
    }
}

?>

<?php include 'header_umum.php'; ?>
<?php include 'sidebar_umum.php'; ?>

<main class="main-content-umum">

    <div class="cap-page">

        <!-- Page Header -->
        <div class="page-heading">
            <div class="page-heading-left">

                <div class="page-icon">
                    <i class="fa-solid fa-stamp"></i>
                </div>

                <div>
                    <span class="page-kicker">Bagian Umum</span>
                    <h1>Cap SPPD</h1>
                    <p>
                        Penyelesaian pengajuan perjalanan dinas yang telah
                        ditandatangani oleh Ketua DPRD.
                    </p>
                </div>

            </div>

            <a href="umum_dashboard.php" class="back-button">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>


        <!-- Informasi Pengajuan -->
        <div class="application-card">

            <div class="application-card-header">

                <div>
                    <span class="section-label">DATA PENGAJUAN</span>
                    <h2>
                        Pengajuan #<?php echo $id; ?>
                    </h2>
                </div>

                <span class="status-badge">
                    <i class="fa-solid fa-circle-check"></i>
                    Siap Cap
                </span>

            </div>


            <div class="application-grid">

                <div class="info-item">
                    <span class="info-label">
                        <i class="fa-solid fa-user"></i>
                        Pemohon
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $pengajuan['nama_pemohon'] ?? '-'
                        ); ?>
                    </strong>
                </div>


                <div class="info-item">
                    <span class="info-label">
                        <i class="fa-solid fa-location-dot"></i>
                        Tujuan
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $pengajuan['tujuan'] ?? '-'
                        ); ?>
                    </strong>
                </div>


                <div class="info-item">
                    <span class="info-label">
                        <i class="fa-solid fa-calendar-days"></i>
                        Tanggal Berangkat
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $pengajuan['tanggal_berangkat'] ?? '-'
                        ); ?>
                    </strong>
                </div>


                <div class="info-item">
                    <span class="info-label">
                        <i class="fa-solid fa-calendar-check"></i>
                        Tanggal Kembali
                    </span>

                    <strong>
                        <?php echo htmlspecialchars(
                            $pengajuan['tanggal_kembali'] ?? '-'
                        ); ?>
                    </strong>
                </div>

            </div>

        </div>


        <!-- Informasi Proses -->
        <div class="process-card">

            <div class="process-icon">
                <i class="fa-solid fa-circle-info"></i>
            </div>

            <div class="process-text">
                <h3>Pengajuan Siap Diproses</h3>

                <p>
                    Pengajuan ini telah mencapai tahap
                    <strong>TTD Ketua</strong>.
                    Bagian Umum dapat melakukan proses cap untuk
                    menyelesaikan pengajuan.
                </p>
            </div>

        </div>


        <!-- Pilihan Proses -->
        <div class="action-card">

            <div class="action-card-header">
                <div>
                    <span class="section-label">PROSES AKHIR</span>
                    <h2>Pilih Proses</h2>
                </div>
            </div>


            <div class="action-grid">

                <!-- Cap Normal -->
                <button type="button" class="action-option normal" onclick="confirmCap('normal')">

                    <div class="action-option-icon">
                        <i class="fa-solid fa-stamp"></i>
                    </div>

                    <div class="action-option-content">
                        <h3>Cap Normal</h3>

                        <p>
                            Memberikan cap pada SPPD dan menyelesaikan
                            proses pengajuan.
                        </p>

                        <span class="action-option-link">
                            Proses Cap
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>

                </button>


                <!-- Setujui Langsung -->
                <button type="button" class="action-option approve" onclick="confirmCap('setujui')">

                    <div class="action-option-icon">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>

                    <div class="action-option-content">
                        <h3>Setujui Langsung</h3>

                        <p>
                            Menyelesaikan pengajuan secara langsung
                            tanpa proses cap normal.
                        </p>

                        <span class="action-option-link">
                            Setujui & Selesai
                            <i class="fa-solid fa-arrow-right"></i>
                        </span>
                    </div>

                </button>

            </div>

        </div>


        <!-- Keterangan Cetak -->
        <div class="print-info">

            <div class="print-info-icon">
                <i class="fa-solid fa-print"></i>
            </div>

            <div>
                <h3>Pencetakan SPPD</h3>

                <p>
                    Setelah proses selesai, pemohon dapat mencetak
                    surat perjalanan dinas melalui dashboard pemohon.
                </p>

                <code>
                    generate_sppd.php?id=<?php echo $id; ?>
                </code>
            </div>

        </div>

    </div>

</main>


<?php include 'footer.php'; ?>


<style>
    /* =========================================================
   CAP SPPD - PAGE
   ========================================================= */

    .main-content-umum {
        margin-left: 250px;
        padding: 92px 25px 90px;
        min-height: 100vh;
        background: #f6f8fb;
        font-family: "Inter", "Segoe UI", Arial, sans-serif;
    }

    .cap-page {
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
   APPLICATION CARD
   ========================================================= */

    .application-card,
    .action-card {
        background: #fff;
        border: 1px solid #e4e9f0;
        border-radius: 14px;
        box-shadow: 0 5px 18px rgba(16, 41, 77, .05);
        overflow: hidden;
    }

    .application-card {
        margin-bottom: 16px;
    }

    .application-card-header,
    .action-card-header {
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

    .application-card-header h2,
    .action-card-header h2 {
        margin: 0;
        color: #10294d;
        font-size: 16px;
        font-weight: 800;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 11px;
        border-radius: 20px;
        background: #fff7df;
        color: #996f00;
        font-size: 10px;
        font-weight: 800;
        white-space: nowrap;
    }

    .application-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
    }

    .info-item {
        padding: 17px 20px;
        border-right: 1px solid #edf1f5;
    }

    .info-item:last-child {
        border-right: 0;
    }

    .info-label {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 7px;
        color: #94a3b8;
        font-size: 10px;
        font-weight: 600;
    }

    .info-label i {
        color: #17477f;
    }

    .info-item strong {
        display: block;
        color: #334155;
        font-size: 12px;
        line-height: 1.45;
    }


    /* =========================================================
   PROCESS CARD
   ========================================================= */

    .process-card {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 16px;
        padding: 15px 18px;
        border: 1px solid #d9e5f2;
        border-radius: 12px;
        background: #f4f8fc;
    }

    .process-icon {
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

    .process-text h3 {
        margin: 1px 0 4px;
        color: #10294d;
        font-size: 12px;
        font-weight: 800;
    }

    .process-text p {
        margin: 0;
        color: #64748b;
        font-size: 10px;
        line-height: 1.6;
    }


    /* =========================================================
   ACTION
   ========================================================= */

    .action-card {
        margin-bottom: 16px;
    }

    .action-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 14px;
        padding: 18px 20px 20px;
    }

    .action-option {
        display: flex;
        align-items: flex-start;
        gap: 13px;
        width: 100%;
        padding: 16px;
        border: 1px solid #e1e7ee;
        border-radius: 12px;
        background: #fff;
        text-align: left;
        cursor: pointer;
        transition: .2s ease;
    }

    .action-option:hover {
        border-color: #b8c7d8;
        box-shadow: 0 7px 18px rgba(16, 41, 77, .08);
        transform: translateY(-2px);
    }

    .action-option-icon {
        width: 42px;
        height: 42px;
        min-width: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        background: #f1f5f9;
        color: #17477f;
        font-size: 16px;
    }

    .action-option.approve .action-option-icon {
        background: #edf8f1;
        color: #21864a;
    }

    .action-option-content {
        min-width: 0;
    }

    .action-option-content h3 {
        margin: 1px 0 5px;
        color: #10294d;
        font-size: 13px;
        font-weight: 800;
    }

    .action-option-content p {
        margin: 0 0 10px;
        color: #64748b;
        font-size: 10px;
        line-height: 1.55;
    }

    .action-option-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #17477f;
        font-size: 10px;
        font-weight: 800;
    }

    .action-option.approve .action-option-link {
        color: #21864a;
    }


    /* =========================================================
   PRINT INFO
   ========================================================= */

    .print-info {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px 18px;
        border: 1px solid #e4e9f0;
        border-radius: 12px;
        background: #fff;
    }

    .print-info-icon {
        width: 34px;
        height: 34px;
        min-width: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 9px;
        background: #fff7df;
        color: #a87900;
        font-size: 13px;
    }

    .print-info h3 {
        margin: 1px 0 4px;
        color: #10294d;
        font-size: 11px;
        font-weight: 800;
    }

    .print-info p {
        margin: 0 0 7px;
        color: #64748b;
        font-size: 10px;
        line-height: 1.5;
    }

    .print-info code {
        display: inline-block;
        padding: 5px 8px;
        border-radius: 6px;
        background: #f1f5f9;
        color: #475569;
        font-family: Consolas, monospace;
        font-size: 9px;
    }


    /* =========================================================
   RESPONSIVE
   ========================================================= */

    @media (max-width: 900px) {

        .application-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .info-item:nth-child(2) {
            border-right: 0;
        }

        .info-item:nth-child(-n + 2) {
            border-bottom: 1px solid #edf1f5;
        }

        .action-grid {
            grid-template-columns: 1fr;
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
    }


    @media (max-width: 600px) {

        .page-heading {
            flex-direction: column;
            gap: 12px;
        }

        .back-button {
            align-self: flex-start;
        }

        .application-card-header,
        .action-card-header {
            padding: 15px;
        }

        .application-grid {
            grid-template-columns: 1fr;
        }

        .info-item {
            border-right: 0;
            border-bottom: 1px solid #edf1f5;
            padding: 13px 15px;
        }

        .info-item:last-child {
            border-bottom: 0;
        }

        .info-item:nth-child(-n + 2) {
            border-bottom: 1px solid #edf1f5;
        }

        .action-grid {
            padding: 14px 15px 15px;
        }

        .action-option {
            padding: 13px;
        }

        .process-card,
        .print-info {
            padding: 13px;
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

        .application-card-header h2,
        .action-card-header h2 {
            font-size: 14px;
        }

        .status-badge {
            padding: 6px 8px;
            font-size: 9px;
        }

        .action-option-content p {
            font-size: 9px;
        }
    }
</style>


<script>
    function confirmCap(action) {

        const isDirectApprove = action === 'setujui';

        const title = isDirectApprove
            ? 'Setujui Langsung?'
            : 'Cap SPPD?';

        const text = isDirectApprove
            ? 'Pengajuan akan langsung berstatus selesai tanpa proses cap normal.'
            : 'Pengajuan akan dicap dan statusnya berubah menjadi selesai.';

        const confirmText = isDirectApprove
            ? 'Ya, Setujui'
            : 'Ya, Cap SPPD';

        Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: confirmText,
            cancelButtonText: 'Batal',
            confirmButtonColor: isDirectApprove ? '#21864a' : '#10294d',
            cancelButtonColor: '#6c757d',
            reverseButtons: true
        }).then(function (result) {

            if (!result.isConfirmed) {
                return;
            }

            submitCapForm(action);
        });
    }


    function submitCapForm(action) {

        const form = document.createElement('form');

        form.method = 'POST';
        form.action = 'umum_cap.php?id=<?php echo $id; ?>';

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = '<?php echo $id; ?>';

        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;

        form.appendChild(idInput);
        form.appendChild(actionInput);

        document.body.appendChild(form);

        form.submit();
    }
</script>


</body>

</html>