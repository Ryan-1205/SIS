<?php
session_start();
include 'koneksi.php';

if (isset($_POST['pinjam_pilihan']) && !empty($_POST['pinjam_pilihan'])) {
    $items = $_POST['pinjam_pilihan'];
    $id_ref = time();
    
    // PERBAIKAN: Tangkap tanggal asli dari kalender device user yang dikirim lewat form
    if (isset($_POST['tgl_kembali_real']) && !empty($_POST['tgl_kembali_real'])) {
        $tgl_kembali_asli = mysqli_real_escape_string($conn, $_POST['tgl_kembali_real']);
    } else {
        // Fallback cadangan jika karena suatu hal JavaScript device gagal mengirim data
        $tgl_kembali_asli = date('Y-m-d'); 
    }
    
    // Tangkap nama pengawas yang dikirim form
    $pengawas = isset($_POST['pengawas_penerima']) ? mysqli_real_escape_string($conn, $_POST['pengawas_penerima']) : '-';

    foreach ($items as $id_pinjam) {
        // Update tabel peminjaman (Menyimpan status 'kembali' dan tanggal dari device user)
        // Note: Jika di struktur tabelmu ada kolom pengawas, silakan selipkan di query ini, contoh: , pengawas_penerima='$pengawas'
        mysqli_query($conn, "UPDATE peminjaman SET status_pengajuan='kembali', tgl_kembali_asli='$tgl_kembali_asli' WHERE id_pinjam='$id_pinjam'");
        
        // Ambil id_barang untuk update status barang
        $res = mysqli_query($conn, "SELECT id_barang FROM peminjaman WHERE id_pinjam='$id_pinjam'");
        $data = mysqli_fetch_assoc($res);
        $id_barang = $data['id_barang'];
        
        // Kembalikan status barang jadi tersedia
        mysqli_query($conn, "UPDATE barang SET status='tersedia' WHERE id_barang='$id_barang'");
    }
    
    // Mempertahankan struktur bawaan dengan melempar parameter sukses
    header("Location: kembali_berhasil.php?id_ref=$id_ref&status=sukses_kembali");
    exit;
} else {
    // Jika lolos atau ditembak langsung, kembalikan ke list dengan status error
    header("Location: list_kembali.php?status=gagal_pilih");
    exit;
}
?>