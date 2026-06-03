<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Data User</title>
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
            <a href="data_user.php" class="nav-item-custom active">Data User</a>
            <a href="data_barang.php" class="nav-item-custom">Data Barang</a>
            <a href="data_peminjam.php" class="nav-item-custom">Data Peminjam</a>
        </section>

        <div class="search-container">
            <input type="text" id="cariUser" class="search-input" placeholder="Tuliskan Nama/NIS/Angkatan">
            <button type="button" id="btnCariUser" class="search-btn">Cari</button>
        </div>

        <div class="table-container">
            <table class="table table-sis">
                <thead>
                    <tr>
                        <th style="width: 50%;">Nama</th>
                        <th style="width: 50%;">NIS</th>
                    </tr>
                </thead>
                <tbody id="bodyTabelUser">
                    <tr>
                        <td>Admin</td>
                        <td>-</td>
                    </tr>
                    <tr>
                        <td>Ahmad</td>
                        <td>00223344</td>
                    </tr>
                    <tr>
                        <td>Zikri</td>
                        <td>55667788</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>