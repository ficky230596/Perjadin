
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'ketua'
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

    <title>Dashboard Ketua</title>

    <link rel="stylesheet" href="assets/css/global.css">

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css"
    >

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >

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
            padding-top: 89px;
            min-height: 100vh;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            background: #f4f7fc;
        }

        /* ========================================
           HEADER UTAMA - TETAP DI ATAS SAAT SCROLL
        ======================================== */
        .main-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            width: 100%;
            z-index: 2000;

            background: linear-gradient(
                110deg,
                #10294d 0%,
                #17477f 100%
            );

            color: #fff;
            box-shadow: 0 4px 18px rgba(16, 41, 77, 0.15);
            border-bottom: 3px solid #f2bd4b;
            isolation: isolate;
        }

        .header-container {
            width: 100%;
            max-width: 1400px;
            min-height: 86px;
            margin: 0 auto;
            padding: 18px 35px;

            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        /* ========================================
           BRAND
        ======================================== */
        .header-brand {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0;
        }

        .brand-icon {
            width: 48px;
            height: 48px;
            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 13px;
            background: #f2bd4b;
            color: #10294d;

            font-size: 23px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.12);
        }

        .brand-text {
            min-width: 0;
        }

        .brand-title {
            margin: 0;
            color: #fff;
            font-size: 21px;
            font-weight: 800;
            letter-spacing: -0.5px;
            line-height: 1.4;
        }

        .brand-role {
            color: #f2bd4b;
            font-size: 14px;
            font-weight: 500;
            letter-spacing: 0;
        }

        .brand-subtitle {
            display: block;
            margin-top: 4px;
            color: #c6d7ed;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 1.5px;
        }

        /* ========================================
           NAVIGASI HEADER
        ======================================== */
        .header-nav {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            flex-shrink: 0;
        }

        .nav-link-custom {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            padding: 11px 16px;
            border-radius: 9px;

            color: #e4edf9;
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;

            transition: all 0.2s ease;
        }

        .nav-link-custom:hover {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
        }

        .nav-link-custom.active {
            background: rgba(255, 255, 255, 0.12);
            color: #fff;
            box-shadow: inset 0 -2px 0 #f2bd4b;
        }

        /* ========================================
           LOGOUT
        ======================================== */
        .logout-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            padding: 10px 17px;
            border: 1px solid rgba(242, 189, 75, 0.75);
            border-radius: 9px;

            color: #f2bd4b;
            background: rgba(242, 189, 75, 0.06);

            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            white-space: nowrap;

            transition: all 0.2s ease;
        }

        .logout-link:hover {
            background: #f2bd4b;
            color: #10294d;
            border-color: #f2bd4b;
        }

        /* ========================================
           TOMBOL SIDEBAR
        ======================================== */
        .sidebar-toggle {
            display: none;
            width: 42px;
            height: 42px;
            flex-shrink: 0;

            align-items: center;
            justify-content: center;

            border: 1px solid rgba(255, 255, 255, 0.25);
            border-radius: 10px;

            background: rgba(255, 255, 255, 0.08);
            color: #fff;

            font-size: 18px;
            cursor: pointer;

            transition: all 0.2s ease;
        }

        .sidebar-toggle:hover {
            background: #f2bd4b;
            color: #10294d;
        }

        /* ========================================
           TABLET
        ======================================== */
        @media (max-width: 768px) {
            body {
                padding-top: 79px;
            }

            .header-container {
                min-height: 76px;
                padding: 16px 20px;
                gap: 15px;
            }

            .brand-icon {
                width: 43px;
                height: 43px;
                font-size: 20px;
            }

            .brand-title {
                font-size: 17px;
            }

            .brand-subtitle {
                font-size: 9px;
                letter-spacing: 1px;
            }

            .brand-role {
                font-size: 12px;
            }

            .sidebar-toggle {
                display: inline-flex;
            }

            .nav-link-custom,
            .logout-link {
                padding: 10px 12px;
                font-size: 12px;
            }
        }

        /* ========================================
           HP
        ======================================== */
        @media (max-width: 520px) {
            .header-container {
                min-height: 76px;
                padding: 12px 14px;
                flex-direction: row;
                align-items: center;
                gap: 10px;
            }

            .header-brand {
                gap: 9px;
                flex: 1;
            }

            .brand-icon {
                width: 38px;
                height: 38px;
                border-radius: 10px;
                font-size: 17px;
            }

            .brand-title {
                font-size: 14px;
                line-height: 1.4;
            }

            .brand-subtitle {
                margin-top: 2px;
                font-size: 8px;
                letter-spacing: 0.6px;
            }

            .brand-role {
                font-size: 11px;
            }

            .header-nav {
                gap: 7px;
            }

            .nav-link-custom {
                padding: 10px;
                font-size: 12px;
            }

            .logout-link {
                padding: 10px;
                font-size: 12px;
            }

            .nav-link-custom,
            .logout-link {
                gap: 6px;
            }
        }

        /* ========================================
           HP KECIL
        ======================================== */
        @media (max-width: 380px) {
            .header-container {
                padding: 10px;
                gap: 7px;
            }

            .header-brand {
                gap: 7px;
            }

            .brand-icon {
                width: 34px;
                height: 34px;
                font-size: 15px;
            }

            .brand-title {
                font-size: 12px;
                letter-spacing: 0;
            }

            .brand-subtitle {
                font-size: 7px;
                letter-spacing: 0;
            }

            .brand-role {
                font-size: 10px;
            }

            .sidebar-toggle {
                width: 36px;
                height: 36px;
                font-size: 15px;
            }

            .nav-link-custom,
            .logout-link {
                padding: 9px;
                font-size: 11px;
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

<header class="main-header">
    <div class="header-container">

        <!-- BRAND -->
        <div class="header-brand">

            <!-- Tombol sidebar untuk tablet dan HP -->
            <button
                type="button"
                class="sidebar-toggle"
                id="sidebarToggle"
                aria-label="Buka atau tutup sidebar"
                aria-controls="sidebarKetua"
                aria-expanded="false"
            >
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="brand-icon">
                <i class="fa-solid fa-landmark"></i>
            </div>

            <div class="brand-text">
                <h2 class="brand-title">
                    Sistem Perjadin DPRD
                    <span class="brand-role">- Ketua</span>
                </h2>

                <span class="brand-subtitle">
                    SISTEM PERJALANAN DINAS
                </span>
            </div>

        </div>

        <!-- MENU HEADER -->
        <nav class="header-nav" aria-label="Navigasi utama">

            <a
                href="ketua_dashboard.php"
                class="nav-link-custom <?= basename($_SERVER['PHP_SELF']) === 'ketua_dashboard.php' ? 'active' : '' ?>"
            >
                <i class="fa-solid fa-house"></i>
                <span>Dashboard</span>
            </a>

            <a href="../logout.php" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>

        </nav>

    </div>
</header>
</body>
</html>