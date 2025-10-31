<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'sekwan') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="sekwan.css">
    <title>Dashboard Sekwan</title>

</head>

<body>
<header style="background:#6c757d;color:white;padding:10px; display:flex; justify-content:space-between; align-items:center;">
    <div class="header-left" style="display:flex; align-items:center; gap:10px;">
        <img src="../assets/gambar/logo.png" alt="Logo DPRD" class="logo" style="width:50px; height:auto;">
        <h2>Aplikasi Pengajuan Perjalanan Dinas DPRD Kepulauan Banggai</h2>
    </div>
    <nav>
        <a href="dashboard_sekwan.php" style="color:white; margin-right:10px;">Dashboard</a>
        <a href="../logout.php" style="color:yellow;">Logout</a>
    </nav>
</header>
