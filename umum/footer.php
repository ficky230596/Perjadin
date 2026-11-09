<footer class="footer-umum">

    <div class="footer-umum-content">

        <!-- =================================================
             FOOTER LEFT
        ================================================= -->

        <div class="footer-umum-left">

            <div class="footer-umum-icon">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div class="footer-umum-info">

                <strong>
                    Aplikasi Pengajuan Perjalanan Dinas
                </strong>

                <span>
                    Sekretariat DPRD Kabupaten Banggai Kepulauan
                </span>

            </div>

        </div>


        <!-- =================================================
             FOOTER RIGHT
        ================================================= -->

        <div class="footer-umum-right">

            <span>
                &copy; 2023 Aplikasi Dinas
            </span>

            <span class="footer-umum-divider">
                •
            </span>

            <span>
                All rights reserved.
            </span>

        </div>

    </div>

</footer>


<style>

/* =====================================================
   FOOTER BAGIAN UMUM
===================================================== */

.footer-umum {

    position: fixed;

    left: 250px;

    right: 0;

    bottom: 0;

    min-height: 60px;

    background: #ffffff;

    border-top:
        1px solid #e4e9f0;

    box-shadow:
        0 -3px 12px
        rgba(16, 41, 77, .06);

    z-index: 800;

    font-family:
        "Inter",
        "Segoe UI",
        Arial,
        sans-serif;

}


/* =====================================================
   FOOTER CONTENT
===================================================== */

.footer-umum-content {

    min-height: 60px;

    padding:
        8px 24px;

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

}


/* =====================================================
   FOOTER LEFT
===================================================== */

.footer-umum-left {

    display: flex;

    align-items: center;

    gap: 10px;

    min-width: 0;

}


.footer-umum-icon {

    width: 36px;

    height: 36px;

    min-width: 36px;

    display: flex;

    align-items: center;

    justify-content: center;

    background: #10294d;

    color: #f2bd4b;

    border-radius: 8px;

    font-size: 15px;

}


.footer-umum-info {

    display: flex;

    flex-direction: column;

    min-width: 0;

}


.footer-umum-info strong {

    color: #10294d;

    font-size: 12px;

    font-weight: 700;

    line-height: 1.3;

}


.footer-umum-info span {

    display: block;

    margin-top: 2px;

    color: #64748b;

    font-size: 10px;

    line-height: 1.3;

}


/* =====================================================
   FOOTER RIGHT
===================================================== */

.footer-umum-right {

    display: flex;

    align-items: center;

    justify-content: flex-end;

    gap: 7px;

    color: #64748b;

    font-size: 10px;

    white-space: nowrap;

}


.footer-umum-divider {

    color: #f2bd4b;

    font-weight: 700;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 900px) {

    .footer-umum-content {

        padding:
            8px 18px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .footer-umum {

        left: 0;

        right: 0;

        min-height: 54px;

    }


    .footer-umum-content {

        min-height: 54px;

        padding:
            7px 12px;

    }


    .footer-umum-icon {

        width: 32px;

        height: 32px;

        min-width: 32px;

        font-size: 13px;

    }


    .footer-umum-info strong {

        font-size: 10px;

    }


    .footer-umum-info span {

        font-size: 8px;

    }


    .footer-umum-right {

        font-size: 8px;

    }

}


/* =====================================================
   MOBILE KECIL
===================================================== */

@media (max-width: 480px) {

    .footer-umum-content {

        justify-content: center;

        padding:
            8px 10px;

    }


    .footer-umum-left {

        gap: 8px;

    }


    .footer-umum-icon {

        width: 30px;

        height: 30px;

        min-width: 30px;

        font-size: 12px;

    }


    .footer-umum-info strong {

        font-size: 9px;

    }


    .footer-umum-info span {

        display: none;

    }


    .footer-umum-right {

        display: none;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    .footer-umum {

        display: none !important;

    }

}

</style>