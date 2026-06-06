<?php
// Cek halaman aktif saat ini (Tetap dipertahankan sesuai kode awal lu)
$current_page_admin = basename($_SERVER['PHP_SELF']);
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>

<div class="admin-header">
    <div class="admin-tabs">
        <a href="admin_user.php" class="admin-tab <?= ($current_page_admin == 'admin_user.php') ? 'active' : ''; ?>">Data User</a>
        <a href="admin_barang.php" class="admin-tab <?= ($current_page_admin == 'admin_barang.php') ? 'active' : ''; ?>">Data Barang</a>
        <a href="admin_peminjam.php" class="admin-tab <?= ($current_page_admin == 'admin_peminjam.php') ? 'active' : ''; ?>">Data Peminjam</a>
        <a href="admin_verifikasi_peminjaman.php" class="admin-tab <?= ($current_page_admin == 'admin_verifikasi_peminjaman.php') ? 'active' : ''; ?>">Verifikasi Peminjaman</a>
        <a href="admin_verifikasi_pengembalian.php" class="admin-tab <?= ($current_page_admin == 'admin_verifikasi_pengembalian.php') ? 'active' : ''; ?>">Verifikasi Pengembalian</a>
    </div>
</div>

<style>
    /* Container utama tab navigasi admin */
    .admin-tabs {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 12px; /* Jarak antar tombol */
        margin: 20px auto;
        max-width: 1100px; /* Diperlebar sedikit agar muat 5 tombol sejajar tanpa merusak layout */
        padding: 0 15px;
    }

    /* Style dasar tombol saat keadaan NORMAL (Gambar ke-2 lu) */
    .admin-tab {
        display: inline-block;
        padding: 10px 24px;
        font-weight: 700;
        font-size: 14px;
        color: #1a5f57 !important; /* Warna text hijau tosca tua */
        background-color: transparent; /* Background transparan */
        border: 2px solid #1a5f57; /* Garis tepi hijau tosca tua */
        border-radius: 30px; /* Membuat sudut tombol membulat (pill-shaped) */
        text-decoration: none !important; /* Menghilangkan garis bawah khas link browser */
        text-transform: uppercase; /* Membuat text kapital semua seperti gambar 1 */
        transition: all 0.3s ease; /* Efek halus saat transisi warna */
    }

    /* Efek saat kursor mouse diarahkan ke tombol (Hover) */
    .admin-tab:hover {
        color: #ffffff !important;
        background-color: #1a5f57;
    }

    /* Style khusus saat tombol dalam keadaan AKTIF (Gambar ke-1 lu) */
    .admin-tab.active {
        color: #ffffff !important; /* Warna text berubah jadi putih */
        background-color: #1a5f57 !important; /* Background terisi penuh hijau tosca tua */
        box-shadow: 0 4px 12px rgba(26, 95, 87, 0.2); /* Efek bayangan halus */
        pointer-events: none; /* Mematikan klik jika admin sudah berada di halaman tersebut */
    }
</style>