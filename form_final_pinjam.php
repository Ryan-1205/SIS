<?php
session_start();
include 'koneksi.php';

$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

if ($id_user == 0) {
    header("Location: login.php");
    exit;
}

// Menangkap kiriman form (Ditambahkan pengecekan untuk no_hp dan keperluan)
if (isset($_POST['item_pilihan']) && isset($_POST['tgl_pinjam']) && isset($_POST['tgl_kembali_rencana']) && isset($_POST['no_hp']) && isset($_POST['keperluan'])) {
    
    $item_array  = $_POST['item_pilihan']; // Berupa array ID barang di keranjang
    $tgl_pinjam  = mysqli_real_escape_string($conn, $_POST['tgl_pinjam']);
    $tgl_rencana = mysqli_real_escape_string($conn, $_POST['tgl_kembali_rencana']);
    
    // BARU: Tangkap data No HP dan Keperluan dari Modal Form
    $no_hp       = mysqli_real_escape_string($conn, $_POST['no_hp']);
    $keperluan   = mysqli_real_escape_string($conn, $_POST['keperluan']);

    $sukses_counter = 0;

    // Masukkan data barang ke database satu per satu menggunakan perulangan
    foreach ($item_array as $id_barang) {
        $id_barang = mysqli_real_escape_string($conn, $id_barang);

        // REVISI QUERY: Sekarang kolom keperluan dan no_hp resmi dimasukkan ke dalam database lek!
        $query_insert = "INSERT INTO peminjaman (id_user, id_barang, tgl_pinjam, tgl_kembali_rencana, keperluan, no_hp, status_pengajuan) 
                         VALUES ('$id_user', '$id_barang', '$tgl_pinjam', '$tgl_rencana', '$keperluan', '$no_hp', 'pending')";
        
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
        // Redirect dengan parameter gagal
        header("Location: list_pinjam.php?status=gagal_pinjam");
        exit;
    }
} else {
    header("Location: list_pinjam.php");
    exit;
}
?>