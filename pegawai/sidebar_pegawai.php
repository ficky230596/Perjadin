<!-- Overlay Sidebar Mobile -->
<div class="pegawai-sidebar-overlay" id="sidebarOverlay"></div>

<!-- Tombol Hamburger -->
<button type="button" id="hamburger-btn" aria-label="Buka menu">
    <i class="fa-solid fa-bars"></i>
    <span>Menu</span>
</button>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fa-solid fa-user-tie"></i>
            </div>

            <div>
                <h3>Menu Pegawai</h3>
                <span>Sistem Perjadin</span>
            </div>
        </div>

        <button type="button" class="sidebar-close" id="sidebarClose" aria-label="Tutup menu">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>

    <div class="sidebar-menu">

        <a href="pegawai_dashboard.php" class="menu-link">
            <span class="menu-icon">
                <i class="fa-solid fa-gauge-high"></i>
            </span>
            <span>Dashboard</span>
        </a>

        <a href="ajukan.php" class="menu-link">
            <span class="menu-icon">
                <i class="fa-solid fa-file-circle-plus"></i>
            </span>
            <span>Ajukan Perjadin</span>
        </a>

        <a href="riwayat_pegawai.php" class="menu-link">
            <span class="menu-icon">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </span>
            <span>Riwayat Pengajuan</span>
        </a>

    </div>

    <div class="sidebar-bottom">
        <a href="../logout.php" class="menu-link logout" id="logoutBtn">
            <span class="menu-icon">
                <i class="fa-solid fa-right-from-bracket"></i>
            </span>
            <span>Logout</span>
        </a>
    </div>

</aside>


