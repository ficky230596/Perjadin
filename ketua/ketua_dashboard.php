<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =====================================================
   VALIDASI SESSION
===================================================== */

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'ketua'
) {
    header('Location: ../index.php');
    exit;
}


/* =====================================================
   KONEKSI DATABASE
===================================================== */

require_once '../config/db.php';


/* =====================================================
   MENGAMBIL ANTRIAN TANDA TANGAN
===================================================== */

$queue = [];

try {

    $queue = getScheduledQueue(
        $pdo,
        'paraf_sekwan'
    );

    if (!is_array($queue)) {
        $queue = [];
    }

} catch (Throwable $e) {

    error_log(
        'Kesalahan mengambil antrean Ketua: ' .
        $e->getMessage()
    );

    $queue = [];

}

?>

<?php include 'header_ketua.php'; ?>

<?php include 'sidebar_ketua.php'; ?>


<!-- =====================================================
     SWEETALERT
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<style>

/* =====================================================
   KONTEN DASHBOARD
===================================================== */

.dashboard-ketua {
    min-width: 0;
    padding: 8px 5px 30px;
    color: #1e293b;
}


/* =====================================================
   HEADER DASHBOARD
===================================================== */

.dashboard-heading {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 26px;
}

.dashboard-heading h1 {
    margin: 0 0 9px;
    color: #10294d;
    font-size: 27px;
    font-weight: 800;
    line-height: 1.4;
}

.dashboard-heading p {
    margin: 0;
    color: #64748b;
    font-size: 14px;
    line-height: 1.7;
}

.dashboard-role {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 14px;
    border: 1px solid #f1d79b;
    border-radius: 10px;
    background: #fff9e9;
    color: #8b6415;
    font-size: 12px;
    font-weight: 700;
}


/* =====================================================
   PANEL ANTREAN
===================================================== */

.queue-card {
    overflow: hidden;
    border: 1px solid #e4eaf2;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 5px 20px rgba(16, 41, 77, 0.06);
}

.queue-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    padding: 22px 24px;
    border-bottom: 1px solid #e9eef5;
}

.queue-title {
    display: flex;
    align-items: center;
    gap: 13px;
}

.queue-title-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 44px;
    height: 44px;
    flex-shrink: 0;
    border-radius: 12px;
    background: #edf4ff;
    color: #17477f;
    font-size: 19px;
}

.queue-title h2 {
    margin: 0 0 5px;
    color: #10294d;
    font-size: 17px;
    font-weight: 800;
}

.queue-title p {
    margin: 0;
    color: #64748b;
    font-size: 12px;
}

.queue-count {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 12px;
    border-radius: 20px;
    background: #fff6df;
    color: #8b6415;
    font-size: 12px;
    font-weight: 700;
}


/* =====================================================
   TABEL
===================================================== */

.queue-table-wrapper {
    width: 100%;
    overflow-x: auto;
}

.queue-table {
    width: 100%;
    min-width: 800px;
    margin: 0;
    border-collapse: collapse;
    background: #fff;
}

.queue-table thead th {
    padding: 15px 20px;
    background: #10294d;
    color: #fff;
    font-size: 12px;
    font-weight: 700;
    text-align: left;
    white-space: nowrap;
    border: none;
}

.queue-table thead th:first-child {
    padding-left: 24px;
}

.queue-table tbody td {
    padding: 17px 20px;
    color: #334155;
    font-size: 13px;
    vertical-align: middle;
    border-bottom: 1px solid #edf1f6;
}

.queue-table tbody td:first-child {
    padding-left: 24px;
}

.queue-table tbody tr:last-child td {
    border-bottom: none;
}

.queue-table tbody tr:nth-child(even) {
    background: #f8fafd;
}

.queue-table tbody tr:hover {
    background: #eef5ff;
}


/* =====================================================
   ID PENGAJUAN
===================================================== */

.queue-id {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    padding: 6px 9px;
    border-radius: 7px;
    background: #edf4ff;
    color: #17477f;
    font-size: 12px;
    font-weight: 800;
}


/* =====================================================
   SKOR PRIORITAS
===================================================== */

.queue-score {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: #8b6415;
    font-weight: 800;
}


/* =====================================================
   TOMBOL AKSI
===================================================== */

.queue-actions {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

.queue-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 9px 12px;
    border: 1px solid transparent;
    border-radius: 8px;
    font-size: 11px;
    font-weight: 700;
    text-decoration: none;
    white-space: nowrap;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: inherit;
}

.queue-btn:hover {
    transform: translateY(-1px);
}


/* TTD NORMAL */

.queue-btn-normal {
    border-color: #c9daf4;
    background: #edf4ff;
    color: #17477f;
}

.queue-btn-normal:hover {
    border-color: #17477f;
    background: #17477f;
    color: #fff;
}


/* TOLAK */

.queue-btn-reject {
    border-color: #f1bfc3;
    background: #fff0f1;
    color: #c82333;
}

.queue-btn-reject:hover {
    border-color: #c82333;
    background: #c82333;
    color: #fff;
}


