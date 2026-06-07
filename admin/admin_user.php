<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi Akses: Memastikan pengguna sudah login dan merupakan bagian dari manajemen admin
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

// Parameter Sortir Baru (Default: nama_lengkap, Urutan: ASC)
$sort  = isset($_GET['sort']) && in_array($_GET['sort'], ['nama_lengkap', 'nis']) ? $_GET['sort'] : 'nama_lengkap';
$order = isset($_GET['order']) && in_array($_GET['order'], ['ASC', 'DESC']) ? $_GET['order'] : 'ASC';

// Membalikkan arah sortir saat tautan header diklik kembali
$toggle_order = ($order == 'ASC') ? 'DESC' : 'ASC';

// Menyimpan role user yang sedang login untuk pembatasan hak akses di bawah
$current_admin_role = $_SESSION['role']; 

// LOGIKA DROPDOWN LIMIT (20 ATAU 50 USER)
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
if (!in_array($limit, [10,25, 50])) {
    $limit = 25; 
}

// LOGIKA HALAMAN AKTIF (PAGINATION)
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman < 1) { $halaman = 1; }
$offset = ($halaman - 1) * $limit;

// Hitung total data berdasarkan filter pencarian untuk membuat pagination
$query_hitung = "SELECT COUNT(*) as total FROM users WHERE 1=1";
if ($search != '') {
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $query_hitung .= " AND (nama_lengkap LIKE '%$search_escaped%' OR nis LIKE '%$search_escaped%')";
}
$sql_hitung = mysqli_query($conn, $query_hitung);
$data_hitung = mysqli_fetch_assoc($sql_hitung);
$total_data = $data_hitung['total'];
$total_halaman = ceil($total_data / $limit);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Data Pengguna</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=2.6">
    <style>
        .admin-search-container { max-width: 1000px; margin: 30px auto 15px auto; }
        .admin-search-form { display: flex; border: 2px solid var(--tosca-tua); border-radius: 50px; padding: 4px; background: white; }
        .admin-search-form input { flex-grow: 1; border: none; padding: 10px 25px; outline: none; color: var(--tosca-tua); font-size: 16px; background: transparent; }
        .admin-search-form button { background-color: var(--tosca-tua); color: white; border: none; padding: 8px 50px; border-radius: 50px; font-weight: 700; font-size: 18px; cursor: pointer; }
        
        .admin-table-wrapper { max-width: 1000px; margin: 0 auto 30px auto; border: 2px solid var(--tosca-tua); background: white; border-radius: 10px; overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: white; background-color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; font-size: 14px; }
        .admin-table th a { color: white; text-decoration: none; display: block; width: 100%; }
        .admin-table td { padding: 15px 15px; border-bottom: 1px solid var(--tosca-muda); color: #333; font-size: 15px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }
        .btn-admin-action { background-color: var(--tosca-tua); color: white; text-decoration: none; padding: 10px 45px; border-radius: 30px; font-weight: 700; font-size: 18px; border: none; cursor: pointer; display: inline-block; }

        .badge-role { padding: 4px 12px; border-radius: 50px; font-size: 13px; font-weight: 600; display: inline-block; text-transform: capitalize; }
        .badge-admin { background-color: var(--tosca-muda); color: var(--tosca-tua); }
        .badge-siswa { background-color: #e2f1ee; color: #1e6f65; }

        .select-item-check, .select-all-check { width: 18px; height: 18px; cursor: pointer; }
        .col-select-master { display: none; }

        .inline-input { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; outline: none; }
        .inline-select { width: 100%; padding: 6px 12px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-size: 15px; background: white; cursor: pointer; }
        
        .pagination .page-link { color: var(--tosca-tua); border-color: var(--tosca-muda); }
        .pagination .page-item.active .page-link { background-color: var(--tosca-tua); border-color: var(--tosca-tua); color: white; }

        /* Style Kursor untuk Foto Thumbnail */
        .img-user-thumb {
            width: 40px; 
            height: 40px; 
            object-fit: cover; 
            border-radius: 50px; 
            border: 2px solid var(--tosca-tua);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .img-user-thumb:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    <?php include '../components/header.php'; ?>

    <div class="safe-container px-3 pb-5">
        <?php include '../components/sub_header_admin.php'; ?>

        <div class="d-flex align-items-center justify-content-between mx-auto mt-4" style="max-width: 1000px;">
            <h4 class="fw-bold m-0" style="color: var(--tosca-tua);">👥 Data Akun Pengguna Terdaftar</h4>
            
            <div class="d-flex gap-2 align-items-center">
                <button type="button" id="btnModeHapus" class="btn btn-outline-danger px-4 rounded-pill fw-bold">Hapus User</button>
                <button type="button" form="formBulkDelete" id="btnBulkDelete" class="btn btn-danger px-4 rounded-pill fw-bold d-none">Konfirmasi Hapus (<span id="checkCount">0</span>)</button>
                <button type="button" id="btnBatalHapus" class="btn btn-secondary px-3 rounded-pill fw-bold d-none">Batal</button>
                <button type="button" class="btn-admin-action" onclick="bukaModalTambahUser()">+ Tambah User</button>
            </div>
        </div>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="hidden" name="sort" value="<?= $sort ?>">
                <input type="hidden" name="order" value="<?= $order ?>">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <input type="text" name="search" placeholder="Tuliskan Nama / NIS Pengguna..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="mx-auto mb-2 d-flex justify-content-end" style="max-width: 1000px;">
            <div class="d-flex align-items-center gap-2">
                <span class="text-muted small fw-semibold">Tampilkan:</span>
                <select class="form-select form-select-sm" id="changeLimit" style="width: 80px; border: 2px solid var(--tosca-tua); border-radius: 8px; font-weight: 600;">
                    <option value="10" <?= $limit == 10 ? 'selected' : ''; ?>>10</option>
                    <option value="25" <?= $limit == 25 ? 'selected' : ''; ?>>25</option>
                    <option value="50" <?= $limit == 50 ? 'selected' : ''; ?>>50</option>
                </select>
            </div>
        </div>

        <div class="admin-table-wrapper"> 
            <form action="admin_user_hapus_massal.php" method="POST" id="formBulkDelete">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="50" class="col-select-master"><input type="checkbox" id="selectAll" class="form-check-input select-all-check"></th>
                        <th class="text-start" style="padding-left: 30px;">
                            <a href="?search=<?= urlencode($search) ?>&sort=nama_lengkap&order=<?= $toggle_order ?>&limit=<?= $limit ?>&halaman=<?= $halaman ?>">NAMA LENGKAP <?= $sort == 'nama_lengkap' ? ($order == 'ASC' ? '▲' : '▼') : '⇅' ?></a>
                        </th>
                        <th width="220">
                            <a href="?search=<?= urlencode($search) ?>&sort=nis&order=<?= $toggle_order ?>&limit=<?= $limit ?>&halaman=<?= $halaman ?>">NOMOR INDUK <?= $sort == 'nis' ? ($order == 'ASC' ? '▲' : '▼') : '⇅' ?></a>
                        </th>
                        <th width="220">HAK AKSES / ROLE</th>
                        <th width="150">FOTO ACUAN</th> 
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
                    
                    $query .= " ORDER BY $sort $order LIMIT $limit OFFSET $offset";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $id_u = $row['id_user'];
                            $target_user_role = $row['role'];
                            
                            $clean_role_name = str_replace('_', ' ', $target_user_role);
                            if (strpos($target_user_role, 'admin') !== false) {
                                $role_badge = '<span class="badge-role badge-admin">' . $clean_role_name . '</span>';
                            } else {
                                $role_badge = '<span class="badge-role badge-siswa">Siswa</span>';
                            }

                            if ($current_admin_role !== 'admin' && strpos($target_user_role, 'admin') !== false && $id_u != $_SESSION['id_user']) {
                                $nis_tampil = "[Akses Terbatas]";
                            } else {
                                $nis_tampil = htmlspecialchars($row['nis']);
                            }

                            $gambar_wajah = (!empty($row['foto_resmi']) && file_exists("../assets/img/pengguna/" . $row['foto_resmi'])) ? "../assets/img/pengguna/" . $row['foto_resmi'] : "../assets/img/pengguna/default_user.jpg";
                    ?>
                    <tr id="row_<?= $id_u; ?>">
                        <td class="col-select-master">
                            <?php if($id_u == $_SESSION['id_user']): ?>
                                <input type="checkbox" disabled class="form-check-input opacity-25" title="Akun sedang digunakan">
                            <?php elseif($current_admin_role !== 'admin' && strpos($target_user_role, 'admin') !== false): ?>
                                <input type="checkbox" disabled class="form-check-input opacity-25" title="Akses terbatas untuk akun manajemen">
                            <?php else: ?>
                                <input type="checkbox" name="id_user_pilihan[]" value="<?= $id_u; ?>" class="form-check-input select-item-check item-checkbox">
                            <?php endif; ?>
                        </td>
                        
                        <td>
                            <span class="view-mode fw-bold text-dark d-block text-start" style="padding-left: 30px;"><?= htmlspecialchars($row['nama_lengkap']) ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none" id="input_nama_<?= $id_u; ?>" value="<?= htmlspecialchars($row['nama_lengkap']) ?>">
                        </td>
                        
                        <td>
                            <span class="view-mode font-monospace"><?= $nis_tampil ?></span>
                            <input type="text" class="form-control inline-input edit-mode d-none text-center font-monospace" id="input_nis_<?= $id_u; ?>" value="<?= htmlspecialchars($row['nis']) ?>">
                        </td>
                        
                        <td>
                            <div class="view-mode"><?= $role_badge ?></div>
                            
                            <?php if ($current_admin_role == 'admin'): ?>
                                <select class="form-select inline-select edit-mode d-none" id="input_role_<?= $id_u; ?>">
                                    <option value="siswa" <?= $row['role'] == 'siswa' ? 'selected' : '' ?>>Siswa</option>
                                    <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>Super Admin</option>
                                    <option value="admin_timber" <?= $row['role'] == 'admin_timber' ? 'selected' : '' ?>>Admin Timber</option>
                                    <option value="admin_dkv1" <?= $row['role'] == 'admin_dkv1' ? 'selected' : '' ?>>Admin DKV 1</option>
                                    <option value="admin_dkv2" <?= $row['role'] == 'admin_dkv2' ? 'selected' : '' ?>>Admin DKV 2</option>
                                    <option value="admin_animasi" <?= $row['role'] == 'admin_animasi' ? 'selected' : '' ?>>Admin Animasi</option>
                                </select>
                            <?php else: ?>
                                <input type="hidden" id="input_role_<?= $id_u; ?>" value="<?= $row['role']; ?>">
                                <span class="edit-mode d-none text-muted small">Akses Terkunci</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <img src="<?= $gambar_wajah; ?>" alt="Preview" class="view-mode img-user-thumb tombol-foto-popup" data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>" data-src="<?= $gambar_wajah; ?>">
                            <input type="file" class="form-control inline-input edit-mode d-none" id="input_foto_<?= $id_u; ?>" accept="image/*" style="font-size: 11px; max-width: 140px; margin: 0 auto;">
                        </td>
                        
                        <td>
                            <?php if ($current_admin_role == 'admin' || (strpos($target_user_role, 'admin') === false || $id_u == $_SESSION['id_user'])): ?>
                                <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold view-mode" onclick="aktifkanEditInline(<?= $id_u; ?>)">Edit</button>
                            <?php else: ?>
                                <span class="view-mode text-muted small">Tidak Ada Akses</span>
                            <?php endif; ?>
                            
                            <div class="edit-mode d-none d-flex justify-content-center gap-2">
                                <button type="button" class="btn btn-sm btn-success px-2 rounded-pill fw-bold" onclick="simpanEditInline(<?= $id_u; ?>)">Simpan</button>
                                <button type="button" class="btn btn-sm btn-secondary px-2 rounded-pill fw-bold" onclick="batalEditInline(<?= $id_u; ?>)">Batal</button>
                            </div>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 30px;' class='text-muted text-center'>Data pengguna tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
            </form>
        </div>

        <?php if ($total_halaman > 1): ?>
        <div class="d-flex justify-content-center mt-4">
            <nav>
                <ul class="pagination pagination-md shadow-sm rounded-pill overflow-hidden">
                    <li class="page-item <?= $halaman <= 1 ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman - 1; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Previous">
                            <span>&laquo; Prev</span>
                        </a>
                    </li>
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <li class="page-item <?= $halaman == $i ? 'active' : ''; ?>">
                            <a class="page-link px-3" href="?halaman=<?= $i; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>"><?= $i; ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $halaman >= $total_halaman ? 'disabled' : ''; ?>">
                        <a class="page-link px-3" href="?halaman=<?= $halaman + 1; ?>&limit=<?= $limit; ?>&search=<?= urlencode($search) ?>&sort=<?= $sort ?>&order=<?= $order ?>" aria-label="Next">
                            <span>Next &raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
        <div class="text-center text-muted small mt-1">
            Menampilkan data ke-<?= $offset + 1; ?> sampai <?= min($offset + $limit, $total_data); ?> dari total <?= $total_data; ?> users.
        </div>
        <?php endif; ?>
    </div>

    <div class="modal fade" id="modalFotoUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" style="max-width: 450px;">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h6 class="modal-title fw-bold" id="title_nama_user">Potret Acuan Biometrik</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 text-center bg-dark rounded-bottom-4">
                    <img id="img_popup_user_besar" src="" class="img-fluid rounded-3 border border-secondary shadow-lg" style="width: 100%; max-height: 480px; object-fit: contain;" alt="Gagal Memuat Gambar">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalTambahUser" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">Tambahkan Pengguna Baru</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_user_tambah_proses.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nama Lengkap :</label>
                            <input type="text" name="nama_lengkap" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Tulis nama lengkap siswa atau admin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Nomor Induk (NIS / ID Login) :</label>
                            <input type="text" name="nis" class="form-control font-monospace" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" placeholder="Contoh: 2207421034" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Hak Akses Level :</label>
                            <?php if ($current_admin_role == 'admin'): ?>
                                <select name="role" class="form-select" style="border: 2px solid var(--tosca-tua); border-radius: 10px;">
                                    <option value="siswa">Siswa (Akses Katalog & Pinjam)</option>
                                    <option value="admin">Super Admin (Akses Penuh Pengguna)</option>
                                    <option value="admin_timber">Admin Timber (Lab Tim Berangkat)</option>
                                    <option value="admin_dkv1">Admin DKV 1</option>
                                    <option value="admin_dkv2">Admin DKV 2</option>
                                    <option value="admin_animasi">Admin Animasi</option>
                                </select>
                            <?php else: ?>
                                <input type="text" class="form-control bg-light" value="Siswa (Akses Katalog & Pinjam)" readonly style="border: 2px solid var(--tosca-tua); border-radius: 10px;">
                                <input type="hidden" name="role" value="siswa">
                            <?php endif; ?>
                        </div>
                        <div class="mb-1">
                            <label class="form-label fw-bold" style="color: var(--tosca-tua);">Foto Wajah Resmi (Acuan Validasi) :</label>
                            <input type="file" name="foto_resmi" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" accept="image/*" required>
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

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // SCRIPT TRIGGER POPUP FOTO USER
        const tombolFotoUser = document.querySelectorAll('.tombol-foto-popup');
        const modalFotoUserBS = new bootstrap.Modal(document.getElementById('modalFotoUser'));

        tombolFotoUser.forEach(img => {
            img.addEventListener('click', function() {
                const srcGambar = this.getAttribute('data-src');
                const namaUser = this.getAttribute('data-nama');
                
                document.getElementById('title_nama_user').innerText = "👤 Acuan Wajah: " + namaUser;
                document.getElementById('img_popup_user_besar').setAttribute('src', srcGambar);
                modalFotoUserBS.show();
            });
        });

        document.getElementById('changeLimit').addEventListener('change', function() {
            const selectedLimit = this.value;
            const searchVal = "<?= urlencode($search) ?>";
            const sortVal = "<?= $sort ?>";
            const orderVal = "<?= $order ?>";
            window.location.href = `?halaman=1&limit=${selectedLimit}&search=${searchVal}&sort=${sortVal}&order=${orderVal}`;
        });

        function bukaModalTambahUser() {
            const modalElement = document.getElementById('modalTambahUser');
            const instanceModal = new bootstrap.Modal(modalElement);
            instanceModal.show();
        }

        function aktifkanEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));
        }

        function batalEditInline(id) {
            const row = document.getElementById('row_' + id);
            row.querySelectorAll('.edit-mode').forEach(el => el.classList.add('d-none'));
            row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
            document.getElementById('input_foto_' + id).value = '';
        }

        function simpanEditInline(id) {
            const namaVal = document.getElementById('input_nama_' + id).value;
            const nisVal  = document.getElementById('input_nis_' + id).value;
            const roleVal = document.getElementById('input_role_' + id).value;
            const fotoInput = document.getElementById('input_foto_' + id);

            const formData = new FormData();
            formData.append("id_user", id);
            formData.append("nama_lengkap", namaVal);
            formData.append("nis", nisVal);
            formData.append("role", roleVal);
            
            if (fotoInput.files.length > 0) {
                formData.append("foto_resmi", fotoInput.files[0]);
            }

            const xhr = new XMLHttpRequest();
            xhr.open("POST", "admin_user_edit_inline_proses.php", true);
            
            xhr.onreadystatechange = function() {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    if (xhr.responseText.trim() === "success") {
                        window.location.reload();
                    } else {
                        alert("Gagal memperbarui data pengguna: " + xhr.responseText);
                    }
                }
            };
            xhr.send(formData);
        }

        const btnModeHapus = document.getElementById('btnModeHapus');
        const btnBulkDelete = document.getElementById('btnBulkDelete');
        const btnBatalHapus = document.getElementById('btnBatalHapus');
        const colSelectMaster = document.querySelectorAll('.col-select-master');
        
        const selectAll = document.getElementById('selectAll');
        const itemCheckboxes = document.querySelectorAll('.item-checkbox:not(:disabled)');
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
            document.querySelectorAll('.item-checkbox').forEach(cb => cb.checked = false);
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
                if (document.querySelectorAll('.item-checkbox:checked:not(:disabled)').length === itemCheckboxes.length) {
                    selectAll.checked = true;
                }
                updateDeleteButtonStatus();
            });
        });

        btnBulkDelete.addEventListener('click', function() {
            const total = document.querySelectorAll('.item-checkbox:checked').length;
            const konfirmasi = confirm("Apakah Anda yakin ingin menghapus permanen sejumlah " + total + " akun pengguna dari database?");
            if (konfirmasi) {
                document.getElementById('formBulkDelete').submit();
            }
        });
    </script>
    <?php include '../components/footer.php'; ?>
</body>
</html>