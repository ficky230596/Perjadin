<!-- Tombol Hamburger -->
<button id="hamburger-btn">☰</button>

<aside class="sidebar" id="sidebar">
    <h3>Menu Pegawai</h3>
    <ul>
        <li><a href="pegawai_dashboard.php" class="menu-link">📑 Dashboard</a></li>
        <li><a href="ajukan.php" class="menu-link">📝 Ajukan Perjadin</a></li>
        <li><a href="riwayat_pegawai.php" class="menu-link">📜 Riwayat Pengajuan</a></li>
        <li><a href="../logout.php" class="logout menu-link">🚪 Logout</a></li>
    </ul>
</aside>

<!-- Include SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Toggle sidebar untuk mobile
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const sidebar = document.getElementById('sidebar');

    hamburgerBtn.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });

    // Aktifkan menu berdasarkan halaman saat ini
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll('.menu-link').forEach(link => {
        if(link.getAttribute('href') === currentPage) {
            link.classList.add('active');
            link.style.fontWeight = 'bold';
            link.style.color = '#dba634ff';
        }
    });

    // Konfirmasi logout
    const logoutBtn = document.querySelector('.logout');
    logoutBtn.addEventListener('click', function(e){
        e.preventDefault();
        Swal.fire({
            title: 'Yakin ingin logout?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, logout',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if(result.isConfirmed){
                window.location.href = logoutBtn.href;
            }
        });
    });
</script>

<style>
/* Styling menu aktif */
.menu-link.active {
    background-color: #0b90efff;
    border-radius: 6px;
    color: black;
    padding: 4px 8px;
    display: inline-block;
}

/* Tombol hamburger */
#hamburger-btn {
    display: none;
    position: fixed;
    top: 15px;
    left: 15px;
    background: #28a745;
    color: #fff;
    border: none;
    padding: 10px 12px;
    font-size: 22px;
    border-radius: 6px;
    z-index: 1100;
    cursor: pointer;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    transition: background 0.3s ease;
}

#hamburger-btn:hover {
    background: #218838;
}

/* Sidebar responsive mobile */
@media (max-width: 768px) {
    #hamburger-btn {
        display: block;
    }

    .sidebar {
        transform: translateX(-100%);
        position: fixed;
        top: 0;
        left: 0;
        width: 250px;
        height: 100%;
        background: #fff;
        padding: 20px;
        box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
        z-index: 1000;
    }

    .sidebar.active {
        transform: translateX(0);
    }
}
</style>
