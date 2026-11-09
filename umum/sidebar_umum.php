<?php

$current_page = basename($_SERVER['PHP_SELF']);

?>

<aside class="sidebar-umum">

    <!-- =================================================
         SIDEBAR HEADER
    ================================================= -->

    <div class="sidebar-umum-header">

        <div class="sidebar-umum-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>

        <div class="sidebar-umum-title">

            <h3>
                Menu Bagian Umum
            </h3>

            <span>
                Sekretariat DPRD
            </span>

        </div>

    </div>


    <!-- =================================================
         MENU
    ================================================= -->

    <ul class="sidebar-umum-menu">


        <!-- Dashboard -->

        <li>

            <a
                href="umum_dashboard.php"
                class="menu-link <?php echo ($current_page === 'umum_dashboard.php') ? 'active' : ''; ?>"
            >

                <span class="menu-icon">
                    <i class="fa-solid fa-gauge-high"></i>
                </span>

                <span class="menu-text">
                    Dashboard
                </span>

            </a>

        </li>


        <!-- Buat Draft SPPD -->

        <li>

            <a
                href="umum_pengajuan.php"
                class="menu-link <?php echo ($current_page === 'umum_pengajuan.php') ? 'active' : ''; ?>"
            >

                <span class="menu-icon">
                    <i class="fa-solid fa-file-circle-plus"></i>
                </span>

                <span class="menu-text">
                    Buat Draft SPPD
                </span>

            </a>

        </li>


        <!-- Cap SPPD -->

        <li>

            <a
                href="umum_list_pengajuan.php"
                class="menu-link <?php echo ($current_page === 'umum_list_pengajuan.php') ? 'active' : ''; ?>"
            >

                <span class="menu-icon">
                    <i class="fa-solid fa-stamp"></i>
                </span>

                <span class="menu-text">
                    Cap SPPD
                </span>

            </a>

        </li>


        <!-- Management User -->

        <li>

            <a
                href="Management_User.php"
                class="menu-link <?php echo ($current_page === 'Management_User.php') ? 'active' : ''; ?>"
            >

                <span class="menu-icon">
                    <i class="fa-solid fa-users-gear"></i>
                </span>

                <span class="menu-text">
                    User
                </span>

            </a>

        </li>


        <!-- Logout -->

        <li class="logout-item">

            <a
                href="../logout.php"
                id="logoutBtn"
                class="menu-link logout"
            >

                <span class="menu-icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </span>

                <span class="menu-text">
                    Logout
                </span>

            </a>

        </li>


    </ul>

</aside>


<style>

/* =====================================================
   SIDEBAR UMUM
===================================================== */

.sidebar-umum {

    position: fixed;

    top: 72px;

    left: 0;

    bottom: 0;

    width: 250px;

    padding: 18px 12px;

    background: #ffffff;

    border-right: 1px solid #e3e9f0;

    box-shadow:
        3px 0 14px
        rgba(16, 41, 77, .06);

    overflow-y: auto;

    z-index: 1800;

    font-family:
        "Inter",
        "Segoe UI",
        Arial,
        sans-serif;

}


/* =====================================================
   HEADER SIDEBAR
===================================================== */

.sidebar-umum-header {

    display: flex;

    align-items: center;

    gap: 10px;

    padding:
        4px 7px 17px;

    margin-bottom: 8px;

    border-bottom:
        1px solid #edf1f5;

}


.sidebar-umum-icon {

    width: 38px;

    height: 38px;

    min-width: 38px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #10294d;

    color: #f2bd4b;

    border-radius: 9px;

    font-size: 15px;

}


.sidebar-umum-title {

    min-width: 0;

}


.sidebar-umum-title h3 {

    margin: 0;

    color: #10294d;

    font-size: 13px;

    font-weight: 750;

    line-height: 1.3;

}


.sidebar-umum-title span {

    display: block;

    margin-top: 2px;

    color: #94a3b8;

    font-size: 9px;

    font-weight: 500;

}


/* =====================================================
   LIST MENU
===================================================== */

.sidebar-umum-menu {

    list-style: none;

    padding: 0;

    margin: 0;

}


.sidebar-umum-menu li {

    margin-bottom: 4px;

}


/* =====================================================
   LINK
===================================================== */

