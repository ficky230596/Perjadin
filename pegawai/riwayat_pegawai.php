<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pegawai') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';
include 'header_pegawai.php';
include 'sidebar_pegawai.php';

// Ambil riwayat pengajuan berdasarkan pegawai login
$stmt = $pdo->prepare("
    SELECT id, pegawai_id, tujuan, tanggal_berangkat, tanggal_kembali,
           alasan, urgensi, status, prioritas_skor, waktu_pengajuan,
           spt_no, spd_no, pangkat, tingkat_biaya, alat_angkutan, pengikut,
           instansi_anggaran, akun_anggaran, alasan_penolakan
    FROM pengajuan
    WHERE pegawai_id = ?
    ORDER BY waktu_pengajuan DESC
");
$stmt->execute([$_SESSION['user_id']]);
$riwayat = $stmt->fetchAll();

?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="main-content">
    <h1>Riwayat Pengajuan Perjalanan Dinas</h1>

    <?php if (empty($riwayat)): ?>
        <p class="empty-riwayat">Belum ada riwayat pengajuan perjalanan dinas.</p>
    <?php else: ?>

        <!-- Pencarian & Filter -->
        <div class="filter-container">
            <input type="text" id="searchInput" placeholder="Cari tujuan atau tanggal...">
            <select id="statusFilter">
                <option value="">Semua Status</option>
                <option value="Diajukan">Diajukan</option>
                <option value="Draft sppd">Draft sppd</option>
                <option value="selesai">Selesai</option>
                <option value="Ditolak">Ditolak</option>
            </select>
        </div>

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
                <?php
                $no = 1;
                foreach ($riwayat as $r):
                    $berangkat = date('d-m-Y', strtotime($r['tanggal_berangkat']));
                    $kembali = date('d-m-Y', strtotime($r['tanggal_kembali']));
                    $diajukan = date('d-m-Y H:i', strtotime($r['waktu_pengajuan']));
                    $statusClass = ($r['status'] === 'selesai') ? 'status-selesai' : 'status-pending';
                ?>
                    <tr>
                        <td><?php echo $no++; ?></td>
                        <td><?php echo htmlspecialchars($r['tujuan']); ?></td>
                        <td><?php echo $berangkat; ?></td>
                        <td><?php echo $kembali; ?></td>
                        <td>
                            <span class="status-badge <?php echo $statusClass; ?>">
                                <?php echo ucfirst(str_replace('_', ' ', $r['status'])); ?>
                            </span>
                        </td>
                        <td><?php echo $diajukan; ?></td>
                        <td>
                            <!-- Tombol Detail -->
                            <button class="btn-detail"
                                data-status="<?php echo ucfirst($r['status']); ?>"
                                data-alasan_penolakan="<?php echo htmlspecialchars($r['alasan_penolakan'] ?? ''); ?>"
                                data-tujuan="<?php echo htmlspecialchars($r['tujuan']); ?>"
                                data-berangkat="<?php echo $berangkat; ?>"
                                data-kembali="<?php echo $kembali; ?>"
                                data-alasan="<?php echo htmlspecialchars($r['alasan']); ?>"
                                data-urgensi="<?php echo ucfirst($r['urgensi']); ?>"
                                data-prioritas="<?php echo $r['prioritas_skor']; ?>"
                                data-diajukan="<?php echo $diajukan; ?>"
                                data-spt="<?php echo htmlspecialchars($r['spt_no']); ?>"
                                data-spd="<?php echo htmlspecialchars($r['spd_no']); ?>"
                                data-pangkat="<?php echo htmlspecialchars($r['pangkat']); ?>"
                                data-biaya="<?php echo htmlspecialchars($r['tingkat_biaya']); ?>"
                                data-angkutan="<?php echo htmlspecialchars($r['alat_angkutan']); ?>"
                                data-pengikut="<?php echo htmlspecialchars($r['pengikut']); ?>"
                                data-instansi="<?php echo htmlspecialchars($r['instansi_anggaran']); ?>"
                                data-akun="<?php echo htmlspecialchars($r['akun_anggaran']); ?>">
                                <i class="fa fa-eye"></i> Detail
                            </button>


                            <?php if ($r['status'] === 'selesai'): ?>
                                <a href="generate_sppd.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-cetak">
                                    <i class="fa fa-print"></i> Cetak
                                </a>
                                <a href="../surat/download_generate_sppd.php?id=<?php echo $r['id']; ?>" target="_blank" class="btn-cetak">
                                    <i class="fa fa-download"></i> Download
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<!-- Modal Popup -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Detail Pengajuan Perjalanan Dinas</h2>
        <table class="detail-table">
            <tbody id="modalBody"></tbody>
        </table>
    </div>
</div>

<style>
    /* Tabel */
    .riwayat-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .riwayat-table th,
    .riwayat-table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: center;
    }

    .riwayat-table th {
        background: #3498db;
        color: white;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 0.9em;
    }

    .status-selesai {
        background: #2ecc71;
        color: white;
    }

    .status-pending {
        background: #f1c40f;
        color: black;
    }

    .btn-detail,
    .btn-cetak {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 0.85em;
        border: none;
        cursor: pointer;
    }

    .btn-detail {
        background: #2980b9;
        color: white;
    }

    .btn-cetak {
        background: #27ae60;
        color: white;
        text-decoration: none;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        z-index: 999;
        padding-top: 80px;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
    }

    .modal-content {
        background-color: #fff;
        margin: auto;
        padding: 20px;
        border-radius: 10px;
        width: 60%;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        cursor: pointer;
    }

    .close:hover {
        color: black;
    }

    .filter-container {
        margin: 15px 0;
        display: flex;
        gap: 10px;
    }

    .filter-container input,
    .filter-container select {
        padding: 6px;
        border-radius: 4px;
        border: 1px solid #ccc;
    }

    /* Tabel Detail */
    .detail-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
    }

    .detail-table th,
    .detail-table td {
        border: 1px solid #ddd;
        padding: 12px;
        text-align: left;
    }

    .detail-table th {
        background-color: #f4f4f4;
        width: 30%;
        font-weight: bold;
        color: #333;
    }

    .detail-table td {
        background-color: #fff;
    }

    .detail-table tr:nth-child(even) td {
        background-color: #f9f9f9;
    }

    .detail-table tr:hover td {
        background-color: #f1f1f1;
    }

    /* Empty State */
    .empty-riwayat {
        color: #888;
        font-style: italic;
        text-align: center;
        margin-top: 20px;
    }
