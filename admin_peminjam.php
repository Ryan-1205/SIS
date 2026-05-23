<?php 
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// SINKRONISASI AKURAT ID: Mengunci kategori lab berdasarkan ID User Admin yang login
// id_user = 1 (timber) -> Kategori Lab 1
// id_user = 2 (dkv)    -> Kategori Lab 2
// id_user = 3 (mm)     -> Kategori Lab 3
// id_user = 4 (anm)    -> Kategori Lab 4
$id_kategori_admin = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;

// Ambil nama kategori lab saat ini untuk judul dashboard admin
$query_nama_lab = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori_admin'");
$data_lab = mysqli_fetch_array($query_nama_lab);
$nama_lab_tampil = $data_lab ? $data_lab['nama_kategori'] : "Laboratorium";

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin <?= $nama_lab_tampil; ?> - Data Peminjam & History</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.7">
    <style>
        /* Memastikan sinkronisasi max-width mengikuti batas safezone 80% */
        .admin-search-container, .admin-table-wrapper, .section-title-admin {
            max-width: 1000px !important;
            margin-left: auto;
            margin-right: auto;
        }
        
        .btn-detail-barang { background-color: #17a2b8; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; cursor: pointer; }
        .btn-detail-barang:hover { background-color: #138496; color: white; }
        .badge-approved { background-color: #28a745; color: white; padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; }
        .badge-returned { background-color: #0288d1; color: white; padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; }
        .badge-rejected { background-color: #dc3545; color: white; padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; }
        .section-title-admin { color: var(--tosca-tua); font-weight: 700; }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3 pb-5 mt-4">
        <?php include 'sub_header_admin.php'; ?>

        <div class="mx-auto mt-4 mb-2" style="max-width: 1000px;">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua);">📍 Log Aktivitas Peminjaman: <?= htmlspecialchars($nama_lab_tampil); ?></h4>
        </div>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Cari nama siswa..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <h4 class="section-title-admin">🖥️ Daftar Siswa yang Sedang Meminjam Aset</h4>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PINJAM</th>
                        <th>RENCANA KEMBALI</th>
                        <th>DAFTAR BARANG</th>
                        <th>STATUS</th>
                        <th>PENGAWAS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // SINKRONISASI FILTER: Menambahkan klausa 'AND barang.id_kategori = $id_kategori_admin'
                    $query_aktif = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.diverifikasi_oleh, users.nama_lengkap,
                                           GROUP_CONCAT(CONCAT(barang.nama_barang, '::', peminjaman.status_pengajuan) SEPARATOR '||') as list_barang_status
                                    FROM peminjaman 
                                    JOIN users ON peminjaman.id_user = users.id_user 
                                    JOIN barang ON peminjaman.id_barang = barang.id_barang 
                                    WHERE peminjaman.status_pengajuan = 'disetujui' AND barang.id_kategori = '$id_kategori_admin'";

                    if ($search != '') {
                        $search_escaped = mysqli_real_escape_string($conn, $search);
                        $query_aktif .= " AND users.nama_lengkap LIKE '%$search_escaped%'";
                    }

                    $query_aktif .= " GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.diverifikasi_oleh ORDER BY peminjaman.tgl_pinjam DESC";
                    $sql_aktif = mysqli_query($conn, $query_aktif);

                    if (mysqli_num_rows($sql_aktif) > 0) {
                        while($row = mysqli_fetch_assoc($sql_aktif)) {
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td class="font-monospace text-secondary"><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                        <td class="font-monospace text-danger fw-bold"><?= date('d M Y', strtotime($row['tgl_kembali_rencana'])) ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-items="<?= htmlspecialchars($row['list_barang_status']) ?>">
                                🔍 Lihat Aset
                            </button>
                        </td>
                        <td><span class="badge-approved">Sedang Dipinjam</span></td>
                        <td class="text-muted fw-bold"><?= !empty($row['diverifikasi_oleh']) ? htmlspecialchars($row['diverifikasi_oleh']) : 'Admin'; ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-4 text-center text-muted'>Saat ini tidak ada aset dari lab ini yang sedang dipinjam siswa.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h4 class="section-title-admin mt-5">📜 History Riwayat Peminjaman (Selesai/Ditolak)</h4>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PINJAM</th>
                        <th>RENCANA KEMBALI</th>
                        <th>KEMBALI ASLI</th>
                        <th>DAFTAR BARANG</th>
                        <th>VERIFIKATOR</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // SINKRONISASI FILTER: Menambahkan klausa 'AND barang.id_kategori = $id_kategori_admin'
                    $query_history = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.tgl_kembali_asli, peminjaman.diverifikasi_oleh, users.nama_lengkap,
                                             GROUP_CONCAT(CONCAT(barang.nama_barang, '::', peminjaman.status_pengajuan) SEPARATOR '||') as list_barang_status
                                      FROM peminjaman 
                                      JOIN users ON peminjaman.id_user = users.id_user 
                                      JOIN barang ON peminjaman.id_barang = barang.id_barang 
                                      WHERE peminjaman.status_pengajuan IN ('kembali', 'ditolak') AND barang.id_kategori = '$id_kategori_admin'";

                    if ($search != '') {
                        $query_history .= " AND users.nama_lengkap LIKE '%$search%'";
                    }

                    $query_history .= " GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.tgl_kembali_asli, peminjaman.diverifikasi_oleh ORDER BY peminjaman.tgl_pinjam DESC";
                    $sql_history = mysqli_query($conn, $query_history);

                    if (mysqli_num_rows($sql_history) > 0) {
                        while($row_h = mysqli_fetch_assoc($sql_history)) {
                            $tgl_kembali_asli = (!empty($row_h['tgl_kembali_asli']) && $row_h['tgl_kembali_asli'] != '0000-00-00') ? date('d M Y', strtotime($row_h['tgl_kembali_asli'])) : '-';
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row_h['nama_lengkap']) ?></td>
                        <td class="font-monospace text-secondary"><?= date('d M Y', strtotime($row_h['tgl_pinjam'])) ?></td>
                        <td class="font-monospace text-muted"><?= date('d M Y', strtotime($row_h['tgl_kembali_rencana'])) ?></td>
                        <td class="font-monospace text-success fw-bold"><?= $tgl_kembali_asli ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row_h['nama_lengkap']) ?>"
                                    data-items="<?= htmlspecialchars($row_h['list_barang_status']) ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td class="fw-bold text-success"><?= !empty($row_h['diverifikasi_oleh']) ? htmlspecialchars($row_h['diverifikasi_oleh']) : 'Admin'; ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' class='py-4 text-center text-muted'>Belum ada rekaman riwayat peminjaman untuk kategori lab ini.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content" style="border: 2px solid #17a2b8; border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: #17a2b8; border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">📋 Rincian Status Barang per Item</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted fs-6">Siswa: <strong id="detail_nama_peminjam" class="text-dark"></strong></p>
                    <hr>
                    <ul id="container_list_barang" class="list-group list-group-flush fw-bold">
                         </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalDetailBS = new bootstrap.Modal(document.getElementById('modalDetail'));
        
        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const rawItemsData = this.getAttribute('data-items'); 
                
                document.getElementById('detail_nama_peminjam').innerText = nama;
                
                const container = document.getElementById('container_list_barang');
                container.innerHTML = ''; 
                
                const arraySesiBarang = rawItemsData.split('||');
                
                arraySesiBarang.forEach(itemRaw => {
                    const bagian = itemRaw.split('::');
                    const namaBarang = bagian[0];
                    const statusBarang = bagian[1];
                    
                    let badgeHTML = '';
                    if (statusBarang === 'disetujui') {
                        badgeHTML = `<span class="badge badge-approved bg-success text-white px-2 py-1 rounded-pill" style="font-size:11px;">Dipinjam</span>`;
                    } else if (statusBarang === 'kembali') {
                        badgeHTML = `<span class="badge badge-returned bg-primary text-white px-2 py-1 rounded-pill" style="font-size:11px;">Selesai</span>`;
                    } else if (statusBarang === 'ditolak') {
                        badgeHTML = `<span class="badge badge-rejected bg-danger text-white px-2 py-1 rounded-pill" style="font-size:11px;">Ditolak</span>`;
                    } else {
                        badgeHTML = `<span class="badge bg-warning text-dark px-2 py-1 rounded-pill" style="font-size:11px;">Pending</span>`;
                    }
                    
                    container.innerHTML += `
                        <li class="list-group-item d-flex align-items-center justify-content-between border-bottom py-2 px-1">
                            <span class="text-secondary">📦 ${namaBarang}</span>
                            ${badgeHTML}
                        </li>`;
                });
                
                modalDetailBS.show();
            });
        });
    </script>
</body>
</html>