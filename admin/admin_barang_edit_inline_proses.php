<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') { exit; }

if (isset($_POST['id_barang'])) {
    $id          = $_POST['id_barang'];
    $kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']); // Tangkap kode baru
    $nama        = mysqli_real_escape_string($conn, $_POST['nama_barang']);
    $desc        = mysqli_real_escape_string($conn, $_POST['deskripsi']);
    $status      = $_POST['status'];

    $query_foto = mysqli_query($conn, "SELECT foto FROM barang WHERE id_barang = '$id'");
    $data_foto = mysqli_fetch_assoc($query_foto);
    $foto_lama = $data_foto['foto'];

    $foto_nama_baru = $foto_lama;
    if (isset($_FILES['foto']) && !empty($_FILES['foto']['name'])) {
        $foto_file = $_FILES['foto'];
        $foto_ext = strtolower(pathinfo($foto_file['name'], PATHINFO_EXTENSION));
        $foto_nama_baru = time() . "_" . $id . "." . $foto_ext;
        
        if (move_uploaded_file($foto_file['tmp_name'], "assets/img/" . $foto_nama_baru)) {
            if (!empty($foto_lama) && file_exists("assets/img/" . $foto_lama)) {
                if (!in_array($foto_lama, ["logoberangkat.png", "logodkv.png", "logomm.png", "logoanm.png"])) {
                    unlink("assets/img/" . $foto_lama);
                }
            }
        }
    }

    $query = "UPDATE barang SET kode_barang = '$kode_barang', nama_barang = '$nama', deskripsi = '$desc', status = '$status', foto = '$foto_nama_baru' WHERE id_barang = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>