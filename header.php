<?php
// Memastikan session aktif di komponen header
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<nav class="navbar-sis">
    <div class="safe-container d-flex align-items-center justify-content-between px-3">
        <a class="navbar-brand-sis text-white text-decoration-none fw-bold fs-4" href="index.php">
            SIS <span class="brand-sub">Sixseven Inventory System</span>
        </a>
        <div class="d-flex">
            <?php if (isset($_SESSION['login'])) : ?>
                <span class="nav-link-custom text-white fw-light me-3">
                    Hai, <strong class="fw-bold"><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                </span>
                <a href="logout.php" class="nav-link-custom text-warning" onclick="return confirm('Yakin ingin keluar dari sistem?')">Keluar</a>
            <?php else : ?>
                <a href="register.php" class="nav-link-custom">Daftar</a>
                <a href="login.php" class="nav-link-custom">Masuk</a>
            <?php endif; ?>
        </div>
    </div>
</nav>