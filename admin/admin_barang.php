<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

$current_admin_role = $_SESSION['role'];
$id_kategori_admin = isset($_SESSION['id_kategori']) ? $_SESSION['id_kategori'] : 1;

// 🔥 PARAMETER FILTER KATEGORI BARU (Khusus Super Admin)
// Jika admin biasa, otomatis pakai id_kategori dari session-nya
$filter_kategori = isset($_GET['filter_kat']) ? $_GET['filter_kat'] : 'all';

// Memuat nama kategori laboratorium secara dinamis
$query_nama_lab = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori_admin'");
$data_lab = mysqli_fetch_array($query_nama_lab);

if ($current_admin_role === 'admin') {
    $nama_lab_tampil = "Semua Laboratorium (Global)";
} else {
    $nama_lab_tampil = $data_lab ? $data_lab['nama_kategori'] : "Laboratorium";
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// LOGIKA DROPDOWN LIMIT (10, 25, 50)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if (!in_array($limit, [10, 25, 50])) {
    $limit = 10; 
}

// LOGIKA HALAMAN AKTIF (PAGINATION)
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }
$offset = ($halaman - 1) * $limit;

// 🔥 LOGIKA COUNT BARU: Menyesuaikan dengan Filter Kategori Dropdown Super Admin
if ($current_admin_role === 'admin') {
    if ($filter_kategori !== 'all') {
        $fk_escaped = mysqli_real_escape_string($conn, $filter_kategori);
        $query_hitung = "SELECT COUNT(*) as total FROM barang WHERE id_kategori = '$fk_escaped'";
    } else {
        $query_hitung = "SELECT COUNT(*) as total FROM barang WHERE 1=1";
    }
} else {
    $query_hitung = "SELECT COUNT(*) as total FROM barang WHERE id_kategori = '$id_kategori_admin'";
}

