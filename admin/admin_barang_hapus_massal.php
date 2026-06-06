<?php
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Inisialisasi status data untuk pemanggilan modal popup
$status_hapus = '';
$jumlah_sukses = 0;

// Memastikan ada data barang yang dikirim lewat checkbox
if (isset($_POST['id_barang_pilihan']) && is_array($_POST['id_barang_pilihan'])) {
    $id_barang_array = $_POST['id_barang_pilihan'];
    
    $sukses = 0;
    $gagal = 0;

    foreach ($id_barang_array as $id_barang) {
        $id_barang = mysqli_real_escape_string($conn, $id_barang);

        // JAGA-JAGA OPTIONAL: Hapus file fisik foto lama di folder img jika ada biar server ga penuh sampah
        $query_foto = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang = '$id_barang'");
        $data_foto = mysqli_fetch_assoc($query_foto);
        if (!empty($data_foto['foto']) && file_exists("assets/img/" . $data_foto['foto'])) {
            unlink("assets/img/" . $data_foto['foto']); // Hapus file foto fisik
        }

        // Eksekusi hapus baris record dari database
        $query_delete = "DELETE FROM barang WHERE id_barang = '$id_barang'";
        if (mysqli_query($conn, $query_delete)) {
            $sukses++;
        } else {
            $gagal++;
        }
    }

    $status_hapus = 'sukses_massal';
    $jumlah_sukses = $sukses;

} else {
    $status_hapus = 'kosong';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Penghapusan Aset</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-header { background-color: #dc3545; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; border-bottom: none; }
        .btn-danger-custom { background-color: #dc3545; color: white; border-radius: 8px; font-weight: 600; padding: 10px 30px; border: none; }
        .btn-danger-custom:hover { background-color: #bd2130; color: white; }
        .btn-warning-custom { background-color: #e67e22; color: white; border-radius: 8px; font-weight: 600; padding: 10px 30px; border: none; }
        .btn-warning-custom:hover { background-color: #d35400; color: white; }
    </style>
</head>
<body>

    <?php if ($status_hapus == 'sukses_massal') : ?>
    <div class="modal fade" id="hasilHapusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">🗑️ Pemberitahuan Sistem</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <span style="font-size: 60px;">🗑️</span>
                    <h4 class="mt-3 fw-bold" style="color: #dc3545;">Penghapusan Selesai</h4>
                    <p class="text-muted mb-0">Berhasil membersihkan sebanyak <strong><?= $jumlah_sukses; ?></strong> data aset barang dari database.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <a href="admin_barang.php" class="btn btn-danger-custom text-decoration-none">OK</a>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($status_hapus == 'kosong') : ?>
    <div class="modal fade" id="hasilHapusModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #e67e22;">
                    <h5 class="modal-title fw-bold">⚠️ Data Tidak Ditemukan</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <span style="font-size: 60px;">⚠️</span>
                    <h4 class="mt-3 fw-bold" style="color: #e67e22;">Aksi Ditolak!</h4>
                    <p class="text-muted mb-0">Tidak ada parameter aset barang yang dipilih untuk dilakukan penghapusan.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <a href="admin_barang.php" class="btn btn-warning-custom text-decoration-none">Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalElemen = document.getElementById('hasilHapusModal');
            if (modalElemen) {
                var pemicuModal = new bootstrap.Modal(modalElemen);
                pemicuModal.show();
            }
        });
    </script>
</body>
</html>