<?php 
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}
$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Data User</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.6">
    <style>
        /* MEMAKSIMALKAN UKURAN CONTAINER DAN SEARCH BAR */
        .admin-search-container { max-width: 1000px; margin: 30px auto 30px auto; }
        .admin-search-form { display: flex; border: 2px solid var(--tosca-tua); border-radius: 50px; padding: 4px; background: white; }
        .admin-search-form input { flex-grow: 1; border: none; padding: 10px 25px; outline: none; color: var(--tosca-tua); font-size: 16px; background: transparent; }
        .admin-search-form button { background-color: var(--tosca-tua); color: white; border: none; padding: 8px 50px; border-radius: 50px; font-weight: 700; font-size: 18px; cursor: pointer; }
        
        /* MEMAKSIMALKAN WRAPPER TABEL USER MENJADI 1000PX SAMA SEPERTI BARANG */
        .admin-table-wrapper { max-width: 1000px; margin: 0 auto 30px auto; border: 2px solid var(--tosca-tua); background: white; border-radius: 10px; overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: white; background-color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; font-size: 14px; }
        .admin-table td { padding: 15px 15px; border-bottom: 1px solid var(--tosca-muda); color: #333; font-size: 15px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .btn-admin-action { background-color: var(--tosca-tua); color: white; text-decoration: none; padding: 10px 45px; border-radius: 30px; font-weight: 700; font-size: 18px; border: none; cursor: pointer; display: inline-block; }

        .badge-role { padding: 4px 12px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-block; }
        .badge-admin { background-color: var(--tosca-muda); color: var(--tosca-tua); }
        .badge-siswa { background-color: #e2f1ee; color: #1e6f65; }

        /* Checkbox Custom toggling */
        .select-item-check, .select-all-check { width: 18px; height: 18px; cursor: pointer; }
        .col-select-master { display: none; }

        /* Inline Form Inputs */
        .inline-input { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; outline: none; }
        .inline-select { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; background: white; cursor: pointer; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="safe-container px-3 pb-5">
       <?php include 'sub_header_admin.php'; ?>

        <div class="d-flex align-items-center justify-content-between mx-auto mt-4" style="max-width: 1000px;">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua);">👥 Data Akun Pengguna Terdaftar</h4>
            
            <div class="d-flex gap-2">
                <button type="button" id="btnModeHapus" class="btn btn-outline-danger px-4 rounded-pill fw-bold">🗑️ Hapus User</button>
                <button type="button" form="formBulkDelete" id="btnBulkDelete" class="btn btn-danger px-4 rounded-pill fw-bold d-none">Konfirmasi Hapus (<span id="checkCount">0</span>)</button>
                <button type="button" id="btnBatalHapus" class="btn btn-secondary px-3 rounded-pill fw-bold d-none">Batal</button>
                <button type="button" class="btn-admin-action" data-bs-toggle="modal" data-bs-target="#modalTambahUser">+ Tambah User</button>
            </div>
        </div>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tuliskan Nama / NIS Pengguna..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper"> 
            <form action="admin_user_hapus_massal.php" method="POST" id="formBulkDelete">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="50" class="col-select-master"><input type="checkbox" id="selectAll" class="form-check-input select-all-check"></th>
                        <th class="text-start" style="padding-left: 30px;">NAMA LENGKAP</th>
                        <th width="250">NOMOR INDUK (NIS / ID)</th>
                        <th width="250">HAK AKSES / ROLE</th>
                        <th width="180">AKSI INLINE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM users WHERE 1=1";
                    if ($search != '') {
                        $search_escaped = mysqli_real_escape_string($conn, $search);
                        $query .= " AND (nama_lengkap LIKE '%$search_escaped%' OR nis LIKE '%$search_escaped%')";
                    }
                    $query .= " ORDER BY role ASC, nama_lengkap ASC";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $id_u = $row['id_user'];
                            
                            if ($row['role'] == 'admin') {
                                $role_badge = '<span class="badge-role badge-admin">Admin Lab</span>';
                            } else {
                                $role_badge = '<span class="badge-role badge-siswa">Siswa</span>';
                            }
                    ?>
                    <tr id="row_<?= $id_u; ?>">
                        <td class="col-select-master">
                            <?php if($id_u != $_SESSION['id_user']): ?>
                                <input type="checkbox" name="id_user_pilihan[]" value="<?= $id_u; ?>" class="form-check-input select-item-check item-checkbox">
                            <?php else: ?>
                                <input type="checkbox" disabled class="form-check-input opacity-25" title="Akun Anda sedang aktif">
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <span class="view-mode fw-bold text-dark d-block text-start" style="padding-left: 30px;"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none" id="input_nama_<?= $id_u; ?>" value="<?= htmlspecialchars($row['nama_lengkap']) ?>">
                        </td>
                        
                        <td>
                            <span class="view-mode font-monospace"><?= htmlspecialchars($row['nis']) ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none text-center font-monospace" id="input_nis_<?= $id_u; ?>" value="<?= htmlspecialchars($row['nis']) ?>">
                        </td>
                        
                        <td>
                            <div class="view-mode"><?= $role_badge ?></div>
                            <select class="form-select inline-select edit-mode d-none" id="input_role_<?= $id_u; ?>">
                                <option value="siswa" <?= $row['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Admin Lab</option>
                            </select>
                        </td>
                        
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold view-mode" onclick="aktifkanEditInline(<?= $id_u; ?>)">✏️ Edit</button>
                            
                            <div class="edit-mode d-none d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-success px-2 rounded-pill fw-bold" onclick="simpanEditInline(<?= $id_u; ?>)">💾 Simpan</button>
                                <button type="button" class="btn btn-sm btn-secondary px-2 rounded-pill fw-bold" onclick="batalEditInline(<?= $id_u; ?>)">Batal</button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px;' class='text-muted'>Data pengguna tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">➕ Tambahkan Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_user_tambah_proses.php" method="POST">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nama Lengkap :</label>
                            <input type="text" name="nama_lengkap" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Tulis nama lengkap siswa/admin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nomor Induk (NIS / ID Login) :</label>
                            <input type="text" name="nis" class="form-control font-monospace" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Contoh: 2207421034" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Password Akun :</label>
                            <input type="password" name="password" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Tentukan password awal akun" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Hak Akses Level :</label>
                            <select name="role" class="form-select" style="border: 2px solid var(--tosca-tua); border-radius: 10px;">
                                <option value="siswa">Siswa (Akses Katalog & Pinjam)</option>
                                <option value="admin">Admin Lab (Akses Verifikasi & Inventory)</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);">Daftarkan Pengguna</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // ================== LOGIKA INLINE EDITING USER ==================
        function aktifkanEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
        }

        function batalEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
        }

        function simpanEditInline(id) {
            const namaVal = document.getElementById('input_nama_' + id).value;
            const nisVal  = document.getElementById('input_nis_' + id).value;
            const roleVal = document.getElementById('input_role_' + id).value;

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "admin_user_edit_inline_proses.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        window.location.reload();
                    } else {
                        alert("Gagal memperbarui pengguna: " + xhr.responseText);
                    }
                }
            };
            xhr.send("id_user=" + id + "&nama_lengkap=" + encodeURIComponent(namaVal) + "&nis=" + encodeURIComponent(nisVal) + "&role=" + encodeURIComponent(roleVal));
        }

        // ================== LOGIKA MODE BULK DELETE USER ==================
        const btnModeHapus = document.getElementById('btnModeHapus');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const btnBatalHapus = document.getElementById('btnBatalHapus');
        const colSelectMaster = document.querySelectorAll('.col-select-master');
        
        const selectAll = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox');
        const checkCount = document.getElementById('checkCount');

        btnModeHapus.addEventListener('click', function() {
            this.classList.add('d-none');
            btnBatalHapus.classList.remove('d-none');
            btnBulkDelete.classList.remove('d-none');
            colSelectMaster.forEach(el => el.style.display = 'table-cell');
            updateDeleteButtonStatus();
        });

        btnBatalHapus.addEventListener('click', function() {
            btnModeHapus.classList.remove('d-none');
            this.classList.add('d-none');
            btnBulkDelete.classList.add('d-none');
            colSelectMaster.forEach(el => el.style.display = 'none');
            selectAll.checked = false;
            itemCheckboxes.forEach(cb => cb.checked = false);
            updateDeleteButtonStatus();
        });

        function updateDeleteButtonStatus() {
            const checkedCount = document.querySelectorAll('.item-checkbox:checked').length;
            checkCount.innerText = checkedCount;

            if (checkedCount > 0) {
                btnBulkDelete.style.opacity = "1";
                btnBulkDelete.removeAttribute('disabled');
            } else {
                btnBulkDelete.style.opacity = "0.5";
                btnBulkDelete.setAttribute('disabled', 'true');
            }
        }

        selectAll.addEventListener('change', function() {
            itemCheckboxes.forEach(cb => cb.checked = selectAll.checked);
            updateDeleteButtonStatus();
        });

        itemCheckboxes.forEach(cb => {
            cb.addEventListener('change', function() {
                if (!this.checked) selectAll.checked = false;
                if (document.querySelectorAll('.item-checkbox:checked').length === itemCheckboxes.length) {
                    selectAll.checked = true;
                }
                updateDeleteButtonStatus();
            });
        });

        btnBulkDelete.addEventListener('click', function() {
            const total = document.querySelectorAll('.item-checkbox:checked').length;
            const konfirmasi = confirm("⚠️ PERINGATAN!\nApakah Anda yakin ingin menghapus permanen " + total + " akun pengguna terpilih dari sistem?");
            if (konfirmasi) {
                document.getElementById('formBulkDelete').submit();
            }
        });
    </script>

    <?php include 'footer.php'; ?>

</body>
</html>