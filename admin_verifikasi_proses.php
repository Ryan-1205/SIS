<?php
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Menangkap data pengawas dan array status keputusan per item barang
if (isset($_POST['pengawas']) && isset($_POST['status_item'])) {
    $pengawas    = mysqli_real_escape_string($conn, $_POST['pengawas']);
    $status_item = $_POST['status_item']; // Berisi array [id_pinjam => 'disetujui'/'ditolak']

    $sukses_count = 0;
    $gagal_count  = 0;

    // LOOPING KEPUTUSAN BARANG SELEKTIF
    foreach ($status_item as $id_pinjam => $keputusan) {
        $id_pinjam = mysqli_real_escape_string($conn, $id_pinjam);
        $keputusan = mysqli_real_escape_string($conn, $keputusan);

        // Update masing-masing record transaksi secara spesifik berdasarkan id_pinjam
        $query_update = "UPDATE peminjaman 
                         SET status_pengajuan = '$keputusan', diverifikasi_oleh = '$pengawas' 
                         WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending'";
        
        if (mysqli_query($conn, $query_update)) {
            $sukses_count++;
        } else {
            $gagal_count++;
        }
    }

    // Tampilkan notifikasi umpan balik hasil eksekusi admin
    if ($gagal_count == 0) {
        echo "<script>
                alert('Verifikasi selektif berhasil diproses oleh $pengawas!');
                window.location.href='admin_verifikasi.php';
              </script>";
    } else {
        echo "<script>
                alert('Proses verifikasi selesai. Sukses: $sukses_count item, Gagal database: $gagal_count item.');
                window.location.href='admin_verifikasi.php';
              </script>";
    }

} else {
    // Balikkan jika admin mencoba nembak file tanpa kirim data form
    header("Location: admin_verifikasi.php");
    exit;
}
?>