<aside class="sidebar-sekwan">

    <div class="sidebar-title">

        <div class="sidebar-title-icon">
            <i class="fa-solid fa-building-columns"></i>
        </div>

        <div>
            <h3>Menu Sekwan</h3>
            <span>Sekretariat DPRD</span>
        </div>

    </div>


    <ul>

        <li>
            <a
                href="sekwan_dashboard.php"
                class="menu-link"
            >
                <span class="icon">
                    <i class="fa-solid fa-gauge-high"></i>
                </span>

                <span>Dashboard</span>
            </a>
        </li>


        <li>
            <a
                href="sekwan_paraf.php"
                class="menu-link"
            >
                <span class="icon">
                    <i class="fa-solid fa-file-signature"></i>
                </span>

                <span>Paraf Draft SPPD</span>
            </a>
        </li>


        <li>
            <a
                href="laporan_sekwan.php"
                class="menu-link"
            >
                <span class="icon">
                    <i class="fa-solid fa-file-lines"></i>
                </span>

                <span>Laporan</span>
            </a>
        </li>


        <li class="logout-item">

            <a
                href="../logout.php"
                class="logout menu-link"
            >
                <span class="icon">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </span>

                <span>Logout</span>
            </a>

        </li>

    </ul>

</aside>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>

    /* =====================================================
       AKTIFKAN MENU BERDASARKAN HALAMAN SAAT INI
    ===================================================== */

    const currentPage =
        window.location.pathname
            .split("/")
            .pop();

    document
        .querySelectorAll(".menu-link")
        .forEach(function(link) {

            if (
                link.getAttribute("href") === currentPage
            ) {
                link.classList.add("active");
            }

        });


    /* =====================================================
       KONFIRMASI LOGOUT
    ===================================================== */

    const logoutBtn =
        document.querySelector(".logout");

    if (logoutBtn) {

        logoutBtn.addEventListener(
            "click",
            function(e) {

                e.preventDefault();

                Swal.fire({

                    title: "Yakin ingin logout?",

                    text: "Anda akan keluar dari akun Sekwan.",

                    icon: "warning",

                    showCancelButton: true,

                    confirmButtonColor: "#10294d",

                    cancelButtonColor: "#dc3545",

                    confirmButtonText: "Ya, Logout",

                    cancelButtonText: "Batal",

                    reverseButtons: true

                }).then(function(result) {

                    if (result.isConfirmed) {

                        window.location.href =
                            logoutBtn.href;

                    }

                });

            }
        );

    }

</script>


<style>

/* =====================================================
   SIDEBAR
===================================================== */

.sidebar-sekwan {

    position: fixed;

    top: 72px;

    left: 0;

    bottom: 0;

    width: 250px;

    background: #10294d;

    color: #fff;

    padding: 18px 14px;

    box-shadow:
        3px 0 12px
        rgba(0, 0, 0, .12);

    z-index: 900;

    overflow-y: auto;

}


/* =====================================================
   HEADER SIDEBAR
===================================================== */

.sidebar-title {

    display: flex;

    align-items: center;

    gap: 12px;

    padding:
        6px 8px 18px;

    margin-bottom: 12px;

    border-bottom:
        1px solid
        rgba(255,255,255,.12);

}


.sidebar-title-icon {

    width: 40px;

    height: 40px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #17477f;

    color: #f2bd4b;

    border-radius: 10px;

    font-size: 17px;

    flex-shrink: 0;

}


.sidebar-title h3 {

    margin: 0;

    font-size: 16px;

    font-weight: 700;

}


.sidebar-title span {

    display: block;

    margin-top: 3px;

    color:
        rgba(255,255,255,.62);

    font-size: 11px;

}


/* =====================================================
   LIST MENU
===================================================== */

.sidebar-sekwan ul {

    list-style: none;

    padding: 0;

    margin: 0;

}


.sidebar-sekwan li {

    margin-bottom: 5px;

}


/* =====================================================
   MENU LINK
===================================================== */

.sidebar-sekwan .menu-link {

    display: flex;

    align-items: center;

    width: 100%;

    min-height: 46px;

    padding:
        11px 12px;

    gap: 12px;

    color:
        rgba(255,255,255,.82);

    text-decoration: none;

    border-radius: 9px;

    font-size: 14px;

    font-weight: 500;

    transition:
        background .2s ease,
        color .2s ease,
        transform .2s ease;

}


/* =====================================================
   ICON
===================================================== */

.sidebar-sekwan .icon {

    width: 24px;

    min-width: 24px;

    text-align: center;

    font-size: 16px;

}


/* =====================================================
   HOVER
===================================================== */

.sidebar-sekwan .menu-link:hover {

    background:
        rgba(255,255,255,.09);

    color: #fff;

    transform:
        translateX(2px);

}


/* =====================================================
   ACTIVE
===================================================== */

.sidebar-sekwan .menu-link.active {

    background: #17477f;

    color: #f2bd4b;

    font-weight: 700;

    box-shadow:
        inset 3px 0 0 #f2bd4b;

}


.sidebar-sekwan .menu-link.active .icon {

    color: #f2bd4b;

}


/* =====================================================
   LOGOUT
===================================================== */

.sidebar-sekwan .logout {

    color: #ffb8be;

    margin-top: 8px;

}


.sidebar-sekwan .logout .icon {

    color: #ff8f98;

}


.sidebar-sekwan .logout:hover {

    background:
        rgba(220,53,69,.16);

    color: #fff;

    transform:
        translateX(2px);

}


.sidebar-sekwan .logout-item {

    margin-top: 14px;

    padding-top: 14px;

    border-top:
        1px solid
        rgba(255,255,255,.12);

}


/* =====================================================
   SCROLLBAR
===================================================== */

.sidebar-sekwan::-webkit-scrollbar {

    width: 5px;

}


.sidebar-sekwan::-webkit-scrollbar-track {

    background: transparent;

}


.sidebar-sekwan::-webkit-scrollbar-thumb {

    background:
        rgba(255,255,255,.18);

    border-radius: 10px;

}


/* =====================================================
   RESPONSIVE
===================================================== */

@media (max-width: 900px) {

    .sidebar-sekwan {

        width: 220px;

    }

}


@media (max-width: 768px) {

    .sidebar-sekwan {

        width: 68px;

        padding:
            14px 8px;

    }


    .sidebar-title {

        justify-content: center;

        padding:
            4px 0 15px;

    }


    .sidebar-title > div:last-child {

        display: none;

    }


    .sidebar-title-icon {

        width: 42px;

        height: 42px;

    }


    .sidebar-sekwan .menu-link {

        justify-content: center;

        padding:
            11px 5px;

    }


    .sidebar-sekwan .menu-link > span:last-child {

        display: none;

    }


    .sidebar-sekwan .icon {

        width: auto;

        min-width: 0;

        font-size: 17px;

    }

}


@media (max-width: 480px) {

    .sidebar-sekwan {

        top: 64px;

        width: 60px;

    }


    .sidebar-title-icon {

        width: 38px;

        height: 38px;

        font-size: 16px;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    .sidebar-sekwan {

        display: none !important;

    }

}

</style>
