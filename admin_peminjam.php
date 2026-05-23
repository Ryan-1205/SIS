<?php 
session_start();
include 'koneksi.php';

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
    <title>Admin - Data Peminjam Sesi</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.3">
    <style>
        /* Sinkronisasi style tombol dan text status sesuai halaman verifikasi */
        .btn-detail-barang { 
            background-color: #17a2b8; 
            color: white; 
            border: none; 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-weight: 600; 
            font-size: 13px; 
            text-decoration: none; 
            cursor: pointer; 
        }
        .btn-detail-barang:hover { 
            background-color: #138496; 
            color: white; 
        }
        .text-hijau { color: #28a745; font-weight: 600; }
        .text-merah { color: #dc3545; font-weight: 600; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="safe-container px-3 pb-5">
        <?php include 'sub_header_admin.php'; ?>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tuliskan Nama/NIS/Angkatan" value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PEMINJAMAN</th>
                        <th>DETAIL BARANG</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Query dikunci per sesi (Kombinasi ID User + Tanggal Ambil) dengan GROUP_CONCAT
                    $query = "SELECT p.tgl_pinjam, p.id_user, p.status_pengajuan, p.diverifikasi_oleh, u.nama_lengkap,
                                     GROUP_CONCAT(b.nama_barang SEPARATOR ', ') as list_barang 
                              FROM peminjaman p 
                              JOIN users u ON p.id_user = u.id_user
                              JOIN barang b ON p.id_barang = b.id_barang
                              WHERE p.status_pengajuan IN ('disetujui', 'kembali')";
                    
                    if ($search != '') {
                        $query .= " AND u.nama_lengkap LIKE '%$search%'";
                    }
                    
                    $query .= " GROUP BY p.id_user, p.tgl_pinjam, p.status_pengajuan, p.diverifikasi_oleh 
                                ORDER BY p.tgl_pinjam DESC";
                                
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            // Logika penentuan status baris
                            $status_text = ($row['status_pengajuan'] == 'kembali') ? 'Sudah Kembali' : 'Belum Kembali';
                            $status_class = ($row['status_pengajuan'] == 'kembali') ? 'text-hijau' : 'text-merah';
                            
                            $tgl = date('d-m-Y', strtotime($row['tgl_pinjam']));
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= $tgl ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-tgl="<?= $tgl ?>"
                                    data-barang="<?= htmlspecialchars($row['list_barang']) ?>"
                                    data-pengawas="<?= !empty($row['diverifikasi_oleh']) ? htmlspecialchars($row['diverifikasi_oleh']) : 'Admin'; ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td class="<?= $status_class ?>"><?= $status_text ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4' style='padding: 20px;'>Belum ada data peminjaman.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalDetailBarang" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 15px;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 12px; border-top-right-radius: 12px;">
                    <h5 class="modal-title fw-bold">Detail Sesi Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <small class="text-muted d-block">Nama Peminjam:</small>
                            <h5 class="fw-bold text-dark m-0" id="txt_nama"></h5>
                        </div>
                        <div class="text-end">
                            <small class="text-muted d-block">Tanggal Ambil:</small>
                            <strong class="text-dark" id="txt_tanggal"></strong>
                        </div>
                    </div>
                    
                    <p class="mb-1 text-muted">Daftar Barang yang Diambil:</p>
                    <ul class="list-group list-group-flush mb-4 border rounded" id="list_container">
                        </ul>

                    <div class="p-2 rounded bg-light border-start border-4 border-success">
                        <small class="text-muted d-block">Petugas/Pengawas yang Memverifikasi:</small>
                        <strong class="text-success" id="txt_pengawas"></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS Handler Modal Detail Barang
        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalBS = new bootstrap.Modal(document.getElementById('modalDetailBarang'));

        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const tanggal = this.getAttribute('data-tgl');
                const barangString = this.getAttribute('data-barang');
                const pengawas = this.getAttribute('data-pengawas');

                document.getElementById('txt_nama').innerText = nama;
                document.getElementById('txt_tanggal').innerText = tanggal;
                document.getElementById('txt_pengawas').innerText = "🔑 " + pengawas;

                const arrayBarang = barangString.split(', ');
                const container = document.getElementById('list_container');
                container.innerHTML = ''; 

                arrayBarang.forEach(item => {
                    container.innerHTML += `<li class="list-group-item d-flex align-items-center gap-2">📦 ${item}</li>`;
                });

                modalBS.show();
            });
        });
    </script>
</body>
</html>