<style>
    :root {
        --pegawai-navy: #10294d;
        --pegawai-blue: #17477f;
        --pegawai-gold: #f2bd4b;
        --pegawai-light: #f4f7fc;
        --pegawai-text: #34445a;

        --sidebar-width: 250px;
        --header-height: 89px;
    }

    /* =========================
       SIDEBAR DESKTOP
       ========================= */

    .sidebar {
        position: fixed;
        top: var(--header-height);
        left: 0;
        bottom: 0;

        width: var(--sidebar-width);

        background: #ffffff;
        border-right: 1px solid #e5eaf2;

        box-shadow: 3px 0 15px rgba(16, 41, 77, 0.06);

        padding: 20px 14px 15px;

        box-sizing: border-box;

        overflow-y: auto;

        z-index: 1500;

        display: flex;
        flex-direction: column;

        transition: transform 0.3s ease;
    }


    /* =========================
       SIDEBAR HEADER
       ========================= */

    .sidebar-header {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 5px 7px 18px;

        margin-bottom: 10px;

        border-bottom: 1px solid #edf0f5;
    }

    .sidebar-brand {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .sidebar-brand-icon {
        width: 40px;
        height: 40px;

        display: flex;
        align-items: center;
        justify-content: center;

        background: linear-gradient(135deg,
                var(--pegawai-navy),
                var(--pegawai-blue));

        color: var(--pegawai-gold);

        border-radius: 10px;

        font-size: 17px;

        flex-shrink: 0;
    }

    .sidebar-brand h3 {
        margin: 0;

        color: var(--pegawai-navy);

        font-size: 14px;
        font-weight: 800;
    }

    .sidebar-brand span {
        display: block;

        margin-top: 2px;

        color: #8793a5;

        font-size: 10px;
        font-weight: 500;
    }


    /* =========================
       CLOSE BUTTON
       ========================= */

    .sidebar-close {
        display: none;

        width: 34px;
        height: 34px;

        border: 0;
        border-radius: 8px;

        background: #f4f6f9;

        color: #657286;

        cursor: pointer;

        align-items: center;
        justify-content: center;

        font-size: 17px;

        transition: 0.2s ease;
    }

    .sidebar-close:hover {
        background: #fff0d0;
        color: var(--pegawai-navy);
    }


    /* =========================
       MENU
       ========================= */

    .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .menu-link {
        position: relative;

        display: flex;
        align-items: center;

        gap: 12px;

        width: 100%;

        min-height: 46px;

        padding: 10px 12px;

        box-sizing: border-box;

        text-decoration: none;

        color: var(--pegawai-text);

        border-radius: 9px;

        font-size: 13px;
        font-weight: 600;

        transition:
            background 0.2s ease,
            color 0.2s ease,
            transform 0.2s ease;
    }

    .menu-link:hover {
        background: #f4f7fb;
        color: var(--pegawai-navy);

        transform: translateX(2px);
    }

    .menu-icon {
        width: 22px;

        display: flex;
        align-items: center;
        justify-content: center;

        color: #718096;

        font-size: 15px;

        flex-shrink: 0;
    }

    .menu-link:hover .menu-icon {
        color: var(--pegawai-navy);
    }


    /* =========================
       MENU AKTIF
       ========================= */

    .menu-link.active {
        background: #fff5df;

        color: var(--pegawai-navy);

        font-weight: 700;
    }

    .menu-link.active::before {
        content: "";

        position: absolute;

        left: 0;
        top: 8px;
        bottom: 8px;

        width: 4px;

        background: var(--pegawai-gold);

        border-radius: 0 4px 4px 0;
    }

    .menu-link.active .menu-icon {
        color: #d99d20;
    }


    /* =========================
       LOGOUT
       ========================= */

    .sidebar-bottom {
        margin-top: auto;

        padding-top: 15px;

        border-top: 1px solid #edf0f5;
    }

    .menu-link.logout {
        color: #c0392b;
    }

    .menu-link.logout .menu-icon {
        color: #c0392b;
    }

    .menu-link.logout:hover {
        background: #fff1ef;
        color: #b52e20;
    }


    /* =========================
       OVERLAY
       ========================= */

    .pegawai-sidebar-overlay {
        display: none;
    }


    /* =========================
       HAMBURGER
       ========================= */

    #hamburger-btn {
        display: none;

        position: fixed;

        /* PENTING:
           Tombol sekarang berada di bawah header */
        top: calc(var(--header-height) + 12px);
        left: 12px;

        height: 42px;

        padding: 0 14px;

        align-items: center;
        justify-content: center;

        gap: 8px;

        border: none;
        border-radius: 9px;

        background: var(--pegawai-navy);
        color: #ffffff;

        font-size: 13px;
        font-weight: 700;

        box-shadow: 0 5px 15px rgba(16, 41, 77, 0.22);

        cursor: pointer;

        z-index: 2100;

        transition:
            background 0.2s ease,
            transform 0.2s ease;
    }

    #hamburger-btn i {
        color: var(--pegawai-gold);
        font-size: 17px;
    }

    #hamburger-btn:hover {
        background: var(--pegawai-blue);

        transform: translateY(-1px);
    }


    /* =========================
       MOBILE
       ========================= */

    @media (max-width: 768px) {

        :root {
            --header-height: 112px;
        }

        /* Sidebar tersembunyi */
        .sidebar {
            top: var(--header-height);
            bottom: 0;

            width: min(280px, 85vw);

            height: calc(100vh - var(--header-height));
            height: calc(100dvh - var(--header-height));

            transform: translateX(-105%);

            visibility: hidden;

            z-index: 2050;

            padding: 15px 13px;
        }

        /* Sidebar dibuka */
        .sidebar.active {
            transform: translateX(0);

            visibility: visible;
        }

        /* Close button muncul */
        .sidebar-close {
            display: flex;
        }

        /* Hamburger muncul */
        #hamburger-btn {
            display: flex;
        }

        /* Overlay */
        .pegawai-sidebar-overlay {
            display: block;

            position: fixed;

            top: var(--header-height);
            left: 0;
            right: 0;
            bottom: 0;

            background: rgba(8, 24, 45, 0.45);

            opacity: 0;
            visibility: hidden;

            z-index: 1400;

            transition:
                opacity 0.25s ease,
                visibility 0.25s ease;
        }

        .pegawai-sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-header {
            padding-top: 3px;
        }
    }


    /* =========================
       MOBILE KECIL
       ========================= */

    @media (max-width: 480px) {

        #hamburger-btn {
            top: calc(var(--header-height) + 10px);
            left: 10px;

            height: 40px;

            padding: 0 12px;
        }

        .sidebar {
            width: min(275px, 88vw);
        }
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function () {

        const hamburgerBtn = document.getElementById('hamburger-btn');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');
        const sidebarClose = document.getElementById('sidebarClose');

        /* =========================
           BUKA SIDEBAR
           ========================= */

        function openSidebar() {
            if (!sidebar) return;

            sidebar.classList.add('active');

            if (sidebarOverlay) {
                sidebarOverlay.classList.add('active');
            }

            document.body.style.overflow = 'hidden';
        }


        /* =========================
           TUTUP SIDEBAR
           ========================= */

        function closeSidebar() {
            if (!sidebar) return;

            sidebar.classList.remove('active');

            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }

            document.body.style.overflow = '';
        }


        /* =========================
           HAMBURGER
           ========================= */

        if (hamburgerBtn) {
            hamburgerBtn.addEventListener('click', function () {
                if (sidebar && sidebar.classList.contains('active')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }


        /* =========================
           CLOSE
           ========================= */

        if (sidebarClose) {
            sidebarClose.addEventListener('click', closeSidebar);
        }


        /* =========================
           OVERLAY
           ========================= */

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }


        /* =========================
           ESCAPE
           ========================= */

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                closeSidebar();
            }
        });


        /* =========================
           MENU AKTIF
           ========================= */

        const currentPage = window.location.pathname
            .split('/')
            .pop()
            .toLowerCase();

        document.querySelectorAll('.menu-link').forEach(function (link) {

            const href = link.getAttribute('href');

            if (!href) return;

            const linkPage = href
                .split('/')
                .pop()
                .split('?')[0]
                .toLowerCase();

            if (linkPage === currentPage) {
                link.classList.add('active');
            }
        });


        /* =========================
           KLIK MENU MOBILE
           ========================= */

        document.querySelectorAll('.sidebar .menu-link').forEach(function (link) {

            link.addEventListener('click', function () {

                if (window.innerWidth <= 768 && !link.classList.contains('logout')) {
                    closeSidebar();
                }

            });

        });


        /* =========================
           RESIZE
           ========================= */

        window.addEventListener('resize', function () {

            if (window.innerWidth > 768) {
                closeSidebar();
            }

        });

    });
</script>