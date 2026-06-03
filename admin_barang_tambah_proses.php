<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') { exit; }

$id_kategori = $_POST['id_kategori'];
$kode_barang = mysqli_real_escape_string($conn, $_POST['kode_barang']); // Kolom baru
$nama_barang = mysqli_real_escape_string($conn, $_POST['nama_barang']);
$deskripsi   = mysqli_real_escape_string($conn, $_POST['deskripsi']);
$status      = $_POST['status'];

$foto_nama = "";
if (!empty($_FILES['foto']['name'])) {
    $foto_nama = time() . "_" . $_FILES['foto']['name'];
    move_uploaded_file($_FILES['foto']['tmp_name'], "assets/img/" . $foto_nama);
}

$query = "INSERT INTO barang (kode_barang, nama_barang, deskripsi, status, id_kategori, foto) VALUES ('$kode_barang', '$nama_barang', '$deskripsi', '$status', '$id_kategori', '$foto_nama')";
if (mysqli_query($conn, $query)) {
    echo "<script>alert('Aset barang baru berhasil ditambahkan!'); window.location.href='admin_barang.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan ke database.'); window.location.href='admin_barang.php';</script>";
}
?>