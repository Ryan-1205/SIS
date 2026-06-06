<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi Akses: Memastikan pengguna yang mengeksekusi adalah kelompok admin
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

if (isset($_POST['nama_lengkap'])) {
    $nama_lengkap = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $nis          = mysqli_real_escape_string($conn, $_POST['nis']); 
    
    // Proteksi Level Akses: Hanya admin_super yang bisa menentukan role dari form
    if ($_SESSION['role'] === 'admin_super' && isset($_POST['role'])) {
        $role_pilihan = mysqli_real_escape_string($conn, $_POST['role']);
    } else {
        // Admin biasa atau role lain otomatis terkunci menjadi siswa
        $role_pilihan = 'siswa';
    }

    // Memeriksa apakah NIS sudah pernah terdaftar di database
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE nis = '$nis'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        echo "<script>
                alert('Gagal: Nomor Induk (NIS) sudah terdaftar dalam sistem.');
                window.location.href='admin_user.php';
              </script>";
        exit;
    }

    // ================= LOGIKA UPLOAD FOTO WAJAH RESMI =================
    $nama_file_final = "";
    
    if (isset($_FILES['foto_resmi']) && $_FILES['foto_resmi']['error'] === 0) {
        $nama_file   = $_FILES['foto_resmi']['name'];
        $ukuran_file = $_FILES['foto_resmi']['size'];
        $tmp_name    = $_FILES['foto_resmi']['tmp_name'];
        
        $ekstensi_valid = ['jpg', 'jpeg', 'png'];
        $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
        
        // Validasi format ekstensi file gambar
        if (!in_array($ekstensi_file, $ekstensi_valid)) {
            echo "<script>
                    alert('Gagal: Format berkas tidak valid. Harap gunakan format JPG, JPEG, atau PNG.');
                    window.location.href='admin_user.php';
                  </script>";
            exit;
        }
        
        // Validasi ukuran berkas (Maksimal 5MB)
        if ($ukuran_file > 5000000) {
            echo "<script>
                    alert('Gagal: Ukuran berkas terlalu besar. Batas maksimal adalah 5MB.');
                    window.location.href='admin_user.php';
                  </script>";
            exit;
        }

        // Standarisasi penamaan berkas agar unik dan tersimpan di direktori assets luar
        $nama_file_final = "user_" . $nis . "_" . time() . "." . $ekstensi_file;
        $target_folder   = "../assets/img/" . $nama_file_final;
        
        if (!move_uploaded_file($tmp_name, $target_folder)) {
            echo "<script>
                    alert('Gagal: Terjadi kesalahan saat mengunggah berkas gambar ke server.');
                    window.location.href='admin_user.php';
                  </script>";
            exit;
        }
    } else {
        echo "<script>
                alert('Gagal: Berkas foto wajah resmi wajib diunggah.');
                window.location.href='admin_user.php';
              </script>";
        exit;
    }
    // =====================================================================

    // Eksekusi penyimpanan data ke tabel master users dengan role dinamis sesuai proteksi level
    $query = "INSERT INTO users (nis, nama_lengkap, role, foto_resmi) 
              VALUES ('$nis', '$nama_lengkap', '$role_pilihan', '$nama_file_final')";
    
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Sistem: Pengguna baru berhasil didaftarkan ke database.');
                window.location.href='admin_user.php';
              </script>";
        exit;
    } else {
        // Pembersihan sampah berkas gambar di server lokal jika kueri database gagal
        if (!empty($nama_file_final) && file_exists("../assets/img/" . $nama_file_final)) {
            unlink("../assets/img/" . $nama_file_final);
        }
        echo "<script>
                alert('Gagal: Terjadi kesalahan teknis saat menyimpan data ke database.');
                window.location.href='admin_user.php';
              </script>";
        exit;
    }
}
?>