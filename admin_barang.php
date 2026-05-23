<?php 
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Data Barang</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.5">
    <style>
        /* CSS internal bawaan rancangan tabel admin kamu */
        .admin-header { display: flex; align-items: center; justify-content: center; position: relative; margin: 50px 0 40px 0; }
        .admin-back-btn { position: absolute; left: 0; color: var(--tosca-tua); }
        .admin-tabs { display: flex; gap: 20px; font-weight: 700; font-size: 20px; }
        .admin-tab { text-decoration: none; color: var(--tosca-tua); padding: 8px 25px; border-radius: 50px; transition: 0.3s; }
        .admin-tab.active { background-color: var(--tosca-muda); }
        .admin-search-container { max-width: 1000px; margin: 0 auto 30px auto; }
        .admin-search-form { display: flex; border: 2px solid var(--tosca-tua); border-radius: 50px; padding: 4px; background: white; }
        .admin-search-form input { flex-grow: 1; border: none; padding: 10px 25px; outline: none; color: var(--tosca-tua); font-size: 16px; background: transparent; }
        .admin-search-form input::placeholder { color: #6c8a87; }
        .admin-search-form button { background-color: var(--tosca-tua); color: white; border: none; padding: 8px 50px; border-radius: 50px; font-weight: 700; font-size: 18px; cursor: pointer; }
        .admin-table-wrapper { max-width: 1000px; margin: 0 auto 50px auto; border: 2px solid var(--tosca-tua); background: white; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; border-bottom: 2px solid var(--tosca-tua); font-size: 14px; }
        .admin-table td { padding: 20px 15px; border-bottom: 1px solid var(--tosca-tua); color: #333; font-size: 15px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .btn-admin-action { background-color: var(--tosca-tua); color: white; text-decoration: none; padding: 10px 45px; border-radius: 30px; font-weight: 700; font-size: 18px; border: none; cursor: pointer; display: inline-block; text-align: center; }
        .btn-admin-action:hover { opacity: 0.9; color: white; }

        /* FIX BADGE TERPOTONG */
        .badge-tersedia { 
            background-color: #28a745; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; 
            white-space: nowrap; display: inline-block; 
        }
        .badge-dipinjam { 
            background-color: #e67e22; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; 
            white-space: nowrap; display: inline-block; cursor: pointer; text-decoration: none;
        }
        .badge-dipinjam:hover { color: white; opacity: 0.9; }
        .badge-perbaikan { 
            background-color: #dc3545; color: white; padding: 6px 16px; border-radius: 50px; font-size: 13px; font-weight: 600; 
            white-space: nowrap; display: inline-block; 
        }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3">
        
        <?php include 'sub_header_admin.php'; ?>
        
        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tuliskan kata kunci pencarian data master..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>KODE BARANG</th>
                        <th>NAMA BARANG</th>
                        <th>DESKRIPSI</th>
                        <th>KONDISI FISIK</th>
                        <th>KETERSEDIAAN</th>
                        <th>FOTO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM barang";
                    if ($search != '') {
                        $query .= " WHERE id_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' OR deskripsi LIKE '%$search%' OR status LIKE '%$search%'";
                    }
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $id_barang = $row['id_barang'];
                            $kondisi = ($row['status'] == 'perbaikan') ? 'Rusak' : 'Baik';

                            // --- LOGIKA MENENTUKAN STATUS KETERSEDIAAN & DETAIL PEMINJAM ---
                            if ($row['status'] == 'perbaikan') {
                                $ketersediaan_badge = '<span class="badge-perbaikan">Maintenance</span>';
                            } else {
                                // SINKRONISASI: Mengubah u.username menjadi u.nis sesuai perubahan tabel DB terbaru
                                $cek_pinjam = mysqli_query($conn, "SELECT p.tgl_pinjam, p.tgl_kembali_rencana, u.nama_lengkap, u.nis 
                                                                   FROM peminjaman p 
                                                                   JOIN users u ON p.id_user = u.id_user 
                                                                   WHERE p.id_barang = '$id_barang' AND p.status_pengajuan = 'disetujui'");
                                
                                if (mysqli_num_rows($cek_pinjam) > 0) {
                                    $data_peminjam = mysqli_fetch_assoc($cek_pinjam);
                                    
                                    // SINKRONISASI: data-username diubah mengambil data_peminjam['nis']
                                    $ketersediaan_badge = '<a href="#" class="badge-dipinjam tombol-peminjam" 
                                                              data-barang="'.htmlspecialchars($row['nama_barang']).'"
                                                              data-nama="'.htmlspecialchars($data_peminjam['nama_lengkap']).'"
                                                              data-username="'.htmlspecialchars($data_peminjam['nis']).'"
                                                              data-tgl="'.date('d M Y', strtotime($data_peminjam['tgl_pinjam'])).'"
                                                              data-deadline="'.date('d M Y', strtotime($data_peminjam['tgl_kembali_rencana'])).'">
                                                              Sedang Dipinjam
                                                           </a>';
                                } else {
                                    $ketersediaan_badge = '<span class="badge-tersedia">Tersedia</span>';
                                }
                            }
                    ?>
                    <tr>
                        <td><?= sprintf("%02d", $row['id_barang']) ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><?= $kondisi ?></td>
                        <td><?= $ketersediaan_badge ?></td>
                        <td>+</td> 
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 30px;'>Tidak ada data barang.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-3" style="max-width: 1000px; margin: 0 auto 50px auto;">
            <a href="admin_barang_tambah.php" class="btn-admin-action">Tambah</a>
            <a href="#" class="btn-admin-action">Edit</a>
        </div>
    </div>

    <div class="modal fade" id="modalPeminjamAktif" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #e67e22; border-radius: 15px;">
                <div class="modal-header text-white" style="background-color: #e67e22; border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title fw-bold">Status Peminjam Aktif</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="text-center mb-3">
                        <small class="text-muted d-block">Aset Barang:</small>
                        <h4 class="fw-bold text-dark m-0" id="info_aset"></h4>
                    </div>
                    <hr>
                    <table class="table table-borderless m-0 fs-6">
                        <tr>
                            <td class="text-muted py-1" style="width: 40%;">Nama Peminjam</td>
                            <td class="fw-bold py-1" id="info_nama"></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Nomor Induk Siswa (NIS)</td> <td class="fw-bold py-1" id="info_username"></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Tanggal Pinjam</td>
                            <td class="fw-bold text-success py-1" id="info_tgl"></td>
                        </tr>
                        <tr>
                            <td class="text-muted py-1">Batas Pengembalian</td>
                            <td class="fw-bold text-danger py-1" id="info_deadline"></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
    </script>
</body>
</html>