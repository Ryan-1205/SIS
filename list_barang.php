<?php 
include 'koneksi.php'; 
session_start();

// Ambil parameter kategori dan pencarian dari URL
$id_kat = isset($_GET['kat']) ? $_GET['kat'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kat'");
$data_kat  = mysqli_fetch_array($query_kat);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Aset - <?php echo $data_kat['nama_kategori']; ?> <?php if($search) echo "- Pencarian: $search"; ?></title>
    
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.3">
</head>
<body>

   <?php include 'header.php'; ?>
   <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 pb-5">
        <div class="row g-4 justify-content-start">
            <?php
            $sql_barang = "SELECT * FROM barang WHERE id_kategori = '$id_kat'";
            
            if (!empty($search)) {
                $sql_barang .= " AND nama_barang LIKE '%$search%'";
            }

            $query_barang = mysqli_query($conn, $sql_barang);
            
            if(mysqli_num_rows($query_barang) > 0) {
                while($row = mysqli_fetch_array($query_barang)) {
                    
                    // --- LOGIKA MENENTUKAN GAMBAR TAMPIL / DEFAULT ---
                    if (!empty($row['foto']) && file_exists("assets/img/" . $row['foto'])) {
                        // Jika ada foto spesifik barang di database, pakai foto itu
                        $gambar_tampil = "assets/img/" . $row['foto'];
                    } else {
                        // Jika kosong, pilih logo default sesuai ID Kategori baru kamu
                        switch ($id_kat) {
                            case 1:
                                $gambar_tampil = "assets/img/logoberangkat.png";
                                break;
                            case 2:
                                $gambar_tampil = "assets/img/logodkv.png";
                                break;
                            case 3:
                                $gambar_tampil = "assets/img/logomm.png";
                                break;
                            case 4:
                                $gambar_tampil = "assets/img/logoanm.png";
                                break;
                            default:
                                $gambar_tampil = "assets/img/logomm.png"; // Cadangan terakhir
                                break;
                        }
                    }
            ?>
                <div class="col-md-2-4 col-sm-6"> 
                    <div class="item-card">
                        <div class="item-img-box">
                            <img src="<?= $gambar_tampil; ?>" alt="Foto Barang" class="img-fluid" style="max-height: 85%; object-fit: contain;">
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
                if (!empty($search)) {
                    echo "<div class='col-12 text-center py-5'><h4>Barang dengan nama '$search' tidak ditemukan di kategori ini.</h4></div>";
                } else {
                    echo "<div class='col-12 text-center py-5'><h4>Belum ada barang di kategori ini.</h4></div>";
                }
            }
            ?>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>