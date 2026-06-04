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

    <?php include 'header.php'; ?>
    <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3">
        <div class="category-wrapper">
            <h2 class="category-title">KATEGORI ASET</h2>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-3">
                    <a href="list_barang.php?kat=1" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logoberangkat.png" alt="Berangkat">
                        </div>
                        <div class="category-name">
                            Aset <strong>Tim Berangkat</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=2" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logodkv.png" alt="DKV">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab DKV 1</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=3" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logomm.png" alt="Multimedia">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab DKV 2</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=4" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logoanm.png" alt="Animasi">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab Animasi</strong>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-sis mt-5 py-5">
    <div class="safe-container px-3">
        <h3 class="footer-main-title text-center mb-5 text-uppercase">LOKASI KAMI</h3>
        
        <div class="row align-items-start g-4">
            <div class="col-lg-7">
                <div class="info-card p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">📍 SMKN 67 Jakarta</h5>
                            <p class="mb-1"><strong>Alamat:</strong> Jl. Telaga No.25, RT.13/RW.9</p>
                            <p class="mb-1">Pekayon, Kec. Ps. Rebo Kota Jakarta Timur</p>
                            <p class="mb-1">Daerah Khusus Ibukota Jakarta, 13710</p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="fw-bold mb-3">🕒 Jam Operasional</h5>
                            <p class="mb-1">Senin - Sabtu: 07.00 - 17.00 WIB</p>
                            <p class="mb-1">Minggu: Tutup</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="map-responsive-wrapper">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1379.6901637238436!2d106.85526126212754!3d-6.346292510338262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69edd7b67bf0c9%3A0x8e78091a50d05efd!2sProfessional%20School%20State%2067%20of%20Jakarta!5e0!3m2!1sen!2sid!4v1780459272994!5m2!1sen!2sid%22" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>

        <div class="text-center mt-5 pt-3 border-top border-secondary opacity-50 small">
            © 2026 SIS Project. All Rights Reserved.
        </div>
    </div>
</footer>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('msg') === 'welcome') {
            Swal.fire({
                title: 'Selamat Datang!',
                text: 'Berhasil masuk ke aplikasi Sixseven Inventory System.',
                icon: 'success',
                confirmButtonColor: '#1e6f65'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    </script>
</body>
</html>