<?php

if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'sekwan'
) {
    header('Location: ../index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <link
        rel="stylesheet"
        href="sekwan.css"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <title>Dashboard Sekwan</title>


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            box-sizing: border-box;
        }


        html,
        body {
            margin: 0;
            padding: 0;
        }


        body {

            font-family:
                "Inter",
                "Segoe UI",
                Arial,
                sans-serif;

        }


        /* =====================================================
           HEADER
           FIXED AGAR TIDAK BERGERAK SAAT SCROLL
        ===================================================== */

        .sekwan-header {

            position: fixed;

            top: 0;

            left: 0;

            width: 100%;

            height: 72px;

            padding:
                10px 24px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            background:
                linear-gradient(
                    135deg,
                    #10294d 0%,
                    #17477f 100%
                );

            color: #fff;

            box-shadow:
                0 3px 12px
                rgba(0, 0, 0, .15);

            z-index: 2000;

        }


        /* =====================================================
           BAGIAN KIRI
        ===================================================== */

        .header-left {

            display: flex;

            align-items: center;

            gap: 14px;

            min-width: 0;

            flex: 1;

        }


        /* =====================================================
           TOMBOL HAMBURGER
        ===================================================== */

        .sidebar-toggle {

            display: none;

            width: 42px;

            height: 42px;

            padding: 0;

            border: none;

            border-radius: 9px;

            background:
                rgba(255,255,255,.10);

            color: #fff;

            align-items: center;

            justify-content: center;

            cursor: pointer;

            font-size: 18px;

            flex-shrink: 0;

            outline: none;

            -webkit-tap-highlight-color: transparent;

            transition:
                background .2s ease,
                color .2s ease,
                transform .15s ease;

        }


        .sidebar-toggle:hover {

            background:
                rgba(255,255,255,.18);

            color: #f2bd4b;

        }


        .sidebar-toggle:active {

            transform:
                scale(.92);

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .sekwan-logo {

            width: 50px;

            height: 50px;

            object-fit: contain;

            flex-shrink: 0;

            display: block;

        }


        /* =====================================================
           JUDUL
        ===================================================== */

        .header-title {

            margin: 0;

            color: #fff;

            font-size: 18px;

            font-weight: 700;

            line-height: 1.3;

            letter-spacing: .2px;

        }


        .header-subtitle {

            display: block;

            margin-top: 3px;

            color: #f2bd4b;

            font-size: 11px;

            font-weight: 500;

        }


        /* =====================================================
           NAVIGASI
        ===================================================== */

        .sekwan-nav {

            display: flex;

            align-items: center;

            gap: 7px;

            flex-shrink: 0;

        }


        .sekwan-nav a {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding:
                9px 13px;

            color:
                rgba(255,255,255,.9);

            text-decoration: none;

            border-radius: 8px;

            font-size: 13px;

            font-weight: 600;

            transition:
                background .2s ease,
                color .2s ease,
                transform .2s ease;

        }


        .sekwan-nav a:hover {

            background:
                rgba(255,255,255,.12);

            color: #f2bd4b;

            transform:
                translateY(-1px);

        }


        /* =====================================================
           DASHBOARD
        ===================================================== */

        .sekwan-nav .dashboard-link {

            background:
                rgba(255,255,255,.08);

        }


        .sekwan-nav .dashboard-link i {

            color: #f2bd4b;

        }


        /* =====================================================
           LOGOUT
        ===================================================== */

        .sekwan-nav .logout {

            background:
                rgba(220,53,69,.14);

            color: #ffd7da;

        }


        .sekwan-nav .logout i {

            color: #ff9da5;

        }


        .sekwan-nav .logout:hover {

            background: #dc3545;

            color: #fff;

        }


        .sekwan-nav .logout:hover i {

            color: #fff;

        }


        /* =====================================================
           SIDEBAR MOBILE OVERLAY
        ===================================================== */

        .sidebar-mobile-overlay {

            display: none;

            position: fixed;

            top: 0;

            left: 0;

            right: 0;

            bottom: 0;

            background:
                rgba(0,0,0,.45);

            z-index: 1500;

        }


        .sidebar-mobile-overlay.show {

            display: block;

        }


        /* =====================================================
           TABLET
        ===================================================== */

        @media (max-width: 900px) {

            .sekwan-header {

                padding:
                    10px 18px;

            }


            .header-title {

                font-size: 16px;

            }

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 768px) {

            .sekwan-header {

                height: 64px;

                min-height: 64px;

                padding:
                    8px 12px;

                gap: 8px;

            }


            /* Hamburger muncul */

            .sidebar-toggle {

                display: flex;

            }


            .header-left {

                gap: 8px;

            }


            .sekwan-logo {

                width: 40px;

                height: 40px;

            }


            .header-title {

                font-size: 13px;

                line-height: 1.25;

                display: -webkit-box;

                -webkit-line-clamp: 2;

                -webkit-box-orient: vertical;

                overflow: hidden;

            }


            .header-subtitle {

                font-size: 9px;

            }


            .sekwan-nav {

                gap: 2px;

            }


            .sekwan-nav a {

                padding: 8px;

            }


            .sekwan-nav a span {

                display: none;

            }

        }


        /* =====================================================
           MOBILE KECIL
        ===================================================== */

        @media (max-width: 480px) {

            .sekwan-header {

                height: 64px;

                padding:
                    8px 10px;

            }


            .sidebar-toggle {

                width: 36px;

                height: 36px;

                font-size: 16px;

            }


            .header-left {

                gap: 6px;

            }


            .sekwan-logo {

                width: 36px;

                height: 36px;

            }


            .header-title {

                font-size: 11px;

                max-width: 170px;

            }


            .header-subtitle {

                display: none;

            }


            .sekwan-nav a {

                padding: 7px;

            }

        }


        /* =====================================================
           PRINT
        ===================================================== */

        @media print {

            .sekwan-header {

                position: relative;

                background:
                    #10294d !important;

                color: #fff !important;

                box-shadow: none;

                -webkit-print-color-adjust:
                    exact;

                print-color-adjust:
                    exact;

            }


            .sidebar-toggle {

                display: none !important;

            }

        }

    </style>

</head>


<body>


<header class="sekwan-header">


    <!-- =================================================
         BAGIAN KIRI
    ================================================== -->

    <div class="header-left">


        <!-- =================================================
             HAMBURGER
        ================================================== -->

        <button
            type="button"
            class="sidebar-toggle"
            id="sidebarToggle"
            aria-label="Buka menu"
            aria-expanded="false"
            title="Buka menu"
        >

            <i
                class="fa-solid fa-bars"
                id="sidebarToggleIcon"
            ></i>

        </button>


        <!-- =================================================
             LOGO
        ================================================== -->

        <img
            src="../assets/gambar/logo.png"
            alt="Logo DPRD"
            class="sekwan-logo"
        >


        <!-- =================================================
             JUDUL
        ================================================== -->

        <div>

            <h2 class="header-title">

                Aplikasi Pengajuan Perjalanan Dinas
                DPRD Kabupaten Banggai Kepulauan

            </h2>


            <span class="header-subtitle">

                Sekretariat DPRD Kabupaten Banggai Kepulauan

            </span>

        </div>

    </div>


    <!-- =================================================
         NAVIGASI
    ================================================== -->

    <nav class="sekwan-nav">


        <a
            href="sekwan_dashboard.php"
            class="dashboard-link"
            title="Dashboard"
        >

            <i class="fa-solid fa-gauge-high"></i>

            <span>
                Dashboard
            </span>

        </a>


        <a
            href="../logout.php"
            class="logout"
            title="Logout"
        >

            <i class="fa-solid fa-right-from-bracket"></i>

            <span>
                Logout
            </span>

        </a>


    </nav>


</header>


<!-- =====================================================
     OVERLAY SIDEBAR MOBILE
===================================================== -->

<div
    class="sidebar-mobile-overlay"
    id="sidebarMobileOverlay"
></div>


<script>

document.addEventListener("DOMContentLoaded", function () {


    /* =====================================================
       AMBIL ELEMEN
    ===================================================== */

    const hamburger =
        document.getElementById("sidebarToggle");

    const hamburgerIcon =
        document.getElementById("sidebarToggleIcon");

    const sidebar =
        document.querySelector(".sidebar-sekwan");

    const overlay =
        document.getElementById("sidebarMobileOverlay");


    /* =====================================================
       CEGAH ERROR JIKA SIDEBAR TIDAK DITEMUKAN
    ===================================================== */

    if (!hamburger) {
        return;
    }


    /* =====================================================
       FUNGSI BUKA SIDEBAR
    ===================================================== */

    function openSidebar() {

        if (sidebar) {

            sidebar.classList.add("show");

        }


        if (overlay) {

            overlay.classList.add("show");

        }


        hamburger.setAttribute(
            "aria-expanded",
            "true"
        );


        hamburger.setAttribute(
            "aria-label",
            "Tutup menu"
        );


        hamburger.setAttribute(
            "title",
            "Tutup menu"
        );


        if (hamburgerIcon) {

            hamburgerIcon.classList.remove(
                "fa-bars"
            );

            hamburgerIcon.classList.add(
                "fa-xmark"
            );

        }


        document.body.style.overflow = "hidden";

    }


    /* =====================================================
       FUNGSI TUTUP SIDEBAR
    ===================================================== */

    function closeSidebar() {

        if (sidebar) {

            sidebar.classList.remove("show");

        }


        if (overlay) {

            overlay.classList.remove("show");

        }


        hamburger.setAttribute(
            "aria-expanded",
            "false"
        );


        hamburger.setAttribute(
            "aria-label",
            "Buka menu"
        );


        hamburger.setAttribute(
            "title",
            "Buka menu"
        );


        if (hamburgerIcon) {

            hamburgerIcon.classList.remove(
                "fa-xmark"
            );

            hamburgerIcon.classList.add(
                "fa-bars"
            );

        }


        document.body.style.overflow = "";

    }


    /* =====================================================
       TOGGLE HAMBURGER
    ===================================================== */

    hamburger.addEventListener(
        "click",
        function (event) {

            event.preventDefault();

            event.stopPropagation();


            if (
                sidebar &&
                sidebar.classList.contains("show")
            ) {

                closeSidebar();

            } else {

                openSidebar();

            }

        }
    );


    /* =====================================================
       KLIK OVERLAY = TUTUP
    ===================================================== */

    if (overlay) {

        overlay.addEventListener(
            "click",
            function () {

                closeSidebar();

            }
        );

    }


    /* =====================================================
       KLIK MENU = TUTUP SIDEBAR MOBILE
    ===================================================== */

    if (sidebar) {

        sidebar
            .querySelectorAll(".menu-link")
            .forEach(function (link) {

                link.addEventListener(
                    "click",
                    function () {

                        if (
                            window.innerWidth <= 768
                        ) {

                            closeSidebar();

                        }

                    }
                );

            });

    }


    /* =====================================================
       ESC = TUTUP SIDEBAR
    ===================================================== */

    document.addEventListener(
        "keydown",
        function (event) {

            if (
                event.key === "Escape"
            ) {

                closeSidebar();

            }

        }
    );


    /* =====================================================
       RESIZE DESKTOP
    ===================================================== */

    window.addEventListener(
        "resize",
        function () {

            if (
                window.innerWidth > 768
            ) {

                closeSidebar();

            }

        }
    );


});

</script>
