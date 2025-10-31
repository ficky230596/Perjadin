<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar-umum">

    <ul>
        <h3>Menu Bagian Umum</h3>
        <li>
            <a href="umum_dashboard.php" class="<?php echo ($current_page == 'umum_dashboard.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-house"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="umum_buat.php" class="<?php echo ($current_page == 'umum_buat.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-file-circle-plus"></i> Buat Draft SPPD
            </a>
        </li>
        <li>
            <a href="umum_cap.php" class="<?php echo ($current_page == 'umum_cap.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-stamp"></i> Cap SPPD
            </a>
        </li>
        <li>
            <a href="Management_User.php" class="<?php echo ($current_page == 'Management_User.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-clock-rotate-left"></i> User
            </a>
        </li>
        <li>
            <!-- Hapus href langsung, ganti dengan id -->
            <a href="#" id="logoutBtn" class="logout <?php echo ($current_page == 'logout.php') ? 'active' : ''; ?>">
                <i class="fa-solid fa-right-from-bracket"></i> Logout
            </a>
        </li>
    </ul>
</aside>

<script>
    document.getElementById('logoutBtn').addEventListener('click', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin keluar?',
            text: "Anda akan logout dari sistem!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "../logout.php";
            }
        });
    });
</script>