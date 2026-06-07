<?php
session_start();
// Mengatur zona waktu agar fungsi date() mengambil waktu WIB secara akurat
date_default_timezone_set('Asia/Jakarta');

// REVISI JALUR: Mundur satu folder karena file ini berjalan di dalam folder siswa/
include '../koneksi.php';

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

if ($id_user == 0) {
    // REVISI JALUR: Mundur ke root untuk mengakses login.php
    header("Location: ../login.php");
    exit;
}

// Menangkap kiriman form (Ditambahkan pengecekan untuk no_hp, keperluan, dan snapshot_wajah)
if (isset($_POST['item_pilihan']) && isset($_POST['tgl_pinjam']) && isset($_POST['tgl_kembali_rencana']) && isset($_POST['no_hp']) && isset($_POST['keperluan']) && isset($_POST['snapshot_wajah'])) {
    
    $item_array     = $_POST['item_pilihan']; // Berupa array ID barang di keranjang
    
    // 🔥 PENYESUAIAN LOGIKA WAKTU REALTIME (DATETIME-LOCAL):
    // Menangkap string format dari HTML (YYYY-MM-DDTHH:MM)
    $tgl_pinjam_raw  = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_kembali_raw = mysqli_real_escape_string($conn, $_POST['tgl_kembali_rencana']);
    
    // Mengubah huruf 'T' menjadi spasi dan menambahkan detik (:00) agar sesuai format DATETIME MySQL (YYYY-MM-DD HH:MM:SS)
    $tgl_pinjam      = str_replace('T', ' ', $tgl_pinjam_raw) . ":00";
    $tgl_rencana     = str_replace('T', ' ', $tgl_kembali_raw) . ":00";
    
    // PROTEKSI SERVER: Validasi logika waktu agar rencana kembali tidak mendahului waktu mulai pinjam
    if (strtotime($tgl_rencana) < strtotime($tgl_pinjam)) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Logika+waktu+kembali+salah");
        exit;
    }
    
    $no_hp          = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $keperluan      = mysqli_real_escape_string($conn, $_POST['keperluan']);
    $snapshot_wajah = $_POST['snapshot_wajah']; // Mengambil string Base64 dari modal kamera

    // ================= VALIDASI & PROSES DEKODE BASE64 SNAPSHOT WAJAH =================
    if (empty($snapshot_wajah)) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Verifikasi+wajah+tidak+valid");
        exit;
    }

    // PROTEKSI SERVER: Pastikan modul GD Library aktif di php.ini XAMPP
    if (!function_exists('imagecreatefromstring')) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Ekstensi+GD+Library+server+belum+aktif");
        exit;
    }

    // Pecah string Base64 untuk mengambil biner murninya
    $data_pecah = explode(',', $snapshot_wajah);
    if (!isset($data_pecah[1])) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Format+gambar+tidak+dikenali");
        exit;
    }

    $gambar_biner = base64_decode($data_pecah[1]);
    $image = imagecreatefromstring($gambar_biner);

    if (!$image) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Gagal+mengolah+data+snapshot+wajah");
        exit;
    }

    // Generate nama file unik untuk bukti otentikasi wajah saat meminjam barang
    $nama_bukti_wajah = "bukti_pinjam_" . $id_user . "_" . time() . ".jpg";
    
    // REVISI ALAMAT: Ditambahkan '/' setelah nama folder agar file tersimpan di dalam folder bukti_pinjam/
    $target_folder    = "../assets/img/bukti_pinjam/" . $nama_bukti_wajah;

    // Simpan gambar biner ke folder lokal dengan kualitas kompresi optmial (75%)
    $simpan_gambar = imagejpeg($image, $target_folder, 75);
    imagedestroy($image);

    if (!$simpan_gambar) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Gagal+menyimpan+berkas+gambar+ke+server");
        exit;
    }
    // ===================================================================================

    $sukses_counter = 0;
    // Membuat nomor referensi transaksi acak untuk kebutuhan tanda terima siswa
    $id_ref_generate = "TRX" . time() . strtoupper(substr(md5(uniqid()), 0, 4));

    // Masukkan data barang ke database satu per satu menggunakan perulangan
    foreach ($item_array as $id_barang) {
        $id_barang = mysqli_real_escape_string($conn, $id_barang);

        // QUERY INSERT DATABASE RESMI (Menggunakan variabel $tgl_pinjam dan $tgl_rencana yang dinamis dari input siswa)
        $query_insert = "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, keperluan, no_hp, bukti_wajah, status_pengajuan) 
                         VALUES ('$id_user', '$id_barang', '$tgl_pinjam', '$tgl_rencana', '$keperluan', '$no_hp', '$nama_bukti_wajah', 'pending')";
        
        if (mysqli_query($conn, $query_insert)) {
            $sukses_counter++;
        }
    }

    if ($sukses_counter > 0) {
        // Kosongkan keranjang session karena pengajuan sukses masuk antrean DB
        $_SESSION['keranjang'] = [];
        
        // Dilempar ke pinjam_berhasil.php yang berada di folder siswa/ yang sama
        header("Location: pinjam_berhasil.php?id_ref=$id_ref_generate&count=$sukses_counter");
        exit;
    } else {
        // Jika query ke database gagal, hapus file gambar bukti yang telanjur tersimpan agar server bersih
        // (Alamat target_folder di sini otomatis ikut benar mengikuti perbaikan di atas)
        if (file_exists($target_folder)) {
            unlink($target_folder);
        }
        header("Location: list_pinjam.php?status=gagal_pinjam");
        exit;
    }
} else {
    header("Location: list_pinjam.php");
    exit;
}
?>  