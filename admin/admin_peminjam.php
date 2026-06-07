<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

// Menyimpan tingkat role untuk menentukan batasan data laboratorium
$current_admin_role = $_SESSION['role'];
$id_kategori_admin = isset($_SESSION['id_kategori']) ? $_SESSION['id_kategori'] : 1;

// Ambil nama kategori lab saat ini untuk judul dashboard admin
$query_nama_lab = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori_admin'");
$data_lab = mysqli_fetch_array($query_nama_lab);

// Konfigurasi teks nama laboratorium berdasarkan tingkat kuasa peran
if ($current_admin_role === 'admin') {
    $nama_lab_tampil = "Semua Laboratorium (Global)";
} else {
    $nama_lab_tampil = $data_lab ? $data_lab['nama_kategori'] : "Laboratorium";
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// PROSES FILTER UTAMA: Menentukan mode view ('aktif' untuk sedang meminjam, 'history' untuk riwayat)
$view_mode = isset($_GET['view']) && $_GET['view'] === 'history' ? 'history' : 'aktif';

// 🔥 FEATUR LIMIT: PILIHAN BARU 10, 25, 50 DATA
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10; // Default diatur ke 10 baris
if (!in_array($limit, [10, 25, 50])) {
    $limit = 10; 
}

// 🔥 FEATUR PAGINATION: LOGIKA HALAMAN AKTIF
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }
$offset = ($halaman - 1) * $limit;

// Klausa filter global/kategori lab admin
$where_clause = ($current_admin_role === 'admin') ? "WHERE 1=1" : "WHERE barang.id_kategori = '$id_kategori_admin'";

// Menentukan kondisi target status query hitung baris
if ($view_mode == 'aktif') {
    $status_filter = "IN ('disetujui', 'pending_kembali')";
} else {
    $status_filter = "IN ('kembali', 'ditolak')";
}

// Buat filter teks search jika diisi admin
$search_sql = "";
if ($search != '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $search_sql = " AND users.nama_lengkap LIKE '%$search_escaped%'";
}

// Hitung total kelompok data (karena query menggunakan GROUP BY, kita hitung baris uniknya menggunakan subquery)
$query_total = "SELECT COUNT(DISTINCT peminjaman.id_user, peminjaman.tgl_pinjam) as total 
                FROM peminjaman 
                JOIN users ON peminjaman.id_user = users.id_user 
                JOIN barang ON peminjaman.id_barang = barang.id_barang 
                $where_clause AND peminjaman.status_pengajuan $status_filter $search_sql";

