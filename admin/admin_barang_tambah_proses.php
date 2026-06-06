<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin_') === false) { 
    exit; 
}

$id_kategori = $_POST['id_kategori'];
$kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']); 
$nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
$deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
$status      = $_POST['status'];

$foto_nama = "";
if (!empty($_FILES['foto']['name'])) {
    $foto_nama = time() . "_" . $_FILES['foto']['name'];
    // Penyesuaian Jalur: File diunggah ke direktori assets yang berada di luar folder admin/
    move_uploaded_file($_FILES['foto']['tmp_name'], "../assets/img/" . $foto_nama);
}

$status_proses = '';

$query = "INSERT INTO barang (kode_barang, nama_barang, deskripsi, status, id_kategori, foto) 
          VALUES ('$kode_barang', '$nama_barang', '$deskripsi', '$status', '$id_kategori', '$foto_nama')";

if (mysqli_query($conn, $query)) {
    $status_proses = 'sukses';
} else {
    $status_proses = 'gagal';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Tambah Aset</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f8f9fa; }
        .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-header { background-color: var(--tosca-tua, #1d5c56); color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; border-bottom: none; }
        .btn-tosca { background-color: var(--tosca-tua, #1d5c56); color: white; border-radius: 8px; font-weight: 600; padding: 10px 30px; border: none; }
        .btn-tosca:hover { opacity: 0.9; color: white; }
        .btn-danger-custom { background-color: #dc3545; color: white; border-radius: 8px; font-weight: 600; padding: 10px 30px; border: none; }
        .btn-danger-custom:hover { background-color: #bd2130; color: white; }
    </style>
</head>
<body>

    <?php if ($status_proses == 'sukses') : ?>
    <div class="modal fade" id="barangModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Notifikasi Sistem</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <h4 class="mt-3 fw-bold" style="color: #1d5c56;">Data Berhasil Disimpan</h4>
                    <p class="text-muted mb-0">Aset barang baru <strong><?= htmlspecialchars($nama_barang); ?></strong> telah sukses dimasukkan ke dalam sistem database.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <a href="admin_barang.php" class="btn btn-tosca text-decoration-none">Selesai</a>
                </div>
            </div>
        </div>
    </div>

    <?php elseif ($status_proses == 'gagal') : ?>
    <div class="modal fade" id="barangModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #dc3545;">
                    <h5 class="modal-title fw-bold">Pemberitahuan Gangguan</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <h4 class="mt-3 fw-bold" style="color: #dc3545;">Gagal Menyimpan Data</h4>
                    <p class="text-muted mb-0">Terjadi kesalahan teknis pada sistem database. Silakan periksa kembali berkas masukan Anda.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <a href="admin_barang.php" class="btn btn-danger-custom text-decoration-none">Coba Kembali</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var modalElemen = document.getElementById('barangModal');
            if (modalElemen) {
                var pemicuModal = new bootstrap.Modal(modalElemen);
                pemicuModal.show();
            }
        });
    </script>
</body>
</html>