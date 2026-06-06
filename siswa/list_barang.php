<?php 
session_start();
// PENYESUAIAN JALUR: Mundur satu folder karena file ini ada di dalam folder siswa/
include '../koneksi.php'; 

// Ambil parameter kategori, pencarian, dan penanda global dari URL dengan proteksi escape string
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$global = isset($_GET['global']) ? $_GET['global'] : 'false';

// Mengambil ID User dari session login siswa
$id_user_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// Memutus inisialisasi paksa ke kategori 1 saat global search aktif
if ($global == 'true') {
    $id_kat = 0;
    $nama_kategori_tampil = "Semua Kategori Lab";
} else {
    // Jika bukan pencarian global, baru gunakan data parameter URL (default ke 1 jika kosong)
    $id_kat = isset($_GET['kat']) ? mysqli_real_escape_string($conn, $_GET['kat']) : 1;

    $query_kat = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kat'");
    $data_kat  = mysqli_fetch_array($query_kat);
    // Proteksi Crash: Jika ID Kategori tidak valid di database, berikan nama default informatif
    $nama_kategori_tampil = $data_kat ? $data_kat['nama_kategori'] : "Laboratorium Terpilih";
}

// Menentukan judul tab browser secara akurat berdasarkan scope pencarian
if ($global == 'true' && !empty($search)) {
    $judul_halaman = "Hasil Pencarian Global: " . htmlspecialchars($search);
} else {
    $judul_halaman = "Daftar Aset - " . $nama_kategori_tampil . ($search ? " (Pencarian: " . htmlspecialchars($search) . ")" : "");
}

// 🔥 FEATUR LIMIT: PILIHAN BARU 10, 25, 50 DATA UNTUK KATALOG SISWA
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default awal 10 data
if (!in_array($limit, [10, 25, 50])) {
    $limit = 10; 
}

// 🔥 FEATUR PAGINATION: TENTUKAN HALAMAN AKTIF
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }
$offset = ($halaman - 1) * $limit;

