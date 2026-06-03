<?php
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

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

    echo "<script>
            alert('Proses Hapus Massal Selesai! Berhasil menghapus $sukses aset dari database.');
            window.location.href='admin_barang.php';
          </script>";

} else {
    echo "<script>
            alert('Silakan pilih minimal satu barang terlebih dahulu!');
            window.location.href='admin_barang.php';
          </script>";
}
?>