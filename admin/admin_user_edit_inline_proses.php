<?php
session_start();
// 🔥 FIX JALUR: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi akses admin (Super Admin atau Admin Lab diizinkan melakukan edit inline)
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    echo "unauthorized";
    exit;
}

if (isset($_POST['id_user'])) {
    $id    = mysqli_real_escape_string($conn, $_POST['id_user']);
    $nama  = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nis   = mysqli_real_escape_string($conn, $_POST['nis']);
    $role  = mysqli_real_escape_string($conn, $_POST['role']);

    // 1. AMBIL NAMA FOTO RESMI LAMA DARI DATABASE (UNTUK BACKUP/HAPUS)
    $query_foto = mysqli_query($conn, "SELECT foto_resmi FROM users WHERE id_user = '$id'");
    $data_foto = mysqli_fetch_assoc($query_foto);
    $foto_lama = $data_foto['foto_resmi'];

    $foto_nama_baru = $foto_lama; // Default pakai foto lama jika admin tidak ganti foto

    // 2. LOGIKA PROSES UPLOAD FOTO BARU (JIKA ADA YANG DIUNGGAH)
    if (isset($_FILES['foto_resmi']) && !empty($_FILES['foto_resmi']['name'])) {
        $file_foto = $_FILES['foto_resmi'];
        $foto_ext = strtolower(pathinfo($file_foto['name'], PATHINFO_EXTENSION));
        
        // Buat penamaan nama file yang konsisten dan unik agar aman dibaca face-api
        $foto_nama_baru = "user_master_" . time() . "_" . $id . "." . $foto_ext;
        
        // 🔥 FIX JALUR: Mundur satu folder agar file masuk ke folder utama assets/img/
        $target_path = "../assets/img/pengguna/" . $foto_nama_baru;

        // Pindahkan file dari temp_name ke folder assets/img
        if (move_uploaded_file($file_foto['tmp_name'], $target_path)) {
            // Jika berhasil upload foto baru, hapus berkas fisik foto lama (kecuali gambar default)
            // 🔥 FIX JALUR: Tambahkan ../ pada fungsi pengecekan file_exists dan unlink
            if (!empty($foto_lama) && file_exists("../assets/img/pengguna/" . $foto_lama)) {
                if ($foto_lama != "default_user.jpg") {
                    unlink("../assets/img/pengguna/" . $foto_lama); // Hapus berkas lama biar hosting gak penuh
                }
            }
        }
    }

    // 3. EKSEKUSI DATA PEMBARUAN TOTAL (TEKS + FOTO) KE DATABASE
    $query = "UPDATE users 
              SET nama_lengkap = '$nama', 
                  nis = '$nis', 
                  role = '$role', 
                  foto_resmi = '$foto_nama_baru' 
              WHERE id_user = '$id'";

    if (mysqli_query($conn, $query)) {
        echo "success"; // Kirim respon sukses ke JavaScript AJAX agar halaman otomatis reload
    } else {
        echo "error_database: " . mysqli_error($conn);
    }
} else {
    echo "error_parameter";
}
?>