// Menghitung total data barang yang valid (Bukan 'perbaikan') untuk membuat pagination
$query_hitung = "SELECT COUNT(*) as total FROM barang WHERE status != 'perbaikan'";
if ($global != 'true') {
    $query_hitung .= " AND id_kategori = '$id_kat'";
}
if (!empty($search)) {
    $query_hitung .= " AND nama_barang LIKE '%$search%'";
}
$sql_hitung = mysqli_query($conn, $query_hitung);
$data_hitung = mysqli_fetch_assoc($sql_hitung);
$total_data = $data_hitung['total'];
$total_halaman = ceil($total_data / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $judul_halaman; ?></title>
    
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.4">
    <style>
        .item-card.is-borrowed, .item-card.in-cart, .item-card.is-waiting {
            filter: grayscale(100%);
            opacity: 0.65;
            position: relative;
        }
        .btn-pinjam.disabled-btn {
            background-color: #6c757d !important;
            color: #ffffff !important;
            pointer-events: none; 
            cursor: not-allowed;
        }
        .badge-status-menunggu {
            position: absolute; top: 10px; right: 10px; background-color: #e67e22; color: white;
            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; z-index: 2;
        }
        .badge-status-keranjang {
            position: absolute; top: 10px; right: 10px; background-color: var(--tosca-tua); color: white;
            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; z-index: 2;
        }
        .badge-status-pinjam {
            position: absolute; top: 10px; right: 10px; background-color: #dc3545; color: white;
            padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 600; z-index: 2;
        }

        /* Navigasi Slide Pagination Siswa */
        .pagination .page-link { color: var(--tosca-tua); border-color: var(--tosca-muda); }
        .pagination .page-item.active .page-link { background-color: var(--tosca-tua); border-color: var(--tosca-tua); color: white; }
    </style>
</head>
<body>

   <?php include '../components/header.php'; ?>
   <?php include '../components/sub_header_siswa.php'; ?>

    <div class="safe-container px-3 pb-5 mt-4">
        
        <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua); padding-left: 5px;">
                <?= $global == 'true' ? '🔍 Hasil Pencarian Semua Kategori' : '📂 Kategori: ' . htmlspecialchars($nama_kategori_tampil); ?>
            </h4>
            
            <div class="d-flex align-items-center gap-2 pe-1">
                <span class="text-muted small fw-semibold">Tampilkan:</span>
                <select class="form-select form-select-sm" id="changeLimit" style="width: 80px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-weight: 600;">
                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
        </div>

        <div class="row g-4 justify-content-start">
            <?php
            // PENYESUAIAN QUERY: Ditambahkan LIMIT dan OFFSET untuk pembatasan data
            if ($global == 'true' && !empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE status != 'perbaikan' AND nama_barang LIKE '%$search%' LIMIT $limit OFFSET $offset";
            } 
            else if (!empty($search)) {
                $sql_barang = "SELECT * FROM barang WHERE status != 'perbaikan' AND id_kategori = '$id_kat' AND nama_barang LIKE '%$search%' LIMIT $limit OFFSET $offset";
            } 
            else {
                $sql_barang = "SELECT * FROM barang WHERE status != 'perbaikan' AND id_kategori = '$id_kat' LIMIT $limit OFFSET $offset";
            }

            $query_barang = mysqli_query($conn, $sql_barang);
            
            if(mysqli_num_rows($query_barang) > 0) {
                while($row = mysqli_fetch_array($query_barang)) {
                    $id_barang = $row['id_barang'];
                    $kategori_barang = $row['id_kategori'];

                    $cek_disetujui = mysqli_query($conn, "SELECT status_pengajuan FROM peminjaman WHERE id_barang = '$id_barang' AND status_pengajuan = 'disetujui' LIMIT 1");
                    $is_borrowed = (mysqli_num_rows($cek_disetujui) > 0) ? true : false;

                    $cek_pending_saya = mysqli_query($conn, "SELECT status_pengajuan FROM peminjaman WHERE id_barang = '$id_barang' AND status_pengajuan = 'pending' AND id_user = '$id_user_login' LIMIT 1");
                    $is_waiting = (mysqli_num_rows($cek_pending_saya) > 0) ? true : false;
                    
                    $is_in_cart = (isset($_SESSION['keranjang']) && in_array($id_barang, $_SESSION['keranjang'])) ? true : false;
                    
                    if (!empty($row['foto']) && file_exists("../assets/img/" . $row['foto'])) {
                        $gambar_tampil = "../assets/img/" . $row['foto'];
                    } else {
                        switch ($kategori_barang) {
                            case 1: $gambar_tampil = "../assets/img/logoberangkat.png"; break;
                            case 2: $gambar_tampil = "../assets/img/logodkv.png"; break;
                            case 3: $gambar_tampil = "../assets/img/logomm.png"; break;
                            case 4: $gambar_tampil = "../assets/img/logoanm.png"; break;
                            default: $gambar_tampil = "../assets/img/logomm.png"; break;
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
                            <h5 class="item-name"><?php echo htmlspecialchars($row['nama_barang']); ?></h5>
                            <p class="item-desc"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                        </div>

                        <?php if ($is_borrowed): ?>
                            <a href="#" class="btn-pinjam disabled-btn">DIPINJAM</a>
                        <?php elseif ($is_waiting): ?>
                            <a href="#" class="btn-pinjam disabled-btn" style="background-color: #e67e22 !important;">PROSES...</a>
                        <?php elseif ($is_in_cart): ?>
                            <a href="#" class="btn-pinjam disabled-btn" style="background-color: #558b84 !important;">DI KERANJANG</a>
                        <?php else: ?>
                            <a href="keranjang_tambah.php?id=<?= $row['id_barang']; ?>&kat=<?= $id_kat; ?>" class="btn-pinjam">PINJAM</a>
                        <?php endif; ?>

                    </div>
                </div>
            <?php 
                } 
            } else {
                if (!empty($search)) {
                    echo "<div class='col-12 text-center py-5'><h4>Barang dengan nama '" . htmlspecialchars($search) . "' tidak ditemukan atau sedang tidak tersedia.</h4></div>";
                } else {
                    echo "<div class='col-12 text-center py-5'><h4>Belum ada barang yang tersedia di kategori ini.</h4></div>";
                }
            }
            ?>
        </div>

        <?php if ($total_halaman > 1): ?>
        <div class="d-flex justify-content-center mt-5">
            <nav>
                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden">
                    <li class="page-item <?= $halaman <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $limit; ?>&kat=<?= $id_kat; ?>&global=<?= $global; ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                            <span>&laquo; Prev</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <li class="page-item <?= $halaman == $i ? 'active' : ''; ?>">
                            <a class="page-link px-3" href="?halaman=<?= $i; ?>&limit=<?= $limit; ?>&kat=<?= $id_kat; ?>&global=<?= $global; ?>&search=<?= urlencode($search) ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $halaman >= $total_halaman ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $limit; ?>&kat=<?= $id_kat; ?>&global=<?= $global; ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                            <span>Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="text-center text-muted small mt-1">
            Menampilkan aset ke-<?= $offset + 1; ?> sampai <?= min($offset + $limit, $total_data); ?> dari total <?= $total_data; ?> unit barang yang siap digunakan.
        </div>
        <?php endif; ?>

    </div>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JAVASCRIPT REDIRECT AUTO-SINKRON SAAT LIMIT DROPDOWN DIGANTI
        document.getElementById('changeLimit').addEventListener('change', function() {
            const selectedLimit = this.value;
            const katVal = "<?= $id_kat ?>";
            const globalVal = "<?= $global ?>";
            const searchVal = "<?= urlencode($search) ?>";
            window.location.href = `?halaman=1&limit=${selectedLimit}&kat=${katVal}&global=${globalVal}&search=${searchVal}`;
        });
    </script>
    <?php include '../components/footer.php'; ?>
    
</body>
</html>