$sql_hitung = mysqli_query($conn, $query_total);
$data_hitung = mysqli_fetch_assoc($sql_hitung);
$total_data = $data_hitung['total'];
$total_halaman = ceil($total_data / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SIS - Sixseven Inventory System</title>
    <link rel="icon" type="image/png" href="../assets/img/logo/smk.png">
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=2.7">
    <style>
        .admin-table-wrapper {
            max-width: 1000px;
            margin: 0 auto 30px auto;
            border: 2px solid var(--tosca-tua);
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .admin-table th {
            color: white;
            background-color: var(--tosca-tua);
            font-weight: 700;
            padding: 15px 15px;
            font-size: 14px;
        }
        .admin-table td {
            padding: 15px 15px;
            border-bottom: 1px solid var(--tosca-muda);
            color: #333;
            font-size: 14px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .btn-detail-barang { 
            background-color: var(--tosca-tua); 
            color: white; 
            border: none; 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 13px; 
            text-decoration: none; 
            cursor: pointer; 
            transition: all 0.2s ease;
        }
        .btn-detail-barang:hover { 
            opacity: 0.9;
            color: white; 
        }

        .badge-approved { background-color: #28a745; color: white; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; }
        .badge-returned { background-color: #0288d1; color: white; padding: 5px 12px; border-radius: 15px; font-size: 12px; font-weight: 600; }
        
        .admin-search-container {
            max-width: 1000px;
            margin: 20px auto 15px auto;
        }
        .admin-search-form {
            display: flex;
            gap: 10px;
        }
        .admin-search-form input {
            flex: 1;
            padding: 10px 15px;
            border: 2px solid var(--tosca-muda);
            border-radius: 8px;
            outline: none;
        }
        .admin-search-form input:focus {
            border-color: var(--tosca-tua);
        }
        .admin-search-form button {
            background-color: var(--tosca-tua);
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
        }
        .filter-select {
            padding: 10px 20px;
            border: 2px solid var(--tosca-tua);
            border-radius: 8px;
            color: var(--tosca-tua);
            font-weight: 600;
            outline: none;
            cursor: pointer;
            background: white;
        }

        /* Navigasi Slide Pagination */
        .pagination .page-link { color: var(--tosca-tua); border-color: var(--tosca-muda); }
        .pagination .page-item.active .page-link { background-color: var(--tosca-tua); border-color: var(--tosca-tua); color: white; }
    </style>
</head>
<body>
    
    <?php include '../components/header.php'; ?>
    
    <div class="safe-container px-3 pb-5 mt-4">
        <?php include '../components/sub_header_admin.php'; ?>

        <div class="mx-auto mt-4 mb-2 d-flex justify-content-between align-items-center" style="max-width: 1000px;">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua);">📍 Log Aktivitas Peminjaman: <?= htmlspecialchars($nama_lab_tampil); ?></h4>
            
            <select class="filter-select" onchange="location = this.value;">
                <option value="admin_peminjam.php?view=aktif&limit=<?= $limit ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" <?= $view_mode == 'aktif' ? 'selected' : '' ?>>🖥️ Sedang Meminjam Aset</option>
                <option value="admin_peminjam.php?view=history&limit=<?= $limit ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" <?= $view_mode == 'history' ? 'selected' : '' ?>>📜 Riwayat Selesai & Ditolak</option>
            </select>
        </div>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="hidden" name="view" value="<?= $view_mode; ?>">
                <input type="hidden" name="limit" value="<?= $limit; ?>">
                <input type="text" name="search" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="mx-auto mb-2 d-flex justify-content-end" style="max-width: 1000px;">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Tampilkan:</span>
                <select class="form-select form-select-sm" id="changeLimit" style="width: 80px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-weight: 600;">
                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="text-start" style="padding-left: 20px;">NAMA PEMINJAM</th>
                        <th width="200">WAKTU PINJAM</th>
                        <th width="200"><?= $view_mode == 'aktif' ? 'BATAS PENGEMBALIAN' : 'WAKTU KEMBALI ASLI' ?></th>
                        <th>RINCIAN ASET</th>
                        <th>STATUS</th>
                        <th>VERIFIKATOR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if ($view_mode == 'aktif') {
                        $query_sql = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.diverifikasi_oleh, users.nama_lengkap,
                                             GROUP_CONCAT(CONCAT(barang.nama_barang, '::', peminjaman.status_pengajuan) SEPARATOR '||') as list_barang_status,
                                             GROUP_CONCAT(IFNULL(peminjaman.no_hp, '-') SEPARATOR '||') as list_no_hp,
                                             GROUP_CONCAT(IFNULL(peminjaman.bukti_wajah, 'no_image.jpg') SEPARATOR '||') as list_bukti_wajah,
                                             GROUP_CONCAT(IFNULL(peminjaman.keperluan, '-') SEPARATOR '||') as list_keperluan
                                      FROM peminjaman 
                                      JOIN users ON peminjaman.id_user = users.id_user 
                                      JOIN barang ON peminjaman.id_barang = barang.id_barang 
                                      $where_clause AND peminjaman.status_pengajuan IN ('disetujui', 'pending_kembali') $search_sql
                                      GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.diverifikasi_oleh 
                                      ORDER BY peminjaman.tgl_pinjam DESC";
                    } else {
                        $query_sql = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_asli, peminjaman.diverifikasi_oleh, users.nama_lengkap,
                                             GROUP_CONCAT(CONCAT(barang.nama_barang, '::', peminjaman.status_pengajuan) SEPARATOR '||') as list_barang_status,
                                             GROUP_CONCAT(IFNULL(peminjaman.no_hp, '-') SEPARATOR '||') as list_no_hp,
                                             GROUP_CONCAT(IFNULL(peminjaman.bukti_wajah, 'no_image.jpg') SEPARATOR '||') as list_bukti_wajah,
                                             GROUP_CONCAT(IFNULL(peminjaman.keperluan, '-') SEPARATOR '||') as list_keperluan
                                      FROM peminjaman 
                                      JOIN users ON peminjaman.id_user = users.id_user 
                                      JOIN barang ON peminjaman.id_barang = barang.id_barang 
                                      $where_clause AND peminjaman.status_pengajuan IN ('kembali', 'ditolak') $search_sql
                                      GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_asli, peminjaman.diverifikasi_oleh 
                                      ORDER BY peminjaman.tgl_kembali_asli DESC";
                    }

                    // Suntikkan pembatas halaman LIMIT & OFFSET baris
                    $query_sql .= " LIMIT $limit OFFSET $offset";
                    $sql_execute = mysqli_query($conn, $query_sql);

                    if (mysqli_num_rows($sql_execute) > 0) {
                        while($row = mysqli_fetch_assoc($sql_execute)) {
                            $arr_no_hp = explode('||', $row['list_no_hp']);
                            $arr_keperluan = explode('||', $row['list_keperluan']);
                            $arr_bukti_wajah = explode('||', $row['list_bukti_wajah']);
                            
                            $no_hp_tampil = (!empty($arr_no_hp[0]) && $arr_no_hp[0] !== '-') ? $arr_no_hp[0] : 'Tidak Diisi';
                            $keperluan_tampil = (!empty($arr_keperluan[0]) && $arr_keperluan[0] !== '-') ? $arr_keperluan[0] : 'Tidak ada deskripsi';
                            $bukti_wajah_tampil = (!empty($arr_bukti_wajah[0]) && $arr_bukti_wajah[0] !== 'no_image.jpg') ? $arr_bukti_wajah[0] : 'no_image.jpg';

                            if ($view_mode == 'aktif') {
                                $tanggal_ujung = date('d M Y, H:i', strtotime($row['tgl_kembali_rencana'])) . " WIB";
                                $status_badge = '<span class="badge-approved">Dipinjam</span>';
                            } else {
                                $tanggal_ujung = (!empty($row['tgl_kembali_asli']) && $row['tgl_kembali_asli'] != '0000-00-00 00:00:00') ? date('d M Y, H:i', strtotime($row['tgl_kembali_asli'])) . " WIB" : '-';
                                $status_badge = '<span class="badge-returned">Selesai</span>';
                            }
                    ?>
                    <tr>
                        <td class="fw-bold text-dark text-start" style="padding-left: 20px;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td class="font-monospace text-secondary" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($row['tgl_pinjam'])) ?> WIB</td>
                        <td class="font-monospace fw-bold <?= $view_mode == 'aktif' ? 'text-danger' : 'text-success' ?>" style="font-size:13px;"><?= $tanggal_ujung ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-no-hp="<?= htmlspecialchars($no_hp_tampil) ?>"
                                    data-keperluan="<?= htmlspecialchars($keperluan_tampil) ?>"
                                    data-foto="<?= htmlspecialchars($bukti_wajah_tampil) ?>"
                                    data-items="<?= htmlspecialchars($row['list_barang_status']) ?>">
                                🔍 Lihat Rincian
                            </button>
                        </td>
                        <td><?= $status_badge ?></td>
                        <td class="text-muted fw-bold"><?= !empty($row['diverifikasi_oleh']) ? htmlspecialchars($row['diverifikasi_oleh']) : 'Admin'; ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-5 text-center text-muted'><h5>Tidak ada rekaman data peminjaman pada filter ini.</h5></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <?php if ($total_halaman > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden">
                    <li class="page-item <?= $halaman <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $limit; ?>&view=<?= $view_mode; ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                            <span>&laquo; Prev</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <li class="page-item <?= $halaman == $i ? 'active' : ''; ?>">
                            <a class="page-link px-3" href="?halaman=<?= $i; ?>&limit=<?= $limit; ?>&view=<?= $view_mode; ?>&search=<?= urlencode($search) ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $halaman >= $total_halaman ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $limit; ?>&view=<?= $view_mode; ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                            <span>Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="text-center text-muted small mt-1">
            Menampilkan data ke-<?= $offset + 1; ?> sampai <?= min($offset + $limit, $total_data); ?> dari total <?= $total_data; ?> rekaman data.
        </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">📋 Dokumen Log Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <strong class="d-block mb-2 small text-secondary text-start">Bukti Otentikasi Kamera Wajah:</strong>
                        <div style="max-width: 100%; height: 180px; overflow: hidden; border-radius: 10px; border: 2px solid var(--tosca-tua);" class="mx-auto d-flex align-items-center justify-content-center bg-dark">
                            <img id="detail_bukti_wajah" src="../assets/img/bukti_pinjam/no_image.jpg" alt="Bukti Wajah Siswa" class="img-fluid" style="height: 100%; width: 100%; object-fit: cover;">
                        </div>
                    </div>
                    
                    <p class="text-muted fs-6 mb-1">Siswa: <strong id="detail_nama_peminjam" class="text-dark"></strong></p>
                    <p class="text-muted fs-6 mb-3">Kontak: <strong id="detail_no_hp" class="text-success"></strong></p>
                    
                    <div class="p-2 mb-3 rounded-2 small text-dark border bg-light">
                        <strong>Deskripsi Keperluan:</strong>
                        <p class="m-0 text-muted" id="detail_keperluan"></p>
                    </div>
                    <hr>
                    <strong class="d-block mb-2 small text-secondary">Daftar Item & Status Validasi:</strong>
                    <ul id="container_list_barang" class="list-group list-group-flush fw-bold"></ul>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('changeLimit').addEventListener('change', function() {
            const selectedLimit = this.value;
            const searchVal = "<?= urlencode($search) ?>";
            const viewVal = "<?= $view_mode ?>";
            window.location.href = `?halaman=1&limit=${selectedLimit}&view=${viewVal}&search=${searchVal}`;
        });

        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalDetailBS = new bootstrap.Modal(document.getElementById('modalDetail'));
        
        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const noHp = this.getAttribute('data-no-hp');
                const keperluan = this.getAttribute('data-keperluan');
                const namaFoto = this.getAttribute('data-foto');
                const rawItemsData = this.getAttribute('data-items'); 
                
                document.getElementById('detail_nama_peminjam').innerText = nama;
                document.getElementById('detail_no_hp').innerText = noHp;
                document.getElementById('detail_keperluan').innerText = keperluan;
                
                // 🔥 LOGIKA MERENDER GAMBAR BUKTI WAJAH SECARA DINAMIS
                const imgElement = document.getElementById('detail_bukti_wajah');
                if (namaFoto && namaFoto !== 'no_image.jpg') {
                    imgElement.src = `../assets/img/bukti_pinjam/${namaFoto}`;
                } else {
                    imgElement.src = '../assets/img/bukti_pinjam/no_image.jpg';
                }
                
                const container = document.getElementById('container_list_barang');
                container.innerHTML = ''; 
                
                const arraySesiBarang = rawItemsData.split('||');
                
                arraySesiBarang.forEach(itemRaw => {
                    const bagian = itemRaw.split('::');
                    const namaBarang = bagian[0];
                    const statusBarang = bagian[1];
                    
                    let badgeHTML = '';
                    if (statusBarang === 'disetujui') {
                        badgeHTML = `<span class="badge bg-success text-white px-2 py-1 rounded-pill" style="font-size:11px;">Dipinjam</span>`;
                    } else if (statusBarang === 'pending_kembali') {
                        badgeHTML = `<span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:11px;">Proses Kembali</span>`;
                    } else if (statusBarang === 'kembali') {
                        badgeHTML = `<span class="badge bg-primary text-white px-2 py-1 rounded-pill" style="font-size:11px;">Selesai</span>`;
                    } else if (statusBarang === 'ditolak') {
                        badgeHTML = `<span class="badge bg-danger text-white px-2 py-1 rounded-pill" style="font-size:11px;">Ditolak</span>`;
                    } else {
                        badgeHTML = `<span class="badge bg-secondary text-white px-2 py-1 rounded-pill" style="font-size:11px;">Pending</span>`;
                    }
                    
                    container.innerHTML += `
                        <li class="list-group-item d-flex align-items-center justify-content-between border-bottom py-2 px-1">
                            <span class="text-secondary" style="font-size:13px;">📦 ${namaBarang}</span>
                            ${badgeHTML}
                        </li>`;
                });
                
                modalDetailBS.show();
            });
        });
    </script>

    <?php include '../components/footer.php'; ?>
</body>
</html>