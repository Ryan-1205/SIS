<?php 
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// SINKRONISASI AKURAT ID: Mengunci antrean verifikasi berdasarkan ID User Admin yang login
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
    <title>Admin <?= $nama_lab_tampil; ?> - Verifikasi Peminjaman Sesi</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.6">
    <style>
        /* Memastikan sinkronisasi max-width mengikuti batas safezone 80% */
        .admin-search-container, .admin-table-wrapper, .section-title-admin {
            max-width: 1000px !important;
            margin-left: auto;
            margin-right: auto;
        }
        .section-title-admin { color: var(--tosca-tua); font-weight: 700; }
        
        .btn-detail-barang { background-color: #17a2b8; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; cursor: pointer; }
        .btn-detail-barang:hover { background-color: #138496; color: white; }
        .btn-verif { background-color: var(--tosca-tua); color: white; border: none; padding: 6px 20px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn-verif:hover { background-color: #14433e; color: white; }
        
        .badge-waiting { background-color: #ffc107; color: #212529; padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; display: inline-block; }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3 pb-5">
        <?php include 'sub_header_admin.php'; ?>

        <div class="mx-auto mt-4 mb-2" style="max-width: 1000px;">
            <h4 class="section-title-admin m-0">📥 Antrean Persetujuan Sesi: <?= htmlspecialchars($nama_lab_tampil); ?></h4>
        </div>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Cari nama siswa yang mengajukan..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>TANGGAL PINJAM</th>
                        <th>RENCANA KEMBALI</th> <th>DETAIL BARANG</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // PROTEKSI SELEKSI LAB: Hanya menarik barang yang id_kategori-nya sesuai dengan lab admin saat ini
                    $query = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.id_user, users.nama_lengkap,
                                     GROUP_CONCAT(barang.nama_barang SEPARATOR '||') as list_barang,
                                     GROUP_CONCAT(peminjaman.id_pinjam SEPARATOR ',') as list_id_pinjam
                              FROM peminjaman 
                              JOIN users ON peminjaman.id_user = users.id_user 
                              JOIN barang ON peminjaman.id_barang = barang.id_barang 
                              WHERE peminjaman.status_pengajuan = 'pending' AND barang.id_kategori = '$id_kategori_admin'";

                    if ($search != '') {
                        $search_escaped = mysqli_real_escape_string($conn, $search);
                        $query .= " AND users.nama_lengkap LIKE '%$search_escaped%'";
                    }

                    // Dikelompokkan berdasarkan waktu input pinjam dan tenggat kembali rencana siswa
                    $query .= " GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana ORDER BY peminjaman.tgl_pinjam ASC";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                    ?>
                    <tr>
                        <td class="fw-bold text-dark"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td class="font-monospace text-secondary"><?= date('d M Y', strtotime($row['tgl_pinjam'])) ?></td>
                        <td class="font-monospace text-danger fw-bold"><?= date('d M Y', strtotime($row['tgl_kembali_rencana'])) ?></td>
                        <td>
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-barang="<?= htmlspecialchars(str_replace('||', ', ', $row['list_barang'])) ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td><span class="badge-waiting">Pending</span></td>
                        <td>
                            <button type="button" class="btn-verif tombol-setuju" 
                                    data-user="<?= $row['id_user'] ?>" 
                                    data-tgl="<?= $row['tgl_pinjam'] ?>"
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-barang-raw="<?= htmlspecialchars($row['list_barang']) ?>"
                                    data-id-pinjam-raw="<?= htmlspecialchars($row['list_id_pinjam']) ?>"
                                    data-bs-toggle="modal" data-bs-target="#modalVerifikasi">
                               Verifikasi Sesi
                            </button>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 40px;' class='text-muted'><h5>Tidak ada antrean pengajuan baru untuk laboratorium ini.</h5></td></tr>";
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
                    <ul id="container_list_barang" class="list-group list-group-flush fw-bold text-secondary"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">⚙️ Verifikasi Selektif Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_verifikasi_proses.php" method="POST">
                    <div class="modal-body p-4">
                        <p class="text-muted fs-6">Siswa Peminjam: <strong id="nama_peminjam_text" class="text-dark"></strong></p>
                        
                        <div class="mb-4">
                            <label class="form-label fw-bold mb-1" style="color: var(--tosca-tua);">Nama Pengawas Piket Hari Ini :</label>
                            <input type="text" name="pengawas" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px; max-width: 400px;" placeholder="Tulis nama pengawas piket" required>
                        </div>

                        <label class="form-label fw-bold" style="color: var(--tosca-tua);">Tentukan Keputusan Per Item Barang:</label>
                        <div class="table-responsive mt-1">
                            <table class="table table-bordered align-middle text-center" style="border-color: #c4e1db;">
                                <thead style="background-color: #f4faf8; color: var(--tosca-tua); font-weight: 700;">
                                    <tr>
                                        <th class="text-start" style="padding-left: 15px;">Nama Aset Barang</th>
                                        <th width="300">Tindakan Admin</th>
                                    </tr>
                                </thead>
                                <tbody id="container_item_verifikasi">
                                    </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);">Simpan Hasil Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JS MODAL DETAIL BARANG
        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalDetailBS = new bootstrap.Modal(document.getElementById('modalDetail'));
        
        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const barangString = this.getAttribute('data-barang');
                document.getElementById('detail_nama_peminjam').innerText = nama;
                
                const arrayBarang = barangString.split(', ');
                const container = document.getElementById('container_list_barang');
                container.innerHTML = ''; 
                
                arrayBarang.forEach(item => {
                    container.innerHTML += `<li class="list-group-item d-flex align-items-center gap-2">📦 ${item}</li>`;
                });
                modalDetailBS.show();
            });
        });

        // JS MODAL SELEKSI TINDAKAN PER ITEM
        const tombolSetuju = document.querySelectorAll('.tombol-setuju');
        tombolSetuju.forEach(button => {
            button.addEventListener('click', function() {
                const namaPeminjam = this.getAttribute('data-peminjam');
                const rawBarang = this.getAttribute('data-barang-raw').split('||');
                const rawIdPinjam = this.getAttribute('data-id-pinjam-raw').split(',');
                
                document.getElementById('nama_peminjam_text').innerText = namaPeminjam;
                
                const container = document.getElementById('container_item_verifikasi');
                container.innerHTML = '';
                
                rawBarang.forEach((namaBarang, index) => {
                    const idPinjam = rawIdPinjam[index];
                    
                    const rowHTML = `
                        <tr>
                            <td class="text-start fw-bold text-dark" style="padding-left: 15px;">📦 ${namaBarang}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-3">
                                    <input type="radio" class="btn-check" name="status_item[${idPinjam}]" id="accept_${idPinjam}" value="disetujui" checked autocomplete="off">
                                    <label class="btn btn-sm btn-outline-success px-3 rounded-pill fw-bold" for="accept_${idPinjam}">✅ Setujui</label>
                                    
                                    <input type="radio" class="btn-check" name="status_item[${idPinjam}]" id="reject_${idPinjam}" value="ditolak" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-bold" for="reject_${idPinjam}">❌ Tolak</label>
                                </div>
                            </td>
                        </tr>
                    `;
                    container.innerHTML += rowHTML;
                });
            });
        });
    </script>

    <?php include 'footer.php'; ?>

</body>
</html>