.sidebar-umum .menu-link {

    position: relative;

    display: flex;

    align-items: center;

    gap: 11px;

    width: 100%;

    min-height: 44px;

    padding: 9px 11px;

    border-radius: 9px;

    color: #475569;

    text-decoration: none;

    font-size: 12px;

    font-weight: 600;

    transition:
        background .2s ease,
        color .2s ease,
        transform .2s ease;

}


.sidebar-umum .menu-link:hover {

    background: #f1f5f9;

    color: #10294d;

    transform:
        translateX(2px);

}


/* =====================================================
   ICON
===================================================== */

.sidebar-umum .menu-icon {

    width: 32px;

    height: 32px;

    min-width: 32px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 8px;

    background: #f1f5f9;

    color: #17477f;

    font-size: 13px;

    transition:
        background .2s ease,
        color .2s ease;

}


/* =====================================================
   ACTIVE
===================================================== */

.sidebar-umum .menu-link.active {

    background:
        linear-gradient(
            90deg,
            #10294d,
            #17477f
        );

    color: #ffffff;

    box-shadow:
        0 4px 10px
        rgba(16, 41, 77, .16);

}


.sidebar-umum .menu-link.active::before {

    content: "";

    position: absolute;

    left: 0;

    top: 8px;

    bottom: 8px;

    width: 3px;

    border-radius:
        0 4px 4px 0;

    background: #f2bd4b;

}


.sidebar-umum .menu-link.active .menu-icon {

    background:
        rgba(242, 189, 75, .18);

    color: #f2bd4b;

}


/* =====================================================
   LOGOUT
===================================================== */

.sidebar-umum .logout {

    color: #b4232f;

}


.sidebar-umum .logout .menu-icon {

    background: #fff0f1;

    color: #dc3545;

}


.sidebar-umum .logout:hover {

    background: #fff0f1;

    color: #dc3545;

}


.sidebar-umum .logout:hover .menu-icon {

    background: #dc3545;

    color: #ffffff;

}


/* =====================================================
   PEMISAH LOGOUT
===================================================== */

.sidebar-umum .logout-item {

    margin-top: 18px;

    padding-top: 12px;

    border-top:
        1px solid #edf1f5;

}


/* =====================================================
   SCROLLBAR
===================================================== */

.sidebar-umum::-webkit-scrollbar {

    width: 5px;

}


.sidebar-umum::-webkit-scrollbar-track {

    background: transparent;

}


.sidebar-umum::-webkit-scrollbar-thumb {

    background: #cbd5e1;

    border-radius: 10px;

}


/* =====================================================
   MOBILE OFF-CANVAS
===================================================== */

@media (max-width: 768px) {

    .sidebar-umum {

        top: 64px;

        left: 0;

        bottom: 0;

        width: 250px;

        transform:
            translateX(-100%);

        transition:
            transform .25s ease;

        box-shadow:
            5px 0 20px
            rgba(0, 0, 0, .15);

    }


    .sidebar-umum.show {

        transform:
            translateX(0);

    }

}


/* =====================================================
   MOBILE KECIL
===================================================== */

@media (max-width: 480px) {

    .sidebar-umum {

        width: 245px;

        padding:
            15px 10px;

    }


    .sidebar-umum .menu-link {

        min-height: 42px;

        font-size: 11px;

    }


    .sidebar-umum .menu-icon {

        width: 30px;

        height: 30px;

        min-width: 30px;

        font-size: 12px;

    }

}

</style>


<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        const logoutBtn =
            document.getElementById('logoutBtn');


        if (!logoutBtn) {
            return;
        }


        logoutBtn.addEventListener(
            'click',
            function (e) {

                e.preventDefault();


                Swal.fire({

                    title:
                        'Yakin ingin keluar?',

                    text:
                        'Anda akan logout dari sistem!',

                    icon:
                        'warning',

                    showCancelButton:
                        true,

                    confirmButtonColor:
                        '#dc3545',

                    cancelButtonColor:
                        '#6c757d',

                    confirmButtonText:
                        'Ya, Logout',

                    cancelButtonText:
                        'Batal',

                    reverseButtons:
                        true

                }).then(
                    function (result) {

                        if (
                            result.isConfirmed
                        ) {

                            window.location.href =
                                '../logout.php';

                        }

                    }
                );

            }
        );

    }
);

</script>