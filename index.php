<?php

session_start();

include 'config/db.php';

/*
|--------------------------------------------------------------------------
| Redirect jika sudah login
|--------------------------------------------------------------------------
*/
if (!empty($_SESSION['role'])) {

    switch ($_SESSION['role']) {

        case 'pegawai':
            header("Location: pegawai/pegawai_dashboard.php");
            exit;

        case 'umum':
            header("Location: umum/umum_dashboard.php");
            exit;

        case 'sekwan':
            header("Location: sekwan/sekwan_dashboard.php");
            exit;

        case 'ketua':
            header("Location: ketua/ketua_dashboard.php");
            exit;

        default:
            session_destroy();
            header("Location: index.php");
            exit;
    }
}


/*
|--------------------------------------------------------------------------
| Proses Login
|--------------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {

        $login_error = 'Username dan password wajib diisi.';

    } else {

        $stmt = $pdo->prepare("
            SELECT *
            FROM users
            WHERE username = ?
            LIMIT 1
        ");

        $stmt->execute([$username]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {

            $_SESSION['user_id']  = $user['id'];
            $_SESSION['role']     = $user['role'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['wa_phone'] = $user['wa_phone'];

            header("Location: index.php");
            exit;

        } else {

            $login_error = 'Username atau password salah.';
        }
    }
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

    <title>Login | Sistem Perjalanan Dinas DPRD</title>

    <!-- Font Awesome -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        :root {
            --navy-dark: #10294d;
            --navy: #17477f;
            --navy-light: #28639d;
            --gold: #f2bd4b;
            --gold-dark: #dca62f;
            --text: #1f2937;
            --muted: #64748b;
            --border: #d9e1ea;
        }


        /*
        |--------------------------------------------------------------------------
        | BODY
        |--------------------------------------------------------------------------
        */

        html,
        body {
            width: 100%;
            height: 100%;
        }

        body {

            min-height: 100vh;

            font-family:
                "Segoe UI",
                Arial,
                sans-serif;

            background:
                linear-gradient(
                    135deg,
                    #10294d 0%,
                    #17477f 55%,
                    #28639d 100%
                );

            display: flex;
            align-items: center;
            justify-content: center;

            overflow: hidden;

            padding: 20px;

            position: relative;
        }


        /*
        |--------------------------------------------------------------------------
        | BACKGROUND DECORATION
        |--------------------------------------------------------------------------
        */

        .bg-circle {

            position: absolute;

            border-radius: 50%;

            pointer-events: none;
        }

        .circle-one {

            width: 420px;
            height: 420px;

            top: -230px;
            right: -160px;

            border: 70px solid rgba(242, 189, 75, 0.08);
        }

        .circle-two {

            width: 350px;
            height: 350px;

            bottom: -210px;
            left: -170px;

            border: 60px solid rgba(255, 255, 255, 0.05);
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN CARD
        |--------------------------------------------------------------------------
        */

        .login-container {

            width: 100%;
            max-width: 400px;

            background: rgba(255, 255, 255, 0.98);

            border-radius: 18px;

            padding: 27px 32px 22px;

            text-align: center;

            position: relative;

            z-index: 5;

            box-shadow:
                0 20px 60px rgba(0, 0, 0, 0.25),
                0 5px 20px rgba(0, 0, 0, 0.10);

            animation: fadeIn 0.6s ease;
        }


        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        .logo-wrapper {

            width: 78px;
            height: 78px;

            margin: 0 auto 11px;

            border-radius: 50%;

            background: #ffffff;

            display: flex;
            align-items: center;
            justify-content: center;

            padding: 7px;

            box-shadow:
                0 4px 15px rgba(16, 41, 77, 0.14),
                0 0 0 4px rgba(242, 189, 75, 0.14);
        }

        .logo-wrapper img {

            width: 64px;
            height: 64px;

            object-fit: contain;
        }


        /*
        |--------------------------------------------------------------------------
        | TITLE
        |--------------------------------------------------------------------------
        */

        .login-title {

            color: var(--navy-dark);

            font-size: 1.05rem;

            font-weight: 800;

            line-height: 1.4;

            letter-spacing: 0.3px;
        }


        .gold-line {

            width: 45px;
            height: 3px;

            background: var(--gold);

            border-radius: 20px;

            margin: 9px auto 8px;
        }


        .login-subtitle {

            color: var(--muted);

            font-size: 0.78rem;

            line-height: 1.4;

            margin-bottom: 19px;
        }


        /*
        |--------------------------------------------------------------------------
        | FORM
        |--------------------------------------------------------------------------
        */

        .form-group {

            text-align: left;

            margin-bottom: 13px;
        }

        .form-group label {

            display: block;

            color: var(--text);

            font-size: 0.82rem;

            font-weight: 600;

            margin-bottom: 6px;
        }


        .input-wrapper {

            position: relative;
        }


        .input-icon {

            position: absolute;

            left: 13px;
            top: 50%;

            transform: translateY(-50%);

            color: var(--navy);

            font-size: 0.85rem;

            pointer-events: none;
        }


        .form-control {

            width: 100%;

            height: 43px;

            padding:
                0 42px 0 38px;

            border:
                1px solid var(--border);

            border-radius: 8px;

            background: #f8fafc;

            color: var(--text);

            font-size: 0.86rem;

            outline: none;

            transition: all 0.2s ease;
        }


        .form-control::placeholder {

            color: #9aa6b5;
        }


        .form-control:focus {

            background: #fff;

            border-color: var(--navy);

            box-shadow:
                0 0 0 3px
                rgba(23, 71, 127, 0.08);
        }


        /*
        |--------------------------------------------------------------------------
        | PASSWORD TOGGLE
        |--------------------------------------------------------------------------
        */

        .password-toggle {

            position: absolute;

            right: 10px;
            top: 50%;

            transform: translateY(-50%);

            border: none;

            background: transparent;

            color: #718096;

            cursor: pointer;

            padding: 5px;

            font-size: 0.82rem;
        }

        .password-toggle:hover {

            color: var(--navy);
        }


        /*
        |--------------------------------------------------------------------------
        | LOGIN BUTTON
        |--------------------------------------------------------------------------
        */

        .login-button {

            width: 100%;

            height: 44px;

            margin-top: 4px;

            border: none;

            border-radius: 8px;

            background:
                linear-gradient(
                    135deg,
                    var(--navy-dark),
                    var(--navy)
                );

            color: #fff;

            font-size: 0.87rem;

            font-weight: 700;

            cursor: pointer;

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 9px;

            box-shadow:
                0 5px 14px
                rgba(16, 41, 77, 0.20);

            transition: all 0.2s ease;
        }


        .login-button:hover {

            background:
                linear-gradient(
                    135deg,
                    var(--navy),
                    var(--navy-light)
                );

            transform: translateY(-1px);

            box-shadow:
                0 7px 18px
                rgba(16, 41, 77, 0.26);
        }


        .login-button:active {

            transform: translateY(0);
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .login-footer {

            margin-top: 17px;

            padding-top: 12px;

            border-top:
                1px solid #edf0f4;

            color: #7b8794;

            font-size: 0.68rem;

            line-height: 1.5;
        }


        .login-footer strong {

            color: var(--navy);

            font-weight: 700;
        }


        .gold-dot {

            display: inline-block;

            width: 5px;
            height: 5px;

            background: var(--gold);

            border-radius: 50%;

            margin:
                0 4px 1px;
        }


        /*
        |--------------------------------------------------------------------------
        | LOADING
        |--------------------------------------------------------------------------
        */

        .loading-overlay {

            display: none;

            position: fixed;

            inset: 0;

            background:
                rgba(16, 41, 77, 0.60);

            backdrop-filter: blur(3px);

            z-index: 9999;

            align-items: center;
            justify-content: center;
        }


        .loading-box {

            background: #fff;

            border-radius: 13px;

            padding: 20px 30px;

            text-align: center;

            box-shadow:
                0 15px 50px
                rgba(0, 0, 0, 0.25);
        }


        .spinner {

            width: 30px;
            height: 30px;

            border:
                3px solid #e5eaf0;

            border-top-color:
                var(--gold);

            border-radius: 50%;

            animation:
                spin 0.8s linear infinite;

            margin:
                0 auto 10px;
        }


        .loading-box span {

            color: var(--navy-dark);

            font-size: 0.78rem;

            font-weight: 600;
        }


        /*
        |--------------------------------------------------------------------------
        | ANIMATION
        |--------------------------------------------------------------------------
        */

        @keyframes fadeIn {

            from {
                opacity: 0;
                transform: translateY(18px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        @keyframes spin {

            to {
                transform: rotate(360deg);
            }
        }


        /*
        |--------------------------------------------------------------------------
        | TABLET / HP
        |--------------------------------------------------------------------------
        */

        @media (max-width: 480px) {

            body {

                padding: 12px;

                overflow: hidden;
            }

            .login-container {

                max-width: 100%;

                padding:
                    22px 20px 18px;

                border-radius: 16px;
            }

            .logo-wrapper {

                width: 68px;
                height: 68px;

                margin-bottom: 9px;
            }

            .logo-wrapper img {

                width: 55px;
                height: 55px;
            }

            .login-title {

                font-size: 0.93rem;
            }

            .login-subtitle {

                font-size: 0.72rem;

                margin-bottom: 15px;
            }

            .form-group {

                margin-bottom: 11px;
            }

            .form-group label {

                font-size: 0.78rem;
            }

            .form-control {

                height: 41px;

                font-size: 0.82rem;
            }

            .login-button {

                height: 42px;

                font-size: 0.83rem;
            }

            .login-footer {

                margin-top: 14px;

                padding-top: 10px;

                font-size: 0.63rem;
            }
        }


        /*
        |--------------------------------------------------------------------------
        | LAYAR PENDEK
        |--------------------------------------------------------------------------
        */

        @media (max-height: 650px) {

            .login-container {

                padding-top: 18px;
                padding-bottom: 15px;
            }

            .logo-wrapper {

                width: 60px;
                height: 60px;

                margin-bottom: 7px;
            }

            .logo-wrapper img {

                width: 48px;
                height: 48px;
            }

            .login-subtitle {

                margin-bottom: 12px;
            }

            .form-group {

                margin-bottom: 9px;
            }

            .login-footer {

                margin-top: 11px;

                padding-top: 8px;
            }
        }

    </style>

</head>


<body>

    <!-- Background -->

    <div class="bg-circle circle-one"></div>
    <div class="bg-circle circle-two"></div>


    <!-- Loading -->

    <div
        class="loading-overlay"
        id="loadingOverlay"
    >

        <div class="loading-box">

            <div class="spinner"></div>

            <span>
                Memproses login...
            </span>

        </div>

    </div>


    <!-- Login -->

    <div class="login-container">


        <!-- Logo -->

        <div class="logo-wrapper">

            <img
                src="assets/gambar/logo.png"
                alt="Logo DPRD"
            >

        </div>


        <!-- Judul -->

        <div class="login-title">

            SISTEM PERJALANAN DINAS

            <br>

            DPRD KABUPATEN BANGGAI KEPULAUAN

        </div>


        <div class="gold-line"></div>


        <div class="login-subtitle">

            Silakan masuk menggunakan akun
            yang telah terdaftar.

        </div>


        <!-- Form -->

        <form
            method="POST"
            id="loginForm"
            autocomplete="off"
        >


            <!-- Username -->

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <div class="input-wrapper">

                    <i
                        class="fa-solid fa-user input-icon"
                    ></i>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Masukkan username"
                        autocomplete="username"
                        required
                    >

                </div>

            </div>


            <!-- Password -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <div class="input-wrapper">

                    <i
                        class="fa-solid fa-lock input-icon"
                    ></i>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        required
                    >

                    <button
                        type="button"
                        class="password-toggle"
                        id="togglePassword"
                        aria-label="Tampilkan password"
                    >

                        <i
                            class="fa-solid fa-eye"
                        ></i>

                    </button>

                </div>

            </div>


            <!-- Button -->

            <button
                type="submit"
                class="login-button"
            >

                <i
                    class="fa-solid fa-right-to-bracket"
                ></i>

                <span>
                    Masuk ke Sistem
                </span>

            </button>

        </form>


        <!-- Footer -->

        <div class="login-footer">

            <strong>
                Aplikasi Pengajuan Perjalanan Dinas
            </strong>

            <br>

            Sekretariat DPRD Kabupaten Banggai Kepulauan

            <br>

            <span class="gold-dot"></span>

            Sistem Informasi Perjalanan Dinas

            <span class="gold-dot"></span>

        </div>

    </div>


    <script>

        /*
        |--------------------------------------------------------------------------
        | Toggle Password
        |--------------------------------------------------------------------------
        */

        const togglePassword =
            document.getElementById('togglePassword');

        const password =
            document.getElementById('password');


        togglePassword.addEventListener(
            'click',
            function () {

                const isPassword =
                    password.type === 'password';

                password.type =
                    isPassword
                        ? 'text'
                        : 'password';

                this.innerHTML =
                    isPassword
                        ? '<i class="fa-solid fa-eye-slash"></i>'
                        : '<i class="fa-solid fa-eye"></i>';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | Loading Login
        |--------------------------------------------------------------------------
        */

        const loginForm =
            document.getElementById('loginForm');

        const loadingOverlay =
            document.getElementById('loadingOverlay');


        loginForm.addEventListener(
            'submit',
            function () {

                loadingOverlay.style.display =
                    'flex';

            }
        );


        /*
        |--------------------------------------------------------------------------
        | SweetAlert Error
        |--------------------------------------------------------------------------
        */

        <?php if (!empty($login_error)): ?>

            Swal.fire({

                icon: 'error',

                title: 'Login Gagal',

                text:
                    <?= json_encode($login_error) ?>,

                confirmButtonText: 'OK',

                confirmButtonColor: '#17477f'

            });

        <?php endif; ?>

    </script>

</body>

</html>
