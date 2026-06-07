<?php
// Memastikan session aktif untuk menarik informasi dinamis jika diperlukan
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menentukan prefix folder seperti komponen header sebelumnya
$current_dir_footer = basename(dirname($_SERVER['PHP_SELF']));
$prefix_footer = ($current_dir_footer === 'admin') ? '../' : '';
?>

<footer class="footer-professional mt-5">
    <div class="footer-top-line"></div>
    <div class="safe-container py-5 px-4">
        <div class="row gy-4 justify-content-between">
            
            <div class="col-lg-4 col-md-12">
                <h5 class="footer-brand mb-3">SIS <span class="brand-sub-footer">| Sixseven Inventory System</span></h5>
                <p class="footer-desc mb-0">
                    Sistem otomasi manajemen, pelacakan, dan verifikasi sirkulasi aset inventaris laboratorium berbasis biometrik resmi untuk efisiensi ekosistem akademis.
                </p>
            </div>
            
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-section-title mb-3">Tim Pengembang</h6>
                <ul class="list-unstyled footer-developer-list m-0 p-0">
                    <li>💻 <strong>Ryan Ardiansyah</strong> <span class="dev-nim">(2407431002)</span></li>
                    <li>💻 <strong>Bimo Refka A. S.</strong> <span class="dev-nim">(2407431004)</span></li>
                    <li>💻 <strong>Fikry Aries Ariansyah</strong> <span class="dev-nim">(2407431026)</span></li>
                </ul>
                <div class="mt-2 text-info-kampus">
                    PNJ • TIK • Teknik Multimedia Digital
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4">
                <h6 class="footer-section-title mb-3">Status Autentikasi</h6>
                <div class="footer-status-card p-3 rounded-3">
                    <?php if (isset($_SESSION['id_user'])) : ?>
                        <span class="d-block small text-white-50">Pengguna Aktif:</span>
                        <strong class="d-block text-white mb-2"><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>
                        <span class="badge-session uppercase-text"><?= str_replace('_', ' ', $_SESSION['role']); ?></span>
                    <?php else : ?>
                        <div class="d-flex align-items-center gap-2 text-warning small">
                            <span>⚠️</span> <span>Sesi Terbuka Anonim (Belum Masuk)</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
        
        <hr class="footer-divider my-4">
        
        <div class="d-flex flex-md-row flex-column justify-content-between align-items-center gap-2">
            <p class="footer-copyright mb-0">© 2026 <strong>SIS Project</strong> </p>
            <p class="footer-version mb-0">Versi Sistem Aplikasi 1.0</p>
        </div>
    </div>
</footer>

<style>
    /* Desain Dasar Footer Profesional */
    .footer-professional {
        background-color: #113834; /* Warna tosca super tua/gelap untuk kesan elegan */
        color: #e2f1ee;
        font-family: 'Poppins', sans-serif;
        position: relative;
    }

    /* Garis Aksen Tosca Terang di Atas */
    .footer-top-line {
        height: 4px;
        background-color: var(--tosca-tua, #1d5c56);
        width: 100%;
    }

    /* Tipografi Komponen */
    .footer-brand {
        color: #ffffff;
        font-weight: 800;
        font-size: 22px;
        letter-spacing: 0.5px;
    }
    .brand-sub-footer {
        font-size: 14px;
        font-weight: 300;
        color: #a4d4cc;
    }
    .footer-desc {
        font-size: 13px;
        line-height: 1.6;
        color: #b0cfcb;
    }

    .footer-section-title {
        color: #ffffff;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* Link Navigasi Footer */
    .footer-links li {
        margin-bottom: 10px;
    }
    .footer-links a {
        color: #b0cfcb;
        text-decoration: none;
        font-size: 13px;
        font-weight: 400;
        transition: all 0.2s ease;
        display: inline-block;
    }
    .footer-links a:hover {
        color: #ffffff;
        transform: translateX(4px); /* Efek bergeser halus saat hover */
    }

    /* Styling Bagian Developer Info */
    .footer-developer-list li {
        font-size: 13px;
        color: #ffffff;
        margin-bottom: 8px;
    }
    .dev-nim {
        color: #8dafa9;
        font-size: 12px;
        font-family: monospace;
    }
    .text-info-kampus {
        font-size: 11px;
        font-weight: 600;
        color: #a4d4cc;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 8px;
        display: inline-block;
    }

    /* Kotak Status Akses */
    .footer-status-card {
        background-color: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
    }
    .badge-session {
        background-color: #1a5f57;
        color: #ffffff;
        font-size: 10px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 50px;
        display: inline-block;
        letter-spacing: 0.5px;
    }
    .uppercase-text {
        text-transform: uppercase;
    }

    /* Bagian Hak Cipta Penutup */
    .footer-divider {
        border-color: rgba(255, 255, 255, 0.1);
    }
    .footer-copyright, .footer-version {
        font-size: 12px;
        color: #8dafa9;
    }
</style>