if ($search != '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query_hitung .= " AND (kode_barang LIKE '%$search_escaped%' OR nama_barang LIKE '%$search_escaped%' OR deskripsi LIKE '%$search_escaped%' OR status LIKE '%$search_escaped%')";
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
    <title>Admin <?= $nama_lab_tampil; ?> - Data Barang</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=2.5">
    <style>
        .admin-search-container { max-width: 1000px; margin: 30px auto 15px auto; }
        .admin-search-form { display: flex; border: 2px solid var(--tosca-tua); border-radius: 50px; padding: 4px; background: white; }
        .admin-search-form input { flex-grow: 1; border: none; padding: 10px 25px; outline: none; color: var(--tosca-tua); font-size: 16px; background: transparent; }
        .admin-search-form button { background-color: var(--tosca-tua); color: white; border: none; padding: 8px 50px; border-radius: 50px; font-weight: 700; font-size: 18px; cursor: pointer; }
        .admin-table-wrapper { max-width: 1000px; margin: 0 auto 30px auto; border: 2px solid var(--tosca-tua); background: white; border-radius: 10px; overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: white; background-color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; font-size: 14px; }
        .admin-table td { padding: 20px 15px; border-bottom: 1px solid var(--tosca-muda); color: #333; font-size: 15px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .btn-admin-action { background-color: var(--tosca-tua); color: white; text-decoration: none; padding: 10px 45px; border-radius: 30px; font-weight: 700; font-size: 18px; border: none; cursor: pointer; display: inline-block; }
        
        .select-item-check, .select-all-check { width: 18px; height: 18px; cursor: pointer; }
        .col-select-master { display: none; }

        .badge-tersedia { background-color: #28a745; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-block; }
        .badge-dipinjam { background-color: #e67e22; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-block; cursor: pointer; text-decoration: none; }
        .badge-perbaikan { background-color: #dc3545; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; white-space: nowrap; display: inline-block; }
        .badge-kat-lab { background-color: #e2f1ee; color: var(--tosca-tua); border: 1px solid var(--tosca-muda); padding: 5px 12px; border-radius: 50px; font-size: 13px; font-weight: 600; }
        
        .inline-input { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; outline: none; }
        .inline-select { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; background: white; cursor: pointer; }

        .pagination .page-link { color: var(--tosca-tua); border-color: var(--tosca-muda); }
        .pagination .page-item.active .page-link { background-color: var(--tosca-tua); border-color: var(--tosca-tua); color: white; }
    </style>
</head>
<body>
    
    <?php include '../components/header.php'; ?>
    
    <div class="safe-container px-3">
        <?php include '../components/sub_header_admin.php'; ?>

        <div class="d-flex align-items-center justify-content-between mx-auto mt-4" style="max-width: 1000px;">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua);">
                📍 Ruang Kontrol Aset: <?= htmlspecialchars($nama_lab_tampil); ?>
            </h4>
            
            <div class="d-flex gap-2">
                <button type="button" id="btnModeHapus" class="btn btn-outline-danger px-4 rounded-pill fw-bold">Hapus Barang</button>
                <button type="button" form="formBulkDelete" id="btnBulkDelete" class="btn btn-danger px-4 rounded-pill fw-bold d-none">Konfirmasi Hapus (<span id="checkCount">0</span>)</button>
                <button type="button" id="btnBatalHapus" class="btn btn-secondary px-3 rounded-pill fw-bold d-none">Batal</button>
                <button type="button" class="btn-admin-action" data-bs-toggle="modal" data-bs-target="#modalTambahBarang">+ Tambah Aset</button>
            </div>
        </div>
        
        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <input type="hidden" name="filter_kat" value="<?= $filter_kategori ?>">
                <input type="text" name="search" placeholder="Cari Kode Barang, Nama, atau Deskripsi..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="mx-auto mb-2 d-flex justify-content-end align-items-center gap-3" style="max-width: 1000px;">
            
            <?php if ($current_admin_role === 'admin'): ?>
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Kategori Lab:</span>
                <select class="form-select form-select-sm" id="filterKategori" style="width: 160px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-weight: 600; color: var(--tosca-tua);">
                    <option value="all" <?= $filter_kategori == 'all' ? 'selected' : ''; ?>>Semua Lab</option>
                    <?php 
                    $list_kategori_query = mysqli_query($conn, "SELECT * FROM kategori");
                    while($kat_row = mysqli_fetch_assoc($list_kategori_query)) {
                        $selected = ($filter_kategori == $kat_row['id_kategori']) ? 'selected' : '';
                        echo "<option value='".$kat_row['id_kategori']."' $selected>".$kat_row['nama_kategori']."</option>";
                    }
                    ?>
                </select>
            </div>
            <?php endif; ?>

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
            <form action="admin_barang_hapus_massal.php" method="POST" id="formBulkDelete">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="50" class="col-select-master"><input type="checkbox" id="selectAll" class="form-check-input select-all-check"></th>
                        <th width="150">KODE BARANG (INV)</th> 
                        <th>NAMA BARANG</th>
                        <?php if ($current_admin_role === 'admin'): ?>
                            <th width="140">KATEGORI LAB</th>
                        <?php endif; ?>
                        <th>DESKRIPSI</th>
                        <th width="130">KONDISI</th>
                        <th width="130">STATUS</th>
                        <th width="90">FOTO</th>
                        <th width="140">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // 🔥 LOGIKA DATA QUERY: Menyaring berdasarkan seleksi dropdown kategori super admin
                    if ($current_admin_role === 'admin') {
                        if ($filter_kategori !== 'all') {
                            $fk_escaped = mysqli_real_escape_string($conn, $filter_kategori);
                            $query = "SELECT barang.*, kategori.nama_kategori FROM barang JOIN kategori ON barang.id_kategori = kategori.id_kategori WHERE barang.id_kategori = '$fk_escaped'";
                        } else {
                            $query = "SELECT barang.*, kategori.nama_kategori FROM barang JOIN kategori ON barang.id_kategori = kategori.id_kategori WHERE 1=1";
                        }
                    } else {
                        $query = "SELECT * FROM barang WHERE id_kategori = '$id_kategori_admin'";
                    }

                    if ($search != '') {
                        $search_escaped = mysqli_real_escape_string($conn, $search);
                        $query .= " AND (barang.kode_barang LIKE '%$search_escaped%' OR barang.nama_barang LIKE '%$search_escaped%' OR barang.deskripsi LIKE '%$search_escaped%' OR barang.status LIKE '%$search_escaped%')";
                    }
                    
                    $query .= " LIMIT $limit OFFSET $offset";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $id_barang = $row['id_barang'];
                            $kondisi = ($row['status'] == 'perbaikan') ? 'Rusak' : 'Baik';

                            $cek_pinjam = mysqli_query($conn, "SELECT p.tgl_pinjam, p.tgl_kembali_rencana, u.nama_lengkap, u.nis 
                                                               FROM peminjaman p 
                                                               JOIN users u ON p.id_user = u.id_user 
                                                               WHERE p.id_barang = '$id_barang' AND p.status_pengajuan = 'disetujui'");
                            $is_dipinjam = (mysqli_num_rows($cek_pinjam) > 0);
                            
                            if ($row['status'] == 'perbaikan') {
                                $ketersediaan_badge = '<span class="badge-perbaikan">Maintenance</span>';
                            } else {
                                if ($is_dipinjam) {
                                    $data_peminjam = mysqli_fetch_assoc($cek_pinjam);
                                    $ketersediaan_badge = '<a href="#" class="badge-dipinjam tombol-peminjam" 
                                                              data-barang="'.htmlspecialchars($row['nama_barang']).'"
                                                              data-nama="'.htmlspecialchars($data_peminjam['nama_lengkap']).'"
                                                              data-username="'.htmlspecialchars($data_peminjam['nis']).'"
                                                              data-tgl="'.date('d M Y', strtotime($data_peminjam['tgl_pinjam'])).'"
                                                              data-deadline="'.date('d M Y', strtotime($data_peminjam['tgl_kembali_rencana'])).'">
                                                              Dipinjam
                                                           </a>';
                                } else {
                                    $ketersediaan_badge = '<span class="badge-tersedia">Tersedia</span>';
                                }
                            }

                            if (!empty($row['foto']) && file_exists("../assets/img/" . $row['foto'])) {
                                $gambar_tampil = "../assets/img/" . $row['foto'];
                            } else {
                                switch ($row['id_kategori']) {
                                    case 1: $gambar_tampil = "../assets/img/logoberangkat.png"; break;
                                    case 2: $gambar_tampil = "../assets/img/logodkv.png"; break;
                                    case 3: $gambar_tampil = "../assets/img/logomm.png"; break;
                                    case 4: $gambar_tampil = "../assets/img/logoanm.png"; break;
                                    default: $gambar_tampil = "../assets/img/logomm.png"; break;
                                }
                            }
                    ?>
                    <tr id="row_<?= $id_barang; ?>">
                        <td class="col-select-master">
                            <input type="checkbox" name="id_barang_pilihan[]" value="<?= $id_barang; ?>" class="form-check-input select-item-check item-checkbox">
                        </td>
                        
                        <td>
                            <span class="view-mode font-monospace fw-bold text-secondary"><?= !empty($row['kode_barang']) ? htmlspecialchars($row['kode_barang']) : 'Belum Teratur' ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none text-center font-monospace" id="input_kode_<?= $id_barang; ?>" value="<?= htmlspecialchars($row['kode_barang']) ?>" placeholder="Ex: DRN-DKV-01">
                        </td>
                        
                        <td>
                            <span class="view-mode fw-bold text-dark"><?= htmlspecialchars($row['nama_barang']) ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none" id="input_nama_<?= $id_barang; ?>" value="<?= htmlspecialchars($row['nama_barang']) ?>">
                        </td>

                        <?php if ($current_admin_role === 'admin'): ?>
                        <td>
                            <span class="badge-kat-lab"><?= htmlspecialchars($row['nama_kategori']) ?></span>
                        </td>
                        <?php endif; ?>

                        <td>
                            <span class="view-mode text-muted"><?= htmlspecialchars($row['deskripsi']) ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none" id="input_desc_<?= $id_barang; ?>" value="<?= htmlspecialchars($row['deskripsi']) ?>">
                        </td>
                        <td>
                            <span class="view-mode badge rounded-pill fw-bold <?= ($kondisi == 'Baik') ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger' ?> px-3 py-1"><?= $kondisi ?></span>
                            <select class="form-select inline-select edit-mode d-none" id="input_status_<?= $id_barang; ?>">
                                <option value="tersedia" <?= $row['status'] != 'perbaikan' ? 'selected' : '' ?>>Baik (Tersedia)</option>
                                <option value="perbaikan" <?= $row['status'] == 'perbaikan' ? 'selected' : '' ?>>Rusak (Maintenance)</option>
                            </select>
                        </td>
                        <td><?= $ketersediaan_badge ?></td>
                        <td>
                            <img src="<?= $gambar_tampil; ?>" alt="Preview" class="view-mode" style="width: 45px; height: 45px; object-fit: contain; border-radius: 6px;">
                            <input type="file" class="form-control inline-input edit-mode d-none" id="input_foto_<?= $id_barang; ?>" accept="image/*" style="font-size: 11px;">
                        </td> 
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold view-mode" onclick="aktifkanEditInline(<?= $id_barang; ?>)">Edit</button>
                            <div class="edit-mode d-none d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-success px-2 rounded-pill fw-bold" onclick="simpanEditInline(<?= $id_barang; ?>)">Simpan</button>
                                <button type="button" class="btn btn-sm btn-secondary px-2 rounded-pill fw-bold" onclick="batalEditInline(<?= $id_barang; ?>)">Batal</button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='".($current_admin_role === 'admin' ? '9' : '8')."' style='padding: 40px;' class='text-muted'><h5>Tidak ditemukan data aset pada filter ini.</h5></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </form>
        </div>

        <?php if ($total_halaman > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden">
                    <li class="page-item <?= $halaman <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $limit; ?>&filter_kat=<?= $filter_kategori; ?>&search=<?= urlencode($search) ?>" aria-label="Previous">
                            <span>&laquo; Prev</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <li class="page-item <?= $halaman == $i ? 'active' : ''; ?>">
                            <a class="page-link px-3" href="?halaman=<?= $i; ?>&limit=<?= $limit; ?>&filter_kat=<?= $filter_kategori; ?>&search=<?= urlencode($search) ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $halaman >= $total_halaman ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $limit; ?>&filter_kat=<?= $filter_kategori; ?>&search=<?= urlencode($search) ?>" aria-label="Next">
                            <span>Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="text-center text-muted small mt-1">
            Menampilkan data ke-<?= $offset + 1; ?> sampai <?= min($offset + $limit, $total_data); ?> dari total <?= $total_data; ?> aset barang.
        </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalTambahBarang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">Tambah Aset Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_barang_tambah_proses.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_kategori" value="<?= ($id_kategori_admin == 0) ? 1 : $id_kategori_admin; ?>">
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Kode Inventaris :</label>
                            <input type="text" name="kode_barang" class="form-control font-monospace" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Contoh: CAM-DKV-01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nama Barang / Aset :</label>
                            <input type="text" name="nama_barang" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Contoh: Kamera Sony Alpha a6400" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Deskripsi & Spesifikasi :</label>
                            <textarea name="deskripsi" class="form-control" rows="3" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Rincian kelengkapan barang..." required></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Kondisi Awal :</label>
                            <select name="status" class="form-select" style="border: 2px solid var(--tosca-tua); border-radius: 10px;">
                                <option value="tersedia">Baik (Tersedia untuk Dipinjam)</option>
                                <option value="perbaikan">Rusak (Maintenance)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Foto Barang (Opsional) :</label>
                            <input type="file" name="foto" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" accept="image/*">
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);">Simpan Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalPeminjamAktif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #e67e22; border-radius: 15px;">
                <div class="modal-header text-white" style="background-color: #e67e22; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title fw-bold">Detail Peminjam Aktif</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <small class="text-muted d-block">Aset Barang:</small>
                        <h4 class="fw-bold text-dark m-0" id="info_aset"></h4>
                    </div>
                    <hr>
                    <table class="table table-borderless m-0 fs-6">
                        <tr><td class="text-muted py-1" style="width: 40%;">Nama Peminjam</td><td class="fw-bold py-1" id="info_nama"></td></tr>
                        <tr><td class="text-muted py-1">Nomor Induk Siswa (NIS)</td><td class="fw-bold py-1" id="info_username"></td></tr>
                        <tr><td class="text-muted py-1">Tanggal Peminjaman</td><td class="fw-bold text-success py-1" id="info_tgl"></td></tr>
                        <tr><td class="text-muted py-1">Batas Pengembalian</td><td class="fw-bold text-danger py-1" id="info_deadline"></td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalKonfirmasiHapus" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
                <div class="modal-header" style="background-color: #dc3545; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; border-bottom: none;">
                    <h5 class="modal-title fw-bold">Konfirmasi Tindakan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h4 class="mt-3 fw-bold" style="color: #dc3545;">Apakah Anda Yakin?</h4>
                    <p class="text-muted mb-0">Tindakan ini akan menghapus permanen sejumlah <strong id="totalHapusTeks" style="color: #dc3545;">0</strong> aset barang pilihan dari database laboratorium.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">Batal</button>
                    <button type="button" id="btnEksekusiHapusForm" class="btn text-white px-4 fw-bold rounded-pill" style="background-color: #dc3545;">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalHapusKosong" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 15px; border: none;">
                <div class="modal-header" style="background-color: #e67e22; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; border-bottom: none;">
                    <h5 class="modal-title fw-bold">Pemberitahuan</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <h4 class="mt-3 fw-bold" style="color: #e67e22;">Pilihan Kosong</h4>
                    <p class="text-muted mb-0">Silakan pilih minimal satu barang terlebih dahulu sebelum melakukan konfirmasi hapus.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" class="btn text-white px-4 fw-bold rounded-pill" style="background-color: #e67e22;" data-bs-dismiss="modal">Mengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JAVASCRIPT REDIRECT AUTO-SINKRON LIMIT DAN FILTER KATEGORI BARU
        const changeLimit = document.getElementById('changeLimit');
        const filterKategori = document.getElementById('filterKategori');
        const searchVal = "<?= urlencode($search) ?>";

        function reloadHalamanWithFilters() {
            const limitVal = changeLimit.value;
            // Jika dropdown filterKategori tidak ada (login sebagai admin lab biasa), default-kan ke 'all'
            const katVal = filterKategori ? filterKategori.value : 'all';
            window.location.href = `?halaman=1&limit=${limitVal}&filter_kat=${katVal}&search=${searchVal}`;
        }

        changeLimit.addEventListener('change', reloadHalamanWithFilters);
        if (filterKategori) {
            filterKategori.addEventListener('change', reloadHalamanWithFilters);
        }

        const tombolPeminjam = document.querySelectorAll('.tombol-peminjam');
        const modalPeminjam = new bootstrap.Modal(document.getElementById('modalPeminjamAktif'));
        tombolPeminjam.forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault(); 
                document.getElementById('info_aset').innerText = this.getAttribute('data-barang');
                document.getElementById('info_nama').innerText = ": " + this.getAttribute('data-nama');
                document.getElementById('info_username').innerText = ": " + this.getAttribute('data-username');
                document.getElementById('info_tgl').innerText = ": " + this.getAttribute('data-tgl');
                document.getElementById('info_deadline').innerText = ": " + this.getAttribute('data-deadline');
                modalPeminjam.show();
            });
        });

        function aktifkanEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
        }

        function batalEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
            document.getElementById('input_foto_' + id).value = '';
        }

        function simpanEditInline(id) {
            const kodeVal   = document.getElementById('input_kode_' + id).value; 
            const namaVal   = document.getElementById('input_nama_' + id).value;
            const descVal   = document.getElementById('input_desc_' + id).value;
            const statusVal = document.getElementById('input_status_' + id).value;
            const fotoInput = document.getElementById('input_foto_' + id);

            const formData = new FormData();
            formData.append("id_barang", id);
            formData.append("kode_barang", kodeVal); 
            formData.append("nama_barang", namaVal);
            formData.append("deskripsi", descVal);
            formData.append("status", statusVal);

            if (fotoInput.files.length > 0) {
                formData.append("foto", fotoInput.files[0]);
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "admin_barang_edit_inline_proses.php", true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        window.location.reload();
                    } else {
                        alert("Gagal memperbarui data: " + xhr.responseText);
                    }
                }
            };
            xhr.send(formData);
        }

        const btnModeHapus = document.getElementById('btnModeHapus');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const btnBatalHapus = document.getElementById('btnBatalHapus');
        const colSelectMaster = document.querySelectorAll('.col-select-master');
        
        const selectAll = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkCount = document.getElementById('checkCount');

        btnModeHapus.addEventListener('click', function() {
            this.classList.add('d-none');
            btnBatalHapus.classList.remove('d-none');
            btnBulkDelete.classList.remove('d-none');
            colSelectMaster.forEach(el => el.style.display = 'table-cell');
            updateDeleteButtonStatus();
        });

        btnBatalHapus.addEventListener('click', function() {
            btnModeHapus.classList.remove('d-none');
            this.classList.add('d-none');
            btnBulkDelete.classList.add('d-none');
            colSelectMaster.forEach(el => el.style.display = 'none');
            selectAll.checked = false;
            itemCheckboxes.forEach(cb => cb.checked = false);
            updateDeleteButtonStatus();
        });

        function updateDeleteButtonStatus() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            checkCount.innerText = checkedCount;

            if (checkedCount > 0) {
                btnBulkDelete.style.opacity = "1";
                btnBulkDelete.removeAttribute('disabled');
            } else {
                btnBulkDelete.style.opacity = "0.5";
                btnBulkDelete.setAttribute('disabled', 'true');
            }
        }

        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDeleteButtonStatus();
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length) {
                    selectAll.checked = true;
                }
                updateDeleteButtonStatus();
            });
        });

        btnBulkDelete.addEventListener('click', function(e) {
            e.preventDefault();
            const total = document.querySelectorAll('.item-checkbox:checked').length;
            if (total === 0) {
                var modalKosong = new bootstrap.Modal(document.getElementById('modalHapusKosong'));
                modalKosong.show();
                return;
            }
            document.getElementById('totalHapusTeks').innerText = total;
            var modalPeringatan = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));
            modalPeringatan.show();
        });

        document.getElementById('btnEksekusiHapusForm').addEventListener('click', function() {
            document.getElementById('formBulkDelete').submit();
        });
    </script>

    <?php include '../components/footer.php'; ?>
</body>
</html>