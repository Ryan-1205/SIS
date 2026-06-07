<?php
// Memastikan session aktif di komponen header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// DETEKSI OTOMATIS DIREKTORI: Mengakomodasi folder root, admin/, dan siswa/
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

// REVISI FIX: Jika berada di folder admin ATAU siswa, maka prefix mundur satu tingkat ('../')
$prefix = ($current_dir === 'admin' || $current_dir === 'siswa') ? '../' : '';

// Logika menentukan arah link logo SIS berdasarkan role login (Mendukung semua level admin)
$logo_link = $prefix . "index.php"; 
if (isset($_SESSION['id_user']) && strpos($_SESSION['role'], 'admin') !== false) {
    // Jika admin, diarahkan ke file admin_user.php di dalam folder admin
    $logo_link = ($current_dir === 'admin') ? 'admin_user.php' : $prefix . 'admin/admin_user.php';
}

// ================= LOGIKA AMBIL FOTO PROFIL DROPDOWN HEADER =================
$avatar_tampil = $prefix . "assets/img/pengguna/default_user.jpg"; // Fallback cadangan
if (isset($_SESSION['id_user'])) {
    // Membawa nama file foto dari session (pastikan saat login lu sudah memasukkan $_SESSION['foto_resmi'] = $data['foto_resmi'])
    $nama_foto_session = isset($_SESSION['foto_resmi']) ? $_SESSION['foto_resmi'] : '';
    
    if (!empty($nama_foto_session) && file_exists($prefix . "assets/img/pengguna/" . $nama_foto_session)) {
        $avatar_tampil = $prefix . "assets/img/pengguna/" . $nama_foto_session;
    }
}
// ============================================================================
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<nav class="navbar-sis">
    <div class="safe-container d-flex align-items-center justify-content-between px-3">
        <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="<?= $logo_link; ?>">
            SIS <span class="brand-sub">Sixseven Inventory System</span>
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['id_user'])) : ?>
                
                <div class="dropdown">
                    <div class="nav-link-custom text-white fw-light dropdown-toggle d-flex align-items-center gap-2" 
                          id="dropdownUserMenu" 
                          data-bs-toggle="dropdown" 
                          aria-expanded="false" 
                          style="cursor: pointer; user-select: none;">
                        <img src="<?= $avatar_tampil; ?>" alt="User" class="rounded-circle border border-white" style="width: 32px; height: 32px; object-fit: cover;">
                        <span>Hai, <strong class="fw-bold"><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong></span>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUserMenu" style="border-radius: 10px;">
                        <?php if (strpos($_SESSION['role'], 'admin') !== false) : ?>
                            <?php $target_dash = ($current_dir === 'admin') ? 'admin_user.php' : $prefix . 'admin/admin_user.php'; ?>
                            <li><a class="dropdown-item py-2 fw-bold" href="<?= $target_dash; ?>" style="color: var(--tosca-tua); font-size: 14px;">🖥️ Panel Kontrol Admin</a></li>
                            <li><hr class="dropdown-divider m-1"></li>
                        <?php endif; ?>
                        
                        <li>
                            <a class="dropdown-item py-2 text-danger fw-bold" href="javascript:void(0);" onclick="swalLogout('<?= $prefix; ?>')">
                                🚪 Keluar / Logout
                            </a>
                        </li>
                    </ul>
                </div>

            <?php else : ?>
                <a href="<?= $prefix; ?>register.php" class="nav-link-custom">Daftar</a>
                <a href="<?= $prefix; ?>login.php" class="nav-link-custom">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<script>
function swalLogout(prefixPath) {
    Swal.fire({
        title: 'Yakin Ingin Keluar?',
        text: 'Anda akan keluar dari sesi sistem inventory SIS.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545', // Merah tegas untuk aksi keluar
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Ya, Keluar!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = prefixPath + 'logout.php';
        }
    });
}
</script>