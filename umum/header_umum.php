<?php

require_once '../config/db.php';
// PDO, fonnte_token, fungsi WA & scheduling

?>
<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Bagian Umum - Sistem Perjadin</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
    >

    <!-- CSS Umum -->
    <link
        rel="stylesheet"
        href="umum.css"
    >

    <!-- SweetAlert2 -->
    <script
        src="https://cdn.jsdelivr.net/npm/sweetalert2@11"
    ></script>

    <script>

        function showAlert(
            title,
            text,
            icon = 'info'
        ) {

            Swal.fire({

                title: title,

                text: text,

                icon: icon,

                confirmButtonText: 'OK',

                confirmButtonColor: '#10294d'

            });

        }

    </script>

    <style>

        /* =====================================================
           HEADER BAGIAN UMUM
        ===================================================== */

        * {
            box-sizing: border-box;
        }


        body {
            margin: 0;
            padding: 0;
        }


        .umum-header {

            position: fixed;

            top: 0;
            left: 0;
            right: 0;

            height: 72px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            padding: 0 22px;

            background: #10294d;

            color: #ffffff;

            z-index: 2000;

            box-shadow:
                0 3px 14px
                rgba(16, 41, 77, .20);

        }


        /* =====================================================
           HEADER LEFT
        ===================================================== */

        .umum-header-left {

            display: flex;

            align-items: center;

            gap: 12px;

            min-width: 0;

        }


        /* =====================================================
           HAMBURGER
        ===================================================== */

        .umum-sidebar-toggle {

            width: 40px;

            height: 40px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-shrink: 0;

            border: 1px solid
                rgba(255,255,255,.15);

            border-radius: 9px;

            background:
                rgba(255,255,255,.08);

            color: #ffffff;

            font-size: 17px;

            cursor: pointer;

            transition:
                background .2s ease,
                transform .2s ease;

        }


        .umum-sidebar-toggle:hover {

            background:
                rgba(242,189,75,.18);

            color: #f2bd4b;

        }


        .umum-sidebar-toggle:active {

            transform: scale(.95);

        }


        /* =====================================================
           LOGO
        ===================================================== */

        .umum-logo {

            width: 42px;

            height: 42px;

            object-fit: contain;

            flex-shrink: 0;

        }


        /* =====================================================
           JUDUL
        ===================================================== */

        .umum-header-title {

            min-width: 0;

        }


        .umum-header-title h1 {

            margin: 0;

            color: #ffffff;

            font-family:
                "Inter",
                "Segoe UI",
                Arial,
                sans-serif;

            font-size: 16px;

            font-weight: 700;

            line-height: 1.3;

            white-space: nowrap;

            overflow: hidden;

            text-overflow: ellipsis;

        }


        .umum-header-title span {

            display: block;

            margin-top: 2px;

            color: #f2bd4b;

            font-family:
                "Inter",
                "Segoe UI",
                Arial,
                sans-serif;

            font-size: 10px;

            font-weight: 500;

        }


        /* =====================================================
           NAVIGATION
        ===================================================== */

        .umum-header-nav {

            display: flex;

            align-items: center;

            gap: 7px;

        }


        .umum-header-nav a {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            gap: 7px;

            min-height: 38px;

            padding: 8px 13px;

            border-radius: 8px;

            color: #ffffff;

            font-family:
                "Inter",
                "Segoe UI",
                Arial,
                sans-serif;

            font-size: 12px;

            font-weight: 600;

            text-decoration: none;

            transition:
                background .2s ease,
                color .2s ease;

        }


        .umum-header-nav a:hover {

            background:
                rgba(255,255,255,.10);

            color: #f2bd4b;

        }


        .umum-header-nav .logout-link {

            background:
                rgba(220,53,69,.12);

            color: #ffb3bb;

        }


        .umum-header-nav .logout-link:hover {

            background: #dc3545;

            color: #ffffff;

        }


        /* =====================================================
           MOBILE OVERLAY
        ===================================================== */

        .umum-sidebar-overlay {

            position: fixed;

            top: 0;
            left: 0;
            right: 0;
            bottom: 0;

            display: none;

            background:
                rgba(0,0,0,.45);

            z-index: 1500;

        }


        .umum-sidebar-overlay.show {

            display: block;

        }


        /* =====================================================
           MOBILE
        ===================================================== */

        @media (max-width: 768px) {

            .umum-header {

                height: 64px;

                padding:
                    0 12px;

            }


            .umum-header-left {

                gap: 9px;

            }


            .umum-sidebar-toggle {

                width: 38px;

                height: 38px;

            }


            .umum-logo {

                width: 36px;

                height: 36px;

            }


            .umum-header-title h1 {

                font-size: 12px;

                max-width:
                    190px;

            }


            .umum-header-title span {

                font-size: 8px;

            }


            .umum-header-nav {

                gap: 3px;

            }


            .umum-header-nav a {

                width: 36px;

                height: 36px;

                min-height: 36px;

                padding: 0;

                font-size: 14px;

            }


            .umum-header-nav a span {

                display: none;

            }

        }


        /* =====================================================
           MOBILE KECIL
        ===================================================== */

        @media (max-width: 480px) {

            .umum-header {

                padding:
                    0 9px;

            }


            .umum-header-title h1 {

                max-width:
                    145px;

                font-size:
                    11px;

            }


            .umum-header-title span {

                display:
                    none;

            }


            .umum-logo {

                width:
                    33px;

                height:
                    33px;

            }


            .umum-sidebar-toggle {

                width:
                    36px;

                height:
                    36px;

            }


            .umum-header-nav a {

                width:
                    34px;

                height:
                    34px;

            }

        }


    </style>

