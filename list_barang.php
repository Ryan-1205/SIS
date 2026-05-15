<?php 
include 'koneksi.php'; 

$id_kat = isset($_GET['kat']) ? $_GET['kat'] : 1;

$query_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kat'");
$data_kat  = mysqli_fetch_array($query_kat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aset - <?php echo $data_kat['nama_kategori']; ?></title>
    
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <nav class="navbar-sis">
        <div class="safe-container d-flex align-items-center justify-content-between px-3">
            <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="index.php">
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
            <a href="index.php" class="back-btn">
                <svg width="45" height="45" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm43.4 289.1c7.5 7.5 7.5 19.8 0 27.3s-19.8 7.5-27.3 0L165.4 257.7c-7.5-7.5-7.5-19.8 0-27.3L272.1 123.7c7.5-7.5 19.8-7.5 27.3 0s7.5 19.8 0 27.3L206.3 244h163.4c10.6 0 19.2 8.6 19.2 19.2s-8.6 19.2-19.2 19.2H206.3l93.1 94.7z" fill="#1d5c56"/>
                </svg>
            </a>

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

    <div class="safe-container px-3 pb-5">
        <div class="row g-4 justify-content-start">
            <?php
            $query_barang = mysqli_query($conn, "SELECT * FROM barang WHERE id_kategori = '$id_kat'");
            
            if(mysqli_num_rows($query_barang) > 0) {
                while($row = mysqli_fetch_array($query_barang)) {
            ?>
                <div class="col-md-2-4 col-sm-6"> <div class="item-card">
                        <div class="item-img-box">
                            <img src="assets/img/<?php echo $row['foto']; ?>" alt="Foto Barang">
                        </div>
                        <div class="item-info">
                            <h5 class="item-name"><?php echo $row['nama_barang']; ?></h5>
                            <p class="item-desc"><?php echo $row['deskripsi']; ?></p>
                        </div>
                        <a href="keranjang_tambah.php?id=<?= $row['id_barang']; ?>" class="btn-pinjam">
                          PINJAM
                        </a>
                    </div>
                </div>
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center py-5'><h4>Belum ada barang di kategori ini.</h4></div>";
            }
            ?>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>