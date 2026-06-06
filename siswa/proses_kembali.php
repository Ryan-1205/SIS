<?php
session_start();
// Mengatur zona waktu agar fungsi date() mengambil waktu WIB secara akurat jika fallback berjalan
date_default_timezone_set('Asia/Jakarta');

// PENYESUAIAN JALUR: Mundur satu folder karena file ini berjalan di dalam subfolder siswa/
include '../koneksi.php';

// Validasi hak akses siswa
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['pinjam_pilihan']) && !empty($_POST['pinjam_pilihan'])) {
    $items = $_POST['pinjam_pilihan'];
    $id_ref = time();
    
    // Tangkap tanggal dan jam penuh dari JavaScript gawai (device) siswa
    if (isset($_POST['tgl_kembali_real']) && !empty($_POST['tgl_kembali_real'])) {
        $tgl_kembali_asli = mysqli_real_escape_string($conn, $_POST['tgl_kembali_real']);
    } else {
        // Fallback cadangan jika JavaScript gawai gagal mengirim data
        $tgl_kembali_asli = date('Y-m-d H:i:s'); 
    }
    
    // Tangkap nama pengawas piket penerima yang diinput siswa lewat form modal
    $pengawas = isset($_POST['pengawas_penerima']) ? mysqli_real_escape_string($conn, $_POST['pengawas_penerima']) : '-';

    foreach ($items as $id_pinjam) {
        $id_pinjam = mysqli_real_escape_string($conn, $id_pinjam);

        /* SINKRONISASI LOGIKA DATABASE RESMI:
           1. Ubah status transaksi menjadi 'pending_kembali' (Menunggu Validasi Fisik Admin).
           2. Simpan waktu pengembalian riil device siswa ke 'tgl_kembali_asli'.
           3. Masukkan calon nama pengawas penerima sementara ke kolom 'diverifikasi_oleh' (nanti bisa di-override admin saat approve).
        */
        $query_update = "UPDATE peminjaman 
                         SET status_pengajuan = 'pending_kembali', 
                             tgl_kembali_asli = '$tgl_kembali_asli', 
                             diverifikasi_oleh = '$pengawas' 
                         WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'disetujui'";
        
        mysqli_query($conn, $query_update);
        
        /* CATATAN LOGISTIK: 
           Status fisik barang di tabel `barang` tetap 'dipinjam' dan tidak diubah di sini.
           Ubah status menjadi 'tersedia' baru dieksekusi setelah Admin menyetujui di backend admin.
        */
    }
    
    // JALUR REDIRECT SUKSES: Diarahkan ke kembali_berhasil.php dalam folder siswa/ yang sama
    header("Location: kembali_berhasil.php?id_ref=$id_ref&status=sukses_kembali&pengawas=" . urlencode($pengawas) . "&count=" . count($items));
    exit;
} else {
    // JALUR REDIRECT GAGAL: Dikembalikan ke list_kembali.php dalam folder siswa/ yang sama
    header("Location: list_kembali.php?status=gagal_pilih");
    exit;
}
?>