<?php 
include 'koneksi.php'; 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Sixseven Inventory System</title>
    
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
</head>
<body>

    <?php include 'components/header.php'; ?>
    <?php include 'components/sub_header_siswa.php'; ?>

    <div class="safe-container px-3">
        <div class="category-wrapper">
            <h2 class="category-title">KATEGORI ASET LABORATORIUM</h2>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-3">
                    <a href="siswa/list_barang.php?kat=1" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logoberangkat.png" alt="Logo Tim Berangkat">
                        </div>
                        <div class="category-name">
                            Aset <strong>Tim Berangkat</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="siswa/list_barang.php?kat=2" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logodkv.png" alt="Logo DKV 1">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab DKV 1</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="siswa/list_barang.php?kat=3" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logodkv.png" alt="Logo DKV 2">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab DKV 2</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="siswa/list_barang.php?kat=4" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logoanm.png" alt="Logo Animasi">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab Animasi</strong>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/footer.php'; ?>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        
        // 1. Notifikasi Sambutan Selamat Datang Saat Berhasil Login
        if (urlParams.get('msg') === 'welcome') {
            Swal.fire({
                title: 'Otentikasi Berhasil',
                text: 'Selamat datang kembali di sistem layanan sirkulasi inventaris Sixseven Inventory System.',
                icon: 'success',
                confirmButtonColor: '#1e6f65'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }

        // REVISI 2: Notifikasi Interaktif Sukses Keluar Sesi Sesuai Parameter Balikan logout.php
        if (urlParams.get('status') === 'logout') {
            Swal.fire({
                title: 'Berhasil Keluar!',
                text: 'Sesi Anda telah dihentikan dengan aman. Terima kasih!',
                icon: 'success',
                confirmButtonColor: '#1d5c56',
                timer: 3000,
                timerProgressBar: true
            }).then(() => {
                // Membersihkan parameter URL setelah alert selesai agar bersih saat di-refresh ulang
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    </script>
    
</body>
</html>