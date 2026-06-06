<?php 
session_start();
include 'koneksi.php';

if(isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis = mysqli_real_escape_string($conn, $_POST['nis']); 

    // Cek apakah NIS sudah pernah terdaftar (Menggunakan kolom baru 'nis')
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE nis = '$nis'");
    
    if(mysqli_num_rows($cek_user) > 0) {
        $error = "NIS sudah terdaftar! Silakan langsung masuk.";
    } else {
        // ================= LOGIKA UPLOAD FOTO MASTER WAJAH AI =================
        $nama_file_final = "";
        
        if (isset($_FILES['foto_resmi']) && $_FILES['foto_resmi']['error'] === 0) {
            $nama_file   = $_FILES['foto_resmi']['name'];
            $ukuran_file = $_FILES['foto_resmi']['size'];
            $tmp_name    = $_FILES['foto_resmi']['tmp_name'];
            
            $ekstensi_valid = ['jpg', 'jpeg', 'png'];
            $ekstensi_file  = strtolower(pathinfo($nama_file, PATHINFO_EXTENSION));
            
            // Validasi format ekstensi gambar
            if (!in_array($ekstensi_file, $ekstensi_valid)) {
                $error = "Format file tidak valid! Wajib menggunakan JPG, JPEG, atau PNG.";
            }
            // Validasi ukuran berkas (Maksimal 5MB)
            elseif ($ukuran_file > 5000000) {
                $error = "Ukuran file foto terlalu besar! Maksimal berkas adalah 5MB.";
            } else {
                // Standarisasi penamaan nama file agar unik dan sinkron
                $nama_file_final = "user_" . $nis . "_" . time() . "." . $ekstensi_file;
                $target_folder   = "assets/img/" . $nama_file_final;
                
                // Pindahkan file ke direktori server lokal
                if (!move_uploaded_file($tmp_name, $target_folder)) {
                    $error = "Gagal mengunggah berkas foto acuan ke server.";
                }
            }
        } else {
            $error = "Foto wajah resmi acuan sistem AI wajib diunggah!";
        }
        // =====================================================================

        // Insert data jika tidak ada masalah atau error upload berkas gambar
        if (!isset($error)) {
            // Insert data ke tabel users menyertakan kolom foto_resmi dengan role default 'siswa'
            $query = "INSERT INTO users (nis, nama_lengkap, role, foto_resmi) 
                      VALUES ('$nis', '$nama', 'siswa', '$nama_file_final')";
            
            if(mysqli_query($conn, $query)) {
                echo "<script>alert('Pendaftaran berhasil! Silakan masuk.'); window.location.href='login.php';</script>";
                exit;
            } else {
                // Hapus kembali gambar yang telanjur masuk jika query database crash
                if (file_exists("assets/img/" . $nama_file_final)) {
                    unlink("assets/img/" . $nama_file_final);
                }
                $error = "Terjadi kesalahan saat menyimpan data pendaftaran.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.8">
    <style>
        .login-input {
            border: 2px solid transparent !important;
            transition: 0.3s;
            background-color: #ffffff !important;
            color: #333 !important;
        }
        .login-input:focus {
            border: 2px solid var(--tosca-tua) !important;
            outline: none;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="safe-container login-wrapper" style="min-height: 85vh; display: flex; align-items: center; justify-content: center; padding: 20px 0;">
        <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 45px; width: 100%; max-width: 440px; text-align: center;">
            <h2 style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 25px;">Daftar Akun</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger py-2 mb-3" style="font-size: 14px;"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data">
                <input type="text" name="nama" class="login-input" placeholder="Nama Lengkap" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 18px; width: 100%;">
                
                <input type="text" name="nis" class="login-input" placeholder="Nomor Induk Siswa (NIS)" value="<?= isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : '' ?>" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 18px; width: 100%;">
                
                <div class="text-start mb-4">
                    <label class="form-label fw-bold small ps-2" style="color: var(--tosca-tua);">Unggah Foto Wajah (Master Acuan AI) :</label>
                    <input type="file" name="foto_resmi" class="form-control login-input" accept="image/*" required style="border-radius: 10px; padding: 10px 15px; width: 100%; font-size: 14px;">
                </div>
                
                <button type="submit" name="daftar" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 5px;">Daftar Akun</button>
            </form>
            
            <a href="login.php" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Punya Akun? <strong style="font-weight: 800;">Masuk</strong></a>
        </div>
    </div>
</body>
</html>