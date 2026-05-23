<?php
session_start();
include 'koneksi.php';

if (isset($_POST['pinjam_pilihan']) && !empty($_POST['pinjam_pilihan'])) {
    $items = $_POST['pinjam_pilihan'];
    $tgl_kembali_asli = date('Y-m-d');
    $id_ref = time();

    foreach ($items as $id_pinjam) {
        // Update tabel peminjaman
        mysqli_query($conn, "UPDATE peminjaman SET status_pengajuan='kembali', tgl_kembali_asli='$tgl_kembali_asli' WHERE id_pinjam='$id_pinjam'");
        
        // Ambil id_barang untuk update status barang
        $res = mysqli_query($conn, "SELECT id_barang FROM peminjaman WHERE id_pinjam='$id_pinjam'");
        $data = mysqli_fetch_assoc($res);
        $id_barang = $data['id_barang'];
        
        // Kembalikan status barang jadi tersedia
        mysqli_query($conn, "UPDATE barang SET status='tersedia' WHERE id_barang='$id_barang'");
    }
    
    header("Location: kembali_berhasil.php?id_ref=$id_ref");
    exit;
} else {
    header("Location: list_kembali.php");
}
?>