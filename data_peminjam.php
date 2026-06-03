<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Sixseven Inventory System</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="sis-header">
        <div class="sis-logo">SIS</div>
        <div class="sis-title">Sixseven Inventory System</div>
    </header>

    <div class="container-fluid">
        
        <section class="nav-section">
            <a href="data_user.php" class="back-circle-btn">&larr;</a>
            <a href="data_user.php" class="nav-item-custom">Data User</a>
            <a href="data_barang.php" class="nav-item-custom">Data Barang</a>
            <a href="data_peminjam.php" class="nav-item-custom active">Data Peminjam</a>
        </section>

        <div class="search-container">
            <input type="text" id="cariPeminjam" class="search-input" placeholder="Tuliskan Nama/NIS/Angkatan">
            <button type="button" id="btnCari" class="search-btn">Cari</button>
        </div>

        <div class="table-container">
            <table class="table table-sis">
                <thead>
                    <tr>
                        <th style="width: 25%;">Nama</th>
                        <th style="width: 30%;">Tanggal Peminjaman</th>
                        <th style="width: 20%;">Barang</th>
                        <th style="width: 25%;">Status</th>
                    </tr>
                </thead>
                <tbody id="bodyTabelPeminjam">
                    <tr>
                        <td>Zainab</td>
                        <td>32-33-2006</td>
                        <td><span class="link-detail">Detail</span></td>
                        <td><span class="status-belum">Belum Kembali</span></td>
                    </tr>
                    <tr>
                        <td>Ahmad</td>
                        <td>32-33-2006</td>
                        <td><span class="link-detail">Detail</span></td>
                        <td><span class="status-sudah">Sudah Kembali</span></td>
                    </tr>
                    <tr>
                        <td>Zikri</td>
                        <td>32-33-2006</td>
                        <td><span class="link-detail">Detail</span></td>
                        <td><span class="status-sudah">Sudah Kembali</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button type="button" id="btnReload" class="btn-reload">Reload</button>
    </div>

</body>
</html>