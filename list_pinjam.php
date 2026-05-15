<?php 
include 'koneksi.php';
session_start();

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pinjam Barang - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.1">
</head>
<body>

    <nav class="navbar-sis">
        <div class="safe-container d-flex align-items-center justify-content-between px-3">
            <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="index.php">
                SIS <span class="brand-sub">Sixseven Inventory System</span>
            </a>
            <div class="d-flex">
                <a href="#" class="nav-link-custom">Daftar</a>
                <a href="#" class="nav-link-custom">Masuk</a>
            </div>
        </div>
    </nav>

    <div class="safe-container">
        <div class="search-section px-3">
            <a href="javascript:history.back()" class="back-btn">
                <svg width="45" height="45" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm43.4 289.1c7.5 7.5 7.5 19.8 0 27.3s-19.8 7.5-27.3 0L165.4 257.7c-7.5-7.5-7.5-19.8 0-27.3L272.1 123.7c7.5-7.5 19.8-7.5 27.3 0s7.5 19.8 0 27.3L206.3 244h163.4c10.6 0 19.2 8.6 19.2 19.2s-8.6 19.2-19.2 19.2H206.3l93.1 94.7z" fill="#1d5c56"/>
                </svg>
            </a>
            <div class="search-bar">
                <input type="text" placeholder="Nama Barang">
                <span class="search-icon">🔍</span>
            </div>
            <div class="action-icons">
                <div class="action-item"><span>📦</span> Pinjam Barang</div>
                <div class="action-item"><span>🔄</span> Kembalikan</div>
            </div>
        </div>
    </div>

    <div class="safe-container px-3 mt-4" style="margin-bottom: 120px;">
        <div class="table-responsive">
            <form action="form_final_pinjam.php" method="POST" id="formPinjam">
                <table class="table table-borderless custom-table">
                    <thead>
                        <tr>
                            <th width="50"></th>
                            <th>Nama Barang</th>
                            <th>Deskripsi</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $id_item) {
                                // Ambil data barang dari database berdasarkan ID di session
                                $res = mysqli_query($conn, "SELECT * FROM barang WHERE id_barang = '$id_item'");
                                $row = mysqli_fetch_assoc($res);
                                if ($row) {
                        ?>
                            <tr>
                                <td><input type="checkbox" name="item_pilihan[]" value="<?= $row['id_barang']; ?>" class="form-check-input custom-check item-checkbox"></td>
                                <td class="fw-bold"><?= $row['nama_barang']; ?></td>
                                <td><?= $row['deskripsi']; ?></td>
                                <td class="text-center">
                                    <a href="keranjang_hapus.php?id=<?= $row['id_barang']; ?>" class="text-danger fw-bold text-decoration-none" onclick="return confirm('Hapus barang ini dari daftar?')">Hapus</a>
                                </td>
                            </tr>
                        <?php 
                                }
                            } 
                        } else { ?>
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <h5 class="text-muted">Daftar pinjam masih kosong.</h5>
                                    <a href="index.php" class="btn btn-sm text-white mt-2" style="background-color: var(--tosca-tua);">Cari Barang</a>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
        </div>
    </div>

    <div class="fixed-bottom action-footer">
    <div class="safe-container d-flex align-items-center justify-content-between px-3 h-100">
        <div class="d-flex align-items-center gap-3 text-white fw-bold">
            <input type="checkbox" id="checkAll" class="form-check-input custom-check-white">
            <label for="checkAll" style="cursor: pointer; user-select: none;">Pilih Semua</label>
        </div>

        <div class="d-flex gap-3">
            <a href="javascript:history.back()" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3">
                + Tambah Barang
            </a>
            
            <button type="submit" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;">
                Pinjam Sekarang
            </button>
        </div>
    </div>
</div>
    </form>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Logika Pilih Semua Checkbox
        const checkAll = document.getElementById('checkAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');

        checkAll.addEventListener('change', function() {
            itemCheckboxes.forEach(checkbox => {
                checkbox.checked = checkAll.checked;
            });
        });

        // Validasi sebelum submit (harus pilih minimal 1 barang)
        document.getElementById('formPinjam').onsubmit = function() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            if (checkedCount === 0) {
                alert('Silakan pilih minimal satu barang untuk dipinjam!');
                return false;
            }
        };
    </script>
</body>
</html>