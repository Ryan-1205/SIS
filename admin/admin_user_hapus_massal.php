<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    exit;
}

if (isset($_POST['id_user_pilihan']) && is_array($_POST['id_user_pilihan'])) {
    $id_user_array = $_POST['id_user_pilihan'];
    $sukses = 0;

    foreach ($id_user_array as $id_u) {
        $id_u = mysqli_real_escape_string($conn, $id_u);
        
        // Proteksi ganda (mencegah hapus diri sendiri)
        if ($id_u == $_SESSION['id_user']) {
            continue;
        }

        $query_delete = "DELETE FROM users WHERE id_user = '$id_u'";
        if (mysqli_query($conn, $query_delete)) {
            $sukses++;
        }
    }

    echo "<script>alert('Berhasil menghapus $sukses akun pengguna dari sistem secara permanen.'); window.location.href='admin_user.php';</script>";
} else {
    echo "<script>alert('Silakan pilih minimal satu pengguna terlebih dahulu!'); window.location.href='admin_user.php';</script>";
}
?>