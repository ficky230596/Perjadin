<?php

require_once '../config/db.php'; // PDO, fonnte_token, fungsi WA & scheduling
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Bagian Umum - Sistem Perjadin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="umum.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showAlert(title, text, icon = 'info') {
            Swal.fire({
                title: title,
                text: text,
                icon: icon,
                confirmButtonText: 'OK'
            });
        }
    </script>
    <style>
        
    </style>
</head>
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


<body>