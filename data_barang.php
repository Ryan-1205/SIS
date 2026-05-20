<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Data Barang</title>
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
            <a href="data_peminjam.php" class="back-circle-btn">&larr;</a>
            <a href="data_user.php" class="nav-item-custom">Data User</a>
            <a href="data_barang.php" class="nav-item-custom active">Data Barang</a>
            <a href="data_peminjam.php" class="nav-item-custom">Data Peminjam</a>
        </section>

        <div class="search-container">
            <input type="text" id="cariBarang" class="search-input" placeholder="Tuliskan Kode Barang/Nama Barang/Deskripsi/Kondisi Barang">
            <button type="button" id="btnCariBarang" class="search-btn">Cari</button>
        </div>

        <div class="table-container">
            <table class="table table-sis">
                <thead>
                    <tr>
                        <th style="width: 15%;">Kode Barang</th>
                        <th style="width: 25%;">Nama Barang</th>
                        <th style="width: 25%;">Deskripsi</th>
                        <th style="width: 20%;">Kondisi Barang</th>
                        <th style="width: 15%;">Foto</th>
                    </tr>
                </thead>
                <tbody id="bodyTabelBarang">
                    <tr>
                        <td>01</td>
                        <td>Kamera Canon 90D</td>
                        <td>Lensa 135</td>
                        <td>Rusak</td>
                        <td><span class="btn-foto-plus">+</span></td>
                    </tr>
                    <tr>
                        <td>02</td>
                        <td>Tripod Takara</td>
                        <td>2026</td>
                        <td>Baik</td>
                        <td><span class="btn-foto-plus">+</span></td>
                    </tr>
                    <tr>
                        <td>03</td>
                        <td>Mic Boya</td>
                        <td>2026</td>
                        <td>Baik</td>
                        <td><span class="btn-foto-plus">+</span></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="action-footer-container">
            <button type="button" id="btnTambah" class="btn-sis-action">Tambah</button>
            <button type="button" id="btnEdit" class="btn-sis-action">Edit</button>
        </div>
    </div>

</body>
</html>