<?php
session_start();
// REVISI JALUR: Mundur satu folder karena file ini sekarang berada di dalam subfolder siswa/
include '../koneksi.php';

// Cek apakah ada ID barang yang dikirim
if (isset($_GET['id'])) {
    // Amankan parameter ID barang dari ancaman SQL Injection
    $id_barang = mysqli_real_escape_string($conn, $_GET['id']);

    // Jika belum ada session keranjang, buat array kosong
    if (!isset($_SESSION['keranjang'])) {
        $_SESSION['keranjang'] = [];
    }

    // Masukkan id_barang ke dalam array jika belum ada
    if (!in_array($id_barang, $_SESSION['keranjang'])) {
        array_push($_SESSION['keranjang'], $id_barang);
        
        // REVISI JALUR: Langsung ke filenya karena sudah satu direktori di dalam folder siswa/
        header("Location: list_pinjam.php?status=sukses");
        exit();
    } else {
        // REVISI JALUR: Langsung ke filenya karena sudah satu direktori di dalam folder siswa/
        header("Location: list_pinjam.php?status=ada");
        exit();
    }
} else {
    // REVISI JALUR: Mundur satu folder untuk kembali ke halaman landing utama root
    header("Location: ../index.php");
    exit();
}
?>