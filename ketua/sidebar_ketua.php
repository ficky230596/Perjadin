<style>
.sidebar-ketua {
    width: 210px;
    min-height: 100vh;
    background: linear-gradient(135deg, #2c3e50 70%, #2980b9 100%);
    color: #fff;
    padding: 24px 0 0 0;
    position: fixed;
    left: 0;
    top: 0;
    transition: all 0.3s;
    z-index: 100;
}
.sidebar-ketua h3 {
    text-align: center;
    margin-bottom: 32px;
    font-size: 1.3em;
    letter-spacing: 1px;
    font-weight: 700;
}
.sidebar-ketua ul {
    list-style: none;
    padding: 0;
    margin: 0;
}
.sidebar-ketua ul li {
    margin: 0;
}
.sidebar-ketua ul li a {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #fff;
    text-decoration: none;
    padding: 14px 32px;
    font-size: 1.05em;
    border-left: 4px solid transparent;
    transition: background 0.2s, border-color 0.2s, color 0.2s;
}
.sidebar-ketua ul li a:hover, .sidebar-ketua ul li a.active {
    background: rgba(255,255,255,0.08);
    border-left: 4px solid #00b894;
    color: #00b894;
}
.sidebar-ketua ul li a[style*="color:red"] {
    color: #ff7675 !important;
}
@media (max-width: 800px) {
    .sidebar-ketua {
        width: 100%;
        min-height: unset;
        height: auto;
        position: relative;
        padding: 12px 0 0 0;
    }
    main {
        margin-left: 0 !important;
        padding-top: 120px !important;
    }
}
</style>
<!-- Hamburger Button -->
<button id="sidebarToggle" class="sidebar-hamburger" style="display:none;position:fixed;top:18px;left:18px;z-index:200;background:rgba(44,62,80,0.95);border:none;border-radius:6px;padding:10px 12px;cursor:pointer;">
    <span style="display:block;width:24px;height:3px;background:#fff;margin:4px 0;border-radius:2px;"></span>
    <span style="display:block;width:24px;height:3px;background:#fff;margin:4px 0;border-radius:2px;"></span>
    <span style="display:block;width:24px;height:3px;background:#fff;margin:4px 0;border-radius:2px;"></span>
</button>

<aside class="sidebar-ketua" id="sidebarKetua">
    <h3>Menu Ketua</h3>
    <ul>
        <li><a href="ketua_dashboard.php">📑 Dashboard</a></li>
        <li><a href="ketua_ttd.php">✍️ Tanda Tangan</a></li>
        <li><a href="laporan_ketua.php">📊 Laporan</a></li>
        <li><a href="../logout.php" style="color:red;">🚪 Logout</a></li>
    </ul>
</aside>

<script>
function checkSidebar() {
    if(window.innerWidth <= 800) {
        document.getElementById('sidebarKetua').style.display = 'none';
        document.getElementById('sidebarToggle').style.display = 'block';
    } else {
        document.getElementById('sidebarKetua').style.display = 'block';
        document.getElementById('sidebarToggle').style.display = 'none';
    }
}
window.addEventListener('resize', checkSidebar);
window.addEventListener('DOMContentLoaded', checkSidebar);

document.getElementById('sidebarToggle').onclick = function() {
    var sidebar = document.getElementById('sidebarKetua');
    if(sidebar.style.display === 'block') {
        sidebar.style.display = 'none';
    } else {
        sidebar.style.display = 'block';
    }
};
</script>
<main style="margin-left:210px;padding:20px;">