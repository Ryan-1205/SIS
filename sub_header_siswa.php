<?php
// Ambil nama file halaman yang sedang aktif saat ini
$current_page = basename($_SERVER['PHP_SELF']);
$id_kat_sub = isset($_GET['kat']) ? $_GET['kat'] : 1;
$search_sub = isset($_GET['search']) ? $_GET['search'] : '';
$global_sub = isset($_GET['global']) ? $_GET['global'] : 'false';
?>

<div class="safe-container">
    <div class="search-section px-3">
        
        <?php if ($current_page == 'index.php') : ?>
            <h4 class="search-title">Cari Barang</h4>
        <?php else : ?>
            <a href="<?= ($global_sub == 'true') ? 'index.php' : 'list_barang.php?kat='.$id_kat_sub; ?>" class="back-btn" style="margin-right: 15px;">
                <svg width="45" height="45" viewBox="0 0 512 512" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M256 48C141.1 48 48 141.1 48 256s93.1 208 208 208 208-93.1 208-208S370.9 48 256 48zm43.4 289.1c7.5 7.5 7.5 19.8 0 27.3s-19.8 7.5-27.3 0L165.4 257.7c-7.5-7.5-7.5-19.8 0-27.3L272.1 123.7c7.5-7.5 19.8-7.5 27.3 0s7.5 19.8 0 27.3L206.3 244h163.4c10.6 0 19.2 8.6 19.2 19.2s-8.6 19.2-19.2 19.2H206.3l93.1 94.7z" fill="#1d5c56"/>
                </svg>
            </a>
        <?php endif; ?>

        <form action="list_barang.php" method="GET" class="search-bar" id="searchForm" style="flex-grow: 1;">
            <?php if ($current_page == 'index.php' || $global_sub == 'true') : ?>
                <input type="hidden" name="global" value="true">
            <?php else : ?>
                <input type="hidden" name="kat" value="<?= htmlspecialchars($id_kat_sub); ?>">
            <?php endif; ?>
            
            <input type="text" name="search" id="searchInput" placeholder="Nama Barang" value="<?= htmlspecialchars($search_sub); ?>" autocomplete="off">
            <button type="submit" style="background: transparent; border: none; position: absolute; right: 20px; top: 12px; color: var(--tosca-tua);">
                🔍
            </button>
        </form>

        <div class="action-icons">
            <a href="list_pinjam.php" class="action-item text-decoration-none" style="color: inherit;">
                <span>📦</span> Pinjam Barang
            </a>
            <a href="list_kembali.php" class="action-item text-decoration-none" style="color: inherit;">
                <span>🔄</span> Kembalikan
            </a>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const searchInput = document.getElementById('searchInput');
    const searchForm = document.getElementById('searchForm');

    if (searchInput && searchForm) {
        searchInput.addEventListener('input', function() {
            // Jika kolom input dihapus sampai bersih kosong
            if (this.value.trim() === '') {
                
                // Ambil data parameter URL saat ini
                const urlParams = new URLSearchParams(window.location.search);
                const isGlobal = urlParams.get('global');
                const idKat = urlParams.get('kat');
                const currentPageFile = "<?= $current_page; ?>";

                // PENGGANTIAN STRATEGI: Menggunakan location.replace agar histori pengetikan tidak menumpuk bug
                if (currentPageFile === 'index.php' || isGlobal === 'true') {
                    window.location.replace('index.php');
                } else if (idKat) {
                    window.location.replace('list_barang.php?kat=' + idKat);
                } else {
                    window.location.replace('index.php');
                }

            } else {
                // Jalankan live search otomatis jika masih ada karakter huruf
                searchForm.submit();
            }
        });

        // Mempertahankan posisi kursor ketikan di akhir kata
        if (searchInput.value !== '') {
            const val = searchInput.value;
            searchInput.value = '';
            searchInput.focus();
            searchInput.value = val;
        }
    }
});
</script>