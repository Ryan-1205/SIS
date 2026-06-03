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
        // Insert data ke tabel users tanpa kolom password, sesuai struktur SQL baru
        $query = "INSERT INTO users (nis, nama_lengkap, role) 
                  VALUES ('$nis', '$nama', 'siswa')";
        
        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Pendaftaran berhasil! Silakan masuk.'); window.location.href='login.php';</script>";
            exit;
        } else {
            $error = "Terjadi kesalahan saat mendaftar.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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

    <div class="safe-container login-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
        <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 50px; width: 100%; max-width: 400px; text-align: center;">
            <h2 style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 30px;">Daftar</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger py-2" style="font-size: 14px;"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="text" name="nama" class="login-input" placeholder="Nama Lengkap" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 20px; width: 100%;">
                
                <input type="text" name="nis" class="login-input" placeholder="Nomor Induk Siswa (NIS)" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 25px; width: 100%;">
                
                <button type="submit" name="daftar" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 10px;">Daftar</button>
            </form>
            
            <a href="login.php" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Punya Akun? <strong style="font-weight: 800;">Masuk</strong></a>
        </div>
    </div>
</body>
</html>