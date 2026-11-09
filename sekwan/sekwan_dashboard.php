
<?php

session_start();

/* =====================================================
   VALIDASI SESSION
===================================================== */

if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'sekwan'
) {
    header('Location: ../index.php');
    exit;
}

require_once '../config/db.php';


/* =====================================================
   MENGAMBIL HEADER DAN SIDEBAR
===================================================== */

include 'header_sekwan.php';
include 'sidebar_sekwan.php';


/* =====================================================
   MENGAMBIL ANTRIAN
===================================================== */

$queue = [];

try {

    $queue = getScheduledQueue(
        $pdo,
        'draft_sppd'
    );

    if (!is_array($queue)) {
        $queue = [];
    }

} catch (Throwable $e) {

    error_log('Kesalahan mengambil antrian Sekwan: ' . $e->getMessage());

    $queue = [];

}


/* =====================================================
   MENGAMBIL STATISTIK
===================================================== */

$total_antrian = 0;
$total_diparaf = 0;
$total_pengajuan = 0;

try {

    /* Total draft siap paraf */

    $stmt_antrian = $pdo->prepare("
        SELECT COUNT(*)
        FROM pengajuan
        WHERE status = 'draft_sppd'
    ");

    $stmt_antrian->execute();

    $total_antrian = (int) $stmt_antrian->fetchColumn();


    /* Total sudah diparaf / diproses */

    $stmt_selesai = $pdo->prepare("
        SELECT COUNT(*)
        FROM pengajuan
        WHERE status IN (
            'paraf_sekwan',
            'ttd_ketua',
            'dicap',
            'selesai'
        )
    ");

    $stmt_selesai->execute();

    $total_diparaf = (int) $stmt_selesai->fetchColumn();


    /* Total seluruh pengajuan */

    $stmt_total = $pdo->prepare("
        SELECT COUNT(*)
        FROM pengajuan
    ");

    $stmt_total->execute();

    $total_pengajuan = (int) $stmt_total->fetchColumn();

} catch (PDOException $e) {

    error_log('Kesalahan statistik Sekwan: ' . $e->getMessage());

    $total_antrian = count($queue);
    $total_diparaf = 0;
    $total_pengajuan = count($queue);

}

?>


<!-- =====================================================
     SWEETALERT & CHART.JS
===================================================== -->

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


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
    background: #f4f7fb;
}


/* =====================================================
   KONTEN UTAMA
===================================================== */

.main-content-sekwan {
    margin-left: 250px;
    padding: 92px 25px 85px;
    width: auto;
    min-height: 100vh;
    font-family: "Inter", "Segoe UI", Arial, sans-serif;
    transition: margin-left .25s ease;
}


/* =====================================================
   HEADER DASHBOARD
===================================================== */

.dashboard-header {
    background: #fff;
    padding: 22px 24px;
    border-radius: 14px;
    border: 1px solid #e8edf4;
    box-shadow: 0 4px 16px rgba(16, 41, 77, .07);
    margin-bottom: 22px;
}

.dashboard-header h1 {
    margin: 0;
    color: #10294d;
    font-size: 24px;
    font-weight: 750;
    line-height: 1.3;
}

.dashboard-header p {
    margin: 7px 0 0;
    color: #6b7280;
    font-size: 14px;
    line-height: 1.6;
}


/* =====================================================
   STATISTIK + CHART
===================================================== */

.stats-grid {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
}


/* =====================================================
   STAT BOX
===================================================== */

.stat-box {
    background: #fff;
    min-height: 110px;
    padding: 20px 22px;
    border-radius: 14px;
    border: 1px solid #e8edf4;
    box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-left: 5px solid #17477f;
    transition: transform .2s ease, box-shadow .2s ease;
}

.stat-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 7px 20px rgba(16, 41, 77, .10);
}

.stat-box.green {
    border-left-color: #28a745;
}

.stat-box.orange {
    border-left-color: #f2bd4b;
}

.stat-info h3 {
    margin: 0;
    font-size: 14px;
    color: #64748b;
    font-weight: 600;
}

.stat-info p {
    margin: 6px 0 0;
    font-size: 24px;
    font-weight: 750;
    color: #10294d;
}

.stat-box > i {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    background: #f4f7fb;
}


/* =====================================================
   CHART
===================================================== */

.chart-container {
    background: #fff;
    min-height: 235px;
    padding: 20px;
    border-radius: 14px;
    border: 1px solid #e8edf4;
    box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.chart-container h3 {
    margin: 0 0 10px;
    width: 100%;
    font-size: 15px;
    color: #10294d;
    font-weight: 700;
}


/* =====================================================
   JUDUL ANTRIAN
===================================================== */

.queue-title {
    margin: 5px 0 14px;
    color: #10294d;
    font-size: 19px;
    font-weight: 750;
}


/* =====================================================
   CONTAINER TABEL
===================================================== */

.sekwan-table-container {
    background: #fff;
    border-radius: 14px;
    border: 1px solid #e8edf4;
    box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
    overflow-x: auto;
    overflow-y: hidden;
    margin-top: 15px;
}


/* =====================================================
   TABEL
===================================================== */

.sekwan-table {
    width: 100%;
    min-width: 750px;
    border-collapse: collapse;
}

.sekwan-table th,
.sekwan-table td {
    padding: 13px 17px;
    text-align: left;
    font-size: 13px;
    border-bottom: 1px solid #edf1f5;
}

.sekwan-table th {
    background: #10294d;
    color: #fff;
    font-weight: 650;
    white-space: nowrap;
}

.sekwan-table tbody tr {
    transition: background .2s ease;
}

.sekwan-table tbody tr:hover {
    background: #f8fafc;
}

.sekwan-table td {
    color: #475569;
}

.sekwan-table td strong {
    color: #10294d;
}


/* =====================================================
   BADGE SKOR
===================================================== */

.badge-score,
.prioritas-tinggi {
    display: inline-block;
    padding: 5px 11px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 650;
}

.badge-score {
    background: #e2f0d9;
    color: #385723;
}

.prioritas-tinggi {
    background: #f8d7da;
    color: #721c24;
}


/* =====================================================
   BUTTON AKSI
===================================================== */

.btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 7px 12px;
    border-radius: 7px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    border: none;
    margin: 2px;
    cursor: pointer;
    transition: transform .2s ease, opacity .2s ease;
    white-space: nowrap;
}

.btn-action:hover {
    opacity: .9;
    transform: translateY(-1px);
}

.btn-detail {
    background: #17a2b8;
    color: #fff;
}

.btn-paraf {
    background: #28a745;
    color: #fff;
}

.btn-tolak {
    background: #dc3545;
    color: #fff;
}


/* =====================================================
   EMPTY STATE
===================================================== */

.empty-antrian {
    background: #fff;
    padding: 40px 25px;
    text-align: center;
    border-radius: 14px;
    border: 1px solid #e8edf4;
    box-shadow: 0 4px 14px rgba(16, 41, 77, .06);
    color: #64748b;
}

.empty-antrian i {
    display: block;
    margin-bottom: 12px;
    font-size: 35px;
    color: #f2bd4b;
}

.empty-antrian p {
    margin: 0;
    font-size: 14px;
}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 1000px) {

    .main-content-sekwan {
        margin-left: 250px;
        padding: 88px 18px 82px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .main-content-sekwan {
        margin-left: 0;
        width: 100%;
        padding: 80px 12px 80px;
    }

    .dashboard-header {
        padding: 18px;
        border-radius: 12px;
    }

    .dashboard-header h1 {
        font-size: 18px;
    }

    .dashboard-header p {
        font-size: 12px;
    }

    .stats-grid {
        grid-template-columns: 1fr;
        gap: 14px;
    }

    .stat-box {
        min-height: 95px;
        padding: 17px;
    }

    .stat-info h3 {
        font-size: 12px;
    }

    .stat-info p {
        font-size: 20px;
    }

    .stat-box > i {
        width: 42px;
        height: 42px;
        font-size: 18px;
    }

    .chart-container {
        min-height: 260px;
        padding: 18px;
    }

    .queue-title {
        font-size: 17px;
        margin-top: 20px;
    }

    .sekwan-table-container {
        border-radius: 10px;
        -webkit-overflow-scrolling: touch;
    }

    .btn-action {
        padding: 7px 10px;
    }

}


/* =====================================================
   MOBILE KECIL
===================================================== */

@media (max-width: 480px) {

    .main-content-sekwan {
        padding: 76px 10px 78px;
    }

    .dashboard-header {
        padding: 16px;
    }

    .dashboard-header h1 {
        font-size: 16px;
    }

    .dashboard-header p {
        font-size: 11px;
        line-height: 1.5;
    }

    .stat-box {
        padding: 15px;
    }

    .stat-info h3 {
        max-width: 210px;
        line-height: 1.4;
    }

    .stat-info p {
        font-size: 19px;
    }

    .chart-container {
        min-height: 250px;
    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    .main-content-sekwan {
        margin-left: 0;
        padding: 10px;
    }

    .dashboard-header,
    .stat-box,
    .chart-container,
    .sekwan-table-container {
        box-shadow: none;
        border: 1px solid #ddd;
    }

    .btn-action {
        display: none;
    }

}

</style>


<!-- =====================================================
     KONTEN UTAMA
===================================================== -->

<main class="main-content-sekwan">


    <!-- HEADER DASHBOARD -->

    <section class="dashboard-header">

        <h1>
            Dashboard Sekwan:
            <?php
            echo htmlspecialchars(
                $_SESSION['nama'] ?? 'Sekwan',
                ENT_QUOTES,
                'UTF-8'
            );
            ?>
        </h1>

        <p>
            Kelola paraf dokumen perjalanan dinas
            dan pantau ringkasan antrian secara real-time.
        </p>

    </section>


    <!-- STATISTIK + CHART -->

    <section class="stats-grid">

        <div class="stat-card-group">

            <!-- ANTRIAN -->

            <div class="stat-box orange">

                <div class="stat-info">

                    <h3>
                        Antrian Draft Siap Paraf
                    </h3>

                    <p>
                        <?php echo $total_antrian; ?>
                        Dokumen
                    </p>

                </div>

                <i
                    class="fas fa-clock"
                    style="color:#f2bd4b;"
                ></i>

            </div>


            <!-- SUDAH DIPARAF -->

            <div class="stat-box green">

                <div class="stat-info">

                    <h3>
                        Dokumen Telah Diparaf / Diproses
                    </h3>

                    <p>
                        <?php echo $total_diparaf; ?>
                        Dokumen
                    </p>

                </div>

                <i
                    class="fas fa-check-circle"
                    style="color:#28a745;"
                ></i>

            </div>

        </div>


        <!-- CHART -->

        <div class="chart-container">

            <h3>
                Statistik Paraf Sekwan
            </h3>

            <div
                style="
                    width:180px;
                    height:180px;
                    position:relative;
                "
            >
                <canvas id="sekwanChart"></canvas>
            </div>

        </div>

    </section>


    <!-- ANTRIAN -->

    <h2 class="queue-title">

        <i
            class="fa-solid fa-list-check"
            style="color:#f2bd4b;"
        ></i>

        Antrian Paraf (Prioritas)

    </h2>


    <?php if (empty($queue)): ?>

        <!-- EMPTY STATE -->

        <div class="empty-antrian">

            <i class="fa-regular fa-folder-open"></i>

            <p>
                Tidak ada draft siap paraf saat ini.
            </p>

        </div>

    <?php else: ?>

        <!-- TABEL -->

        <div class="sekwan-table-container">

            <table class="sekwan-table">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Tujuan</th>

                        <th>Skor Prioritas</th>

                        <th style="text-align:center;">
                            Aksi
                        </th>

                    </tr>

                </thead>


                <tbody>

                    <?php foreach ($queue as $q): ?>

                        <?php

                        $isTinggi = (
                            isset($q['prioritas_skor']) &&
                            (float)$q['prioritas_skor'] > 5
                        );

                        $scoreClass = $isTinggi
                            ? 'prioritas-tinggi'
                            : 'badge-score';

                        $idPengajuan = (int)($q['id'] ?? 0);

                        ?>

                        <tr>

                            <!-- ID -->

                            <td>

                                <strong>
                                    #<?php
                                    echo htmlspecialchars(
                                        (string)($q['id'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </strong>

                            </td>


                            <!-- TUJUAN -->

                            <td>

                                <strong>
                                    <?php
                                    echo htmlspecialchars(
                                        (string)($q['tujuan'] ?? ''),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>
                                </strong>

                            </td>


                            <!-- SKOR -->

                            <td>

                                <span class="<?php echo $scoreClass; ?>">

                                    <?php
                                    echo htmlspecialchars(
                                        (string)($q['prioritas_skor'] ?? 0),
                                        ENT_QUOTES,
                                        'UTF-8'
                                    );
                                    ?>

                                </span>

                            </td>


                            <!-- AKSI -->

                            <td
                                style="
                                    text-align:center;
                                    white-space:nowrap;
                                "
                            >

                                <!-- DETAIL -->

                                <a
                                    href="detail_sppd.php?id=<?php echo urlencode((string)$idPengajuan); ?>"
                                    class="btn-action btn-detail"
                                    title="Lihat detail"
                                >

                                    <i class="fa-solid fa-eye"></i>

                                    Detail

                                </a>


                                <!-- TOLAK -->

                                <button
                                    type="button"
                                    onclick="tolakPengajuanSekwan(<?php echo $idPengajuan; ?>)"
                                    class="btn-action btn-tolak"
                                    title="Tolak pengajuan"
                                >

                                    <i class="fa-solid fa-xmark"></i>

                                    Tolak

                                </button>


                                <!-- PARAF -->

                                <button
                                    type="button"
                                    onclick="konfirmasiParaf(<?php echo $idPengajuan; ?>)"
                                    class="btn-action btn-paraf"
                                    title="Paraf sekarang"
                                >

                                    <i class="fa-solid fa-file-signature"></i>

                                    Paraf Sekarang

                                </button>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                </tbody>

            </table>

        </div>

    <?php endif; ?>

</main>


<script>

/* =====================================================
   CHART SEKWA N
===================================================== */

document.addEventListener('DOMContentLoaded', function () {

    const chartElement = document.getElementById('sekwanChart');

    if (chartElement && typeof Chart !== 'undefined') {

        const ctx = chartElement.getContext('2d');

        new Chart(ctx, {

            type: 'doughnut',

            data: {

                labels: [
                    'Belum Diparaf (Antrian)',
                    'Sudah Diparaf / Proses'
                ],

                datasets: [{

                    data: [
                        <?php echo (int)$total_antrian; ?>,
                        <?php echo (int)$total_diparaf; ?>
                    ],

                    backgroundColor: [
                        '#f2bd4b',
                        '#28a745'
                    ],

                    borderWidth: 1

                }]

            },

            options: {

                responsive: true,

                maintainAspectRatio: false,

                plugins: {

                    legend: {

                        position: 'bottom',

                        labels: {

                            boxWidth: 12,

                            font: {
                                size: 11
                            }

                        }

                    }

                }

            }

        });

    }

});


/* =====================================================
   AUTO REFRESH 30 DETIK
===================================================== */

setTimeout(function () {

    window.location.reload();

}, 30000);


/* =====================================================
   KONFIRMASI PARAF
===================================================== */

function konfirmasiParaf(id) {

    Swal.fire({

        title: 'Konfirmasi Paraf SPPD',

        text:
            'Apakah Anda yakin ingin memparaf pengajuan ID #' +
            id +
            '? Notifikasi WhatsApp akan dikirim ke Ketua.',

        icon: 'question',

        showCancelButton: true,

        confirmButtonColor: '#28a745',

        cancelButtonColor: '#d33',

        confirmButtonText: 'Ya, Paraf!',

        cancelButtonText: 'Batal',

        reverseButtons: true

    }).then(function (result) {

        if (result.isConfirmed) {

            window.location.href =
                'sekwan_paraf.php?proses_id=' +
                encodeURIComponent(id);

        }

    });

}


/* =====================================================
   TOLAK PENGAJUAN OLEH SEKWAN
===================================================== */

function tolakPengajuanSekwan(id) {

    Swal.fire({

        title: 'Tolak Pengajuan?',

        text: 'Masukkan alasan penolakan pengajuan SPPD.',

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

        preConfirm: function (alasan) {

            alasan = (alasan || '').trim();

            if (!alasan) {

                Swal.showValidationMessage(
                    'Alasan penolakan wajib diisi!'
                );

                return false;

            }

            return alasan;

        }

    }).then(function (result) {

        if (!result.isConfirmed) {
            return;
        }

        Swal.fire({

            title: 'Memproses...',

            text: 'Sedang menolak pengajuan.',

            allowOutsideClick: false,

            allowEscapeKey: false,

            didOpen: function () {
                Swal.showLoading();
            }

        });

        fetch('sekwan_tolak_pengajuan.php', {

            method: 'POST',

            headers: {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            },

            body:
                'id=' + encodeURIComponent(id) +
                '&alasan=' + encodeURIComponent(result.value)

        })

        .then(function (response) {

            return response.text().then(function (text) {

                if (!response.ok) {
                    throw new Error(text || 'Terjadi kesalahan pada server.');
                }

                return text;

            });

        })

        .then(function (response) {

            const hasil = response.trim();

            if (hasil === 'OK') {

                Swal.fire({

                    icon: 'success',

                    title: 'Pengajuan Ditolak!',

                    text: 'Pengajuan berhasil ditolak.',

                    confirmButtonColor: '#10294d'

                }).then(function () {

                    window.location.href = 'sekwan_dashboard.php';

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

        .catch(function (error) {

            Swal.fire({

                icon: 'error',

                title: 'Koneksi Gagal!',

                text: error.message || 'Tidak dapat terhubung ke server.',

                confirmButtonColor: '#10294d'

            });

        });

    });

}

</script>


<?php include 'footer.php'; ?>
