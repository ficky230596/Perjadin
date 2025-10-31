<?php
session_start();
include 'config/db.php';

// Kalau sudah login, langsung lempar ke dashboard sesuai role
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
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Login Sistem Perjadin DPRD</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            background: linear-gradient(135deg, #2b5876 0%, #4e4376 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #ffffff8d;
            padding: 2.5rem 2rem 2rem 2rem;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.25);
            max-width: 350px;
            width: 100%;
            text-align: center;
            animation: fadeIn 1s;
        }

        .login-container img {
            width: 90px;
            height: 90px;
            object-fit: contain;
            margin-bottom: 10px;
        }

        .login-container h1 {
            margin-bottom: 1.7rem;
            color: #4e4376;
            font-size: 1.1rem;
            letter-spacing: 1px;
        }

        .login-container br {
            font-size: 0.9rem;
            color: #666;
        }

        .login-container label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            text-align: left;
            font-weight: 500;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 0.7rem;
            margin-bottom: 1.2rem;
            border: 1px solid #d1d1d1;
            border-radius: 8px;
            font-size: 1rem;
            background: #f7f7f7;
            transition: border 0.2s;
        }

        .login-container input:focus {
            border-color: #4e4376;
            outline: none;
        }

        .login-container button {
            width: 100%;
            padding: 0.8rem;
            background: linear-gradient(90deg, #2b5876 0%, #4e4376 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 0.5rem;
            box-shadow: 0 2px 8px rgba(78, 67, 118, 0.08);
        }

        .login-container button:hover {
            background: linear-gradient(90deg, #4e4376 0%, #2b5876 100%);
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 1.5rem 0.8rem 1.2rem 0.8rem;
                max-width: 95vw;
            }

            .login-container h1 {
                font-size: 1.2rem;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <div class="login-container">

        <!-- 🏛️ Tambahkan logo di sini -->
        <img src="assets/gambar/logo.png" alt="Logo DPRD">

        <h1>LOGIN SISTEM PERJADIN 
            <br>DPRD
            <br>KABUPATEN BANGGAI KEPULAUAN</br>
        </h1>

        <?php
        // Jika ada POST login
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = trim($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['role']     = $user['role'];
                $_SESSION['nama']     = $user['nama'];
                $_SESSION['wa_phone'] = $user['wa_phone'];

                header("Location: index.php");
                exit;
            } else {
                echo "<script>Swal.fire('Error', 'Username atau password salah!', 'error');</script>";
            }
        }
        ?>

        <form method="POST">
            <label>Username:</label>
            <input type="text" name="username" required>

            <label>Password:</label>
            <input type="password" name="password" required>

            <button type="submit">Login</button>
        </form>
    </div>
</body>

</html>