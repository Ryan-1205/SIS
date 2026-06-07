<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin (Mendukung role 'admin' global maupun 'admin_lab')
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) { 
    echo "unauthorized";
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
        
        // 🔥 FIX JALUR: Disamakan ke folder utama assets/img/ agar sinkron dengan tambah_proses
        $target_upload = "../assets/img/barang/" . $foto_nama_baru;
        
        if (move_uploaded_file($foto_file['tmp_name'], $target_upload)) {
            // 🔥 FIX JALUR: Pengecekan dan penghapusan foto lama diselaraskan ke folder assets/img/
            $jalur_foto_lama = "../assets/img/barang/" . $foto_lama;
            
            if (!empty($foto_lama) && file_exists($jalur_foto_lama)) {
                // Proteksi gambar logo bawaan sistem agar tidak ikut terhapus
                if (!in_array($foto_lama, ["logoberangkat.png", "logodkv.png", "logodkv.png", "logoanm.png"])) {
                    unlink($jalur_foto_lama); // Berkas lama sukses dihancurkan, hosting aman!
                }
            }
        }
    }

    // Melakukan pembaruan data pada tabel barang
    $query = "UPDATE barang SET kode_barang = '$kode_barang', nama_barang = '$nama', deskripsi = '$desc', status = '$status', foto = '$foto_nama_baru' WHERE id_barang = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "success"; // Respon ini dibaca oleh Javascript AJAX di admin_barang.php untuk reload otomatis
    } else {
        echo "error_database: " . mysqli_error($conn);
    }
} else {
    echo "error_parameter";
}
?>