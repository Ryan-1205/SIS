<?php
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

// Menangkap data pengunci sesi dari form POST
if (isset($_POST['id_user']) && isset($_POST['tgl_pinjam']) && isset($_POST['pengawas'])) {
    $id_user    = $_POST['id_user'];
    $tgl_pinjam = $_POST['tgl_pinjam'];
    $pengawas   = mysqli_real_escape_string($conn, $_POST['pengawas']);

    // UPDATE SEMUA BARANG DALAM SATU SESI PINJAM YANG SAMA
    $query = "UPDATE peminjaman 
              SET status_pengajuan = 'disetujui', diverifikasi_oleh = '$pengawas' 
              WHERE id_user = '$id_user' AND tgl_pinjam = '$tgl_pinjam' AND status_pengajuan = 'pending'";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Sesi peminjaman paket berhasil disetujui oleh $pengawas!');
                window.location.href='admin_verifikasi.php';
              </script>";
    } else {
        echo "<script>
                alert('Gagal memproses verifikasi sesi database.');
                window.location.href='admin_verifikasi.php';
              </script>";
    }
} else {
    header("Location: admin_verifikasi.php");
}
?>