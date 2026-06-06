<?php 
session_start();
include 'koneksi.php';

if (isset($_POST['login'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis  = mysqli_real_escape_string($conn, $_POST['nis']);

    // Mencocokkan nama lengkap dan NIS pada database
    $query = mysqli_query($conn, "SELECT * FROM users WHERE nama_lengkap = '$nama' AND nis = '$nis'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Inisialisasi session dasar pengguna
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        
        $role_user = $data['role'];
        $id_lab_mapped = 1; // Default fallback jika tidak ada kecocokan

        // Pemetaan otomatis role string ke ID Kategori Laboratorium
        if ($role_user == 'admin_timber') {
            $id_lab_mapped = 1;
        } elseif ($role_user == 'admin_dkv1') {
            $id_lab_mapped = 2;
        } elseif ($role_user == 'admin_dkv2') {
            $id_lab_mapped = 3;
        } elseif ($role_user == 'admin_animasi') {
            $id_lab_mapped = 4;
        }

        // Menyimpan ID Kategori ke dalam session global
        $_SESSION['id_kategori'] = $id_lab_mapped;
        
        // Pengalihan halaman berdasarkan hak akses (Role) dan struktur folder baru
        if (strpos($role_user, 'admin') !== false) {
            header("Location: admin/admin_barang.php"); 
        } else {
            header("Location: siswa/list_barang.php"); // Diarahkan langsung ke halaman utama siswa
        }
        exit;
    } else {
        $error = "Kombinasi Nama Lengkap atau NIS tidak valid.";
    }
}
?>