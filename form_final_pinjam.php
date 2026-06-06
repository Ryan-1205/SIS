<?php
session_start();
include 'koneksi.php';

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

if ($id_user == 0) {
    header("Location: login.php");
    exit;
}

// Menangkap kiriman form (Ditambahkan pengecekan untuk no_hp, keperluan, dan snapshot_wajah)
if (isset($_POST['item_pilihan']) && isset($_POST['tgl_pinjam']) && isset($_POST['tgl_kembali_rencana']) && isset($_POST['no_hp']) && isset($_POST['keperluan']) && isset($_POST['snapshot_wajah'])) {
    
    $item_array     = $_POST['item_pilihan']; // Berupa array ID barang di keranjang
    $tgl_pinjam     = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_rencana    = mysqli_real_escape_string($conn, $_POST['tgl_kembali_rencana']);
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
    $target_folder    = "assets/img/" . $nama_bukti_wajah;

    // Simpan gambar biner ke folder lokal dengan kualitas kompresi optmial (75%)
    $simpan_gambar = imagejpeg($image, $target_folder, 75);
    imagedestroy($image);

    if (!$simpan_gambar) {
        header("Location: list_pinjam.php?status=gagal_pinjam&msg=Gagal+menyimpan+berkas+gambar+ke+server");
        exit;
    }
    // ===================================================================================

    $sukses_counter = 0;

    // Masukkan data barang ke database satu per satu menggunakan perulangan
    foreach ($item_array as $id_barang) {
        $id_barang = mysqli_real_escape_string($conn, $id_barang);

        // REVISI TOTAL QUERY: Sekarang menyertakan kolom bukti_wajah beralur ke tabel database resmi!
        $query_insert = "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, keperluan, no_hp, bukti_wajah, status_pengajuan) 
                         VALUES ('$id_user', '$id_barang', '$tgl_pinjam', '$tgl_rencana', '$keperluan', '$no_hp', '$nama_bukti_wajah', 'pending')";
        
        if (mysqli_query($conn, $query_insert)) {
            $sukses_counter++;
        }
    }

    if ($sukses_counter > 0) {
        // Kosongkan keranjang session karena pengajuan sukses masuk antrean DB
        $_SESSION['keranjang'] = [];
        // Redirect dengan parameter jumlah sukses
        header("Location: list_pinjam.php?status=sukses_pinjam&count=$sukses_counter");
        exit;
    } else {
        // Jika query ke database gagal, hapus file gambar bukti yang telanjur tersimpan agar server bersih
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