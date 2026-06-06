<?php
session_start();
// REVISI JALUR: Mundur satu folder karena file ini sekarang resmi berada di dalam subfolder siswa/
include '../koneksi.php';

// Pastikan ada parameter ID barang yang dikirim untuk dihapus
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Cek apakah ID barang tersebut ada di dalam keranjang session
    if (isset($_SESSION['keranjang']) && ($key = array_search($id, $_SESSION['keranjang'])) !== false) {
        // Hapus barang dari keranjang
        unset($_SESSION['keranjang'][$key]);
        
        // Mengurutkan ulang index array agar tidak ada index yang bolong/terlompat
        $_SESSION['keranjang'] = array_values($_SESSION['keranjang']);
    }
}

// REVISI JALUR REDIRECT: Langsung panggil list_pinjam.php karena sudah berada di dalam satu folder siswa/
header("Location: list_pinjam.php");
exit;
?>