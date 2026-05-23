<?php 
include 'koneksi.php'; 
session_start();

// Pastikan user sudah login untuk mengambil data antrean pribadinya
$id_user_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Ambil data referer (halaman asal) untuk mengarahkan tombol "+ Tambah Barang" secara dinamis
$halaman_kembali = 'index.php'; 
if (isset($_SERVER['HTTP_REFERER'])) {
    $halaman_kembali = $_SERVER['HTTP_REFERER'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pinjam Barang - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
    <style>
        /* Mengadopsi style standar admin table agar seragam */
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
            font-size: 15px;
        }
        .admin-table td {
            padding: 15px 15px;
            border-bottom: 1px solid var(--tosca-muda);
            color: #333;
            font-size: 15px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Style Badge Kategori Lab */
        .badge-lab {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
            background-color: var(--tosca-muda);
            color: var(--tosca-tua);
            display: inline-block;
            white-space: nowrap;
        }

        /* Style Badge Status Pengajuan Dinamis */
        .badge-status {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background-color: #ffeaa7; color: #e67e22; }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 mt-5" style="margin-bottom: 150px;">
        
        <h4 class="fw-bold mb-3" style="color: var(--tosca-tua); max-width: 1000px; margin: 0 auto 15px auto;">
            🛒 Keranjang Peminjaman Anda
        </h4>
        <div class="admin-table-wrapper">
            <form action="form_final_pinjam.php" method="POST" id="formPinjam">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-start" style="padding-left: 25px;">NAMA BARANG</th>
                            <th class="text-start">DESKRIPSI</th>
                            <th>KATEGORI ASET</th> 
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $id_item) {
                                $query_item = "SELECT b.*, k.nama_kategori 
                                               FROM barang b 
                                               JOIN kategori k ON b.id_kategori = k.id_kategori 
                                               WHERE b.id_barang = '$id_item'";
                                
                                $res = mysqli_query($conn, $query_item);
                                $row = mysqli_fetch_assoc($res);
                                if ($row) {
                        ?>
                            <tr>
                                <input type="hidden" name="item_pilihan[]" value="<?= $row['id_barang']; ?>">
                                
                                <td class="fw-bold text-start" style="padding-left: 25px; color: var(--tosca-tua);"><?= htmlspecialchars($row['nama_barang']); ?></td>
                                <td class="text-start"><?= htmlspecialchars($row['deskripsi']); ?></td>
                                <td>
                                    <span class="badge-lab">📍 <?= htmlspecialchars($row['nama_kategori']); ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link text-danger fw-bold text-decoration-none p-0 tombol-hapus" 
                                            data-id="<?= $row['id_barang']; ?>" 
                                            data-nama="<?= htmlspecialchars($row['nama_barang']); ?>">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php 
                                }
                            } 
                        } else { ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <span class="fs-6">Keranjang kosong. Belum ada barang yang kamu pilih untuk diajukan.</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        </div>

        <h4 class="fw-bold mb-3 mt-5" style="color: var(--tosca-tua); max-width: 1000px; margin: 40px auto 15px auto;">
            ⏳ Pengajuan yang Menunggu Verifikasi Admin
        </h4>
        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>TANGGAL PENGAJUAN</th>
                        <th class="text-start">NAMA BARANG</th>
                        <th>KATEGORI</th>
                        <th>STATUS PROSES</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query_antrean = "SELECT p.*, b.nama_barang, k.nama_kategori 
                                      FROM peminjaman p
                                      JOIN barang b ON p.id_barang = b.id_barang
                                      JOIN kategori k ON b.id_kategori = k.id_kategori
                                      WHERE p.id_user = '$id_user_login' AND p.status_pengajuan = 'pending'
                                      ORDER BY p.id_pinjam DESC";
                    
                    $res_antrean = mysqli_query($conn, $query_antrean);

                    if (mysqli_num_rows($res_antrean) > 0) {
                        while ($antrean = mysqli_fetch_assoc($res_antrean)) {
                    ?>
                        <tr>
                            <td class="text-muted font-monospace"><?= date('d M Y - H:i', strtotime($antrean['tgl_pinjam'])); ?> WIB</td>
                            <td class="fw-bold text-start text-dark"><?= htmlspecialchars($antrean['nama_barang']); ?></td>
                            <td>
                                <span class="badge-lab">📍 <?= htmlspecialchars($antrean['nama_kategori']); ?></span>
                            </td>
                            <td>
                                <span class="badge-status status-pending">⏳ Menunggu Verifikasi</span>
                            </td>
                        </tr>
                    <?php 
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                <span>Tidak ada pengajuan aktif yang sedang mengantre.</span>
                            </td>
                        </tr>
                    <?php 
                    } 
                    ?>
                </tbody>
            </table>
        </div>

    </div>

    <div class="fixed-bottom action-footer">
        <div class="safe-container d-flex align-items-center justify-content-between px-3 h-100">
            <div>
                <?php if (!empty($_SESSION['keranjang'])): ?>
                    <span class="text-white fw-bold">Total: <?= count($_SESSION['keranjang']); ?> Aset Siap Diajukan</span>
                <?php endif; ?>
            </div>

            <div class="d-flex gap-3">
                <a href="<?= $halaman_kembali; ?>" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3">
                    + Tambah Barang
                </a>
                
                <?php if (!empty($_SESSION['keranjang'])): ?>
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;" onclick="bukaModalTanggal()">
                        Pinjam Sekarang
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3 disabled" style="opacity: 0.5;">
                        Pinjam Sekarang
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalInputTanggal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">📅 Tentukan Waktu Peminjaman</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small mb-3">Silakan tentukan tanggal pemakaian aset lab beserta batas rencana pengembaliannya.</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--tosca-tua);">Tanggal Mulai Pinjam :</label>
                        <input type="date" name="tgl_pinjam" id="modal_tgl_pinjam" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="color: var(--tosca-tua);">Rencana Tanggal Kembali :</label>
                        <input type="date" name="tgl_kembali_rencana" id="modal_tgl_kembali" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);" onclick="validasiDanKirimForm()">Kirim Pengajuan</button>
                </div>
            </div>
        </div>
    </div>
    </form> <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #dc3545; border-radius: 20px; text-align: left;">
                <div class="modal-header text-white" style="background-color: #dc3545; border-top-left-radius: 17px; border-top-right-radius: 17px; border-bottom: none;">
                    <h5 class="modal-title fw-bold">🗑️ Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <span style="font-size: 50px; display: block; margin-bottom: 10px;">🗑️</span>
                    <h5 class="fw-bold mt-2 mb-2" style="color: #dc3545;">Keluarkan dari Daftar?</h5>
                    <p class="text-muted fs-6 mb-2">Apakah Anda yakin ingin menghapus barang berikut dari rencana peminjaman?</p>
                    <strong class="d-block fs-5 text-dark mb-4" id="nama_barang_hapus"></strong>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 gap-3">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" style="font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="link_eksekusi_hapus" class="btn btn-danger px-4 rounded-pill" style="font-weight: 600;">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // LOGIKA MODAL HAPUS ITEM KERANJANG
        const tombolHapus = document.querySelectorAll('.tombol-hapus');
        const modalHapusBS = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));

        tombolHapus.forEach(btn => {
            btn.addEventListener('click', function() {
                const idBarang = this.getAttribute('data-id');
                const namaBarang = this.getAttribute('data-nama');
                document.getElementById('nama_barang_hapus').innerText = namaBarang;
                document.getElementById('link_eksekusi_hapus').setAttribute('href', 'keranjang_hapus.php?id=' + idBarang);
                modalHapusBS.show();
            });
        });

        // ================= LOGIKA BARU: INTERAKSI MODAL INPUT TANGGAL =================
        const modalTanggalBS = new bootstrap.Modal(document.getElementById('modalInputTanggal'));

        function bukaModalTanggal() {
            // Set default minimal tanggal hari ini agar siswa tidak bisa milih tanggal kemarin
            const hariIni = new Date().toISOString().split('T')[0];
            document.getElementById('modal_tgl_pinjam').min = hariIni;
            document.getElementById('modal_tgl_kembali').min = hariIni;
            
            modalTanggalBS.show();
        }

        function validasiDanKirimForm() {
            const tglPinjam = document.getElementById('modal_tgl_pinjam').value;
            const tglKembali = document.getElementById('modal_tgl_kembali').value;

            // Validasi 1: Kelengkapan input
            if (tglPinjam === "" || tglKembali === "") {
                alert("⚠️ Mohon lengkapi Tanggal Pinjam dan Rencana Kembali!");
                return;
            }

            // Validasi 2: Rencana tanggal kembali tidak boleh mendahului tanggal pinjam
            if (new Date(tglKembali) < new Date(tglPinjam)) {
                alert("❌ Logika Salah! Rencana tanggal pengembalian tidak boleh mendahului tanggal mulai meminjam.");
                return;
            }

            // Jika validasi sukses, jalankan instruksi submit form
            document.getElementById('formPinjam').submit();
        }
    </script>
</body>
</html>