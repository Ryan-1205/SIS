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
            <h2 class="category-title">Kategori Aset</h2>
            
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
                            Aset <strong>Lab DKV</strong>
                        </div>
                    </a>
                </div>

                <div class="col-md-3">
                    <a href="list_barang.php?kat=3" class="card-category">
                        <div class="white-box">
                            <img src="assets/img/logomm.png" alt="Multimedia">
                        </div>
                        <div class="category-name">
                            Aset <strong>Lab Multimedia</strong>
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

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>