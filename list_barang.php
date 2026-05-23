<?php 
include 'koneksi.php'; 
session_start();

// Ambil parameter kategori, pencarian, dan penanda global dari URL
$id_kat = isset($_GET['kat']) ? $_GET['kat'] : 1;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$global = isset($_GET['global']) ? $_GET['global'] : 'false';

// Ambil nama kategori untuk judul (hanya relevan jika bukan pencarian global)
$query_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kat'");
$data_kat  = mysqli_fetch_array($query_kat);

// Menentukan judul halaman secara cerdas
if ($global == 'true' && !empty($search)) {
    $judul_halaman = "Hasil Pencarian Global: " . htmlspecialchars($search);
} else {
    $judul_halaman = "Daftar Aset - " . $data_kat['nama_kategori'] . ($search ? " (Pencarian: $search)" : "");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman; ?></title>
    
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
    <style>
        /* Gaya tambahan khusus untuk barang yang sedang dipinjam */
        .item-card.is-borrowed {
            filter: grayscale(100%);
            opacity: 0.65;
            position: relative;
        }
        .btn-pinjam.disabled-btn {
            background-color: #6c757d !important;
            color: #ffffff !important;
            pointer-events: none; /* Mencegah klik */
            cursor: not-allowed;
        }
        .badge-status-pinjam {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #dc3545;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            z-index: 2;
        }
    </style>
</head>
<body>

   <?php include 'header.php'; ?>
   <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 pb-5 mt-4">
        <h4 class="mb-4 fw-bold" style="color: var(--tosca-tua); padding-left: 5px;">
            <?= $global == 'true' ? '🔍 Hasil Pencarian Semua Kategori' : '📂 Kategori: ' . $data_kat['nama_kategori']; ?>
        </h4>

        <div class="row g-4 justify-content-start">
            <?php
            // --- LOGIKA QUERY DINAMIS SESUAI REQUEST RYAN ---
            // 1. Jika diketik dari halaman utama (Mencari di SEMUA kategori tanpa batas)
            if ($global == 'true' && !empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE nama_barang LIKE '%$search%'";
            } 
            // 2. Jika diketik dari dalam kategori tertentu (Dikunci hanya kategori itu saja)
            else if (!empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE id_kategori = '$id_kat' AND nama_barang LIKE '%$search%'";
            } 
            // 3. Tampilan normal tanpa keyword pencarian (Berdasarkan kategori tab)
            else {
                $sql_barang = "SELECT * FROM barang WHERE id_kategori = '$id_kat'";
            }

            $query_barang = mysqli_query($conn, $sql_barang);
            
            if(mysqli_num_rows($query_barang) > 0) {
                while($row = mysqli_fetch_array($query_barang)) {
                    $id_barang = $row['id_barang'];
                    $kategori_barang = $row['id_kategori']; // Mengambil ID kategori barang asli tiap row

                    // --- LOGIKA CEK APAKAH BARANG SEDANG DIPINJAM ---
                    $cek_pinjam = mysqli_query($conn, "SELECT id_pinjam FROM peminjaman WHERE id_barang = '$id_barang' AND status_pengajuan = 'disetujui'");
                    $is_borrowed = (mysqli_num_rows($cek_pinjam) > 0) ? true : false;
                    
                    // --- LOGIKA MENENTUKAN GAMBAR TAMPIL / DEFAULT ---
                    if (!empty($row['foto']) && file_exists("assets/img/" . $row['foto'])) {
                        $gambar_tampil = "assets/img/" . $row['foto'];
                    } else {
                        // Switch image disesuaikan dengan $kategori_barang asli dari record DB
                        switch ($kategori_barang) {
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
                                $gambar_tampil = "assets/img/logomm.png";
                                break;
                        }
                    }
            ?>
                <div class="col-md-2-4 col-sm-6"> 
                    <div class="item-card <?= $is_borrowed ? 'is-borrowed' : ''; ?>">
                        
                        <?php if ($is_borrowed): ?>
                            <span class="badge-status-pinjam">Dipinjam</span>
                        <?php endif; ?>

                        <div class="item-img-box">
                            <img src="<?= $gambar_tampil; ?>" alt="Foto Barang" class="img-fluid" style="max-height: 85%; object-fit: contain;">
                        </div>
                        <div class="item-info">
                            <h5 class="item-name"><?php echo $row['nama_barang']; ?></h5>
                            <p class="item-desc"><?php echo $row['deskripsi']; ?></p>
                        </div>

                        <?php if ($is_borrowed): ?>
                            <a href="#" class="btn-pinjam disabled-btn">DIPINJAM</a>
                        <?php else: ?>
                            <a href="keranjang_tambah.php?id=<?= $row['id_barang']; ?>" class="btn-pinjam">PINJAM</a>
                        <?php endif; ?>

                    </div>
                </div>
            <?php 
                } 
            } else {
                if (!empty($search)) {
                    echo "<div class='col-12 text-center py-5'><h4>Barang dengan nama '$search' tidak ditemukan.</h4></div>";
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