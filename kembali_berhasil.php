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
        <div class="success-card shadow-sm bg-white">
            <div class="success-icon">
                <svg width="40" height="40" fill="none" stroke="currentColor" stroke-width="4" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="fw-bold text-dark mb-4">Pengembalian Berhasil</h3>
            
            <table class="table table-borderless text-start fs-5" style="max-width: 300px; margin: 0 auto;">
                <tr>
                    <td class="fw-bold">ID Peminjaman</td>
                    <td>: <?= $id_ref ?></td>
                </tr>
                <tr>
                    <td class="fw-bold">Tanggal</td>
                    <td>: <?= $tanggal ?></td>
                </tr>
            </table>

            <p class="mt-4 mb-4" style="font-size: 14px; color: #555;">
                Silahkan antar barang pengembalian yang dipinjam ke bagian ruangan penanggung jawab. Terima kasih atas pengertiannya.
            </p>

            <div class="d-flex justify-content-between px-3">
                <a href="index.php" class="btn text-white fw-bold px-4" style="background-color: var(--tosca-tua);">Kembali</a>
                <a href="logout.php" class="btn text-white fw-bold px-4" style="background-color: var(--tosca-tua);">Keluar</a>
            </div>
        </div>
    </div>
</body>
</html>