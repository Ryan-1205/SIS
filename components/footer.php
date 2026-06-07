<?php
// Memastikan session aktif jika suatu saat dibutuhkan
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
        <div class="row gy-4 justify-content-between align-items-start">
            
            <div class="col-lg-4 col-md-12">
                <h5 class="footer-brand mb-3">SIS <span class="brand-sub-footer">| Sixseven Inventory System</span></h5>
                <p class="footer-desc mb-0">
                    Sistem otomasi manajemen, pelacakan, dan verifikasi sirkulasi aset inventaris laboratorium berbasis biometrik resmi untuk efisiensi ekosistem akademis.
                </p>
            </div>
            
            <div class="col-lg-3 col-md-6">
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
            
            <div class="col-lg-4 col-md-6">
                <h6 class="footer-section-title mb-3">Lokasi</h6>
                <div class="map-responsive-wrapper">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1379.6901637238436!2d106.85526126212754!3d-6.346292510338262!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69edd7b67bf0c9%3A0x8e78091a50d05efd!2sProfessional%20School%20State%2067%20of%20Jakarta!5e0!3m2!1sen!2sid!4v1780459272994!5m2!1sen!2sid%22" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
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

    /* 🔥 CSS MAPS RESPONSIF SEJAJAR DAN RAPI */
    .map-responsive-wrapper {
        width: 100%;
        height: 115px; /* Tinggi disesuaikan agar simetris dengan boks tengah */
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid rgba(164, 212, 204, 0.15);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transition: border-color 0.3s ease;
    }
    .map-responsive-wrapper:hover {
        border-color: rgba(164, 212, 204, 0.4);
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