<?php
// Memastikan session aktif di komponen header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Logika menentukan arah link logo SIS berdasarkan role login
$logo_link = "index.php"; // Default jika belum login atau login sebagai siswa
if (isset($_SESSION['id_user']) && $_SESSION['role'] == 'admin') {
    $logo_link = "admin_barang.php"; // Jika yang login adalah admin
}
?>
<nav class="navbar-sis">
    <div class="safe-container d-flex align-items-center justify-content-between px-3">
        <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="<?= $logo_link; ?>">
            SIS <span class="brand-sub">Sixseven Inventory System</span>
        </a>
        <div class="d-flex align-items-center">
            <?php if (isset($_SESSION['id_user'])) : ?>
                
                <div class="dropdown">
                    <span class="nav-link-custom text-white fw-light dropdown-toggle" 
                          id="dropdownUserMenu" 
                          data-bs-toggle="dropdown" 
                          aria-expanded="false" 
                          style="cursor: pointer; user-select: none;">
                        Hai, <strong class="fw-bold"><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="dropdownUserMenu" style="border-radius: 10px;">
                        <?php if ($_SESSION['role'] == 'admin') : ?>
                            <li><a class="dropdown-item py-2 fw-bold" href="admin_barang.php" style="color: var(--tosca-tua); font-size: 14px;">🖥️ Data Barang</a></li>
                            <li><hr class="dropdown-divider m-1"></li>
                        <?php endif; ?>
                        
                        <li>
                            <a class="dropdown-item py-2 text-danger fw-bold" href="logout.php" 
                               onclick="return confirm('Yakin ingin keluar dari sistem SIS?')">
                                🚪 Keluar / Logout
                            </a>
                        </li>
                    </ul>
                </div>

            <?php else : ?>
                <a href="register.php" class="nav-link-custom">Daftar</a>
                <a href="login.php" class="nav-link-custom">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>