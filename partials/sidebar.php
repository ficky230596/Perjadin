<aside style="width:200px;float:left;background:#f4f4f4;padding:10px;height:100vh;">
    <h3>Menu</h3>
    <ul style="list-style:none;padding:0;">
        <?php if ($_SESSION['role'] === 'ketua'): ?>
            <li><a href="dashboard_ketua.php">📑 Dashboard</a></li>
        <?php elseif ($_SESSION['role'] === 'pegawai'): ?>
            <li><a href="dashboard_pegawai.php">📑 Dashboard</a></li>
            <li><a href="ajukan.php">📝 Ajukan Perjadin</a></li>
        <?php elseif ($_SESSION['role'] === 'sekwan'): ?>
            <li><a href="dashboard_sekwan.php">📑 Dashboard</a></li>
        <?php endif; ?>
    </ul>
</aside>
<main style="margin-left:210px;padding:20px;">