/* SETUJUI LANGSUNG */

.queue-btn-direct {
    border-color: #b8e5cf;
    background: #eaf8f0;
    color: #167044;
}

.queue-btn-direct:hover {
    border-color: #167044;
    background: #167044;
    color: #fff;
}


/* =====================================================
   KONDISI KOSONG
===================================================== */

.queue-empty {
    padding: 55px 20px;
    text-align: center;
}

.queue-empty-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 65px;
    height: 65px;
    margin: 0 auto 17px;
    border-radius: 50%;
    background: #eaf8f0;
    color: #16834b;
    font-size: 25px;
}

.queue-empty h3 {
    margin: 0 0 8px;
    color: #10294d;
    font-size: 17px;
    font-weight: 800;
}

.queue-empty p {
    margin: 0;
    color: #64748b;
    font-size: 13px;
    line-height: 1.7;
}


/* =====================================================
   FOOTER ANTREAN
===================================================== */

.queue-footer {
    padding: 14px 24px;
    border-top: 1px solid #e9eef5;
    background: #fafcff;
    color: #64748b;
    font-size: 11px;
    line-height: 1.7;
}


/* =====================================================
   RESPONSIVE TABLET
===================================================== */

@media (max-width: 800px) {

    .dashboard-ketua {
        padding: 4px 0 25px;
    }

    .dashboard-heading h1 {
        font-size: 22px;
    }

    .queue-card-header {
        padding: 18px;
    }

    .queue-table thead th,
    .queue-table tbody td {
        padding: 14px;
    }

    .queue-table thead th:first-child,
    .queue-table tbody td:first-child {
        padding-left: 16px;
    }

}


/* =====================================================
   RESPONSIVE MOBILE
===================================================== */

@media (max-width: 480px) {

    .dashboard-heading h1 {
        font-size: 20px;
    }

    .dashboard-heading p {
        font-size: 12px;
    }

    .queue-title h2 {
        font-size: 15px;
    }

    .queue-title p {
        font-size: 11px;
    }

    .queue-count {
        font-size: 11px;
    }

    .queue-card-header {
        align-items: flex-start;
    }

}

</style>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<main>

    <div class="dashboard-ketua">


        <!-- =================================================
             JUDUL DASHBOARD
        ================================================== -->

        <section class="dashboard-heading">

            <div>

                <h1>
                    Dashboard Ketua
                </h1>

                <p>

                    Selamat datang,

                    <strong>

                        <?php
                        echo htmlspecialchars(
                            $_SESSION['nama'] ?? 'Ketua',
                            ENT_QUOTES,
                            'UTF-8'
                        );
                        ?>

                    </strong>.

                    Kelola antrean tanda tangan dan persetujuan SPPD.

                </p>

            </div>


            <div class="dashboard-role">

                <i class="fa-solid fa-user-tie"></i>

                Ketua DPRD

            </div>

        </section>


        <!-- =================================================
             ANTREAN TANDA TANGAN
        ================================================== -->

        <section class="queue-card">


            <!-- HEADER ANTREAN -->

            <div class="queue-card-header">

                <div class="queue-title">

                    <div class="queue-title-icon">

                        <i class="fa-solid fa-file-signature"></i>

                    </div>


                    <div>

                        <h2>
                            Antrian Tanda Tangan
                        </h2>

                        <p>
                            Daftar SPPD yang menunggu proses Ketua.
                        </p>

                    </div>

                </div>


                <div class="queue-count">

                    <i class="fa-solid fa-list-check"></i>

                    <?php echo count($queue); ?>

                    antrean

                </div>

            </div>


            <!-- =================================================
                 JIKA ANTREAN KOSONG
            ================================================== -->

            <?php if (empty($queue)): ?>

                <div class="queue-empty">

                    <div class="queue-empty-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>

                    <h3>
                        Tidak Ada Antrean
                    </h3>

                    <p>

                        Saat ini tidak ada antrean tanda tangan
                        yang perlu diproses.

                    </p>

                </div>


            <?php else: ?>


                <!-- =================================================
                     TABEL ANTREAN
                ================================================== -->

                <div class="queue-table-wrapper">

                    <table class="queue-table">

                        <thead>

                            <tr>

                                <th>ID</th>

                                <th>Tujuan</th>

                                <th>Skor Prioritas</th>

                                <th>Aksi</th>

                            </tr>

                        </thead>


                        <tbody>


                            <?php foreach ($queue as $q): ?>


                                <?php

                                $idPengajuan = (int)($q['id'] ?? 0);

                                $tujuan = $q['tujuan'] ?? '';

                                $skor = $q['prioritas_skor'] ?? 0;

                                ?>


                                <tr>


                                    <!-- ID -->

                                    <td>

                                        <span class="queue-id">

                                            <?php
                                            echo htmlspecialchars(
                                                (string)$idPengajuan,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <!-- TUJUAN -->

                                    <td>

                                        <?php
                                        echo htmlspecialchars(
                                            (string)$tujuan,
                                            ENT_QUOTES,
                                            'UTF-8'
                                        );
                                        ?>

                                    </td>


                                    <!-- SKOR PRIORITAS -->

                                    <td>

                                        <span class="queue-score">

                                            <i class="fa-solid fa-star"></i>

                                            <?php
                                            echo htmlspecialchars(
                                                (string)$skor,
                                                ENT_QUOTES,
                                                'UTF-8'
                                            );
                                            ?>

                                        </span>

                                    </td>


                                    <!-- AKSI -->

                                    <td>

                                        <div class="queue-actions">


                                            <!-- TTD NORMAL -->

                                            <a

                                                class="queue-btn queue-btn-normal"

                                                href="ketua_ttd.php?ttd_id=<?php
                                                echo urlencode((string)$idPengajuan);
                                                ?>"

                                            >

                                                <i class="fa-solid fa-pen-nib"></i>

                                                TTD Normal

                                            </a>


                                            <!-- TOLAK -->

                                            <button

                                                type="button"

                                                class="queue-btn queue-btn-reject"

                                                onclick="tolakPengajuanKetua(<?php
                                                echo $idPengajuan;
                                                ?>)"

                                                title="Tolak pengajuan"

                                            >

                                                <i class="fa-solid fa-xmark"></i>

                                                Tolak

                                            </button>


                                            <!-- SETUJUI LANGSUNG -->

                                            <a

                                                class="queue-btn queue-btn-direct"

                                                href="ketua_ttd.php?skip_id=<?php
                                                echo urlencode((string)$idPengajuan);
                                                ?>"

                                            >

                                                <i class="fa-solid fa-check"></i>

                                                Setujui Langsung

                                            </a>


                                        </div>

                                    </td>


                                </tr>


                            <?php endforeach; ?>


                        </tbody>

                    </table>

                </div>


                <!-- FOOTER ANTREAN -->

                <div class="queue-footer">

                    <i class="fa-solid fa-circle-info"></i>

                    Periksa data SPPD sebelum melakukan tanda tangan,
                    penolakan, atau persetujuan langsung.

                </div>


            <?php endif; ?>


        </section>


    </div>

