<?php
// Cek halaman aktif saat ini
$current_page_admin = basename($_SERVER['PHP_SELF']);
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<div class="admin-header">
    
    <div class="admin-tabs">
        <a href="admin_user.php" class="admin-tab <?= ($current_page_admin == 'admin_user.php') ? 'active' : ''; ?>">Data User</a>
        <a href="admin_barang.php" class="admin-tab <?= ($current_page_admin == 'admin_barang.php') ? 'active' : ''; ?>">Data Barang</a>
        <a href="admin_peminjam.php" class="admin-tab <?= ($current_page_admin == 'admin_peminjam.php') ? 'active' : ''; ?>">Data Peminjam</a>
        <a href="admin_verifikasi.php" class="admin-tab <?= ($current_page_admin == 'admin_verifikasi.php') ? 'active' : ''; ?>">Verifikasi Peminjaman</a>
    </div>
</div>
