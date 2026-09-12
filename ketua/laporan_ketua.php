<?php
session_start();
if ($_SESSION['role'] !== 'ketua') {
    header('Location: ../index.php');
    exit;
}
include 'header_ketua.php';
include 'sidebar_ketua.php';
include '../config/db.php';

// Ambil filter tanggal jika ada
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Menggunakan tabel 'pengajuan' sesuai struktur database db_perjadin
$query = "SELECT * FROM pengajuan WHERE status = 'selesai'";
$params = [];

if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    // Filter berdasarkan tanggal berangkat (atau waktu_pengajuan, silakan disesuaikan)
    $query .= " AND tanggal_berangkat BETWEEN ? AND ?";
    $params = [$tanggal_mulai, $tanggal_selesai];
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$laporanList = $stmt->fetchAll();
?>

<h1>Laporan SPPD - Ketua</h1>
<p>Berikut adalah rekapitulasi data laporan SPPD yang telah disetujui.</p>

<!-- Form Filter Berdasarkan Tanggal -->
<form method="GET" action=""
    style="margin: 20px auto; width: 90%; background: #fff; padding: 15px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.07);">
    <label for="tanggal_mulai">Dari Tanggal:</label>
    <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="<?php echo htmlspecialchars($tanggal_mulai); ?>"
        style="padding: 6px; margin-right: 10px;">

    <label for="tanggal_selesai">Sampai Tanggal:</label>
    <input type="date" id="tanggal_selesai" name="tanggal_selesai"
        value="<?php echo htmlspecialchars($tanggal_selesai); ?>" style="padding: 6px; margin-right: 10px;">

    <button type="submit"
        style="padding: 7px 15px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Filter</button>
    <a href="laporan_ketua.php" style="margin-left: 10px; color: #6c757d;">Reset</a>
</form>

<?php if (empty($laporanList)): ?>
    <p style="text-align: center;">Tidak ada data laporan untuk periode ini.</p>
<?php else: ?>
    <div style="width: 90%; margin: 0 auto; text-align: right; margin-bottom: 10px;">
        <button onclick="window.print()"
            style="padding: 8px 15px; background: #28a745; color: #fff; border: none; border-radius: 4px; cursor: pointer;">Cetak
            Laporan</button>
    </div>

    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Tujuan</th>
            <th>Tanggal Berangkat</th>
            <th>Tanggal Kembali</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($laporanList as $row): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['tujuan']); ?></td>
                <td><?php echo htmlspecialchars($row['tanggal_berangkat']); ?></td>
                <td><?php echo htmlspecialchars($row['tanggal_kembali']); ?></td>
                <td><span style="color: green; font-weight: bold;"><?php echo ucfirst($row['status']); ?></span></td>
                <td>
                    <a href="detail_sppd.php?id=<?php echo $row['id']; ?>">Detail</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<style>
    table {
        border-collapse: collapse;
        width: 90%;
        margin: 20px auto;
        background: #fff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.07);
        border-radius: 8px;
        overflow: hidden;
    }

    th,
    td {
        padding: 12px 18px;
        text-align: left;
    }

    th {
        background: #007bff;
        color: #fff;
        font-weight: 600;
    }

    tr:nth-child(in-range) {
        background: #f7f7f7;
    }

    tr:nth-child(even) {
        background: #f7f7f7;
    }

    tr:hover {
        background: #e9f5ff;
        transition: background 0.2s;
    }

    a {
        text-decoration: none;
        color: #007bff;
        font-weight: 500;
        transition: color 0.2s;
    }

    a:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    @media print {

        form,
        button,
        .sidebar,
        header {
            display: none !important;
        }

        table {
            width: 100% !important;
            box-shadow: none !important;
        }
    }
</style>

<?php include 'footer.php'; ?>