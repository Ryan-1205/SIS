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
            header("Location: index.php?msg=welcome"); // Diarahkan ke index utama untuk memicu SweetAlert siswa
        }
        exit;
    } else {
        $error = "Kombinasi Nama Lengkap atau NIS tidak valid.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.9">
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
        .link-bantuan {
            color: var(--tosca-tua);
            font-size: 13px;
            text-decoration: none;
            display: block;
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
            font-weight: 600;
            cursor: pointer;
        }
        .link-bantuan:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="safe-container login-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
        <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 50px; width: 100%; max-width: 400px; text-align: center;">
            <h2 style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 30px;">Login Sistem</h2>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 small text-start" role="alert">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <input type="text" name="nama" class="login-input" placeholder="Nama Lengkap" autocomplete="off" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 20px; width: 100%;">
                <input type="text" name="nis" class="login-input" placeholder="Nomor Induk Siswa (NIS)" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 15px; width: 100%;">
                
                <span class="link-bantuan" data-bs-toggle="modal" data-bs-target="#modalBantuanLogin">Butuh Bantuan?</span>
                
                <button type="submit" name="login" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 10px;">Masuk</button>
            </form>
            
            <a href="register.php" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Belum Punya Akun? <strong style="font-weight: 800;">Daftar</strong></a>
        </div>
    </div>

    <div class="modal fade" id="modalBantuanLogin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px; text-align: left;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">Panduan Akses Masuk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h5 class="fw-bold mt-2 mb-3" style="color: var(--tosca-tua);">Hubungi Layanan Pengurus Lab</h5>
                    <p class="text-muted fs-6 mb-4">Jika NIS Anda tidak dikenali oleh sistem, silakan hubungi teknisi atau pengawas laboratorium yang sedang bertugas untuk melakukan sinkronisasi data master user Anda.</p>
                    
                    <div class="alert alert-warning text-start" style="border-radius: 10px; font-size: 14px;">
                        <strong>Informasi Otentikasi:</strong><br>
                        Aplikasi menggunakan metode pencocokan data <strong>Nama Lengkap</strong> dan <strong>NIS</strong> resmi yang telah terdaftar pada database sistem.
                    </div>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn text-white px-5 rounded-pill" style="background-color: var(--tosca-tua); font-weight: 600;" data-bs-dismiss="modal">Dimengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>