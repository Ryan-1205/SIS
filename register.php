<?php 
session_start();
include 'koneksi.php';

if(isset($_POST['daftar'])) {
    $nama = $_POST['nama'];
    $nis = $_POST['nis']; // NIS akan dijadikan username
    $password = $_POST['password']; 

    // Cek apakah NIS sudah pernah terdaftar
    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE username = '$nis'");
    
    if(mysqli_num_rows($cek_user) > 0) {
        $error = "NIS sudah terdaftar! Silakan langsung masuk.";
    } else {
        // Insert data ke tabel users dengan role default 'siswa'
        $query = "INSERT INTO users (username, password, nama_lengkap, role) 
                  VALUES ('$nis', '$password', '$nama', 'siswa')";
        
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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <nav class="navbar-sis" style="background-color: var(--tosca-tua); padding: 15px 0;">
        <div class="safe-container d-flex align-items-center justify-content-between px-3">
            <a class="text-white text-decoration-none fw-bold fs-4" href="index.php">
                SIS <span style="font-weight: 300; font-size: 16px; margin-left: 10px;">Sixseven Inventory System</span>
            </a>
        </div>
    </nav>

    <div class="safe-container login-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
        <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 50px; width: 100%; max-width: 400px; text-align: center;">
            <h2 style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 30px;">Daftar</h2>
            
            <?php if(isset($error)): ?>
                <div class="alert alert-danger py-2" style="font-size: 14px;"><?= $error ?></div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="text" name="nama" class="login-input" placeholder="Nama" required style="border-radius: 10px; padding: 12px 20px; border: none; margin-bottom: 20px; width: 100%;">
                
                <input type="text" name="nis" class="login-input" placeholder="NIS" required style="border-radius: 10px; padding: 12px 20px; border: none; margin-bottom: 20px; width: 100%;">
                
                <input type="password" name="password" class="login-input" placeholder="NIS/Password" required style="border-radius: 10px; padding: 12px 20px; border: none; margin-bottom: 20px; width: 100%;">
                
                <button type="submit" name="daftar" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 10px;">Daftar</button>
            </form>
            
            <a href="login.php" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Punya Akun? <strong style="font-weight: 800;">Masuk</strong></a>
        </div>
    </div>
</body>
</html>