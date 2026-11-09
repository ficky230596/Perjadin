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
   AMBIL ANTRIAN PARAF
===================================================== */

$queue = getScheduledQueue(
    $pdo,
    'draft_sppd'
);


/* =====================================================
   PROSES POST PARAF
===================================================== */

$isSuccess = false;

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['proses_id'])
) {

    $id = $_POST['proses_id'];


    /* Update status pengajuan */

    $stmt = $pdo->prepare(
        "UPDATE pengajuan
         SET status = 'paraf_sekwan'
         WHERE id = ?"
    );

    $stmt->execute([
        $id
    ]);


    /* Ambil nomor WhatsApp Ketua */

    $stmt_ketua = $pdo->prepare(
        "SELECT wa_phone
         FROM users
         WHERE role = 'ketua'
         LIMIT 1"
    );

    $stmt_ketua->execute();

    $ketua = $stmt_ketua->fetch();


    /* Kirim notifikasi WhatsApp */

    if (
        $ketua &&
        !empty($ketua['wa_phone'])
    ) {

        sendWaNotification(
            $ketua['wa_phone'],
            "SPPD ID #$id siap TTd.",
            $fonnte_token
        );

    }


    $isSuccess = true;
}


/* =====================================================
   HEADER
===================================================== */

include 'header_sekwan.php';

include 'sidebar_sekwan.php';

?>


<!-- =====================================================
     SWEETALERT2
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<?php if ($isSuccess): ?>

<script>

document.addEventListener(
    "DOMContentLoaded",
    function () {

        Swal.fire({

            title:
                "Sukses",

            text:
                "Paraf & WA terkirim",

            icon:
                "success",

            confirmButtonColor:
                "#10294d",

            confirmButtonText:
                "OK"

        }).then(function () {

            window.location.href =
                "sekwan_dashboard.php";

        });

    }
);

</script>

<?php endif; ?>


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
        22px;

    border-radius:
        14px;

    border:
        1px solid #e8edf4;

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
   INFORMASI ANTRIAN
===================================================== */

.queue-info {

    display:
        flex;

    align-items:
        center;

    gap:
        12px;

    padding:
        15px 18px;

    margin-bottom:
        18px;

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-left:
        4px solid #f2bd4b;

    border-radius:
        11px;

    box-shadow:
        0 3px 12px
        rgba(16, 41, 77, .05);

}


.queue-info-icon {

    width:
        38px;

    height:
        38px;

    min-width:
        38px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    border-radius:
        9px;

    background:
        #fff5d6;

    color:
        #b17b00;

}


.queue-info-text {

    min-width:
        0;

}


.queue-info-text strong {

    display:
        block;

    color:
        #10294d;

    font-size:
        14px;

}


.queue-info-text span {

    display:
        block;

    margin-top:
        2px;

    color:
        #64748b;

    font-size:
        12px;

}


/* =====================================================
   LIST ANTRIAN
===================================================== */

.paraf-list {

    display:
        flex;

    flex-direction:
        column;

    gap:
        12px;

}


/* =====================================================
   CARD PENGAJUAN
===================================================== */

.paraf-card {

    background:
        #fff;

    border:
        1px solid #e8edf4;

    border-radius:
        13px;

    padding:
        17px 18px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        18px;

    transition:
        transform .2s ease,
        box-shadow .2s ease,
        border-color .2s ease;

}


.paraf-card:hover {

    transform:
        translateY(-2px);

    box-shadow:
        0 7px 20px
        rgba(16, 41, 77, .10);

    border-color:
        #d7e0eb;

}


/* =====================================================
   INFORMASI PENGAJUAN
===================================================== */

.paraf-card-info {

    display:
        flex;

    align-items:
        center;

    gap:
        14px;

    min-width:
        0;

}


.paraf-card-icon {

    width:
        45px;

    height:
        45px;

    min-width:
        45px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #eef4fb;

    color:
        #17477f;

    border-radius:
        10px;

    font-size:
        18px;

}


.paraf-card-text {

    min-width:
        0;

}


.paraf-card-text .label {

    display:
        block;

    margin-bottom:
        3px;

    color:
        #94a3b8;

    font-size:
        11px;

    font-weight:
        600;

    text-transform:
        uppercase;

    letter-spacing:
        .4px;

}


.paraf-card-text .id {

    color:
        #10294d;

    font-size:
        17px;

    font-weight:
        750;

}


/* =====================================================
   BADGE STATUS
===================================================== */

.status-badge {

    display:
        inline-flex;

    align-items:
        center;

    gap:
        6px;

    margin-left:
        8px;

    padding:
        5px 9px;

    border-radius:
        20px;

    background:
        #fff5d6;

    color:
        #9a6b00;

    font-size:
        11px;

    font-weight:
        650;

    vertical-align:
        middle;

}


/* =====================================================
   FORM AKSI
===================================================== */

.paraf-action {

    flex-shrink:
        0;

}


.paraf-form {

    margin:
        0;

}


/* =====================================================
   BUTTON PARAF
===================================================== */

