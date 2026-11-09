<style>
    /* SIDEBAR KETUA */
    .sidebar-ketua {
        position: fixed;
        top: 89px;
        left: 0;
        z-index: 1100;
        width: 250px;
        height: calc(100vh - 89px);
        height: calc(100dvh - 89px);
        overflow-y: auto;
        overflow-x: hidden;
        padding: 24px 0;
        background: linear-gradient(180deg, #10294d 0%, #17477f 100%);
        color: #fff;
        box-shadow: 4px 0 18px rgba(16, 41, 77, 0.12);
        transition: transform 0.25s ease, visibility 0.25s ease;
    }

    /* JUDUL SIDEBAR */
    .sidebar-ketua h3 {
        margin: 0 18px 28px;
        padding: 0 10px 20px;
        text-align: center;
        font-size: 15px;
        letter-spacing: 1.2px;
        font-weight: 800;
        color: #fff;
        border-bottom: 1px solid rgba(255, 255, 255, 0.14);
    }

    /* DAFTAR MENU */
    .sidebar-ketua ul {
        list-style: none;
        padding: 0 12px;
        margin: 0;
    }

    .sidebar-ketua ul li {
        margin: 0 0 7px;
    }

    /* TAUTAN MENU */
    .sidebar-ketua ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 46px;
        padding: 12px 15px;
        border: 1px solid transparent;
        border-left: 3px solid transparent;
        border-radius: 9px;
        color: #dce7f5;
        text-decoration: none;
        font-size: 13px;
        font-weight: 500;
        transition: background 0.2s, border-color 0.2s, color 0.2s;
    }

    .sidebar-ketua ul li a .sidebar-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        flex-shrink: 0;
        color: #f2bd4b;
        font-size: 15px;
    }

    /* HOVER DAN MENU AKTIF */
    .sidebar-ketua ul li a:hover,
    .sidebar-ketua ul li a.active {
        background: rgba(255, 255, 255, 0.10);
        border-left-color: #f2bd4b;
        color: #fff;
    }

    .sidebar-ketua ul li a.active {
        background: linear-gradient(90deg,
                rgba(242, 189, 75, 0.18),
                rgba(255, 255, 255, 0.05));
        font-weight: 700;
    }

    .sidebar-ketua ul li a:hover .sidebar-icon,
    .sidebar-ketua ul li a.active .sidebar-icon {
        color: #f2bd4b;
    }

    /* MENU LOGOUT */
    .sidebar-ketua ul li a.sidebar-logout {
        margin-top: 18px;
        color: #ffb4b4;
    }

    .sidebar-ketua ul li a.sidebar-logout .sidebar-icon {
        color: #ff8585;
    }

    .sidebar-ketua ul li a.sidebar-logout:hover {
        background: rgba(255, 118, 117, 0.12);
        border-left-color: #ff7675;
        color: #fff;
    }

    /* LAPISAN GELAP DI BELAKANG SIDEBAR */
    .sidebar-overlay-ketua {
        display: none;
    }

    /* KONTEN HALAMAN */
    main {
        min-width: 0;
        margin-left: 250px;
        padding: 25px;
    }

    /* RESPONSIVE TABLET DAN HP */
    @media (max-width: 800px) {
        .sidebar-ketua {
            top: 76px;
            left: 0;
            z-index: 1150;
            width: 270px;
            max-width: 85vw;
            height: calc(100vh - 76px);
            height: calc(100dvh - 76px);
            padding-top: 22px;
            transform: translateX(-100%);
            visibility: hidden;
        }

        .sidebar-ketua.show {
            transform: translateX(0);
            visibility: visible;
        }

        .sidebar-ketua h3 {
            margin-bottom: 22px;
        }

        .sidebar-overlay-ketua.show {
            display: block;
            position: fixed;
            inset: 76px 0 0;
            z-index: 1140;
            background: rgba(10, 24, 43, 0.48);
        }

        main {
            margin-left: 0 !important;
            padding: 22px 16px !important;
        }
    }
</style>

<!-- SIDEBAR -->
<aside class="sidebar-ketua" id="sidebarKetua">
    <h3>MENU KETUA</h3>

    <ul>
        <li>
            <a href="ketua_dashboard.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'ketua_dashboard.php' ? 'active' : '' ?>">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-house"></i>
                </span>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="ketua_ttd.php" class="<?= basename($_SERVER['PHP_SELF']) === 'ketua_ttd.php' ? 'active' : '' ?>">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-file-signature"></i>
                </span>
                <span>Tanda Tangan</span>
            </a>
        </li>

        <li>
            <a href="laporan_ketua.php"
                class="<?= basename($_SERVER['PHP_SELF']) === 'laporan_ketua.php' ? 'active' : '' ?>">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-chart-column"></i>
                </span>
                <span>Laporan</span>
            </a>
        </li>

        <li>
            <a href="../logout.php" class="sidebar-logout">
                <span class="sidebar-icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </span>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</aside>

<!-- OVERLAY MOBILE -->
<div class="sidebar-overlay-ketua" id="sidebarOverlayKetua"></div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebar = document.getElementById('sidebarKetua');
        const toggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlayKetua');

        if (!sidebar || !toggle || !overlay) {
            return;
        }

        function bukaSidebar() {
            sidebar.classList.add('show');
            overlay.classList.add('show');
            toggle.setAttribute('aria-expanded', 'true');

            const icon = toggle.querySelector('i');

            if (icon) {
                icon.classList.remove('fa-bars');
                icon.classList.add('fa-xmark');
            }
        }

        function tutupSidebar() {
            sidebar.classList.remove('show');
            overlay.classList.remove('show');
            toggle.setAttribute('aria-expanded', 'false');

            const icon = toggle.querySelector('i');

            if (icon) {
                icon.classList.remove('fa-xmark');
                icon.classList.add('fa-bars');
            }
        }

        toggle.addEventListener('click', function () {
            if (sidebar.classList.contains('show')) {
                tutupSidebar();
            } else {
                bukaSidebar();
            }
        });

        overlay.addEventListener('click', tutupSidebar);

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                tutupSidebar();
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth > 800) {
                tutupSidebar();
            }
        });
    });
</script>