</main>


<!-- =====================================================
     JAVASCRIPT TOLAK PENGAJUAN
===================================================== -->

<script>

/* =====================================================
   TOLAK PENGAJUAN OLEH KETUA
===================================================== */

function tolakPengajuanKetua(id) {

    Swal.fire({

        title: 'Tolak Pengajuan SPPD?',

        text: 'Masukkan alasan penolakan pengajuan ini.',

        input: 'textarea',

        inputPlaceholder: 'Tuliskan alasan penolakan...',

        inputAttributes: {
            'aria-label': 'Alasan penolakan'
        },

        inputAutoTrim: true,

        showCancelButton: true,

        confirmButtonText: 'Ya, Tolak',

        cancelButtonText: 'Batal',

        confirmButtonColor: '#dc3545',

        cancelButtonColor: '#6c757d',

        reverseButtons: true,

        focusCancel: true,

        preConfirm: function(alasan) {

            alasan = (alasan || '').trim();

            if (!alasan) {

                Swal.showValidationMessage(
                    'Alasan penolakan wajib diisi!'
                );

                return false;

            }

            return alasan;

        }

    }).then(function(result) {

        if (!result.isConfirmed) {
            return;
        }


        /* LOADING */

        Swal.fire({

            title: 'Memproses...',

            text: 'Sedang menolak pengajuan SPPD.',

            allowOutsideClick: false,

            allowEscapeKey: false,

            didOpen: function() {

                Swal.showLoading();

            }

        });


        /* KIRIM DATA KE SERVER */

        fetch('ketua_tolak_pengajuan.php', {

            method: 'POST',

            headers: {

                'Content-Type':
                    'application/x-www-form-urlencoded; charset=UTF-8'

            },

            body:

                'id=' + encodeURIComponent(id) +

                '&alasan=' + encodeURIComponent(result.value)

        })


        .then(function(response) {

            return response.text().then(function(text) {

                if (!response.ok) {

                    throw new Error(
                        text || 'Terjadi kesalahan pada server.'
                    );

                }

                return text;

            });

        })


        .then(function(response) {

            const hasil = response.trim();


            if (hasil === 'OK') {


                Swal.fire({

                    icon: 'success',

                    title: 'Pengajuan Ditolak!',

                    text: 'Pengajuan berhasil ditolak.',

                    confirmButtonColor: '#10294d'

                }).then(function() {

                    window.location.href = 'ketua_dashboard.php';

                });


            } else {


                Swal.fire({

                    icon: 'error',

                    title: 'Gagal!',

                    text: hasil || 'Pengajuan gagal ditolak.',

                    confirmButtonColor: '#10294d'

                });

            }

        })


        .catch(function(error) {

            Swal.fire({

                icon: 'error',

                title: 'Koneksi Gagal!',

                text:
                    error.message ||
                    'Tidak dapat terhubung ke server.',

                confirmButtonColor: '#10294d'

            });

        });

    });

}

</script>


<?php include 'footer.php'; ?>
