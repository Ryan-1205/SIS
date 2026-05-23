<?php
// Cek halaman aktif saat ini
$current_page_admin = basename($_SERVER['PHP_SELF']);
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<div class="admin-header">
    <a href="dashboard_admin.php" class="admin-back-btn">
        <svg width="45" height="45" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm43.4 289.1c7.5 7.5 7.5 19.8 0 27.3s-19.8 7.5-27.3 0L165.4 257.7c-7.5-7.5-7.5-19.8 0-27.3L272.1 123.7c7.5-7.5 19.8-7.5 27.3 0s7.5 19.8 0 27.3L206.3 244h163.4c10.6 0 19.2 8.6 19.2 19.2s-8.6 19.2-19.2 19.2H206.3l93.1 94.7z" fill="#1d5c56"/>
        </svg>
    </a>
    
    <div class="admin-tabs">
        <a href="admin_user.php" class="admin-tab <?= ($current_page_admin == 'admin_user.php') ? 'active' : ''; ?>">Data User</a>
        <a href="admin_barang.php" class="admin-tab <?= ($current_page_admin == 'admin_barang.php') ? 'active' : ''; ?>">Data Barang</a>
        <a href="admin_peminjam.php" class="admin-tab <?= ($current_page_admin == 'admin_peminjam.php') ? 'active' : ''; ?>">Data Peminjam</a>
        <a href="admin_verifikasi.php" class="admin-tab <?= ($current_page_admin == 'admin_verifikasi.php') ? 'active' : ''; ?>">Verifikasi Peminjaman</a>
    </div>
</div>