</head>


<body>


<header class="umum-header">


    <!-- =================================================
         HEADER LEFT
    ================================================= -->

    <div class="umum-header-left">


        <!-- Hamburger -->

        <button
            type="button"
            class="umum-sidebar-toggle"
            id="umumSidebarToggle"
            aria-label="Buka menu"
            aria-expanded="false"
        >

            <i
                class="fa-solid fa-bars"
                id="umumSidebarToggleIcon"
            ></i>

        </button>


        <!-- Logo -->

        <img
            src="../assets/gambar/logo.png"
            alt="Logo DPRD Banggai Kepulauan"
            class="umum-logo"
        >


        <!-- Title -->

        <div class="umum-header-title">

            <h1>
                Aplikasi Pengajuan Perjalanan Dinas DPRD Kepulauan Banggai
            </h1>

            <span>
                Bagian Umum
            </span>

        </div>


    </div>


    <!-- =================================================
         NAVIGATION
    ================================================= -->

    <nav class="umum-header-nav">


        <a
            href="profil.php"
            title="Profil"
        >

            <i class="fa-solid fa-user"></i>

            <span>
                Profil
            </span>

        </a>


        <a
            href="../logout.php"
            class="logout-link"
            id="umumLogout"
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
     MOBILE SIDEBAR OVERLAY
===================================================== -->

<div
    class="umum-sidebar-overlay"
    id="umumSidebarOverlay"
></div>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {


        const toggle =
            document.getElementById(
                'umumSidebarToggle'
            );


        const icon =
            document.getElementById(
                'umumSidebarToggleIcon'
            );


        const overlay =
            document.getElementById(
                'umumSidebarOverlay'
            );


        const sidebar =
            document.querySelector(
                '.sidebar-umum'
            );


        const logout =
            document.getElementById(
                'umumLogout'
            );


        /* =================================================
           BUKA SIDEBAR
        ================================================= */

        function openSidebar() {

            if (!sidebar) {
                return;
            }


            sidebar.classList.add('show');


            if (overlay) {

                overlay.classList.add('show');

            }


            if (icon) {

                icon.classList.remove(
                    'fa-bars'
                );

                icon.classList.add(
                    'fa-xmark'
                );

            }


            if (toggle) {

                toggle.setAttribute(
                    'aria-expanded',
                    'true'
                );

                toggle.setAttribute(
                    'aria-label',
                    'Tutup menu'
                );

            }


            document.body.style.overflow =
                'hidden';

        }


        /* =================================================
           TUTUP SIDEBAR
        ================================================= */

        function closeSidebar() {

            if (sidebar) {

                sidebar.classList.remove(
                    'show'
                );

            }


            if (overlay) {

                overlay.classList.remove(
                    'show'
                );

            }


            if (icon) {

                icon.classList.remove(
                    'fa-xmark'
                );

                icon.classList.add(
                    'fa-bars'
                );

            }


            if (toggle) {

                toggle.setAttribute(
                    'aria-expanded',
                    'false'
                );

                toggle.setAttribute(
                    'aria-label',
                    'Buka menu'
                );

            }


            document.body.style.overflow =
                '';

        }


        /* =================================================
           TOGGLE
        ================================================= */

        if (toggle) {

            toggle.addEventListener(
                'click',
                function () {

                    if (
                        sidebar &&
                        sidebar.classList.contains('show')
                    ) {

                        closeSidebar();

                    } else {

                        openSidebar();

                    }

                }
            );

        }


        /* =================================================
           OVERLAY
        ================================================= */

        if (overlay) {

            overlay.addEventListener(
                'click',
                closeSidebar
            );

        }


        /* =================================================
           MENU SIDEBAR
        ================================================= */

        document
            .querySelectorAll(
                '.sidebar-umum .menu-link'
            )
            .forEach(function (link) {

                link.addEventListener(
                    'click',
                    function () {

                        if (
                            window.innerWidth <= 768
                        ) {

                            closeSidebar();

                        }

                    }
                );

            });


        /* =================================================
           ESC
        ================================================= */

        document.addEventListener(
            'keydown',
            function (event) {

                if (
                    event.key === 'Escape'
                ) {

                    closeSidebar();

                }

            }
        );


        /* =================================================
           RESIZE
        ================================================= */

        window.addEventListener(
            'resize',
            function () {

                if (
                    window.innerWidth > 768
                ) {

                    closeSidebar();

                }

            }
        );


        /* =================================================
           LOGOUT CONFIRMATION
        ================================================= */

        if (logout) {

            logout.addEventListener(
                'click',
                function (event) {

                    event.preventDefault();


                    Swal.fire({

                        title: 'Logout?',

                        text:
                            'Apakah Anda yakin ingin keluar dari sistem?',

                        icon: 'warning',

                        showCancelButton: true,

                        confirmButtonText:
                            'Ya, Logout',

                        cancelButtonText:
                            'Batal',

                        confirmButtonColor:
                            '#dc3545',

                        cancelButtonColor:
                            '#6c757d'

                    }).then(function (result) {

                        if (
                            result.isConfirmed
                        ) {

                            window.location.href =
                                '../logout.php';

                        }

                    });

                }
            );

        }


    }
);

</script>
