<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

// Menangkap nama lengkap admin dari session secara otomatis (karena input form pengawas sudah dihapus)
$pengawas = mysqli_real_escape_string($conn, $_SESSION['nama_lengkap']);

// Menangkap array keputusan status kelayakan fisik barang dari modal radio button
if (isset($_POST['status_kembali']) && !empty($_POST['status_kembali'])) {
    $status_kembali = $_POST['status_kembali']; // Berisi array [id_pinjam => 'kembali_bagus'/'kembali_rusak']

    $sukses_count = 0;
    $gagal_count  = 0;

    // LOOPING VALIDASI FISIK BARANG MASUK
    foreach ($status_kembali as $id_pinjam => $kondisi_fisik) {
        $id_pinjam     = mysqli_real_escape_string($conn, $id_pinjam);
        $kondisi_fisik = mysqli_real_escape_string($conn, $kondisi_fisik);

        // 1. Cari tahu ID barang terkait dari record peminjaman sebelum datanya diperbarui
        $query_cari_barang = mysqli_query($conn, "SELECT id_barang FROM peminjaman WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending_kembali'");
        
        if ($data_barang = mysqli_fetch_assoc($query_cari_barang)) {
            $id_barang_terkait = $data_barang['id_barang'];

            // 2. Update record transaksi peminjaman: Status jadi 'kembali', catat waktu asli masuk (NOW()), dan verifikator admin login
            $query_update = "UPDATE peminjaman 
                             SET status_pengajuan = 'kembali', 
                                 tgl_kembali_asli = NOW(), 
                                 diverifikasi_oleh = '$pengawas' 
                             WHERE id_pinjam = '$id_pinjam'";
            
            if (mysqli_query($conn, $query_update)) {
                $sukses_count++;

                // 3. SINKRONISASI LOGISTIK OTOMATIS KE TABEL BARANG:
                if ($kondisi_fisik === 'kembali_bagus') {
                    // Jika kondisi baik, barang langsung siap dipinjam lagi
                    mysqli_query($conn, "UPDATE barang SET status = 'tersedia' WHERE id_barang = '$id_barang_terkait'");
                } else {
                    // Jika kondisi rusak, kunci status barang ke status 'perbaikan' agar tidak muncul di katalog siswa
                    mysqli_query($conn, "UPDATE barang SET status = 'perbaikan' WHERE id_barang = '$id_barang_terkait'");
                }
            } else {
                $gagal_count++;
            }
        } else {
            $gagal_count++;
        }
    }

    // Tampilkan umpan balik eksekusi dan alihkan kembali ke halaman antrean pengembalian
    if ($gagal_count == 0) {
        // Sukses total: Redirect membawa parameter status sukses dan nama pengawas penerima asli dari session
        header("Location: admin_verifikasi_pengembalian.php?status=sukses_kembali&pengawas=" . urlencode($pengawas));
        exit;
    } else {
        // Sukses parsial: Redirect membawa jumlah kuantitas data berhasil dan gagal
        header("Location: admin_verifikasi_pengembalian.php?status=parsial_kembali&sukses=$sukses_count&gagal=$gagal_count");
        exit;
    }

} else {
    // Balikkan paksa jika file ditembak langsung tanpa pengiriman data form modal
    header("Location: admin_verifikasi_pengembalian.php");
    exit;
}
?>