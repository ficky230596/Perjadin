<?php
session_start();
if ($_SESSION['role'] !== 'ketua') {
    header('Location: ../index.php');
    exit;
}
include 'header_ketua.php';
include 'sidebar_ketua.php';
include '../config/db.php';
$queue = getScheduledQueue($pdo, 'paraf_sekwan');
?>
<h1>Dashboard Ketua: <?php echo htmlspecialchars($_SESSION['nama']); ?></h1>
<p>Berikan TTd atau setujui langsung SPPD.</p>

<h2>Antrian TTd (Prioritas)</h2>
<?php if (empty($queue)): ?>
    <p>Tidak ada antrian TTd.</p>
<?php else: ?>
    <table border="1" cellpadding="5">
        <tr>
            <th>ID</th>
            <th>Tujuan</th>
            <th>Skor</th>
            <th>Aksi</th>
        </tr>
        <?php foreach ($queue as $q): ?>
            <tr>
                <td><?php echo $q['id']; ?></td>
                <td><?php echo htmlspecialchars($q['tujuan']); ?></td>
                <td><?php echo $q['prioritas_skor']; ?></td>
                <td>
                    <a href="ketua_ttd.php?ttd_id=<?php echo $q['id']; ?>">TTd Normal</a> |
                    <a href="ketua_ttd.php?skip_id=<?php echo $q['id']; ?>" style="color: green;">Setujui Langsung</a>
                </td>
            </tr>
        <?php endforeach; ?>
        <style>
            table {
            border-collapse: collapse;
            width: 90%;
            margin: 20px auto;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.07);
            border-radius: 8px;
            overflow: hidden;
            }
            th, td {
            padding: 12px 18px;
            text-align: left;
            }
            th {
            background: #007bff;
            color: #fff;
            font-weight: 600;
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
        </style>
    </table>
<?php endif; ?>
<?php include 'footer.php'; ?>
