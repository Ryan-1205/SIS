<?php
session_start();
include 'koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['id_user'])) {
    echo "<script>alert('Silakan login terlebih dahulu!'); window.location.href='login.php';</script>";
    exit;
}

$id_user = $_SESSION['id_user'];
$tgl_pinjam = date('Y-m-d');
$tgl_kembali_rencana = date('Y-m-d'); // Default dikembalikan di hari yang sama (sesuaikan jika ada inputan)

if (isset($_POST['item_pilihan']) && !empty($_POST['item_pilihan'])) {
    $items = $_POST['item_pilihan'];
    
    // Generate ID Peminjaman (Contoh sederhana pakai timestamp)
    $id_pinjam_tampil = time(); 

    foreach ($items as $id_barang) {
        // Insert ke database peminjaman
        mysqli_query($conn, "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, status_pengajuan) 
                             VALUES ('$id_user', '$id_barang', '$tgl_pinjam', '$tgl_kembali_rencana', 'pending')");
        
        // Update status barang menjadi dipinjam/pending
        mysqli_query($conn, "UPDATE barang SET status='dipinjam' WHERE id_barang='$id_barang'");

        // Hapus dari session keranjang
        if (($key = array_search($id_barang, $_SESSION['keranjang'])) !== false) {
            unset($_SESSION['keranjang'][$key]);
        }
    }
    
    // Redirect ke halaman sukses membawa ID pinjam
    header("Location: pinjam_berhasil.php?id_ref=$id_pinjam_tampil");
    exit;
} else {
    header("Location: list_pinjam.php");
}
?>