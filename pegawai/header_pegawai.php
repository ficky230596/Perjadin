<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'pegawai'
) {
    header('Location: ../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Dashboard Pegawai</title>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- CSS Pegawai -->
    <link rel="stylesheet" href="Pegawai.css">
    <link rel="stylesheet" href="ajukan.css">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        html {
            min-height: 100%;
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            padding-top: 90px;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
        }

        /* =====================================
           HEADER TETAP DI ATAS SAAT SCROLL
        ===================================== */
        body>header,
        header.pegawai-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 2000;

            width: 100%;
            min-height: 90px;
            padding: 14px 32px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;

            background: linear-gradient(110deg,
                    #10294d 0%,
                    #17477f 100%);

            color: #fff;
            border-bottom: 3px solid #f2bd4b;
            box-shadow: 0 4px 18px rgba(16, 41, 77, 0.16);
        }

        /* =====================================
           BAGIAN LOGO DAN JUDUL
        ===================================== */
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .logo {
            display: block;
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            object-fit: contain;
            padding: 4px;
            background: #fff;
            border: 2px solid #f2bd4b;
            border-radius: 12px;
        }

        .header-left h1 {
            margin: 0;
            max-width: 700px;
            color: #fff;
            font-size: 18px;
            font-weight: 700;
            line-height: 1.5;
        }

        /* =====================================
           MENU NAVIGASI
        ===================================== */
        body>header nav,
        header.pegawai-header nav {
            flex-shrink: 0;
        }

        body>header nav ul,
        header.pegawai-header nav ul {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;

            margin: 0;
            padding: 0;
            list-style: none;
        }

        body>header nav ul li,
        header.pegawai-header nav ul li {
            margin: 0;
            padding: 0;
        }

        body>header nav ul li a,
        header.pegawai-header nav ul li a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;

            min-height: 42px;
            padding: 10px 16px;

            color: #e4edf9;
            background: rgba(255, 255, 255, 0.07);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 9px;

            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;

            transition: background 0.2s ease,
                color 0.2s ease,
                border-color 0.2s ease;
        }

        body>header nav ul li a:hover,
        header.pegawai-header nav ul li a:hover {
            color: #10294d;
            background: #f2bd4b;
            border-color: #f2bd4b;
        }

        /* =====================================
           RESPONSIVE TABLET
        ===================================== */
        @media (max-width: 900px) {
            body {
                padding-top: 100px;
            }

            body>header,
            header.pegawai-header {
                min-height: 100px;
                padding: 14px 20px;
                gap: 15px;
            }

            .logo {
                width: 46px;
                height: 46px;
            }

            .header-left h1 {
                font-size: 15px;
            }
        }

        /* =====================================
           RESPONSIVE HP
        ===================================== */
        @media (max-width: 600px) {
            body {
                padding-top: 116px;
            }

            body>header,
            header.pegawai-header {
                min-height: 116px;
                padding: 10px 14px;

                flex-direction: column;
                align-items: stretch;
                justify-content: center;
                gap: 9px;
            }

            .header-left {
                gap: 10px;
            }

            .logo {
                width: 39px;
                height: 39px;
                border-radius: 9px;
            }

            .header-left h1 {
                font-size: 12px;
                line-height: 1.4;
            }

            body>header nav ul,
            header.pegawai-header nav ul {
                justify-content: flex-end;
                gap: 8px;
            }

            body>header nav ul li a,
            header.pegawai-header nav ul li a {
                min-height: 34px;
                padding: 7px 12px;
                font-size: 11px;
                border-radius: 7px;
            }
        }

        @media (max-width: 360px) {

            body>header,
            header.pegawai-header {
                padding-left: 10px;
                padding-right: 10px;
            }

            .header-left h1 {
                font-size: 11px;
            }

            .logo {
                width: 35px;
                height: 35px;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            html {
                scroll-behavior: auto;
            }

            *,
            *::before,
            *::after {
                transition: none !important;
            }
        }
    </style>
</head>

<body>

    <header class="pegawai-header">

        <!-- LOGO DAN JUDUL -->
        <div class="header-left">

            <img src="../assets/gambar/logo.png" alt="Logo DPRD Banggai" class="logo">

            <h1>
                Aplikasi Pengajuan Perjalanan Dinas
                DPRD Kepulauan Banggai
            </h1>

        </div>

        <!-- NAVIGASI -->
        <nav aria-label="Navigasi Pegawai">
            <ul>
                <!-- <li>
                    <a href="profil.php">
                        <i class="fa-solid fa-user"></i>
                        <span>Profil</span>
                    </a>
                </li> -->

                <li>
                    <a href="../logout.php">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        <span>Logout</span>
                    </a>
                </li>
            </ul>
        </nav>

    </header>