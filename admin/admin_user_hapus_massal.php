<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi Akses: Memastikan pengguna sudah login dan merupakan bagian dari manajemen admin
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

$current_admin_id   = $_SESSION['id_user'];
$current_admin_role = $_SESSION['role'];

// Memastikan parameter array pilihan dikirim melalui checkbox form
if (isset($_POST['id_user_pilihan']) && is_array($_POST['id_user_pilihan'])) {
    $id_user_array = $_POST['id_user_pilihan'];
    $sukses = 0;

    foreach ($id_user_array as $id_u) {
        $id_u = mysqli_real_escape_string($conn, $id_u);
        
        // Proteksi 1: Mencegah penghapusan akun milik diri sendiri yang sedang aktif
        if ($id_u == $current_admin_id) {
            continue;
        }

        // Pengecekan data tingkat peran (role) target pengguna dari database
        $query_cek = mysqli_query($conn, "SELECT role, foto_resmi FROM users WHERE id_user = '$id_u'");
        if (mysqli_num_rows($query_cek) == 0) {
            continue;
        }
        $data_user = mysqli_fetch_assoc($query_cek);
        $role_target = $data_user['role'];

        // Proteksi 2: Admin biasa dilarang keras menghapus akun pengurus laboratorium/admin lainnya
        if ($current_admin_role !== 'admin' && strpos($role_target, 'admin') !== false) {
            continue;
        }

        // Manajemen Berkas: Menghapus data gambar acuan fisik wajah di server agar tidak menumpuk
        if (!empty($data_user['foto_resmi']) && file_exists("../assets/img/" . $data_user['foto_resmi'])) {
            if ($data_user['foto_resmi'] !== 'default_user.jpg') {
                unlink("../assets/img/" . $data_user['foto_resmi']);
            }
        }

        // Eksekusi penghapusan data baris record pengguna dari database
        $query_delete = "DELETE FROM users WHERE id_user = '$id_u'";
        if (mysqli_query($conn, $query_delete)) {
            $sukses++;
        }
    }

    echo "<script>
            alert('Sistem: Berhasil menghapus sejumlah " . $sukses . " record data pengguna dari database.'); 
            window.location.href='admin_user.php';
          </script>";
    exit;
} else {
    echo "<script>
            alert('Pemberitahuan: Silakan pilih minimal satu pengguna terlebih dahulu.'); 
            window.location.href='admin_user.php';
          </script>";
    exit;
}
?>