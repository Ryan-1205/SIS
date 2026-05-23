<?php 
include 'koneksi.php'; 
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
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
    <link rel="stylesheet" href="assets/css/style.css?v=1.3">
    <style>
        /* Mengadopsi style standar admin table agar seragam */
        .admin-table-wrapper {
            max-width: 1000px;
            margin: 0 auto 50px auto;
            border: 2px solid var(--tosca-tua);
            background: white;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .admin-table th {
            color: var(--tosca-tua);
            font-weight: 700;
            padding: 20px 15px;
            border-bottom: 2px solid var(--tosca-tua);
            font-size: 16px;
        }
        .admin-table td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--tosca-tua);
            color: #333;
            font-size: 16px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        
        /* Style Badge Kategori Lab */
        .badge-lab {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
            background-color: var(--tosca-muda);
            color: var(--tosca-tua);
            display: inline-block;
            white-space: nowrap;
        }
    </style>
</head>
<body>

    <?php include 'header.php'; ?>
    <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 mt-5" style="margin-bottom: 120px;">
        <div class="admin-table-wrapper">
            <form action="form_final_pinjam.php" method="POST" id="formPinjam">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-start" style="padding-left: 25px;">NAMA BARANG</th>
                            <th class="text-start">DESKRIPSI</th>
                            <th>KATEGORI ASET</th> <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $id_item) {
                                // Ambil data barang dengan JOIN kategori lab
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
                                    <span class="badge-lab">
                                        📍 <?= htmlspecialchars($row['nama_kategori']); ?>
                                    </span>
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
                                <td colspan="4" class="text-center py-5 text-mutedfw-bold">
                                    <h5>Daftar pinjam masih kosong.</h5>
                                    <a href="index.php" class="btn btn-sm text-white mt-2 px-4 rounded-pill" style="background-color: var(--tosca-tua); font-weight: 600;">Cari Barang</a>
                                </td>
                            </tr>
                        <?php } ?>
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
                <a href="javascript:history.back()" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3">
                    + Tambah Barang
                </a>
                
                <?php if (!empty($_SESSION['keranjang'])): ?>
                    <button type="submit" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;">
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
    </form>

    <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
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
        // Logika Oper Data ke Modal Konfirmasi Hapus
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
    </script>
</body>
</html>