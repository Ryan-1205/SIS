<?php
session_start();
include '../koneksi.php';

// Validasi Akses
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

$current_admin_id   = $_SESSION['id_user'];
$current_admin_role = $_SESSION['role'];

if (isset($_POST['id_user_pilihan']) && is_array($_POST['id_user_pilihan'])) {
    $id_user_array = $_POST['id_user_pilihan'];
    $sukses = 0;

    foreach ($id_user_array as $id_u) {
        $id_u = mysqli_real_escape_string($conn, $id_u);
        
        if ($id_u == $current_admin_id) continue;

        $query_cek = mysqli_query($conn, "SELECT role, foto_resmi FROM users WHERE id_user = '$id_u'");
        if (mysqli_num_rows($query_cek) == 0) continue;
        
        $data_user = mysqli_fetch_assoc($query_cek);
        $role_target = $data_user['role'];

        if ($current_admin_role !== 'admin' && strpos($role_target, 'admin') !== false) continue;

        // Manajemen Berkas (Pastikan folder sesuai: ../assets/img/pengguna/)
        if (!empty($data_user['foto_resmi']) && file_exists("../assets/img/pengguna/" . $data_user['foto_resmi'])) {
            if ($data_user['foto_resmi'] !== 'default_user.jpg') {
                unlink("../assets/img/pengguna/" . $data_user['foto_resmi']);
            }
        }

        if (mysqli_query($conn, "DELETE FROM users WHERE id_user = '$id_u'")) {
            $sukses++;
        }
    }

    // Redirect dengan status sukses dan jumlah data
    header("Location: admin_user.php?status=hapus_sukses&count=$sukses");
    exit;
} else {
    header("Location: admin_user.php?status=gagal_pilih");
    exit;
}
?>