<footer class="pegawai-footer">

    <div class="footer-content">

        <div class="footer-brand">
            <div class="footer-icon">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div class="footer-text">
                <p class="footer-title">
                    Aplikasi Pengajuan Perjalanan Dinas
                    <span>DPRD Kepulauan Banggai</span>
                </p>

                <p class="footer-copy">
                    &copy; 2026 Sistem Perjadin DPRD. All rights reserved.
                </p>
            </div>
        </div>

        <div class="footer-info">
            <span>
                <i class="fa-solid fa-shield-halved"></i>
                Sistem Informasi Perjalanan Dinas
            </span>
        </div>

    </div>

</footer>


<style>
    /* =========================================
       FOOTER FIXED
       ========================================= */

    .pegawai-footer {
        position: fixed;

        left: 0;
        right: 0;
        bottom: 0;

        width: 100%;

        background: linear-gradient(110deg,
                #10294d 0%,
                #17477f 100%);

        color: #ffffff;

        border-top: 3px solid #f2bd4b;

        box-sizing: border-box;

        /*
         * Footer berada di depan sidebar,
         * tetapi tetap di bawah header.
         */
        z-index: 1900;

        box-shadow: 0 -4px 18px rgba(16, 41, 77, 0.15);
    }


    /* =========================================
       FOOTER CONTENT
       ========================================= */

    .footer-content {
        width: 100%;
        max-width: 1400px;

        margin: 0 auto;

        padding: 14px 30px;

        box-sizing: border-box;

        display: flex;

        align-items: center;

        justify-content: space-between;

        gap: 25px;
    }


    /* =========================================
       BRAND
       ========================================= */

    .footer-brand {
        display: flex;

        align-items: center;

        gap: 12px;

        min-width: 0;
    }


    /* =========================================
       ICON
       ========================================= */

    .footer-icon {
        width: 40px;
        height: 40px;

        flex: 0 0 40px;

        display: flex;

        align-items: center;

        justify-content: center;

        border-radius: 9px;

        background: rgba(242, 189, 75, 0.14);

        border: 1px solid rgba(242, 189, 75, 0.5);

        color: #f2bd4b;

        font-size: 17px;
    }


    /* =========================================
       TEXT
       ========================================= */

    .footer-text {
        min-width: 0;
    }

    .footer-title {
        margin: 0;

        color: #ffffff;

        font-size: 12px;

        font-weight: 700;

        line-height: 1.4;
    }

    .footer-title span {
        font-weight: 700;
    }

    .footer-copy {
        margin: 2px 0 0;

        color: rgba(255, 255, 255, 0.68);

        font-size: 10px;

        line-height: 1.4;
    }


    /* =========================================
       FOOTER INFO
       ========================================= */

    .footer-info {
        flex-shrink: 0;
    }

    .footer-info span {
        display: flex;

        align-items: center;

        gap: 7px;

        color: rgba(255, 255, 255, 0.7);

        font-size: 10px;

        white-space: nowrap;
    }

    .footer-info i {
        color: #f2bd4b;
    }


    /* =========================================
       RUANG UNTUK FOOTER FIXED
       
       Tambahkan class ini pada wrapper
       isi halaman jika diperlukan.
       ========================================= */

    .pegawai-page-content {
        padding-bottom: 85px;
    }


    /* =========================================
       TABLET
       ========================================= */

    @media (max-width: 900px) {

        .footer-content {
            padding: 13px 20px;
        }

        .footer-info {
            display: none;
        }

        .pegawai-page-content {
            padding-bottom: 80px;
        }
    }


    /* =========================================
       MOBILE
       ========================================= */

    @media (max-width: 768px) {

        .pegawai-footer {
            z-index: 1900;
        }

        .footer-content {
            padding: 11px 15px;

            justify-content: center;

            text-align: center;
        }

        .footer-brand {
            width: 100%;

            justify-content: center;

            gap: 9px;
        }

        .footer-icon {
            width: 34px;
            height: 34px;

            flex-basis: 34px;

            border-radius: 8px;

            font-size: 14px;
        }

        .footer-title {
            font-size: 10px;

            line-height: 1.4;
        }

        .footer-copy {
            font-size: 8px;
        }

        .pegawai-page-content {
            padding-bottom: 70px;
        }
    }


    /* =========================================
       MOBILE KECIL
       ========================================= */

    @media (max-width: 480px) {

        .footer-content {
            padding: 9px 10px;
        }

        .footer-brand {
            gap: 8px;
        }

        .footer-icon {
            width: 30px;
            height: 30px;

            flex-basis: 30px;

            font-size: 12px;
        }

        .footer-title {
            font-size: 9px;
        }

        .footer-copy {
            font-size: 7px;
        }

        .pegawai-page-content {
            padding-bottom: 60px;
        }
    }
</style>

</body>

</html>