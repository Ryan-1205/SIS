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
    <title>Admin - Verifikasi Peminjaman Sesi</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.9">
    <style>
        .admin-header { display: flex; align-items: center; justify-content: center; position: relative; margin: 50px 0 40px 0; }
        .admin-back-btn { position: absolute; left: 0; color: var(--tosca-tua); }
        .admin-tabs { display: flex; gap: 20px; font-weight: 700; font-size: 20px; }
        .admin-tab { text-decoration: none; color: var(--tosca-tua); padding: 8px 25px; border-radius: 50px; transition: 0.3s; }
        .admin-tab.active { background-color: var(--tosca-muda); }
        .admin-search-container { max-width: 1000px; margin: 0 auto 30px auto; }
        .admin-search-form { display: flex; border: 2px solid var(--tosca-tua); border-radius: 50px; padding: 4px; background: white; }
        .admin-search-form input { flex-grow: 1; border: none; padding: 10px 25px; outline: none; color: var(--tosca-tua); font-size: 16px; background: transparent; }
        .admin-search-form button { background-color: var(--tosca-tua); color: white; border: none; padding: 8px 50px; border-radius: 50px; font-weight: 700; font-size: 18px; cursor: pointer; }
        .admin-table-wrapper { max-width: 1000px; margin: 0 auto 40px auto; border: 2px solid var(--tosca-tua); background: white; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; border-bottom: 2px solid var(--tosca-tua); font-size: 16px; }
        .admin-table td { padding: 20px 15px; border-bottom: 1px solid var(--tosca-tua); color: #333; font-size: 16px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        
        .btn-detail-barang { background-color: #17a2b8; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; cursor: pointer; }
        .btn-detail-barang:hover { background-color: #138496; color: white; }
        .btn-verif { background-color: #28a745; color: white; border: none; padding: 6px 20px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn-verif:hover { background-color: #218838; color: white; }
        
        .badge-waiting { background-color: #ffc107; color: #212529; padding: 5px 12px; border-radius: 15px; font-size: 14px; font-weight: 600; }
        .badge-approved { background-color: #28a745; color: white; padding: 5px 12px; border-radius: 15px; font-size: 14px; font-weight: 600; }
        .section-title-admin { max-width: 1000px; margin: 0 auto 15px auto; color: var(--tosca-tua); font-weight: 700; }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3 pb-5">
        
        <?php include 'sub_header_admin.php'; ?>

        <h4 class="section-title-admin">📥 Antrean Persetujuan Sesi</h4>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PINJAM</th>
                        <th>DETAIL BARANG</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // GROUP BY digunakan untuk menyatukan barang-barang yang dipinjam di hari yang sama oleh orang yang sama
                    $query = "SELECT peminjaman.tgl_pinjam, peminjaman.id_user, users.nama_lengkap,
                                     GROUP_CONCAT(barang.nama_barang SEPARATOR ', ') as list_barang
                              FROM peminjaman 
                              JOIN users ON peminjaman.id_user = users.id_user 
                              JOIN barang ON peminjaman.id_barang = barang.id_barang 
                              WHERE peminjaman.status_pengajuan = 'pending'";

                    if ($search != '') {
                        $query .= " AND users.nama_lengkap LIKE '%$search%'";
                    }

                    $query .= " GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam ORDER BY peminjaman.tgl_pinjam ASC";
                    
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-barang="<?= htmlspecialchars($row['list_barang']) ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td><span class="badge-waiting">Pending</span></td>
                        <td>
                            <button type="button" class="btn-verif tombol-setuju" 
                                    data-user="<?= $row['id_user'] ?>" 
                                    data-tgl="<?= $row['tgl_pinjam'] ?>"
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalVerifikasi">
                               Setujui Sesi
                            </button>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px;'>Tidak ada antrean sesi peminjaman.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <h4 class="section-title-admin mt-5">📜 Riwayat Verifikasi Keluar</h4>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PINJAM</th>
                        <th>DETAIL BARANG</th>
                        <th>STATUS</th>
                        <th>DIVERIFIKASI OLEH</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query_history = "SELECT peminjaman.tgl_pinjam, peminjaman.diverifikasi_oleh, peminjaman.status_pengajuan, users.nama_lengkap,
                                             GROUP_CONCAT(barang.nama_barang SEPARATOR ', ') as list_barang
                                      FROM peminjaman 
                                      JOIN users ON peminjaman.id_user = users.id_user 
                                      JOIN barang ON peminjaman.id_barang = barang.id_barang 
                                      WHERE peminjaman.status_pengajuan IN ('disetujui', 'kembali')
                                      GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.diverifikasi_oleh, peminjaman.status_pengajuan
                                      ORDER BY peminjaman.tgl_pinjam DESC";
                    
                    $sql_history = mysqli_query($conn, $query_history);

                    if (mysqli_num_rows($sql_history) > 0) {
                        while($row_h = mysqli_fetch_assoc($sql_history)) {
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row_h['nama_lengkap']) ?></td>
                        <td><?= date('d M Y', strtotime($row_h['tgl_pinjam'])) ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row_h['nama_lengkap']) ?>"
                                    data-barang="<?= htmlspecialchars($row_h['list_barang']) ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td><span class="badge-approved"><?= ucfirst($row_h['status_pengajuan']) ?></span></td>
                        <td class="fw-bold text-success">
                            <?= !empty($row_h['diverifikasi_oleh']) ? htmlspecialchars($row_h['diverifikasi_oleh']) : 'Admin'; ?>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px;'>Belum ada riwayat verifikasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #17a2b8; border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: #17a2b8; border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">Daftar Barang Dipinjam</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">Peminjam: <strong id="detail_nama_peminjam" class="text-dark"></strong></p>
                    <hr>
                    <ul id="container_list_barang" class="list-group list-group-flush fw-bold text-secondary">
                        </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">Konfirmasi Pengawas Sesi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_verifikasi_proses.php" method="POST">
                    <div class="modal-body p-4">
                        <input type="hidden" name="id_user" id="modal_id_user">
                        <input type="hidden" name="tgl_pinjam" id="modal_tgl_pinjam">
                        
                        <p class="text-muted">Menyetujui semua barang milik: <strong id="nama_peminjam_text" class="text-dark"></strong></p>
                        
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nama Pengawas Hari Ini :</label>
                            <input type="text" name="pengawas" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Tulis nama pengawas piket" required>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);">Setujui Semua</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS UNTUK MODAL DETAIL BARANG
        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalDetailBS = new bootstrap.Modal(document.getElementById('modalDetail'));
        
        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const barangString = this.getAttribute('data-barang');
                
                document.getElementById('detail_nama_peminjam').innerText = nama;
                
                // Pecah string nama barang menjadi list HTML
                const arrayBarang = barangString.split(', ');
                const container = document.getElementById('container_list_barang');
                container.innerHTML = ''; // bersihkan isi lama
                
                arrayBarang.forEach(item => {
                    container.innerHTML += `<li class="list-group-item d-flex align-items-center gap-2">📦 ${item}</li>`;
                });
                
                modalDetailBS.show();
            });
        });

        // JS UNTUK MODAL INPUT PENGAWAS
        const tombolSetuju = document.querySelectorAll('.tombol-setuju');
        tombolSetuju.forEach(button => {
            button.addEventListener('click', function() {
                document.getElementById('modal_id_user').value = this.getAttribute('data-user');
                document.getElementById('modal_tgl_pinjam').value = this.getAttribute('data-tgl');
                document.getElementById('nama_peminjam_text').innerText = this.getAttribute('data-peminjam');
            });
        });
    </script>
</body>
</html>