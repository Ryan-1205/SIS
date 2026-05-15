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

    // Masukkan id_barang ke dalam array jika belum ada (mencegah duplikat)
    if (!in_array($id_barang, $_SESSION['keranjang'])) {
        array_push($_SESSION['keranjang'], $id_barang);
    }

    // Redirect ke halaman list pinjam
    header("Location: list_pinjam.php");
} else {
    header("Location: index.php");
}
?>