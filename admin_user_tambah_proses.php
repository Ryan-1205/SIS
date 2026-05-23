<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    exit;
}

$nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
$nis          = mysqli_real_escape_string($conn, $_POST['nis']);
$password     = mysqli_real_escape_string($conn, $_POST['password']);
$role         = mysqli_real_escape_string($conn, $_POST['role']);

// Cek apakah NIS ganda/sudah terdaftar sebelumnya
$cek_nis = mysqli_query($conn, "SELECT id_user FROM users WHERE nis = '$nis'");
if (mysqli_num_rows($cek_nis) > 0) {
    echo "<script>alert('Gagal! Nomor Induk (NIS) tersebut sudah terdaftar di sistem.'); window.location.href='admin_user.php';</script>";
    exit;
}

$query = "INSERT INTO users (nama_lengkap, nis, password, role) VALUES ('$nama_lengkap', '$nis', '$password', '$role')";
if (mysqli_query($conn, $query)) {
    echo "<script>alert('Pengguna baru berhasil didaftarkan!'); window.location.href='admin_user.php';</script>";
} else {
    echo "<script>alert('Gagal menyimpan data pengguna ke database.'); window.location.href='admin_user.php';</script>";
}
?>