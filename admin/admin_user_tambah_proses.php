<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin global
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['nama_lengkap'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nis          = mysqli_real_escape_string($conn, $_POST['nis']); 
    
    // 🔥 FIX LOGIKA: Diubah dari 'admin_super' menjadi 'admin' sesuai dengan session login lo!
    $role_pilihan = ($_SESSION['role'] === 'admin' && isset($_POST['role'])) 
                    ? mysqli_real_escape_string($conn, $_POST['role']) 
                    : 'siswa';

    // Memeriksa duplikasi NIS
    $cek_user = mysqli_query($conn, "SELECT id_user FROM users WHERE nis = '$nis'");
    if (mysqli_num_rows($cek_user) > 0) {
        header("Location: admin_user.php?status=gagal_duplikat");
        exit;
    }

    // LOGIKA UPLOAD FOTO MASTER USER
    if (isset($_FILES['foto_resmi']) && $_FILES['foto_resmi']['error'] === 0) {
        $nama_file   = $_FILES['foto_resmi']['name'];
        $ukuran_file = $_FILES['foto_resmi']['size'];
        $tmp_name    = $_FILES['foto_resmi']['tmp_name'];
        $ekstensi    = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        // Batasi format gambar dan kapasitas maksimal berkas (5MB)
        if (!in_array($ekstensi, ['jpg', 'jpeg', 'png']) || $ukuran_file > 5000000) {
            header("Location: admin_user.php?status=gagal_format");
            exit;
        }

        $nama_file_final = "user_" . $nis . "_" . time() . "." . $ekstensi;
        
        // 🔥 SINKRONISASI JALUR: Disamakan ke folder utama assets/img/ biar sinkron dengan file proses lainnya
        $target_folder   = "../assets/img/pengguna/" . $nama_file_final; 
        
        if (move_uploaded_file($tmp_name, $target_folder)) {
            $query = "INSERT INTO users (nis, nama_lengkap, role, foto_resmi) VALUES ('$nis', '$nama_lengkap', '$role_pilihan', '$nama_file_final')";
            
            if (mysqli_query($conn, $query)) {
                header("Location: admin_user.php?status=tambah_sukses");
            } else {
                unlink($target_folder); // Hapus gambar instan jika kueri SQL gagal dieksekusi
                header("Location: admin_user.php?status=gagal_db");
            }
        } else {
            header("Location: admin_user.php?status=gagal_upload");
        }
    } else {
        header("Location: admin_user.php?status=gagal_foto");
    }
}
?>