<?php
session_start();
// Mengatur zona waktu agar fungsi date() mengambil waktu WIB secara akurat
date_default_timezone_set('Asia/Jakarta');

// 🔥 FIX JALUR: Mundur satu folder karena file ini berjalan di dalam subfolder siswa/
include '../koneksi.php';

// Proteksi Akses: Jika tidak ada session user login, kembalikan ke gerbang login di luar subfolder
if (!isset($_SESSION['id_user'])) {
    header("Location: ../login.php");
    exit;
}

$id_ref = isset($_GET['id_ref']) ? $_GET['id_ref'] : '0000000';
$pengawas_tampil = isset($_GET['pengawas']) ? $_GET['pengawas'] : '-';

// SINKRONISASI FORMAT WAKTU: Menampilkan tanggal dan jam penyerahan secara aktual
$tanggal_aktual = date('d F Y, H:i') . " WIB";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengembalian Diajukan - SIS</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.5">
</head>
<body class="bg-light">

    <?php include '../components/header.php'; ?>

    <div class="safe-container px-3">
        <div class="success-card shadow-sm bg-white" style="max-width: 520px; margin: 60px auto 100px auto; padding: 40px 30px; border-radius: 20px; text-align: center; border: 1px solid #e2f1ee;">
            
            <div class="success-icon mb-3" style="display: inline-flex; align-items: center; justify-content: center; width: 75px; height: 75px; background-color: #e2f1ee; color: #1a5f57; border-radius: 50%;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            
            <h3 class="fw-bold mb-2" style="color: var(--tosca-tua);">Pengembalian Diajukan!</h3>
            <p class="text-muted small mb-4">Berkas tanda terima digital masuk antrean laboratorium</p>
            
            <div class="mx-auto p-3 mb-4 rounded-3 text-start" style="max-width: 440px; background-color: #f8faf9; border: 1px solid #c4e1db;">
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-5 fw-bold text-secondary" style="font-size: 14px;">Nomor Referensi</div>
                    <div class="col-1 text-center text-muted">:</div>
                    <div class="col-6 font-monospace fw-bold text-dark text-break" style="font-size: 14px; letter-spacing: 0.5px;"><?= htmlspecialchars($id_ref) ?></div>
                </div>
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-5 fw-bold text-secondary" style="font-size: 14px;">Waktu Serah</div>
                    <div class="col-1 text-center text-muted">:</div>
                    <div class="col-6 fw-bold text-dark" style="font-size: 14px;"><?= htmlspecialchars($tanggal_aktual) ?></div>
                </div>
                <div class="row g-0 align-items-center">
                    <div class="col-5 fw-bold text-secondary" style="font-size: 14px;">Target Penerima</div>
                    <div class="col-1 text-center text-muted">:</div>
                    <div class="col-6 fw-bold style-color" style="font-size: 14px; color: var(--tosca-tua);"><?= htmlspecialchars($pengawas_tampil) ?></div>
                </div>
            </div>

            <p class="mb-4 text-muted" style="font-size: 13.5px; line-height: 1.6; padding: 0 10px;">
                Silakan bawa dan serahkan aset fisik barang langsung ke ruangan laboratorium tempat meminjam. Pastikan <strong><?= htmlspecialchars($pengawas_tampil); ?></strong> atau pengawas piket aktif memeriksa kecocokan data dan kelayakan barang untuk merilis tanggungan pinjaman Anda.
            </p>

            <div class="d-flex justify-content-between gap-3 mt-4 px-2">
                <a href="../index.php" class="btn text-white fw-bold py-2 w-50 rounded-pill shadow-sm" style="background-color: var(--tosca-tua); font-size: 14px;">Katalog Utama</a>
                
                <a href="list_kembali.php" class="btn btn-outline-secondary fw-bold py-2 w-50 rounded-pill" style="font-size: 14px;">Daftar Kembali</a>
            </div>
        </div>
    </div>

    <?php include '../components/footer.php'; ?>
</body>
</html>