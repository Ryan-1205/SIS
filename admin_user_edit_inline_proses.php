<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    exit;
}

if (isset($_POST['id_user'])) {
    $id    = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nis   = mysqli_real_escape_string($conn, $_POST['nis']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);

    $query = "UPDATE users SET nama_lengkap = '$nama', nis = '$nis', role = '$role' WHERE id_user = '$id'";
    if (mysqli_query($conn, $query)) {
        echo "success";
    } else {
        echo "error";
    }
}
?>