<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin_') === false) { 
    exit; 
}

if (isset($_POST['id_barang'])) {
    $id          = $_POST['id_barang'];
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']);
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $desc        = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status      = $_POST['status'];

    // Memeriksa informasi berkas gambar lama pada database
    $query_foto = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang = '$id'");
    $data_foto = mysqli_fetch_assoc($query_foto);
    $foto_lama = $data_foto['foto'];

    $foto_nama_baru = $foto_lama;
    if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'])) {
        $foto_file = $_FILES['foto'];
        $foto_ext = strtolower(pathinfo($foto_file['name'], PATHINFO_EXTENSION));
        $foto_nama_baru = time() . "_" . $id . "." . $foto_ext;
        
        // Penyesuaian Jalur: Unggah berkas gambar ke direktori assets di luar folder admin/
        if (move_uploaded_file($foto_file['tmp_name'], "../assets/img/" . $foto_nama_baru)) {
            // Penyesuaian Jalur: Melakukan pengecekan dan penghapusan gambar lama dari folder yang tepat
            if (!empty($foto_lama) && file_exists("../assets/img/" . $foto_lama)) {
                // Proteksi gambar bawaan agar tidak tidak terhapus dari sistem
                if (!in_array($foto_lama, ["logoberangkat.png", "logodkv.png", "logomm.png", "logoanm.png"])) {
                    unlink("../assets/img/" . $foto_lama);
                }
            }
        }
    }

    // Melakukan pembaruan data pada tabel barang
    $query = "UPDATE barang SET kode_barang = '$kode_barang', nama_barang = '$nama', deskripsi = '$desc', status = '$status', foto = '$foto_nama_baru' WHERE id_barang = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>