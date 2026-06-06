<?php
session_start();
include 'koneksi.php';

// Cek apakah ada ID barang yang dikirim
if (isset($_GET['id'])) {
    $id_barang = $_GET['id'];

    // Jika belum ada session keranjang, buat array kosong
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Masukkan id_barang ke dalam array jika belum ada
    if (!in_array($id_barang, $_SESSION['keranjang'])) {
        array_push($_SESSION['keranjang'], $id_barang);
        
        // Redirect dengan status SUKSES karena barang baru masuk
        header("Location: list_pinjam.php?status=sukses");
        exit();
    } else {
        // Redirect dengan status ADA karena barangnya duplikat (sudah dipilih sebelumnya)
        header("Location: list_pinjam.php?status=ada");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}
?>