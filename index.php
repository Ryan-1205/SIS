<?php include 'koneksi.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Sixseven Inventory System</title>
    
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar-sis">
        <div class="safe-container d-flex align-items-center justify-content-between px-3">
            <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="#">
                SIS <span class="brand-sub">Sixseven Inventory System</span>
            </a>
            <div class="d-flex">
                <a href="#" class="nav-link-custom">Daftar</a>
                <a href="#" class="nav-link-custom">Masuk</a>
            </div>
        </div>
    </nav>

    <div class="safe-container">
        <div class="search-section px-3">
            <h4 class="search-title">Cari Barang</h4>
            <div class="search-bar">
                <input type="text" placeholder="Nama Barang">
                <span class="search-icon">🔍</span>
            </div>
            <div class="action-icons">
                <div class="action-item">
                    <span>📦</span> Pinjam Barang
                </div>
                <div class="action-item">
                    <span>🔄</span> Kembalikan
                </div>
            </div>
        </div>
    </div>

    <div class="safe-container px-3">
        <div class="category-wrapper">
            <h2 class="category-title">Kategori Aset</h2>
            
            <div class="row g-4 justify-content-center">
                <div class="col-md-3">
                    <a href="list_barang.php?kat=1" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logo_berangkat.png" alt="Berangkat">
                        </div>
                        <div class="category-name">
                            Aset <strong>Tim Berangkat</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=2" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logo_dkv.png" alt="DKV">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab DKV</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=3" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logo_mm.png" alt="Multimedia">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab Multimedia</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=4" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logo_animasi.png" alt="Animasi">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab Animasi</strong>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <footer class="footer-sis mt-5">
        <div class="safe-container px-3">
            <h3 class="footer-main-title text-center text-white mb-4 text-uppercase">Kategori</h3>
            
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="row g-3">
                        
                        <div class="col-6 col-md-3">
                            <h5 class="footer-category-heading text-white fw-bold">Tim Berangkat</h5>
                            <div class="footer-scroll-list">
                                <a href="#" class="footer-item-link">Kamera Canon 90D</a>
                                <a href="#" class="footer-item-link">Tripod Takara</a>
                                <a href="#" class="footer-item-link">Mic Boya</a>
                                <a href="#" class="footer-item-link">Lensa Fix 50mm</a>
                                <a href="#" class="footer-item-link">Lighting Godox</a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <h5 class="footer-category-heading text-white fw-bold">Lab DKV</h5>
                            <div class="footer-scroll-list">
                                <a href="#" class="footer-item-link">PC iMac Retina</a>
                                <a href="#" class="footer-item-link">Wacom Tablet</a>
                                <a href="#" class="footer-item-link">Printer A3 Epson</a>
                                <a href="#" class="footer-item-link">Scanner Canon</a>
                                <a href="#" class="footer-item-link">Meja Gambar Portable</a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <h5 class="footer-category-heading text-white fw-bold">Lab Multimedia</h5>
                            <div class="footer-scroll-list">
                                <a href="#" class="footer-item-link">PC Core i7</a>
                                <a href="#" class="footer-item-link">Kamera Sony Alpha</a>
                                <a href="#" class="footer-item-link">Handycam Panasonic</a>
                                <a href="#" class="footer-item-link">Green Screen Kit</a>
                                <a href="#" class="footer-item-link">Audio Mixer</a>
                            </div>
                        </div>

                        <div class="col-6 col-md-3">
                            <h5 class="footer-category-heading text-white fw-bold">Lab Animasi</h5>
                            <div class="footer-scroll-list">
                                <a href="#" class="footer-item-link">Render Farm Station</a>
                                <a href="#" class="footer-item-link">Pen Display Huion</a>
                                <a href="#" class="footer-item-link">Light Box Animation</a>
                                <a href="#" class="footer-item-link">Projector Epson</a>
                                <a href="#" class="footer-item-link">Headphone Audio-Technica</a>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-4">
                    <div class="map-responsive-wrapper">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.0232490150244!2d106.61830237583685!3d-6.26067169372808!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69fc06fc000001%3A0x6334f66dbb72183e!2sMultimedia%20Nusantara%20University!5e0!3m2!1sen!2sid!4v1716710000000!5m2!1sen!2sid" 
                            allowfullscreen="" 
                            loading="lazy" 
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>
            </div>

            <div class="text-center text-white mt-4 pt-3 border-top border-secondary opacity-50 small">
                © 2026 SIS Project. All Rights Reserved.
            </div>
        </div>
    </footer>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>