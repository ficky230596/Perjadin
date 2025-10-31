<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'umum') {
    echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
          <script>
            Swal.fire({
                title: 'Akses Ditolak!',
                text: 'Silakan login sebagai Bagian Umum.',
                icon: 'error'
            }).then(() => {
                window.location.href = '../index.php';
            });
          </script>";
    exit;
}

require_once '../config/db.php';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Draft SPPD</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f6f8fa;
            margin: 0;
            padding: 0;
        }
        .main-content-umum {
            padding: 20px;
        }
        h1 {
            margin-bottom: 5px;
        }
        .content-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
            margin-top: 20px;
        }
        /* FORM */
        .draft-form {
            flex: 2;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .draft-form label {
            display: block;
            margin-top: 10px;
            font-weight: bold;
        }
        .draft-form input,
        .draft-form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        .draft-form button {
            margin-top: 15px;
            padding: 10px 15px;
            background: #007bff;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        .draft-form button:hover {
            background: #0056b3;
        }
        /* CARD PRIORITAS */
        .priority-reasons {
            flex: 1;
            background: #fafafa;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        .priority-reasons h2 {
            margin-bottom: 15px;
            color: #b71c1c;
        }
        .reasons-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .reason-card {
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: #fff;
        }
        .reason-card h3 {
            margin: 0 0 5px;
            font-size: 16px;
            color: #333;
        }
        .reason-card p {
            margin: 0;
            font-size: 14px;
            color: #555;
        }
        .empty-antrian {
            color: #888;
            font-style: italic;
        }
        .form-row {
            display: flex;
            gap: 15px;
            margin-top: 10px;
        }
        .form-group {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        .form-group input,
        .form-group textarea {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>
    <?php include 'header_umum.php'; ?>
    <?php include 'sidebar_umum.php'; ?>

    <div class="main-content-umum">
        <?php
        $id = $_GET['id'] ?? $_POST['id'] ?? null;

        if (!$id) {
            echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'ID pengajuan tidak valid!',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = 'umum_dashboard.php';
                    });
                  </script>";
            exit;
        }

        // Ambil data pengajuan
        $stmt = $pdo->prepare("SELECT * FROM pengajuan WHERE id = ? AND status = 'diajukan'");
        $stmt->execute([$id]);
        $pengajuan = $stmt->fetch();

        if (!$pengajuan) {
            echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Pengajuan tidak ditemukan atau status salah!',
                        icon: 'error'
                    }).then(() => {
                        window.location.href = 'umum_dashboard.php';
                    });
                  </script>";
            exit;
        }

        // Ambil alasan prioritas
        $stmt_priority = $pdo->prepare("SELECT alasan FROM pengajuan WHERE urgensi = 'tinggi' AND status = 'diajukan' LIMIT 3");
        $stmt_priority->execute();
        $priority_reasons = $stmt_priority->fetchAll(PDO::FETCH_COLUMN);

        // Proses form submit
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['id'] == $id) {
            $spt_no = $_POST['spt_no'] ?? '';
            $spd_no = $_POST['spd_no'] ?? '';
            $pangkat = $_POST['pangkat'] ?? '';
            $tingkat_biaya = $_POST['tingkat_biaya'] ?? '';
            $alat_angkutan = $_POST['alat_angkutan'] ?? '';
            $pengikut = $_POST['pengikut'] ?? '';
            $akun_anggaran = $_POST['akun_anggaran'] ?? '';

            if (empty($tingkat_biaya) || empty($alat_angkutan)) {
                echo "<script>
                        Swal.fire({
                            title: 'Peringatan!',
                            text: 'Semua field wajib diisi!',
                            icon: 'warning'
                        });
                      </script>";
            } else {
                $stmt_update = $pdo->prepare("UPDATE pengajuan 
                                              SET status = 'draft_sppd', 
                                                  spt_no = ?, spd_no = ?, pangkat = ?, 
                                                  tingkat_biaya = ?, alat_angkutan = ?, 
                                                  pengikut = ?, akun_anggaran = ? 
                                              WHERE id = ?");
                $stmt_update->execute([
                    $spt_no,
                    $spd_no,
                    $pangkat,
                    $tingkat_biaya,
                    $alat_angkutan,
                    $pengikut,
                    $akun_anggaran,
                    $id
                ]);

                echo "<script>
                        Swal.fire({
                            title: 'Sukses!',
                            text: 'Draft dibuat & WA terkirim ke Sekwan!',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            window.location.href = 'umum_dashboard.php';
                        });
                      </script>";
                exit;
            }
        }

        // Fungsi detail pengajuan prioritas
        function getPriorityDetails($pdo)
        {
            $stmt = $pdo->prepare("SELECT tujuan, tanggal_berangkat, tanggal_kembali, alasan, urgensi, status 
                                   FROM pengajuan 
                                   WHERE urgensi = 'tinggi' AND status = 'diajukan'
                                   LIMIT 3");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        $priority_details = getPriorityDetails($pdo);
        ?>

        <p>Tujuan: <?php echo htmlspecialchars($pengajuan['tujuan']); ?> | Urgensi: <?php echo ucfirst($pengajuan['urgensi']); ?></p>

        <div class="content-wrapper">
            <!-- FORM -->
            <form method="POST" class="draft-form">
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <!-- Tingkat Biaya & Alat Angkutan -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Tingkat Biaya Perjadin:</label>
                        <input type="text" name="tingkat_biaya" required>
                    </div>
                    <div class="form-group">
                        <label>Alat Angkutan:</label>
                        <input type="text" name="alat_angkutan" value="Mobil" required>
                    </div>
                </div>

                <!-- Pengikut -->
                <div class="form-group">
                    <label>Pengikut (pisah koma):</label>
                    <textarea name="pengikut"></textarea>
                </div>

                <!-- Nomor SPT, SPD, Akun Anggaran -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor SPT:</label>
                        <input type="text" name="spt_no">
                    </div>
                    <div class="form-group">
                        <label>Nomor SPD:</label>
                        <input type="text" name="spd_no">
                    </div>
                    <div class="form-group">
                        <label>Akun Anggaran:</label>
                        <input type="text" name="akun_anggaran">
                    </div>
                </div>

                <button type="submit">Simpan Draft & Kirim ke Sekwan</button>
            </form>

            <!-- CARD PRIORITAS -->
            <div class="priority-reasons">
                <h2>Prioritas Tinggi</h2>
                <?php if (empty($priority_reasons)): ?>
                    <p class="empty-antrian">Tidak ada pengajuan dengan urgensi tinggi saat ini.</p>
                <?php else: ?>
                    <div class="reasons-container">
                        <?php foreach ($priority_reasons as $index => $reason): ?>
                            <div class="reason-card">
                                <h3>Alasan #<?php echo $index + 1; ?></h3>
                                <p><?php echo htmlspecialchars($reason); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="priority-reasons">
                <h2>Detail Pengajuan</h2>
                <?php if (empty($priority_details)): ?>
                    <p class="empty-antrian">Tidak ada pengajuan dengan urgensi tinggi saat ini.</p>
                <?php else: ?>
                    <div class="reasons-container">
                        <?php foreach ($priority_details as $index => $data): ?>
                            <div class="reason-card">
                                <h3>Pengajuan #<?php echo $index + 1; ?></h3>
                                <p><strong>Tujuan:</strong> <?php echo htmlspecialchars($data['tujuan']); ?></p>
                                <p><strong>Tanggal Berangkat:</strong> <?php echo date('d-m-Y', strtotime($data['tanggal_berangkat'])); ?></p>
                                <p><strong>Tanggal Kembali:</strong> <?php echo date('d-m-Y', strtotime($data['tanggal_kembali'])); ?></p>
                                <p><strong>Urgensi:</strong> <?php echo ucfirst($data['urgensi']); ?></p>
                                <p><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $data['status'])); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