.btn-paraf {

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
        10px 17px;

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

    box-shadow:
        0 3px 8px
        rgba(40,167,69,.20);

    transition:
        background .2s ease,
        transform .2s ease,
        box-shadow .2s ease;

}


.btn-paraf:hover {

    background:
        #218838;

    transform:
        translateY(-1px);

    box-shadow:
        0 5px 12px
        rgba(40,167,69,.25);

}


.btn-paraf:active {

    transform:
        scale(.97);

}


/* =====================================================
   EMPTY STATE
===================================================== */

.empty-antrian {

    background:
        #fff;

    min-height:
        230px;

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
        14px;

    box-shadow:
        0 4px 14px
        rgba(16, 41, 77, .06);

}


.empty-icon {

    width:
        58px;

    height:
        58px;

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


.empty-antrian h3 {

    margin:
        0;

    color:
        #10294d;

    font-size:
        16px;

}


.empty-antrian p {

    margin:
        6px 0 0;

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

        padding:
            18px;

        border-radius:
            12px;

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


    .queue-info {

        padding:
            13px 14px;

    }


    .paraf-card {

        align-items:
            flex-start;

        flex-direction:
            column;

        gap:
            14px;

        padding:
            15px;

    }


    .paraf-card-info {

        width:
            100%;

    }


    .paraf-action {

        width:
            100%;

    }


    .paraf-form {

        width:
            100%;

    }


    .btn-paraf {

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


    .queue-info-text strong {

        font-size:
            13px;

    }


    .queue-info-text span {

        font-size:
            11px;

    }


    .paraf-card-text .id {

        font-size:
            16px;

    }


    .status-badge {

        display:
            table;

        margin:
            6px 0 0;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    .main-content-sekwan {

        margin-left:
            0;

        padding:
            10px;

    }


    .paraf-card {

        box-shadow:
            none;

        border:
            1px solid #ddd;

    }


    .paraf-action {

        display:
            none;

    }

}

</style>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<main class="main-content-sekwan">


    <!-- =================================================
         HEADER HALAMAN
    ================================================== -->

    <section class="page-header">

        <div class="page-header-content">

            <h1>
                Antrian Paraf Sekwan
            </h1>

            <p>
                Periksa dan proses dokumen perjalanan dinas
                yang menunggu paraf Sekwan.
            </p>

        </div>


        <div class="page-header-icon">

            <i
                class="fa-solid fa-file-signature"
            ></i>

        </div>

    </section>


    <!-- =================================================
         INFORMASI ANTRIAN
    ================================================== -->

    <?php if (!empty($queue)): ?>

        <div class="queue-info">

            <div class="queue-info-icon">

                <i
                    class="fa-solid fa-clock"
                ></i>

            </div>


            <div class="queue-info-text">

                <strong>

                    <?php
                    echo count($queue);
                    ?>

                    dokumen menunggu paraf

                </strong>


                <span>
                    Silakan periksa dokumen sebelum melakukan paraf.
                </span>

            </div>

        </div>

    <?php endif; ?>


    <!-- =================================================
         DAFTAR ANTRIAN
    ================================================== -->

    <?php if (empty($queue)): ?>


        <!-- =============================================
             EMPTY STATE
        ============================================== -->

        <div class="empty-antrian">

            <div class="empty-icon">

                <i
                    class="fa-regular fa-folder-open"
                ></i>

            </div>


            <h3>
                Tidak Ada Antrian
            </h3>


            <p>
                Tidak ada draft siap paraf saat ini.
            </p>

        </div>


    <?php else: ?>


        <div class="paraf-list">


            <?php foreach ($queue as $q): ?>


                <div class="paraf-card">


                    <!-- =================================
                         INFORMASI
                    ================================== -->

                    <div class="paraf-card-info">


                        <div class="paraf-card-icon">

                            <i
                                class="fa-solid fa-file-lines"
                            ></i>

                        </div>


                        <div class="paraf-card-text">

                            <span class="label">
                                Nomor Pengajuan
                            </span>


                            <span class="id">

                                #

                                <?php
                                echo htmlspecialchars(
                                    $q['id']
                                );
                                ?>


                                <span class="status-badge">

                                    <i
                                        class="fa-solid fa-clock"
                                    ></i>

                                    Menunggu Paraf

                                </span>

                            </span>

                        </div>


                    </div>


                    <!-- =================================
                         AKSI PARAF
                    ================================== -->

                    <div class="paraf-action">

                        <form
                            method="POST"
                            class="paraf-form"
                        >

                            <input
                                type="hidden"
                                name="proses_id"
                                value="<?php
                                echo htmlspecialchars(
                                    $q['id']
                                );
                                ?>"
                            >


                            <button
                                type="submit"
                                class="btn-paraf"
                            >

                                <i
                                    class="fa-solid fa-file-signature"
                                ></i>

                                Paraf ID

                                <?php
                                echo htmlspecialchars(
                                    $q['id']
                                );
                                ?>

                            </button>

                        </form>

                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    <?php endif; ?>


</main>


<?php include 'footer.php'; ?>
