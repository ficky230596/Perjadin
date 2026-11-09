
<footer class="footer-ketua">
    <div class="footer-content">
        <span>
            &copy; <?= date('Y') ?> Aplikasi Dinas Ketua.
            All rights reserved.
        </span>

        <span class="footer-label">
            <i class="fa-solid fa-landmark"></i>
            Sistem Perjalanan Dinas DPRD
        </span>
    </div>
</footer>

<style>
    html,
    body {
        min-height: 100%;
        margin: 0;
    }

    body {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .footer-ketua {
        margin-left: 250px;
        width: calc(100% - 250px);
        margin-top: auto;
        box-sizing: border-box;
        padding: 20px 25px;
        background: #ffffff;
        border-top: 3px solid #f2bd4b;
        color: #64748b;
        font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
        font-size: 12px;
        flex-shrink: 0;
    }

    .footer-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px 20px;
    }

    .footer-label {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #17477f;
        font-weight: 600;
    }

    .footer-label i {
        color: #c9972e;
    }

    @media (max-width: 800px) {
        .footer-ketua {
            margin-left: 0;
            width: 100%;
            padding: 18px 15px;
        }

        .footer-content {
            justify-content: center;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .footer-ketua {
            font-size: 11px;
            line-height: 1.7;
        }

        .footer-content {
            flex-direction: column;
            gap: 7px;
        }
    }
</style>

</body>
</html>