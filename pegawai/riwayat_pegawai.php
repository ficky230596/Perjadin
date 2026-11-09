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

/*
|--------------------------------------------------------------------------
| Ambil riwayat pengajuan berdasarkan pegawai yang sedang login
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT
        id,
        pegawai_id,
        tujuan,
        tanggal_berangkat,
        tanggal_kembali,
        alasan,
        urgensi,
        status,
        prioritas_skor,
        waktu_pengajuan,
        spt_no,
        spd_no,
        pangkat,
        tingkat_biaya,
        alat_angkutan,
        pengikut,
        instansi_anggaran,
        akun_anggaran,
        alasan_penolakan
    FROM pengajuan
    WHERE pegawai_id = ?
    ORDER BY waktu_pengajuan DESC
");

$stmt->execute([$_SESSION['user_id']]);
$riwayat = $stmt->fetchAll(PDO::FETCH_ASSOC);

/*
|--------------------------------------------------------------------------
| Helper
|--------------------------------------------------------------------------
*/
function e($value)
{
    return htmlspecialchars((string) ($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatTanggal($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    return date('d-m-Y', strtotime($tanggal));
}

function formatTanggalWaktu($tanggal)
{
    if (empty($tanggal)) {
        return '-';
    }

    return date('d-m-Y H:i', strtotime($tanggal));
}

function formatStatus($status)
{
    return ucfirst(str_replace('_', ' ', (string) $status));
}

function getStatusClass($status)
{
    $status = strtolower(trim((string) $status));

    switch ($status) {
        case 'selesai':
            return 'status-selesai';

        case 'ditolak':
            return 'status-ditolak';

        case 'draft sppd':
        case 'draft_sppd':
            return 'status-draft';

        case 'diajukan':
            return 'status-diajukan';

        default:
            return 'status-pending';
    }
}

include 'header_pegawai.php';
include 'sidebar_pegawai.php';

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

<style>
    /* =========================================================
   RIWAYAT PEGAWAI
   ========================================================= */

    .riwayat-page {
        margin-left: 250px;
        width: calc(100% - 250px);
        min-height: calc(100vh - 89px);

        /* UBAH NILAI PADA BAGIAN INI (angka pertama untuk padding atas) */
        padding: 10px 28px 105px 28px;

        box-sizing: border-box;
        background: #f4f7fc;
        overflow-x: hidden;
    }

    /* =========================================================
   HEADER CONTENT
   ========================================================= */

    .riwayat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;

        margin-bottom: 18px;
    }

    .riwayat-heading {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .riwayat-heading-icon {
        width: 46px;
        height: 46px;

        display: flex;
        align-items: center;
        justify-content: center;

        flex-shrink: 0;

        border-radius: 12px;

        background: #10294d;
        color: #f2bd4b;

        font-size: 19px;

        box-shadow:
            0 5px 14px rgba(16, 41, 77, .15);
    }

    .riwayat-heading h1 {
        margin: 0;

        color: #10294d;

        font-size: 23px;
        font-weight: 800;

        line-height: 1.2;
    }

    .riwayat-heading p {
        margin: 4px 0 0;

        color: #718096;

        font-size: 12px;
    }

    /* =========================================================
   CARD
   ========================================================= */

    .riwayat-card {
        background: #fff;

        border: 1px solid #e5eaf2;
        border-radius: 15px;

        box-shadow:
            0 6px 20px rgba(16, 41, 77, .07);

        overflow: hidden;
    }

    /* =========================================================
   TOOLBAR
   ========================================================= */

    .riwayat-toolbar {
        padding: 14px 16px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        gap: 12px;

        border-bottom: 1px solid #e8edf4;
        background: #fbfcfe;
    }

    .toolbar-left {
        display: flex;
        align-items: center;

        gap: 9px;

        flex: 1;
    }

    .search-box {
        position: relative;

        flex: 1;
        max-width: 380px;
    }

    .search-box i {
        position: absolute;

        left: 13px;
        top: 50%;

        transform: translateY(-50%);

        color: #8a98aa;

        font-size: 13px;

        pointer-events: none;
    }

    .search-box input,
    .status-filter {
        width: 100%;

        height: 38px;

        border: 1px solid #d9e1ec;
        border-radius: 8px;

        background: #fff;

        color: #263449;

        font-family: 'Inter', sans-serif;
        font-size: 12px;

        outline: none;

        transition: .2s;
    }

    .search-box input {
        padding: 0 12px 0 36px;
        box-sizing: border-box;
    }

    .status-filter {
        width: 155px;
        padding: 0 10px;
    }

    .search-box input:focus,
    .status-filter:focus {
        border-color: #17477f;

        box-shadow:
            0 0 0 3px rgba(23, 71, 127, .08);
    }

    .total-data {
        display: flex;
        align-items: center;
        gap: 7px;

        color: #6b788a;

        font-size: 12px;
        white-space: nowrap;
    }

    .total-data strong {
        color: #10294d;
        font-size: 13px;
    }

    /* =========================================================
   TABLE WRAPPER
   ========================================================= */

    .table-wrapper {
        width: 100%;

        max-height: calc(100vh - 270px);

        overflow-x: auto;
        overflow-y: auto;

        -webkit-overflow-scrolling: touch;
    }

    /* scrollbar */

    .table-wrapper::-webkit-scrollbar {
        width: 7px;
        height: 7px;
    }

    .table-wrapper::-webkit-scrollbar-track {
        background: #f1f4f8;
    }

    .table-wrapper::-webkit-scrollbar-thumb {
        background: #bcc7d5;
        border-radius: 10px;
    }

    .table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #8f9db0;
    }

    /* =========================================================
   TABLE
   ========================================================= */

    .riwayat-table {
        width: 100%;

        min-width: 850px;

        border-collapse: separate;
        border-spacing: 0;

        font-size: 12px;
    }

    .riwayat-table th {
        position: sticky;
        top: 0;

        z-index: 5;

        padding: 11px 10px;

        background: #10294d;
        color: #fff;

        border-bottom: 2px solid #f2bd4b;

        font-size: 11px;
        font-weight: 700;

        text-align: center;

        white-space: nowrap;
    }

    .riwayat-table td {
        padding: 10px 9px;

        border-bottom: 1px solid #edf0f5;

        color: #3d4c60;

        text-align: center;

        vertical-align: middle;

        background: #fff;
    }

    .riwayat-table tbody tr {
        transition: .15s;
    }

    .riwayat-table tbody tr:hover td {
        background: #f8fafc;
    }

    .riwayat-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Kolom tujuan */

    .riwayat-table td:nth-child(2) {
        text-align: left;

        max-width: 220px;

        font-weight: 600;
        color: #263b55;
    }

    /* Nomor */

    .riwayat-table td:first-child {
        width: 45px;

        color: #7a8798;
        font-weight: 700;
    }

    /* =========================================================
   STATUS
   ========================================================= */

    .status-badge {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        min-width: 72px;

        padding: 5px 9px;

        border-radius: 20px;

        font-size: 10px;
        font-weight: 700;

        white-space: nowrap;
    }

    .status-badge::before {
        content: '';

        width: 6px;
        height: 6px;

        margin-right: 6px;

        border-radius: 50%;

        background: currentColor;
    }

    /* Diajukan */

    .status-diajukan {
        background: #fff4d8;
        color: #a46d00;
    }

    /* Draft */

    .status-draft {
        background: #eaf2ff;
        color: #245a9b;
    }

    /* Selesai */

    .status-selesai {
        background: #e6f7ee;
        color: #218451;
    }

    /* Ditolak */

    .status-ditolak {
        background: #fde9e9;
        color: #c43d3d;
    }

    /* Default */

    .status-pending {
        background: #f0f2f5;
        color: #697586;
    }

    /* =========================================================
   ACTION BUTTON
   ========================================================= */

    .action-buttons {
        display: flex;

        align-items: center;
        justify-content: center;

        gap: 5px;

        flex-wrap: wrap;
    }

    .btn-detail,
    .btn-cetak {
        display: inline-flex;

        align-items: center;
        justify-content: center;

        height: 30px;

        padding: 0 9px;

        border: none;
        border-radius: 7px;

        font-family: 'Inter', sans-serif;

        font-size: 10px;
        font-weight: 700;

        cursor: pointer;

        text-decoration: none;

        transition:
            transform .15s,
            box-shadow .15s,
            background .15s;
    }

    .btn-detail {
        background: #17477f;
        color: #fff;
    }

    .btn-detail:hover {
        background: #10294d;

        transform: translateY(-1px);

        box-shadow:
            0 4px 10px rgba(23, 71, 127, .2);
    }

    .btn-cetak {
        background: #e8f6ef;
        color: #218451;
    }

    .btn-cetak:hover {
        background: #d8f0e4;

        transform: translateY(-1px);
    }

    .btn-detail i,
    .btn-cetak i {
        margin-right: 5px;
    }

    /* =========================================================
   EMPTY STATE
   ========================================================= */

    .empty-riwayat-card {
        min-height: 300px;

        display: flex;

        align-items: center;
        justify-content: center;

        text-align: center;

        padding: 30px;
    }

    .empty-content {
        max-width: 400px;
    }

    .empty-icon {
        width: 65px;
        height: 65px;

        margin: 0 auto 14px;

        display: flex;
        align-items: center;
        justify-content: center;

        border-radius: 50%;

        background: #edf3fa;

        color: #17477f;

        font-size: 25px;
    }

    .empty-content h3 {
        margin: 0 0 6px;

        color: #263449;

        font-size: 16px;
    }

    .empty-content p {
        margin: 0;

        color: #8995a5;

        font-size: 12px;
    }

    /* =========================================================
   MODAL
   ========================================================= */

    .detail-modal {
        display: none;

        position: fixed;

        inset: 0;

        z-index: 5000;

        padding: 105px 20px 80px;

        box-sizing: border-box;

        background: rgba(7, 22, 40, .62);

        backdrop-filter: blur(3px);

        overflow-y: auto;
    }

    .detail-modal.show {
        display: flex;

        align-items: flex-start;
        justify-content: center;
    }

    .modal-content {
        width: min(720px, 100%);

        margin: auto;

        background: #fff;

        border-radius: 15px;

        box-shadow:
            0 20px 55px rgba(0, 0, 0, .25);

        overflow: hidden;

        animation: modalIn .18s ease;
    }

    @keyframes modalIn {
        from {
            opacity: 0;
            transform: translateY(-12px) scale(.98);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-header {
        display: flex;

        align-items: center;
        justify-content: space-between;

        padding: 15px 18px;

        background: linear-gradient(110deg,
                #10294d 0%,
                #17477f 100%);

        border-bottom: 3px solid #f2bd4b;
    }

    .modal-title {
        display: flex;

        align-items: center;

        gap: 9px;
    }

    .modal-title i {
        color: #f2bd4b;
        font-size: 16px;
    }

    .modal-title h2 {
        margin: 0;

        color: #fff;

        font-size: 15px;
        font-weight: 700;
    }

    .modal-close {
        width: 31px;
        height: 31px;

        display: flex;
        align-items: center;
        justify-content: center;

        border: none;
        border-radius: 7px;

        background: rgba(255, 255, 255, .12);

        color: #fff;

        font-size: 18px;

        cursor: pointer;

        transition: .2s;
    }

    .modal-close:hover {
        background: rgba(255, 255, 255, .22);
    }

    .modal-body {
        padding: 16px;

        max-height: 65vh;

        overflow-y: auto;
    }

    /* =========================================================
   DETAIL TABLE
   ========================================================= */

    .detail-table {
        width: 100%;

        border-collapse: collapse;

        font-size: 12px;
    }

    .detail-table th,
    .detail-table td {
        padding: 9px 11px;

        border: 1px solid #e4e9f0;

        text-align: left;

        vertical-align: top;
    }

    .detail-table th {
        width: 30%;

        background: #f4f7fb;

        color: #34445a;

        font-weight: 700;
    }

    .detail-table td {
        color: #4d5d71;

        background: #fff;

        word-break: break-word;
    }

    .detail-table tr:hover td {
        background: #fbfcfe;
    }

    /* =========================================================
   RESPONSIVE
   ========================================================= */

    @media (max-width: 1100px) {

        .riwayat-page {
            padding-left: 20px;
            padding-right: 20px;
        }

        .riwayat-heading h1 {
            font-size: 20px;
        }

        .table-wrapper {
            max-height: calc(100vh - 260px);
        }
    }

    @media (max-width: 768px) {

        .riwayat-page {
            margin-left: 0;

            width: 100%;

            min-height: 100vh;

            padding:
                174px 12px 95px 12px;
        }

        .riwayat-header {
            margin-bottom: 13px;
        }

        .riwayat-heading {
            gap: 9px;
        }

        .riwayat-heading-icon {
            width: 40px;
            height: 40px;

            border-radius: 10px;

            font-size: 16px;
        }

        .riwayat-heading h1 {
            font-size: 17px;
        }

        .riwayat-heading p {
            font-size: 10px;
        }

        .riwayat-toolbar {
            flex-direction: column;

            align-items: stretch;

            padding: 11px;
        }

        .toolbar-left {
            width: 100%;

            flex-direction: column;

            align-items: stretch;
        }

        .search-box {
            max-width: none;
        }

        .status-filter {
            width: 100%;
        }

        .total-data {
            justify-content: flex-end;
        }

        .table-wrapper {
            max-height: calc(100vh - 335px);
        }

        .detail-modal {
            padding:
                125px 10px 80px;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-body {
            padding: 10px;

            max-height: 65vh;
        }

        .detail-table th,
        .detail-table td {
            padding: 8px;
            font-size: 11px;
        }

        .detail-table th {
            width: 38%;
        }
    }

    @media (max-width: 480px) {

        .riwayat-page {
            padding-left: 9px;
            padding-right: 9px;
        }

        .riwayat-heading h1 {
            font-size: 15px;
        }

        .riwayat-heading p {
            display: none;
        }

        .riwayat-heading-icon {
            width: 36px;
            height: 36px;

            font-size: 14px;
        }

        .riwayat-table {
            min-width: 820px;
        }

        .btn-detail,
        .btn-cetak {
            height: 28px;

            padding: 0 7px;

            font-size: 9px;
        }

        .status-badge {
            font-size: 9px;
            min-width: 65px;
        }
    }
</style>

<!-- =======================================================
     MAIN CONTENT
     ======================================================= -->

<main class="riwayat-page">

    <!-- Header -->

    <div class="riwayat-header">

        <div class="riwayat-heading">

            <div class="riwayat-heading-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>

            <div>
                <h1>Riwayat Pengajuan Perjalanan Dinas</h1>
                <p>
                    Daftar pengajuan perjalanan dinas Anda
                </p>
            </div>

        </div>

    </div>


    <?php if (empty($riwayat)): ?>

        <!-- Empty State -->

        <section class="riwayat-card empty-riwayat-card">

            <div class="empty-content">

                <div class="empty-icon">
                    <i class="fa-regular fa-folder-open"></i>
                </div>

                <h3>Belum Ada Riwayat</h3>

                <p>
                    Belum ada pengajuan perjalanan dinas yang tercatat.
                </p>

            </div>

        </section>

    <?php else: ?>

        <!-- Table Card -->

        <section class="riwayat-card">

            <!-- Toolbar -->

            <div class="riwayat-toolbar">

                <div class="toolbar-left">

                    <div class="search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input type="text" id="searchInput" placeholder="Cari tujuan, tanggal, atau status..."
                            autocomplete="off">

                    </div>

                    <select id="statusFilter" class="status-filter">
                        <option value="">Semua Status</option>
                        <option value="diajukan">Diajukan</option>
                        <option value="draft sppd">Draft SPPD</option>
                        <option value="selesai">Selesai</option>
                        <option value="ditolak">Ditolak</option>
                    </select>

                </div>

                <div class="total-data">

                    <i class="fa-solid fa-list-check"></i>

                    <span>
                        Total:
                        <strong><?= count($riwayat); ?></strong>
                    </span>

                </div>

            </div>


            <!-- Table -->

            <div class="table-wrapper">

                <table class="riwayat-table" id="riwayatTable">

                    <thead>

                        <tr>
                            <th>No</th>
                            <th>Tujuan</th>
                            <th>Tanggal Berangkat</th>
                            <th>Tanggal Kembali</th>
                            <th>Status</th>
                            <th>Diajukan</th>
                            <th>Aksi</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php $no = 1; ?>

                        <?php foreach ($riwayat as $r): ?>

                            <?php

                            $berangkat = formatTanggal(
                                $r['tanggal_berangkat']
                            );

                            $kembali = formatTanggal(
                                $r['tanggal_kembali']
                            );

                            $diajukan = formatTanggalWaktu(
                                $r['waktu_pengajuan']
                            );

                            $statusClass = getStatusClass(
                                $r['status']
                            );

                            $statusText = formatStatus(
                                $r['status']
                            );

                            ?>

                            <tr>

                                <td>
                                    <?= $no++; ?>
                                </td>

                                <td>
                                    <?= e($r['tujuan']); ?>
                                </td>

                                <td>
                                    <?= e($berangkat); ?>
                                </td>

                                <td>
                                    <?= e($kembali); ?>
                                </td>

                                <td>

                                    <span class="status-badge <?= e($statusClass); ?>">
                                        <?= e($statusText); ?>
                                    </span>

                                </td>

                                <td>
                                    <?= e($diajukan); ?>
                                </td>

                                <td>

                                    <div class="action-buttons">

                                        <!-- DETAIL -->

                                        <button type="button" class="btn-detail" data-status="<?= e($statusText); ?>"
                                            data-alasan_penolakan="<?= e($r['alasan_penolakan']); ?>"
                                            data-tujuan="<?= e($r['tujuan']); ?>" data-berangkat="<?= e($berangkat); ?>"
                                            data-kembali="<?= e($kembali); ?>" data-alasan="<?= e($r['alasan']); ?>"
                                            data-urgensi="<?= e(ucfirst($r['urgensi'])); ?>"
                                            data-prioritas="<?= e($r['prioritas_skor']); ?>"
                                            data-diajukan="<?= e($diajukan); ?>" data-spt="<?= e($r['spt_no']); ?>"
                                            data-spd="<?= e($r['spd_no']); ?>" data-pangkat="<?= e($r['pangkat']); ?>"
                                            data-biaya="<?= e($r['tingkat_biaya']); ?>"
                                            data-angkutan="<?= e($r['alat_angkutan']); ?>"
                                            data-pengikut="<?= e($r['pengikut']); ?>"
                                            data-instansi="<?= e($r['instansi_anggaran']); ?>"
                                            data-akun="<?= e($r['akun_anggaran']); ?>">
                                            <i class="fa-solid fa-eye"></i>
                                            Detail
                                        </button>


                                        <?php if (
                                            strtolower(
                                                trim($r['status'])
                                            ) === 'selesai'
                                        ): ?>

                                            <!-- CETAK -->

                                            <a href="generate_sppd.php?id=<?= (int) $r['id']; ?>" target="_blank" class="btn-cetak">
                                                <i class="fa-solid fa-print"></i>
                                                Cetak
                                            </a>


                                            <!-- DOWNLOAD -->

                                            <a href="../surat/download_generate_sppd.php?id=<?= (int) $r['id']; ?>" target="_blank"
                                                class="btn-cetak">
                                                <i class="fa-solid fa-download"></i>
                                                Download
                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </section>

    <?php endif; ?>

</main>


<!-- =======================================================
     MODAL DETAIL
     ======================================================= -->

<div id="detailModal" class="detail-modal" aria-hidden="true">

    <div class="modal-content">

        <div class="modal-header">

            <div class="modal-title">

                <i class="fa-solid fa-file-lines"></i>

                <h2>
                    Detail Pengajuan Perjalanan Dinas
                </h2>

            </div>

            <button type="button" class="modal-close" id="modalClose" aria-label="Tutup">
                &times;
            </button>

        </div>


        <div class="modal-body">

            <table class="detail-table">

                <tbody id="modalBody"></tbody>

            </table>

        </div>

    </div>

</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        /* =====================================================
           ELEMENT
           ===================================================== */

        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const table = document.getElementById('riwayatTable');

        const modal = document.getElementById('detailModal');
        const modalBody = document.getElementById('modalBody');
        const modalClose = document.getElementById('modalClose');


        /* =====================================================
           FILTER TABLE
           ===================================================== */

        function filterTable() {

            if (!table) {
                return;
            }

            const searchValue = searchInput
                ? searchInput.value.toLowerCase().trim()
                : '';

            const statusValue = statusFilter
                ? statusFilter.value.toLowerCase().trim()
                : '';

            const rows = table.querySelectorAll('tbody tr');

            rows.forEach(function (row) {

                const rowText = row.innerText.toLowerCase();

                const statusCell = row.querySelector(
                    'td:nth-child(5)'
                );

                const rowStatus = statusCell
                    ? statusCell.innerText.toLowerCase().trim()
                    : '';

                const matchesSearch =
                    searchValue === '' ||
                    rowText.includes(searchValue);

                const matchesStatus =
                    statusValue === '' ||
                    rowStatus.includes(statusValue);

                row.style.display =
                    matchesSearch && matchesStatus
                        ? ''
                        : 'none';

            });
        }


        if (searchInput) {
            searchInput.addEventListener(
                'input',
                filterTable
            );
        }

        if (statusFilter) {
            statusFilter.addEventListener(
                'change',
                filterTable
            );
        }


        /* =====================================================
           ESCAPE HTML
           ===================================================== */

        function escapeHtml(value) {

            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');

        }


        /* =====================================================
           MODAL
           ===================================================== */

        function openModal(data) {

            if (!modal || !modalBody) {
                return;
            }

            modalBody.innerHTML = `

            <tr>
                <th>Status</th>
                <td>${escapeHtml(data.status || '-')}</td>
            </tr>

            <tr>
                <th>Alasan Penolakan</th>
                <td>${escapeHtml(data.alasan_penolakan || '-')}</td>
            </tr>

            <tr>
                <th>Tujuan</th>
                <td>${escapeHtml(data.tujuan || '-')}</td>
            </tr>

            <tr>
                <th>Tanggal Berangkat</th>
                <td>${escapeHtml(data.berangkat || '-')}</td>
            </tr>

            <tr>
                <th>Tanggal Kembali</th>
                <td>${escapeHtml(data.kembali || '-')}</td>
            </tr>

            <tr>
                <th>Alasan</th>
                <td>${escapeHtml(data.alasan || '-')}</td>
            </tr>

            <tr>
                <th>Urgensi</th>
                <td>${escapeHtml(data.urgensi || '-')}</td>
            </tr>

            <tr>
                <th>Prioritas Skor</th>
                <td>${escapeHtml(data.prioritas || '-')}</td>
            </tr>

            <tr>
                <th>Waktu Pengajuan</th>
                <td>${escapeHtml(data.diajukan || '-')}</td>
            </tr>

            <tr>
                <th>No. SPT</th>
                <td>${escapeHtml(data.spt || '-')}</td>
            </tr>

            <tr>
                <th>No. SPD</th>
                <td>${escapeHtml(data.spd || '-')}</td>
            </tr>

            <tr>
                <th>Pangkat</th>
                <td>${escapeHtml(data.pangkat || '-')}</td>
            </tr>

            <tr>
                <th>Tingkat Biaya</th>
                <td>${escapeHtml(data.biaya || '-')}</td>
            </tr>

            <tr>
                <th>Alat Angkutan</th>
                <td>${escapeHtml(data.angkutan || '-')}</td>
            </tr>

            <tr>
                <th>Pengikut</th>
                <td>${escapeHtml(data.pengikut || '-')}</td>
            </tr>

            <tr>
                <th>Instansi Anggaran</th>
                <td>${escapeHtml(data.instansi || '-')}</td>
            </tr>

            <tr>
                <th>Akun Anggaran</th>
                <td>${escapeHtml(data.akun || '-')}</td>
            </tr>

        `;

            modal.classList.add('show');
            modal.setAttribute('aria-hidden', 'false');

            document.body.style.overflow = 'hidden';
        }


        function closeModal() {

            if (!modal) {
                return;
            }

            modal.classList.remove('show');
            modal.setAttribute('aria-hidden', 'true');

            document.body.style.overflow = '';
        }


        /* =====================================================
           DETAIL BUTTON
           ===================================================== */

        document.querySelectorAll('.btn-detail')
            .forEach(function (button) {

                button.addEventListener(
                    'click',
                    function () {

                        openModal({

                            status:
                                this.dataset.status,

                            alasan_penolakan:
                                this.dataset.alasan_penolakan,

                            tujuan:
                                this.dataset.tujuan,

                            berangkat:
                                this.dataset.berangkat,

                            kembali:
                                this.dataset.kembali,

                            alasan:
                                this.dataset.alasan,

                            urgensi:
                                this.dataset.urgensi,

                            prioritas:
                                this.dataset.prioritas,

                            diajukan:
                                this.dataset.diajukan,

                            spt:
                                this.dataset.spt,

                            spd:
                                this.dataset.spd,

                            pangkat:
                                this.dataset.pangkat,

                            biaya:
                                this.dataset.biaya,

                            angkutan:
                                this.dataset.angkutan,

                            pengikut:
                                this.dataset.pengikut,

                            instansi:
                                this.dataset.instansi,

                            akun:
                                this.dataset.akun

                        });

                    }
                );

            });


        /* =====================================================
           CLOSE MODAL
           ===================================================== */

        if (modalClose) {

            modalClose.addEventListener(
                'click',
                closeModal
            );

        }


        if (modal) {

            modal.addEventListener(
                'click',
                function (event) {

                    if (event.target === modal) {
                        closeModal();
                    }

                }
            );

        }


        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape' &&
                    modal &&
                    modal.classList.contains('show')
                ) {
                    closeModal();
                }

            }
        );

    });
</script>

<?php include 'footer.php'; ?>