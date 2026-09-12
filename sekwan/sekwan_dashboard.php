<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekwan') {
    header('Location: ../index.php');
    exit;
}
include '../config/db.php';
include 'header_sekwan.php';
include 'sidebar_sekwan.php';

$queue = getScheduledQueue($pdo, 'draft_sppd');

// Mengambil informasi/statistik terkait status dokumen untuk Sekwan dari database
try {
    // Total draft siap paraf (dalam antrian)
    $stmt_antrian = $pdo->prepare("SELECT COUNT(*) FROM pengajuan WHERE status = 'draft_sppd'");
    $stmt_antrian->execute();
    $total_antrian = $stmt_antrian->fetchColumn();

    // Total yang sudah diparaf oleh Sekwan (status paraf_sekwan, ttd_ketua, dicap, selesai)
    $stmt_selesai = $pdo->prepare("SELECT COUNT(*) FROM pengajuan WHERE status IN ('paraf_sekwan', 'ttd_ketua', 'dicap', 'selesai')");
    $stmt_selesai->execute();
    $total_diparaf = $stmt_selesai->fetchColumn();

    // Total keseluruhan pengajuan
    $stmt_total = $pdo->prepare("SELECT COUNT(*) FROM pengajuan");
    $stmt_total->execute();
    $total_pengajuan = $stmt_total->fetchColumn();
} catch (PDOException $e) {
    $total_antrian = count($queue);
    $total_diparaf = 0;
    $total_pengajuan = count($queue);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Sekwan</title>
    <link rel="stylesheet" href="assets/css/sekwan.css">
    <!-- SweetAlert2 & Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .main-content-sekwan {
            width: 92%;
            margin: 20px auto;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .dashboard-header {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .dashboard-header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .dashboard-header p {
            margin: 5px 0 0 0;
            color: #666;
            font-size: 14px;
        }

        /* Statistik Cards & Chart Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .stat-card-group {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .stat-box {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-left: 5px solid #007bff;
        }

        .stat-box.green {
            border-left-color: #28a745;
        }

        .stat-box.orange {
            border-left-color: #ffc107;
        }

        .stat-info h3 {
            margin: 0;
            font-size: 14px;
            color: #666;
        }

        .stat-info p {
            margin: 5px 0 0 0;
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .chart-container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .chart-container h3 {
            margin: 0 0 15px 0;
            font-size: 16px;
            color: #333;
            align-self: flex-start;
        }

        .sekwan-table-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            overflow: hidden;
            margin-top: 15px;
        }

        .sekwan-table {
            width: 100%;
            border-collapse: collapse;
        }

        .sekwan-table th,
        .sekwan-table td {
            padding: 12px 18px;
            text-align: left;
            font-size: 14px;
            border-bottom: 1px solid #f2f2f2;
        }

        .sekwan-table th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
        }

        .sekwan-table tr:hover {
            background: #f8f9fa;
            transition: background 0.2s;
        }

        .badge-score {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            background: #e2f0d9;
            color: #385723;
        }

        .prioritas-tinggi {
            background: #f8d7da !important;
            color: #721c24 !important;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            display: inline-block;
            margin-right: 5px;
        }

        .btn-detail {
            background: #17a2b8;
            color: #fff;
        }

        .btn-paraf {
            background: #28a745;
            color: #fff;
            border: none;
            cursor: pointer;
        }

        .btn-detail:hover,
        .btn-paraf:hover {
            opacity: 0.85;
        }

        .empty-antrian {
            background: #fff;
            padding: 30px;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            color: #666;
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="main-content-sekwan">
        <div class="dashboard-header">
            <h1>Dashboard Sekwan: <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
            <p>Kelola paraf dokumen perjalanan dinas dan pantau ringkasan antrian secara real-time.</p>
        </div>

        <!-- Bagian Informasi Ringkasan & Chart -->
        <div class="stats-grid">
            <div class="stat-card-group">
                <div class="stat-box orange">
                    <div class="stat-info">
                        <h3>Antrian Draft Siap Paraf</h3>
                        <p><?php echo $total_antrian; ?> Dokumen</p>
                    </div>
                    <i class="fas fa-clock fa-2x" style="color: #ffc107;"></i>
                </div>
                <div class="stat-box green">
                    <div class="stat-info">
                        <h3>Dokumen Telah Diparaf / Diproses</h3>
                        <p><?php echo $total_diparaf; ?> Dokumen</p>
                    </div>
                    <i class="fas fa-check-circle fa-2x" style="color: #28a745;"></i>
                </div>
            </div>

            <div class="chart-container">
                <h3>Statistik Paraf Sekwan</h3>
                <div style="width: 180px; height: 180px;">
                    <canvas id="sekwanChart"></canvas>
                </div>
            </div>
        </div>

        <h2>Antrian Paraf (Prioritas)</h2>

        <?php if (empty($queue)): ?>
            <div class="empty-antrian">
                <p>Tidak ada draft siap paraf saat ini.</p>
            </div>
        <?php else: ?>
            <div class="sekwan-table-container">
                <table class="sekwan-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tujuan</th>
                            <th>Skor Prioritas</th>
                            <th style="text-align: center;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($queue as $q):
                            $isTinggi = ($q['prioritas_skor'] > 5);
                            $scoreClass = $isTinggi ? 'prioritas-tinggi' : 'badge-score';
                            ?>
                            <tr>
                                <td>#<?php echo htmlspecialchars($q['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($q['tujuan']); ?></strong></td>
                                <td>
                                    <span class="<?php echo $scoreClass; ?>">
                                        <?php echo htmlspecialchars($q['prioritas_skor']); ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="detail_sppd.php?id=<?php echo $q['id']; ?>"
                                        class="btn-action btn-detail">Detail</a>
                                    <button onclick="konfirmasiParaf(<?php echo $q['id']; ?>)"
                                        class="btn-action btn-paraf">Paraf Sekarang</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Inisialisasi Grafik menggunakan Chart.js
        const ctx = document.getElementById('sekwanChart').getContext('2d');
        const sekwanChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Belum Diparaf (Antrian)', 'Sudah Diparaf / Proses'],
                datasets: [{
                    data: [<?php echo $total_antrian; ?>, <?php echo $total_diparaf; ?>],
                    backgroundColor: ['#ffc107', '#28a745'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11 } }
                    }
                }
            }
        });

        // Auto-refresh halaman setiap 30 detik
        setTimeout(() => location.reload(), 30000);

        // Fungsi konfirmasi SweetAlert2
        function konfirmasiParaf(id) {
            Swal.fire({
                title: 'Konfirmasi Paraf SPPD',
                text: "Apakah Anda yakin ingin memparaf pengajuan ID #" + id + "? Notifikasi WhatsApp akan dikirim ke Ketua.",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Paraf!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'sekwan_paraf.php?proses_id=' + id;
                }
            });
        }
    </script>

    <?php include 'footer.php'; ?>
</body>

</html>