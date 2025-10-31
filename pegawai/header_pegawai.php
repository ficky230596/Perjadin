<?php

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pegawai') {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <title>Dashboard Pegawai</title>
    <link rel="stylesheet" href="Pegawai.css">
    <link rel="stylesheet" href="ajukan.css">
</head>

<body>
    <header>
        <div class="header-left">
            <img src="../assets/gambar/logo.png" alt="Logo DPRD Banggai" class="logo">
            <h1>Aplikasi Pengajuan Perjalanan Dinas DPRD Kepulauan Banggai</h1>
        </div>
        <nav>
            <ul>
                <li><a href="profil.php">Profil</a></li>
                <li><a href="../logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>
