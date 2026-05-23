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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'sub_header_siswa.php'; ?>

    <div class="safe-container px-3 mt-2" style="margin-bottom: 120px;">
        <form action="proses_kembali.php" method="POST" id="formKembali">
            <table class="table table-borderless custom-table text-center" style="max-width: 800px; margin: 0 auto;">
                <thead>
                    <tr>
                        <th width="50"></th>
                        <th>Nama Barang</th>
                        <th>Detail Barang</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Query data barang yang dipinjam user ini dan belum dikembalikan
                    $query = mysqli_query($conn, "SELECT p.id_pinjam, b.nama_barang, b.deskripsi 
                                                  FROM peminjaman p 
                                                  JOIN barang b ON p.id_barang = b.id_barang 
                                                  WHERE p.id_user = '$id_user' AND p.status_pengajuan != 'kembali'");
                    
                    if (mysqli_num_rows($query) > 0) {
                        while ($row = mysqli_fetch_assoc($query)) {
                    ?>
                        <tr>
                            <td><input type="checkbox" name="pinjam_pilihan[]" value="<?= $row['id_pinjam'] ?>" class="form-check-input custom-check item-checkbox"></td>
                            <td class="text-start px-4"><?= $row['nama_barang'] ?></td>
                            <td><?= $row['deskripsi'] ?: '-' ?></td>
                        </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3' class='py-4'>Tidak ada barang yang sedang dipinjam.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
    </div>

    <div class="fixed-bottom action-footer">
        <div class="safe-container d-flex align-items-center justify-content-between px-3 h-100">
            <div class="d-flex align-items-center gap-3 text-white fw-bold">
                <input type="checkbox" id="checkAll" class="form-check-input custom-check-white">
                <label for="checkAll" style="cursor: pointer; user-select: none;">Pilih Semua</label>
            </div>
            <div>
                <button type="submit" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;">
                    Kembalikan Sekarang
                </button>
            </div>
        </div>
    </div>
    </form>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const checkAll = document.getElementById('checkAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        checkAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
        });

        document.getElementById('formKembali').onsubmit = function() {
            if (document.querySelectorAll('.item-checkbox:checked').length === 0) {
                alert('Silakan pilih minimal satu barang untuk dikembalikan!');
                return false;
            }
        };
    </script>
</body>
</html>