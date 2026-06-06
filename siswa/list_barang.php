<?php 
include 'koneksi.php'; 
session_start();

// Ambil parameter kategori, pencarian, dan penanda global dari URL
$search = isset($_GET['search']) ? $_GET['search'] : '';
$global = isset($_GET['global']) ? $_GET['global'] : 'false';

// Mengambil ID User dari session login siswa
$id_user_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// Memutus inisialisasi paksa ke kategori 1 saat global search aktif
if ($global == 'true') {
    $id_kat = 0;
    $nama_kategori_tampil = "Semua Kategori Lab";
} else {
    // Jika bukan pencarian global, baru gunakan data parameter URL (default ke 1 jika kosong)
    $id_kat = isset($_GET['kat']) ? $_GET['kat'] : 1;

    $query_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kat'");
    $data_kat  = mysqli_fetch_array($query_kat);
    $nama_kategori_tampil = $data_kat['nama_kategori'];
}

// Menentukan judul tab browser secara akurat berdasarkan scope pencarian
if ($global == 'true' && !empty($search)) {
    $judul_halaman = "Hasil Pencarian Global: " . htmlspecialchars($search);
} else {
    $judul_halaman = "Daftar Aset - " . $nama_kategori_tampil . ($search ? " (Pencarian: $search)" : "");
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
        /* Gaya tambahan khusus untuk barang yang sedang dipinjam, di keranjang, atau pending verifikasi */
        .item-card.is-borrowed, .item-card.in-cart, .item-card.is-waiting {
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
        /* Style badge khusus penanda status pending verifikasi admin (Orange) */
        .badge-status-menunggu {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: #e67e22;
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            z-index: 2;
        }
        /* Style badge khusus penanda di keranjang (Toska Tua) */
        .badge-status-keranjang {
            position: absolute;
            top: 10px;
            right: 10px;
            background-color: var(--tosca-tua);
            color: white;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            z-index: 2;
        }
        /* Style badge khusus penanda sedang dipinjam (Merah) */
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
            <?= $global == 'true' ? '🔍 Hasil Pencarian Semua Kategori' : '📂 Kategori: ' . $nama_kategori_tampil; ?>
        </h4>

        <div class="row g-4 justify-content-start">
            <?php
            // --- EKSEKUSI FORK QUERY DINAMIS ---
            if ($global == 'true' && !empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE nama_barang LIKE '%$search%'";
            } 
            else if (!empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE id_kategori = '$id_kat' AND nama_barang LIKE '%$search%'";
            } 
            else {
                $sql_barang = "SELECT * FROM barang WHERE id_kategori = '$id_kat'";
            }

            $query_barang = mysqli_query($conn, $sql_barang);
            
            if(mysqli_num_rows($query_barang) > 0) {
                while($row = mysqli_fetch_array($query_barang)) {
                    $id_barang = $row['id_barang'];
                    $kategori_barang = $row['id_kategori'];

                    // --- SELEKSI SEPAKAT: LOGIKA PINJAM & LOGIKA PENDING YANG DI-FORK DENGAN ID_USER ---
                    // 1. Status 'disetujui' (Berlaku GLOBAL untuk semua siswa karena barangnya emang lagi dipakai keluar)
                    $cek_disetujui = mysqli_query($conn, "SELECT status_pengajuan FROM peminjaman WHERE id_barang = '$id_barang' AND status_pengajuan = 'disetujui' LIMIT 1");
                    $is_borrowed = (mysqli_num_rows($cek_disetujui) > 0) ? true : false;

                    // 2. Status 'pending' (Hanya berlaku INDIVIDUAL bagi user pemilik antrean pengajuan tersebut saja)
                    $cek_pending_saya = mysqli_query($conn, "SELECT status_pengajuan FROM peminjaman WHERE id_barang = '$id_barang' AND status_pengajuan = 'pending' AND id_user = '$id_user_login' LIMIT 1");
                    $is_waiting = (mysqli_num_rows($cek_pending_saya) > 0) ? true : false;
                    
                    // --- CEK APAKAH BARANG SUDAH ADA DI KERANJANG SESSION ---
                    $is_in_cart = (isset($_SESSION['keranjang']) && in_array($id_barang, $_SESSION['keranjang'])) ? true : false;
                    
                    // --- LOGIKA MENENTUKAN GAMBAR TAMPIL / DEFAULT ---
                    if (!empty($row['foto']) && file_exists("assets/img/" . $row['foto'])) {
                        $gambar_tampil = "assets/img/" . $row['foto'];
                    } else {
                        switch ($kategori_barang) {
                            case 1: $gambar_tampil = "assets/img/logoberangkat.png"; break;
                            case 2: $gambar_tampil = "assets/img/logodkv.png"; break;
                            case 3: $gambar_tampil = "assets/img/logomm.png"; break;
                            case 4: $gambar_tampil = "assets/img/logoanm.png"; break;
                            default: $gambar_tampil = "assets/img/logomm.png"; break;
                        }
                    }
            ?>
                <div class="col-md-2-4 col-sm-6"> 
                    <div class="item-card <?= $is_borrowed ? 'is-borrowed' : ($is_waiting ? 'is-waiting' : ($is_in_cart ? 'in-cart' : '')); ?>">
                        
                        <?php if ($is_borrowed): ?>
                            <span class="badge-status-pinjam">Dipinjam</span>
                        <?php elseif ($is_waiting): ?>
                            <span class="badge-status-menunggu">Menunggu Verifikasi</span>
                        <?php elseif ($is_in_cart): ?>
                            <span class="badge-status-keranjang">Di Keranjang</span>
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
                        <?php elseif ($is_waiting): ?>
                            <a href="#" class="btn-pinjam disabled-btn" style="background-color: #e67e22 !important;">PROSES...</a>
                        <?php elseif ($is_in_cart): ?>
                            <a href="#" class="btn-pinjam disabled-btn" style="background-color: #558b84 !important;">DI KERANJANG</a>
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

    <?php include 'footer.php'; ?>
    
</body>
</html>