<aside class="sidebar-sekwan">
    <h3>Menu Sekwan</h3>
    <ul>
        <li><a href="sekwan_dashboard.php" class="menu-link"><span class="icon">📊</span> Dashboard</a></li>
        <li><a href="sekwan_paraf.php" class="menu-link"><span class="icon">📝</span> Paraf Draft SPPD</a></li>
        <li><a href="laporan_sekwan.php" class="menu-link"><span class="icon">📄</span> Laporan</a></li>
        <li><a href="../logout.php" class="logout menu-link"><span class="icon">🚪</span> Logout</a></li>
    </ul>
</aside>

<!-- Include SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Aktifkan menu berdasarkan halaman saat ini
    const currentPage = window.location.pathname.split("/").pop();
    document.querySelectorAll('.menu-link').forEach(link => {
        if(link.getAttribute('href') === currentPage) {
            link.classList.add('active');
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
