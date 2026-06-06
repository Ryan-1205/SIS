<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
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

        // 1. Jalankan kueri update status transaksi pada tabel peminjaman
        $query_update = "UPDATE peminjaman 
                         SET status_pengajuan = '$keputusan', diverifikasi_oleh = '$pengawas' 
                         WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending'";
        
        if (mysqli_query($conn, $query_update)) {
            $sukses_count++;

            // 🛠️ SINKRONISASI LOGISTIK SAKTI: Jika admin menyetujui, ubah status barang menjadi 'dipinjam'
            if ($keputusan === 'disetujui') {
                // Ambil id_barang terlebih dahulu dari record peminjaman terkait
                $query_cari_barang = mysqli_query($conn, "SELECT id_barang FROM peminjaman WHERE id_pinjam = '$id_pinjam'");
                if ($data_barang = mysqli_fetch_assoc($query_cari_barang)) {
                    $id_barang_terkait = $data_barang['id_barang'];
                    
                    // Kunci status barang agar tidak bisa dipilih siswa lain sementara waktu
                    mysqli_query($conn, "UPDATE barang SET status = 'dipinjam' WHERE id_barang = '$id_barang_terkait'");
                }
            }
        } else {
            $gagal_count++;
        }
    }

    // Tampilkan notifikasi umpan balik hasil eksekusi admin ke file interface yang baru
    if ($gagal_count == 0) {
        // Redirect dengan membawa status sukses verifikasi dan nama pengawas
        header("Location: admin_verifikasi_peminjaman.php?status=sukses_verif&pengawas=" . urlencode($pengawas));
        exit;
    } else {
        // Redirect membawa info kuantitas log data sukses dan gagal secara parsial
        header("Location: admin_verifikasi_peminjaman.php?status=parsial_verif&sukses=$sukses_count&gagal=$gagal_count");
        exit;
    }

} else {
    // Balikkan jika admin mencoba nembak file tanpa kirim data form
    header("Location: admin_verifikasi_peminjaman.php");
    exit;
}
?>