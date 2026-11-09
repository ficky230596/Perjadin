<?php

session_start();

/*
|--------------------------------------------------------------------------
| CEK LOGIN DAN ROLE
|--------------------------------------------------------------------------
*/

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'pegawai'
) {
    header('Location: pegawai_dashboard.php');
    exit;
}


/*
|--------------------------------------------------------------------------
| DATABASE
|--------------------------------------------------------------------------
*/

include '../config/db.php';


/*
|--------------------------------------------------------------------------
| AMBIL DATA PANGKAT & GOLONGAN PEGAWAI
|--------------------------------------------------------------------------
*/

$stmt_pg = $pdo->prepare("
    SELECT pangkat, golongan
    FROM users
    WHERE id = ?
    LIMIT 1
");

$stmt_pg->execute([
    $_SESSION['user_id']
]);

$pegawai = $stmt_pg->fetch(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| NILAI DEFAULT
|--------------------------------------------------------------------------
*/

$errors = [];

$tujuan = '';
$tgl_berangkat = '';
$tgl_kembali = '';
$alasan = '';
$urgensi = 'rendah';


/*
|--------------------------------------------------------------------------
| PROSES FORM
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tujuan = trim($_POST['tujuan'] ?? '');
    $tgl_berangkat = trim($_POST['tgl_berangkat'] ?? '');
    $tgl_kembali = trim($_POST['tgl_kembali'] ?? '');
    $alasan = trim($_POST['alasan'] ?? '');
    $urgensi = trim($_POST['urgensi'] ?? '');


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TUJUAN
    |--------------------------------------------------------------------------
    */

    if ($tujuan === '') {

        $errors[] = 'Tujuan perjalanan wajib diisi.';

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TANGGAL BERANGKAT
    |--------------------------------------------------------------------------
    */

    if ($tgl_berangkat === '') {

        $errors[] = 'Tanggal berangkat wajib diisi.';

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI TANGGAL KEMBALI
    |--------------------------------------------------------------------------
    */

    if ($tgl_kembali === '') {

        $errors[] = 'Tanggal kembali wajib diisi.';

    }


    /*
    |--------------------------------------------------------------------------
    | TANGGAL KEMBALI TIDAK BOLEH SEBELUM TANGGAL BERANGKAT
    |--------------------------------------------------------------------------
    */

    if (
        $tgl_berangkat !== '' &&
        $tgl_kembali !== '' &&
        $tgl_kembali < $tgl_berangkat
    ) {

        $errors[] =
            'Tanggal kembali tidak boleh sebelum tanggal berangkat.';

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI ALASAN
    |--------------------------------------------------------------------------
    */

    if ($alasan === '') {

        $errors[] = 'Alasan perjalanan wajib diisi.';

    }


    /*
    |--------------------------------------------------------------------------
    | VALIDASI URGENSI
    |--------------------------------------------------------------------------
    */

    if (
        !in_array(
            $urgensi,
            ['rendah', 'sedang', 'tinggi'],
            true
        )
    ) {

        $errors[] = 'Tingkat urgensi tidak valid.';

    }


    /*
    |--------------------------------------------------------------------------
    | JIKA VALIDASI BERHASIL
    |--------------------------------------------------------------------------
    */

    if (empty($errors)) {


        /*
        |--------------------------------------------------------------------------
        | SKOR PRIORITAS
        |--------------------------------------------------------------------------
        */

        $skor = 1;

        if ($urgensi === 'sedang') {

            $skor = 2;

        } elseif ($urgensi === 'tinggi') {

            $skor = 3;

        }


        /*
        |--------------------------------------------------------------------------
        | SIMPAN PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $stmt = $pdo->prepare("
            INSERT INTO pengajuan (
                pegawai_id,
                tujuan,
                tanggal_berangkat,
                tanggal_kembali,
                alasan,
                urgensi,
                prioritas_skor,
                pangkat,
                golongan
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        $stmt->execute([
            $_SESSION['user_id'],
            $tujuan,
            $tgl_berangkat,
            $tgl_kembali,
            $alasan,
            $urgensi,
            $skor,
            $pegawai['pangkat'] ?? '',
            $pegawai['golongan'] ?? ''
        ]);


        /*
        |--------------------------------------------------------------------------
        | AMBIL ID PENGAJUAN
        |--------------------------------------------------------------------------
        */

        $id = $pdo->lastInsertId();


        /*
        |--------------------------------------------------------------------------
        | AMBIL NOMOR WHATSAPP UMUM
        |--------------------------------------------------------------------------
        */

        $stmt_umum = $pdo->prepare("
            SELECT wa_phone
            FROM users
            WHERE role = 'umum'
            LIMIT 1
        ");

        $stmt_umum->execute();

        $umum = $stmt_umum->fetch(PDO::FETCH_ASSOC);


        /*
        |--------------------------------------------------------------------------
        | KIRIM NOTIFIKASI WHATSAPP
        |--------------------------------------------------------------------------
        */

        $waTerkirim = false;

        if (
            $umum &&
            !empty($umum['wa_phone']) &&
            isset($fonnte_token) &&
            function_exists('sendWaNotification')
        ) {

            sendWaNotification(
                $umum['wa_phone'],
                "Pengajuan baru ID #$id dari " .
                ($_SESSION['nama'] ?? 'Pegawai') .
                " - Urgensi: $urgensi. Tujuan: $tujuan.",
                $fonnte_token
            );

            $waTerkirim = true;
        }


        /*
        |--------------------------------------------------------------------------
        | PESAN SUKSES
        |--------------------------------------------------------------------------
        */

        $pesanSukses = $waTerkirim
            ? 'Pengajuan berhasil disimpan dan notifikasi WhatsApp telah dikirim.'
            : 'Pengajuan berhasil disimpan.';


        /*
        |--------------------------------------------------------------------------
        | SWEETALERT
        |--------------------------------------------------------------------------
        */

        $pesanSuksesJs = json_encode(
            $pesanSukses,
            JSON_UNESCAPED_UNICODE
        );

        echo "
        <script>
            document.addEventListener('DOMContentLoaded', function () {

                Swal.fire({
                    icon: 'success',
                    title: 'Pengajuan Berhasil',
                    text: $pesanSuksesJs,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#10294d'
                }).then(function () {

                    window.location.href =
                        'pegawai_dashboard.php';

                });

            });
        </script>
        ";

        /*
         * Jangan proses form lagi.
         */
        $formBerhasil = true;

    } else {

        $formBerhasil = false;

    }

} else {

    $formBerhasil = false;

}


/*
|--------------------------------------------------------------------------
| HEADER DAN SIDEBAR
|--------------------------------------------------------------------------
*/

include 'header_pegawai.php';
include 'sidebar_pegawai.php';

?>


<style>

/* =========================================================
   HALAMAN AJUKAN
   ========================================================= */

.ajukan-page {

    margin-left: 250px;

    /*
     * Header desktop = 89px
     */
    padding-top: 14px;

    padding-left: 30px;
    padding-right: 30px;

    /*
     * Memberikan ruang agar footer fixed
     * tidak menutupi konten.
     */
    padding-bottom: 110px;

    min-height: 100vh;

    width: calc(100% - 250px);

    background: #f4f7fc;

    box-sizing: border-box;

    overflow-x: hidden;
}


.ajukan-container {

    width: 100%;

    max-width: 1150px;

    margin: 0 auto;
}


/* =========================================================
   JUDUL HALAMAN
   ========================================================= */

.ajukan-page-header {

    margin-bottom: 20px;
}

.ajukan-page-header h1 {

    margin: 0;

    color: #10294d;

    font-size: 25px;

    font-weight: 800;

    line-height: 1.3;
}

.ajukan-page-header p {

    margin: 6px 0 0;

    color: #7c8899;

    font-size: 12px;

    line-height: 1.6;
}


/* =========================================================
   FORM CARD
   ========================================================= */

.ajukan-card {

    width: 100%;

    background: #ffffff;

    border: 1px solid #e5eaf2;

    border-radius: 13px;

    box-shadow:
        0 4px 16px rgba(16, 41, 77, 0.06);

    overflow: hidden;
}


/* =========================================================
   CARD HEADER
   ========================================================= */

.ajukan-card-header {

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 18px 22px;

    background: linear-gradient(
        110deg,
        #10294d 0%,
        #17477f 100%
    );

    color: #ffffff;

    border-bottom: 3px solid #f2bd4b;
}


.ajukan-card-icon {

    width: 40px;
    height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    flex-shrink: 0;

    border-radius: 9px;

    background: rgba(242, 189, 75, 0.15);

    border: 1px solid rgba(242, 189, 75, 0.45);

    color: #f2bd4b;

    font-size: 17px;
}


.ajukan-card-header h2 {

    margin: 0;

    font-size: 15px;

    font-weight: 800;
}


.ajukan-card-header span {

    display: block;

    margin-top: 3px;

    color: rgba(255, 255, 255, 0.7);

    font-size: 10px;
}


/* =========================================================
   FORM
   ========================================================= */

.ajukan-form {

    padding: 25px;
}


/* =========================================================
   BARIS FORM
   ========================================================= */

.form-row {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 18px;

    margin-bottom: 18px;
}


.form-row.full-row {

    grid-template-columns: 1fr;
}


/* =========================================================
   FORM GROUP
   ========================================================= */

.form-group {

    min-width: 0;
}


.form-group label {

    display: block;

    margin-bottom: 7px;

    color: #34445a;

    font-size: 11px;

    font-weight: 700;
}


.form-group label::after {

    content: " *";

    color: #d9534f;
}


/* =========================================================
   INPUT / SELECT / TEXTAREA
   ========================================================= */

.form-group input,
.form-group select,
.form-group textarea {

    width: 100%;

    border: 1px solid #dce2eb;

    border-radius: 8px;

    background: #ffffff;

    color: #34445a;

    font-family: 'Inter', Arial, sans-serif;

    font-size: 12px;

    outline: none;

    transition:
        border-color 0.2s ease,
        box-shadow 0.2s ease;

    box-sizing: border-box;
}


.form-group input,
.form-group select {

    height: 43px;

    padding: 0 12px;
}


.form-group textarea {

    min-height: 125px;

    padding: 11px 12px;

    resize: vertical;

    line-height: 1.6;
}


/* =========================================================
   FOCUS
   ========================================================= */

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {

    border-color: #17477f;

    box-shadow:
        0 0 0 3px rgba(23, 71, 127, 0.08);
}


/* =========================================================
   READONLY
   ========================================================= */

.form-group input[readonly] {

    background: #f4f6f9;

    color: #697586;

    cursor: not-allowed;
}


/* =========================================================
   BUTTON
   ========================================================= */

.btn-group {

    display: flex;

    justify-content: flex-end;

    margin-top: 2px;
}


.btn-submit {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 8px;

    min-width: 155px;

    height: 43px;

    padding: 0 20px;

    border: none;

    border-radius: 8px;

    background: linear-gradient(
        135deg,
        #10294d,
        #17477f
    );

    color: #ffffff;

    font-family: 'Inter', Arial, sans-serif;

    font-size: 12px;

    font-weight: 700;

    cursor: pointer;

    box-shadow:
        0 4px 10px rgba(16, 41, 77, 0.15);

    transition:
        transform 0.2s ease,
        box-shadow 0.2s ease;
}


.btn-submit i {

    color: #f2bd4b;
}


.btn-submit:hover {

    transform: translateY(-1px);

    box-shadow:
        0 6px 14px rgba(16, 41, 77, 0.2);
}


.btn-submit:disabled {

    opacity: 0.7;

    cursor: wait;

    transform: none;
}


/* =========================================================
   INFORMASI
   ========================================================= */

.ajukan-info {

    display: flex;

    align-items: flex-start;

    gap: 9px;

    margin-top: 16px;

    padding: 11px 13px;

    background: #edf4ff;

    border: 1px solid #dce9f8;

    border-radius: 8px;

    color: #58708c;

    font-size: 10px;

    line-height: 1.6;
}


.ajukan-info i {

    flex-shrink: 0;

    margin-top: 2px;

    color: #17477f;
}


/* =========================================================
   ERROR VALIDASI
   ========================================================= */

.form-error-box {

    margin-bottom: 18px;

    padding: 12px 15px;

    background: #fff0ef;

    border: 1px solid #f1d0cd;

    border-radius: 9px;

    color: #a94442;

    font-size: 11px;

    line-height: 1.6;
}


.form-error-box-title {

    display: flex;

    align-items: center;

    gap: 7px;

    margin-bottom: 5px;

    font-weight: 800;
}


.form-error-box ul {

    margin: 4px 0 0 22px;

    padding: 0;
}


/* =========================================================
   TABLET
   ========================================================= */

@media (max-width: 1000px) {

    .ajukan-page {

        padding-left: 20px;

        padding-right: 20px;
    }

    .form-row {

        gap: 14px;
    }

}


/* =========================================================
   MOBILE
   ========================================================= */

@media (max-width: 768px) {

    .ajukan-page {

        /*
         * Sidebar mobile tidak mengambil ruang kiri.
         */
        margin-left: 0;

        width: 100%;

        /*
         * Header mobile = 112px.
         * Tambahan ruang untuk tombol hamburger.
         */
        padding-top: 174px;

        padding-left: 12px;

        padding-right: 12px;

        /*
         * Ruang untuk footer fixed.
         */
        padding-bottom: 100px;
    }


    .ajukan-page-header {

        margin-bottom: 15px;
    }


    .ajukan-page-header h1 {

        font-size: 19px;
    }


    .ajukan-page-header p {

        font-size: 10px;
    }


    .ajukan-card {

        border-radius: 10px;
    }


    .ajukan-card-header {

        padding: 15px;

        gap: 10px;
    }


    .ajukan-card-icon {

        width: 36px;
        height: 36px;

        font-size: 15px;
    }


    .ajukan-card-header h2 {

        font-size: 13px;
    }


    .ajukan-card-header span {

        font-size: 9px;
    }


    .ajukan-form {

        padding: 18px 14px;
    }


    /*
     * Mobile menjadi satu kolom.
     */
    .form-row {

        grid-template-columns: 1fr;

        gap: 14px;

        margin-bottom: 14px;
    }


    .form-group input,
    .form-group select {

        height: 42px;

        font-size: 11px;
    }


    .form-group textarea {

        min-height: 115px;

        font-size: 11px;
    }


    .btn-group {

        display: block;
    }


    .btn-submit {

        width: 100%;

        height: 42px;
    }


    .ajukan-info {

        font-size: 9px;
    }

}


/* =========================================================
   MOBILE KECIL
   ========================================================= */

@media (max-width: 480px) {

    .ajukan-page {

        padding-top: 170px;

        padding-left: 9px;

        padding-right: 9px;

        padding-bottom: 90px;
    }


    .ajukan-page-header h1 {

        font-size: 17px;
    }


    .ajukan-page-header p {

        font-size: 9px;
    }


    .ajukan-card-header {

        padding: 13px;
    }


    .ajukan-form {

        padding: 15px 11px;
    }


    .form-group label {

        font-size: 10px;
    }


    .form-group input,
    .form-group select {

        height: 40px;

        font-size: 10px;
    }


    .form-group textarea {

        min-height: 105px;

        font-size: 10px;
    }


    .ajukan-info {

        font-size: 8px;

        padding: 9px 10px;
    }

}

</style>


<!-- =========================================================
     MAIN CONTENT
     ========================================================= -->

<main class="ajukan-page">

    <div class="ajukan-container">


        <!-- =================================================
             JUDUL
             ================================================= -->

        <div class="ajukan-page-header">

            <h1>
                Ajukan Perjalanan Dinas
            </h1>

            <p>
                Lengkapi formulir berikut untuk mengajukan
                perjalanan dinas Anda.
            </p>

        </div>


        <!-- =================================================
             FORM CARD
             ================================================= -->

        <section class="ajukan-card">


            <!-- CARD HEADER -->

            <div class="ajukan-card-header">

                <div class="ajukan-card-icon">

                    <i class="fa-solid fa-file-circle-plus"></i>

                </div>

                <div>

                    <h2>
                        Form Pengajuan Perjadin
                    </h2>

                    <span>
                        Isi data perjalanan dinas dengan lengkap.
                    </span>

                </div>

            </div>


            <!-- =================================================
                 FORM
                 ================================================= -->

            <form
                class="ajukan-form"
                method="POST"
                id="ajukanForm"
            >


                <!-- =================================================
                     ERROR
                     ================================================= -->

                <?php if (!empty($errors)): ?>

                    <div class="form-error-box">

                        <div class="form-error-box-title">

                            <i class="fa-solid fa-circle-exclamation"></i>

                            Periksa kembali data pengajuan:

                        </div>

                        <ul>

                            <?php foreach ($errors as $error): ?>

                                <li>
                                    <?php
                                    echo htmlspecialchars($error);
                                    ?>
                                </li>

                            <?php endforeach; ?>

                        </ul>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     BARIS 1
                     ================================================= -->

                <div class="form-row">


                    <!-- PANGKAT -->

                    <div class="form-group">

                        <label for="pangkat">
                            Pangkat
                        </label>

                        <input
                            type="text"
                            id="pangkat"
                            value="<?php
                                echo htmlspecialchars(
                                    $pegawai['pangkat'] ?? '-'
                                );
                            ?>"
                            readonly
                        >

                    </div>


                    <!-- GOLONGAN -->

                    <div class="form-group">

                        <label for="golongan">
                            Golongan
                        </label>

                        <input
                            type="text"
                            id="golongan"
                            value="<?php
                                echo htmlspecialchars(
                                    $pegawai['golongan'] ?? '-'
                                );
                            ?>"
                            readonly
                        >

                    </div>


                    <!-- TUJUAN -->

                    <div class="form-group">

                        <label for="tujuan">
                            Tujuan
                        </label>

                        <input
                            type="text"
                            id="tujuan"
                            name="tujuan"
                            placeholder="Masukkan tujuan perjalanan"
                            value="<?php
                                echo htmlspecialchars($tujuan);
                            ?>"
                            maxlength="255"
                            required
                        >

                    </div>

                </div>


                <!-- =================================================
                     BARIS 2
                     ================================================= -->

                <div class="form-row">


                    <!-- TANGGAL BERANGKAT -->

                    <div class="form-group">

                        <label for="tgl_berangkat">
                            Tanggal Berangkat
                        </label>

                        <input
                            type="date"
                            id="tgl_berangkat"
                            name="tgl_berangkat"
                            value="<?php
                                echo htmlspecialchars(
                                    $tgl_berangkat
                                );
                            ?>"
                            required
                        >

                    </div>


                    <!-- TANGGAL KEMBALI -->

                    <div class="form-group">

                        <label for="tgl_kembali">
                            Tanggal Kembali
                        </label>

                        <input
                            type="date"
                            id="tgl_kembali"
                            name="tgl_kembali"
                            value="<?php
                                echo htmlspecialchars(
                                    $tgl_kembali
                                );
                            ?>"
                            required
                        >

                    </div>


                    <!-- URGENSI -->

                    <div class="form-group">

                        <label for="urgensi">
                            Urgensi
                        </label>

                        <select
                            id="urgensi"
                            name="urgensi"
                            required
                        >

                            <option
                                value="rendah"
                                <?php
                                echo (
                                    $urgensi === 'rendah'
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >
                                Rendah
                            </option>

                            <option
                                value="sedang"
                                <?php
                                echo (
                                    $urgensi === 'sedang'
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >
                                Sedang
                            </option>

                            <option
                                value="tinggi"
                                <?php
                                echo (
                                    $urgensi === 'tinggi'
                                )
                                    ? 'selected'
                                    : '';
                                ?>
                            >
                                Tinggi
                            </option>

                        </select>

                    </div>

                </div>


                <!-- =================================================
                     ALASAN
                     ================================================= -->

                <div class="form-row full-row">

                    <div class="form-group">

                        <label for="alasan">
                            Alasan Perjalanan
                        </label>

                        <textarea
                            id="alasan"
                            name="alasan"
                            placeholder="Jelaskan alasan perjalanan dinas..."
                            maxlength="2000"
                            required
                        ><?php
                            echo htmlspecialchars($alasan);
                        ?></textarea>

                    </div>

                </div>


                <!-- =================================================
                     BUTTON
                     ================================================= -->

                <div class="form-row full-row">

                    <div class="form-group btn-group">

                        <button
                            type="submit"
                            class="btn-submit"
                            id="submitBtn"
                        >

                            <i class="fa-solid fa-paper-plane"></i>

                            Ajukan Perjadin

                        </button>

                    </div>

                </div>


                <!-- =================================================
                     INFORMASI
                     ================================================= -->

                <div class="ajukan-info">

                    <i class="fa-solid fa-circle-info"></i>

                    <span>
                        Setelah pengajuan dikirim, data akan diproses
                        sesuai alur persetujuan perjalanan dinas.
                        Anda dapat melihat perkembangan pengajuan
                        melalui menu Riwayat Pengajuan.
                    </span>

                </div>


            </form>

        </section>

    </div>

</main>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const form =
        document.getElementById('ajukanForm');

    const tanggalBerangkat =
        document.getElementById('tgl_berangkat');

    const tanggalKembali =
        document.getElementById('tgl_kembali');

    const submitBtn =
        document.getElementById('submitBtn');


    /*
    |--------------------------------------------------------------------------
    | TANGGAL
    |--------------------------------------------------------------------------
    */

    function updateTanggalKembali() {

        if (tanggalBerangkat.value) {

            tanggalKembali.min =
                tanggalBerangkat.value;

        }

        validateTanggal();

    }


    function validateTanggal() {

        if (
            tanggalBerangkat.value &&
            tanggalKembali.value
        ) {

            if (
                tanggalKembali.value <
                tanggalBerangkat.value
            ) {

                tanggalKembali.setCustomValidity(
                    'Tanggal kembali tidak boleh sebelum tanggal berangkat.'
                );

            } else {

                tanggalKembali.setCustomValidity('');

            }

        } else {

            tanggalKembali.setCustomValidity('');

        }

    }


    tanggalBerangkat.addEventListener(
        'change',
        updateTanggalKembali
    );


    tanggalKembali.addEventListener(
        'change',
        validateTanggal
    );


    /*
    |--------------------------------------------------------------------------
    | SET MINIMUM TANGGAL SAAT HALAMAN DIBUKA
    |--------------------------------------------------------------------------
    */

    if (tanggalBerangkat.value) {

        tanggalKembali.min =
            tanggalBerangkat.value;

    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT FORM
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                validateTanggal();

                if (!form.checkValidity()) {

                    event.preventDefault();

                    form.reportValidity();

                    return;

                }


                /*
                 * Mencegah klik submit berkali-kali.
                 */

                if (submitBtn) {

                    submitBtn.disabled = true;

                    submitBtn.innerHTML = `
                        <i class="fa-solid fa-spinner fa-spin"></i>
                        Mengirim...
                    `;

                }

            }
        );

    }

});

</script>


<?php include 'footer.php'; ?>