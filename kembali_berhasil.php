<?php
session_start();
$id_ref = isset($_GET['id_ref']) ? $_GET['id_ref'] : '0000000';
$tanggal = date('d F Y');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pengembalian Berhasil - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="safe-container">
        <div class="success-card shadow-sm bg-white" style="max-width: 500px; margin: 50px auto; padding: 30px; border-radius: 15px; text-align: center;">
            <div class="success-icon mb-3" style="display: inline-flex; align-items: center; justify-content: center; width: 70px; height: 70px; background-color: #d1e7dd; color: #0f5132; border-radius: 50%;">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="fw-bold text-dark mb-4">Pengembalian Berhasil</h3>
            
            <div class="mx-auto p-3 mb-4 rounded-3 text-start" style="max-width: 400px; background-color: #f8f9fa; border: 1px solid #e9ecef;">
                <div class="row g-0 mb-2 align-items-center">
                    <div class="col-5 fw-bold text-secondary" style="font-size: 15px;">ID Peminjaman</div>
                    <div class="col-1 text-center text-muted">:</div>
                    <div class="col-6 fw-bold text-dark text-break" style="font-size: 15px; letter-spacing: 0.5px;"><?= htmlspecialchars($id_ref) ?></div>
                </div>
                <div class="row g-0 align-items-center">
                    <div class="col-5 fw-bold text-secondary" style="font-size: 15px;">Tanggal</div>
                    <div class="col-1 text-center text-muted">:</div>
                    <div class="col-6 fw-bold text-dark" style="font-size: 15px;"><?= htmlspecialchars($tanggal) ?></div>
                </div>
            </div>

            <p class="mb-4 text-muted" style="font-size: 14px; line-height: 1.6;">
                Silahkan antar barang pengembalian yang dipinjam ke bagian ruangan penanggung jawab. Terima kasih atas pengertiannya.
            </p>

            <div class="d-flex justify-content-between gap-3 mt-4">
                <a href="index.php" class="btn text-white fw-bold px-4 py-2 w-50 rounded-3" style="background-color: var(--tosca-tua);">Kembali</a>
                <a href="logout.php" class="btn text-white fw-bold px-4 py-2 w-50 rounded-3" style="background-color: var(--tosca-tua);">Keluar</a>
            </div>
        </div>
    </div>
</body>
</html>