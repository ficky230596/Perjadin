<footer class="footer-sekwan">

    <div class="footer-content">

        <div class="footer-left">

            <div class="footer-icon">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div class="footer-info">
                <strong>
                    Aplikasi Pengajuan Perjalanan Dinas
                </strong>

                <span>
                    Sekretariat DPRD Kabupaten Banggai Kepulauan
                </span>
            </div>

        </div>


        <div class="footer-right">

            <span>
                &copy; 2023 Aplikasi Dinas
            </span>

            <span class="footer-divider">
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
   FOOTER SEKWAN
===================================================== */

.footer-sekwan {

    position: fixed;

    left: 250px;

    right: 0;

    bottom: 0;

    min-height: 60px;

    background: #ffffff;

    border-top: 1px solid #e4e9f0;

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

.footer-content {

    min-height: 60px;

    padding:
        8px 24px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        space-between;

    gap:
        20px;

}


/* =====================================================
   FOOTER LEFT
===================================================== */

.footer-left {

    display:
        flex;

    align-items:
        center;

    gap:
        10px;

    min-width:
        0;

}


.footer-icon {

    width:
        36px;

    height:
        36px;

    min-width:
        36px;

    display:
        flex;

    align-items:
        center;

    justify-content:
        center;

    background:
        #10294d;

    color:
        #f2bd4b;

    border-radius:
        8px;

    font-size:
        15px;

}


.footer-info {

    display:
        flex;

    flex-direction:
        column;

    min-width:
        0;

}


.footer-info strong {

    color:
        #10294d;

    font-size:
        12px;

    font-weight:
        700;

    line-height:
        1.3;

}


.footer-info span {

    margin-top:
        2px;

    color:
        #64748b;

    font-size:
        10px;

    line-height:
        1.3;

}


/* =====================================================
   FOOTER RIGHT
===================================================== */

.footer-right {

    display:
        flex;

    align-items:
        center;

    justify-content:
        flex-end;

    gap:
        7px;

    color:
        #64748b;

    font-size:
        10px;

    white-space:
        nowrap;

}


.footer-divider {

    color:
        #f2bd4b;

    font-weight:
        700;

}


/* =====================================================
   TABLET
===================================================== */

@media (max-width: 900px) {

    .footer-content {

        padding:
            8px 18px;

    }

}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 768px) {

    .footer-sekwan {

        left:
            0;

        right:
            0;

        min-height:
            54px;

    }


    .footer-content {

        min-height:
            54px;

        padding:
            7px 12px;

    }


    .footer-icon {

        width:
            32px;

        height:
            32px;

        min-width:
            32px;

        font-size:
            13px;

    }


    .footer-info strong {

        font-size:
            10px;

    }


    .footer-info span {

        font-size:
            8px;

    }


    .footer-right {

        font-size:
            8px;

    }

}


/* =====================================================
   MOBILE KECIL
===================================================== */

@media (max-width: 480px) {

    .footer-content {

        justify-content:
            center;

        padding:
            8px 10px;

    }


    .footer-left {

        gap:
            8px;

    }


    .footer-icon {

        width:
            30px;

        height:
            30px;

        min-width:
            30px;

    }


    .footer-info strong {

        font-size:
            9px;

    }


    .footer-info span {

        display:
            none;

    }


    .footer-right {

        display:
            none;

    }

}


/* =====================================================
   PRINT
===================================================== */

@media print {

    .footer-sekwan {

        display:
            none !important;

    }

}

</style>
