<?php 
session_start();
include 'koneksi.php';

// Redirect jika belum login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit;
}

$id_user = $_SESSION['id_user'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kembalikan Barang - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.4">
    <style>
        /* Mengadopsi wrapper tabel standar SIS agar seragam */
        .admin-table-wrapper {
            max-width: 1000px;
            margin: 0 auto 50px auto;
            border: 2px solid var(--tosca-tua);
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .admin-table th {
            color: white;
            background-color: var(--tosca-tua);
            font-weight: 700;
            padding: 15px 15px;
            font-size: 15px;
        }
        .admin-table td {
            padding: 15px 15px;
            border-bottom: 1px solid var(--tosca-muda);
            color: #333;
            font-size: 15px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }

        /* Style Badge Kategori Lab */
        .badge-lab {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
            background-color: var(--tosca-muda);
            color: var(--tosca-tua);
            display: inline-block;
            white-space: nowrap;
        }

        /* Merapikan visual Checkbox custom */
        .custom-check {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .custom-check-white {
            width: 20px;
            height: 20px;
            cursor: pointer;
            border: 2px solid white !important;
            background-color: transparent;
        }
        .custom-check-white:checked {
            background-color: white !important;
            border-color: white !important;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 mt-5" style="margin-bottom: 150px;">
        <h4 class="fw-bold mb-3" style="color: var(--tosca-tua); max-width: 1000px; margin: 0 auto 15px auto;">
            🔄 Pilih Barang yang Ingin Dikembalikan
        </h4>
        
        <div class="admin-table-wrapper">
            <form action="proses_kembali.php" method="POST" id="formKembali">
                <input type="hidden" name="pengawas_penerima" id="hidden_pengawas">
                
                <input type="hidden" name="tgl_kembali_real" id="tgl_kembali_real">

                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="70">PILIH</th>
                            <th class="text-start" style="padding-left: 10px;">NAMA BARANG</th>
                            <th class="text-start">DESKRIPSI / DETAIL</th>
                            <th>KATEGORI ASET</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query_string = "SELECT p.id_pinjam, b.nama_barang, b.deskripsi, k.nama_kategori 
                                         FROM peminjaman p 
                                         JOIN barang b ON p.id_barang = b.id_barang 
                                         JOIN kategori k ON b.id_kategori = k.id_kategori
                                         WHERE p.id_user = '$id_user' AND p.status_pengajuan = 'disetujui'";
                        
                        $query = mysqli_query($conn, $query_string);
                        $hitung_data = mysqli_num_rows($query);
                        
                        if ($hitung_data > 0) {
                            while ($row = mysqli_fetch_assoc($query)) {
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="pinjam_pilihan[]" value="<?= $row['id_pinjam'] ?>" class="form-check-input custom-check item-checkbox">
                                </td>
                                <td class="fw-bold text-start text-dark" style="padding-left: 10px;"><?= htmlspecialchars($row['nama_barang']) ?></td>
                                <td class="text-start text-muted"><?= $row['deskripsi'] ? htmlspecialchars($row['deskripsi']) : '-' ?></td>
                                <td>
                                    <span class="badge-lab">📍 <?= htmlspecialchars($row['nama_kategori']) ?></span>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='4' class='py-5 text-muted'><h5>Tidak ada barang yang bisa dikembalikan saat ini.</h5><small>Barang baru muncul di sini jika pengajuan pinjam sudah disetujui oleh admin lab.</small></td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </form> 
        </div>
    </div>

    <div class="fixed-bottom action-footer">
        <div class="safe-container d-flex align-items-center justify-content-between px-3 h-100">
            <div class="d-flex align-items-center gap-3 text-white fw-bold">
                <input type="checkbox" id="checkAll" class="form-check-input custom-check-white">
                <label for="checkAll" style="cursor: pointer; user-select: none; font-size: 16px;">Pilih Semua</label>
            </div>
            <div>
                <?php if ($hitung_data > 0): ?>
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;" onclick="pemicuModalKembali()">
                        Kembalikan Sekarang
                    </button>
                <?php else: ?>
                    <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3 disabled" style="opacity: 0.5;">
                        Kembalikan Sekarang
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerifKembali" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">📝 Verifikasi Pengembalian</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted small">Harap isi nama staf / pengawas piket laboratorium yang berjaga dan menerima pengembalian barang fisik hari ini.</p>
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nama Pengawas Penerima :</label>
                        <input type="text" id="pengawas_penerima" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Tulis nama pengawas laboratorium" required>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);" onclick="eksekusiFormKembali()">Konfirmasi & Serahkan</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const checkAll = document.getElementById('checkAll');
        const modalVerifBS = new bootstrap.Modal(document.getElementById('modalVerifKembali'));

        // Logika check/uncheck seluruh checkbox sekaligus
        if (checkAll) {
            checkAll.addEventListener('change', function() {
                const itemCheckboxes = document.querySelectorAll('.item-checkbox');
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = checkAll.checked;
                });
            });
        }

        // Fungsi saat tombol "Kembalikan Sekarang" di footer diklik
        function pemicuModalKembali() {
            // Validasi: Hitung berapa item yang diberi centang oleh user secara real-time
            const totalDicentang = document.querySelectorAll('input[name="pinjam_pilihan[]"]:checked').length;
            
            console.log("Jumlah barang yang dicentang: " + totalDicentang);

            if (totalDicentang === 0) {
                alert('⚠️ Silakan pilih minimal satu barang yang ingin dikembalikan!');
                return; 
            }

            // Jika lolos validasi, reset form input nama pengawas lalu buka modal popup
            document.getElementById('pengawas_penerima').value = '';
            modalVerifBS.show();
        }

        // Fungsi final saat tombol "Konfirmasi & Serahkan" di dalam modal diklik
        function eksekusiFormKembali() {
            const namaPengawas = document.getElementById('pengawas_penerima').value.trim();
            
            if (namaPengawas === "") {
                alert("❌ Nama pengawas tidak boleh kosong! Mohon konfirmasikan dengan penjaga lab.");
                return;
            }

            // 1. Amankan nama pengawas ke input hidden utama
            document.getElementById('hidden_pengawas').value = namaPengawas;

            // 2. OTOMATISASI TANGGAL DEVICE: Tangkap waktu internal laptop/HP user saat ini juga
            const dateDevice = new Date();
            const year = dateDevice.getFullYear();
            const month = String(dateDevice.getMonth() + 1).padStart(2, '0'); // Ditambah 1 karena index bulan JS mulai dari 0
            const day = String(dateDevice.getDate()).padStart(2, '0');
            
            const formatTanggalMySQL = `${year}-${month}-${day}`;
            
            // 3. Amankan hasil konversi tanggal kalender device ke input hidden tgl_kembali_real
            document.getElementById('tgl_kembali_real').value = formatTanggalMySQL;

            // 4. Eksekusi kirim data ke file proses_kembali.php
            document.getElementById('formKembali').submit();
        }

        // Cek parameter error URL bawaan sistem
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('status') === 'gagal_pilih') {
            alert('⚠️ Gagal memproses! Silakan pilih minimal satu barang yang ingin dikembalikan!');    
        }
    </script>
</body>
</html>