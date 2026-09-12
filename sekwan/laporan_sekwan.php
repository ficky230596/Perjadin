<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekwan') {
    header('Location: ../index.php');
    exit;
}

include '../config/db.php';
include 'header_sekwan.php';
include 'sidebar_sekwan.php';

// Ambil filter tanggal jika ada
$tanggal_mulai = $_GET['tanggal_mulai'] ?? '';
$tanggal_selesai = $_GET['tanggal_selesai'] ?? '';

// Query data laporan untuk Sekwan (mencakup dokumen yang sudah diparaf sekwan atau tahap selanjutnya)
$query = "SELECT p.*, COALESCE(u.nama, p.nama, 'Tidak Diketahui') AS nama_pegawai 
          FROM pengajuan p 
          LEFT JOIN users u ON p.pegawai_id = u.id 
          WHERE p.status IN ('paraf_sekwan', 'ttd_ketua', 'dicap', 'selesai')";
$params = [];

if (!empty($tanggal_mulai) && !empty($tanggal_selesai)) {
    $query .= " AND p.tanggal_berangkat BETWEEN ? AND ?";
    $params = [$tanggal_mulai, $tanggal_selesai];
}

$query .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$laporanList = $stmt->fetchAll();
?>

<div class="main-content-sekwan"
    style="width: 92%; margin: 20px auto; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div class="page-header"
        style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div>
            <h1>Laporan Pengajuan SPPD - Sekwan</h1>
            <p style="margin: 5px 0 0 0; color: #666; font-size: 14px;">Rekapitulasi data perjalanan dinas yang telah
                melalui proses paraf.</p>
        </div>
        <div>
            <button onclick="window.print()"
                style="padding: 8px 16px; background: #28a745; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-weight: 500;">Cetak
                Laporan</button>
        </div>
    </div>

    <!-- Form Filter Berdasarkan Tanggal -->
    <form method="GET" action=""
        style="margin-bottom: 20px; background: #fff; padding: 15px 20px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; align-items: center; flex-wrap: wrap; gap: 10px;">
        <div>
            <label for="tanggal_mulai" style="font-size: 14px; color: #555; margin-right: 5px;">Dari Tanggal:</label>
            <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                value="<?php echo htmlspecialchars($tanggal_mulai); ?>"
                style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div>
            <label for="tanggal_selesai"
                style="font-size: 14px; color: #555; margin-right: 5px; margin-left: 10px;">Sampai Tanggal:</label>
            <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                value="<?php echo htmlspecialchars($tanggal_selesai); ?>"
                style="padding: 6px 10px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <div style="margin-left: 10px;">
            <button type="submit"
                style="padding: 7px 15px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">Filter</button>
            <a href="laporan_sekwan.php"
                style="margin-left: 10px; color: #6c757d; text-decoration: none; font-size: 14px;">Reset</a>
        </div>
    </form>

    <?php if (empty($laporanList)): ?>
        <div
            style="background: #fff; padding: 30px; text-align: center; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <p style="color: #666; margin: 0;">Tidak ada data laporan untuk periode ini.</p>
        </div>
    <?php else: ?>
        <div style="background: #fff; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden;">
            <table class="table-laporan" style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #007bff; color: #fff;">
                        <th style="padding: 12px 18px; text-align: left;">ID</th>
                        <th style="padding: 12px 18px; text-align: left;">Pemohon</th>
                        <th style="padding: 12px 18px; text-align: left;">Tujuan</th>
                        <th style="padding: 12px 18px; text-align: left;">Tanggal Berangkat</th>
                        <th style="padding: 12px 18px; text-align: left;">Status</th>
                        <th style="padding: 12px 18px; text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($laporanList as $row): ?>
                        <tr style="border-bottom: 1px solid #f2f2f2;">
                            <td style="padding: 12px 18px;"><?php echo $row['id']; ?></td>
                            <td style="padding: 12px 18px;"><?php echo htmlspecialchars($row['nama_pegawai']); ?></td>
                            <td style="padding: 12px 18px;"><?php echo htmlspecialchars($row['tujuan']); ?></td>
                            <td style="padding: 12px 18px;"><?php echo htmlspecialchars($row['tanggal_berangkat']); ?></td>
                            <td style="padding: 12px 18px;">
                                <span
                                    style="padding: 4px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; background: #e2f0d9; color: #385723;">
                                    <?php echo strtoupper(str_replace('_', ' ', $row['status'])); ?>
                                </span>
                            </td>
                            <td style="padding: 12px 18px; text-align: center;">
                                <a href="detail_sppd.php?id=<?php echo $row['id']; ?>"
                                    style="color: #007bff; text-decoration: none; font-weight: 500;">Detail</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
    .table-laporan tr:hover {
        background: #f8f9fa;
        transition: background 0.2s;
    }

    @media print {

        form,
        button,
        .sidebar,
        header,
        .page-header p {
            display: none !important;
        }

        .main-content-sekwan {
            width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        .page-header {
            background: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            border-bottom: 2px solid #000;
            margin-bottom: 15px;
        }

        .page-header h1 {
            color: #000 !important;
            font-size: 20px;
        }

        table {
            border: 1px solid #ddd;
        }

        th {
            background: #eee !important;
            color: #000 !important;
        }
    }
</style>

<?php include 'footer.php'; ?>