</style>

<script>
    // Pencarian
    document.getElementById('searchInput').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#riwayatTable tbody tr');
        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });

    // Filter status
    document.getElementById('statusFilter').addEventListener('change', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#riwayatTable tbody tr');
        rows.forEach(row => {
            let statusText = row.querySelector('td:nth-child(5)').innerText.toLowerCase();
            row.style.display = (filter === '' || statusText.includes(filter)) ? '' : 'none';
        });
    });

    // Modal detail
    let modal = document.getElementById("detailModal");
    let span = document.getElementsByClassName("close")[0];

    document.querySelectorAll('.btn-detail').forEach(btn => {
        btn.addEventListener('click', function() {
            let html = `
            <tr><th>Status</th><td>${this.dataset.status}</td></tr>
            <tr><th>Alasan Penolakan</th><td>${this.dataset.alasan_penolakan || '-'}</td></tr>
            <tr><th>Tujuan</th><td>${this.dataset.tujuan}</td></tr>
            <tr><th>Tanggal Berangkat</th><td>${this.dataset.berangkat}</td></tr>
            <tr><th>Tanggal Kembali</th><td>${this.dataset.kembali}</td></tr>
            <tr><th>Alasan</th><td>${this.dataset.alasan}</td></tr>
            <tr><th>Urgensi</th><td>${this.dataset.urgensi}</td></tr>
            <tr><th>Prioritas Skor</th><td>${this.dataset.prioritas}</td></tr>
            <tr><th>Waktu Pengajuan</th><td>${this.dataset.diajukan}</td></tr>
            <tr><th>No. SPT</th><td>${this.dataset.spt || '-'}</td></tr>
            <tr><th>No. SPD</th><td>${this.dataset.spd || '-'}</td></tr>
            <tr><th>Pangkat</th><td>${this.dataset.pangkat || '-'}</td></tr>
            <tr><th>Tingkat Biaya</th><td>${this.dataset.biaya || '-'}</td></tr>
            <tr><th>Alat Angkutan</th><td>${this.dataset.angkutan || '-'}</td></tr>
            <tr><th>Pengikut</th><td>${this.dataset.pengikut || '-'}</td></tr>
            <tr><th>Instansi Anggaran</th><td>${this.dataset.instansi || '-'}</td></tr>
            <tr><th>Akun Anggaran</th><td>${this.dataset.akun || '-'}</td></tr>
        `;
            document.getElementById('modalBody').innerHTML = html;
            modal.style.display = "block";
        });
    });

    span.onclick = function() {
        modal.style.display = "none";
    }
    window.onclick = function(event) {
        if (event.target == modal) modal.style.display = "none";
    }
</script>

<?php include 'footer.php'; ?>