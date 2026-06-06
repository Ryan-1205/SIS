<?php
// Ambil nama file halaman yang sedang aktif saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$id_kat_sub = isset($_GET['kat']) ? $_GET['kat'] : 1;
$search_sub = isset($_GET['search']) ? $_GET['search'] : '';
$global_sub = isset($_GET['global']) ? $_GET['global'] : 'false';

// SINKRONISASI JALUR DIREKTORI: Deteksi otomatis posisi file yang memanggil komponen ini
$current_dir_sub = basename(dirname($_SERVER['PHP_SELF']));

// Jika pemanggil ada di dalam folder siswa, prefix untuk ke folder root adalah '../'
$prefix_sub = ($current_dir_sub === 'siswa') ? '../' : '';

// SINKRONISASI TARGET NAVIGASI INTERNAL SISWA
// Jika dipanggil dari dalam folder siswa, akses langsung filenya. Jika dari root, tambahkan folder siswa/
$path_siswa_sub = ($current_dir_sub === 'siswa') ? '' : 'siswa/';
?>

<div class="safe-container">
    <div class="search-section px-3" style="display: flex; align-items: center;">
        
        <?php if ($current_page == 'index.php') : ?>
            <h4 class="search-title" style="margin: 0; white-space: nowrap; font-weight: 700; color: var(--tosca-tua);">Cari Barang</h4>
        <?php else : ?>
            <a href="<?= $prefix_sub; ?>index.php" class="back-to-home-btn" title="Kembali ke Halaman Utama">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"></line>
                    <polyline points="12 19 5 12 12 5"></polyline>
                </svg>
            </a>
        <?php endif; ?>

        <?php $action_form = ($current_dir_sub === 'siswa') ? 'list_barang.php' : 'siswa/list_barang.php'; ?>
        <form action="<?= $action_form; ?>" method="GET" class="search-bar" id="searchForm" style="flex-grow: 1; position: relative; z-index: 1;">
            <?php if ($current_page == 'index.php' || $global_sub == 'true') : ?>
                <input type="hidden" name="global" value="true">
            <?php else : ?>
                <input type="hidden" name="kat" value="<?= htmlspecialchars($id_kat_sub); ?>">
            <?php endif; ?>
            
            <input type="text" name="search" id="searchInput" placeholder="Nama Barang..." value="<?= htmlspecialchars($search_sub); ?>" autocomplete="off">
            <button type="submit" style="background: transparent; border: none; position: absolute; right: 20px; top: 12px; color: var(--tosca-tua); z-index: 2;">
                🔍
            </button>
        </form>

        <div class="action-icons">
            <a href="<?= $path_siswa_sub; ?>list_pinjam.php" class="action-item text-decoration-none" style="color: inherit;">
                <span>📦</span> Pinjam Barang
            </a>
            <a href="<?= $path_siswa_sub; ?>list_kembali.php" class="action-item text-decoration-none" style="color: inherit;">
                <span>🔄</span> Kembalikan
            </a>
        </div>
    </div>
</div>

<style>
    /* Styling Khusus Tombol Kembali Baru yang Profesional */
    .back-to-home-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 42px;
        height: 42px;
        background-color: #ffffff;
        color: #1d5c56; /* Warna hijau tosca tua */
        border: 2px solid #e2f1ee; /* Border tosca muda */
        border-radius: 50%; /* Lingkaran bulat sempurna */
        margin-right: 15px;
        position: relative;
        z-index: 999;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.25s ease-in-out;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    /* Efek Transisi Mikro saat Kursor Mouse Membidik Tombol */
    .back-to-home-btn:hover {
        color: #ffffff;
        background-color: #1d5c56; /* Pembalikan warna solid tosca tua */
        border-color: #1d5c56;
        transform: scale(1.05); /* Membesar secara halus */
        box-shadow: 0 4px 12px rgba(29, 92, 86, 0.2);
    }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');
    let timerPencarian = null;

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            if (timerPencarian) clearTimeout(timerPencarian);

            if (this.value.trim() === '') {
                const urlParams = new URLSearchParams(window.location.search);
                const isGlobal = urlParams.get('global');
                const idKat = urlParams.get('kat');
                const currentPageFile = "<?= $current_page; ?>";
                const prefixJS = "<?= $prefix_sub; ?>";
                const pathSiswaJS = "<?= $path_siswa_sub; ?>";

                // REVISI FIX JAVASCRIPT LOGIC: Reset pencarian kosong agar tidak memicu 404
                if (currentPageFile === 'index.php' || isGlobal === 'true') {
                    window.location.replace(prefixJS + 'index.php');
                } else if (idKat) {
                    window.location.replace(pathSiswaJS + 'list_barang.php?kat=' + idKat);
                } else {
                    window.location.replace(prefixJS + 'index.php');
                }
            } else {
                timerPencarian = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            }
        });

        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }
    }
});
</script>