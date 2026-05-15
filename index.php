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

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>