<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Data User</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Mengatur variabel warna utama aplikasi (Teal/Toska) */
        :root {
            --main-teal: #1e6f65;
            --light-teal: #e2f1ee;
            --border-teal: #1e6f65;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #ffffff;
        }

        /* Header Style */
        .sis-header {
            background-color: var(--main-teal);
            color: white;
            padding: 15px 40px;
            display: flex;
            align-items: center;
        }

        .sis-logo {
            font-weight: 800;
            font-size: 24px;
            letter-spacing: 1px;
        }

        .sis-title {
            font-size: 14px;
            margin-left: 15px;
            opacity: 0.8;
            border-left: 2px solid white;
            padding-left: 15px;
        }

        /* Navigation Tabs - Dikalibrasi agar seirama dengan lebar elemen bawah */
        .nav-section {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 60px;
            margin-top: 40px;
            position: relative;
            max-width: 750px;
            margin-left: auto;
            margin-right: auto;
        }

        .nav-item-custom {
            font-size: 20px;
            font-weight: bold;
            color: var(--main-teal);
            text-decoration: none;
            padding: 5px 10px;
        }

        .nav-item-custom:hover {
            color: var(--main-teal);
            opacity: 0.8;
            text-decoration: none;
        }

        .nav-item-custom.active {
            background-color: var(--light-teal);
            border-radius: 20px;
            padding: 5px 25px;
        }

        /* Back Button Lingkaran - Presisi mengunci di sisi kiri area navigasi */
        .back-circle-btn {
            border: 2px solid #000;
            border-radius: 50%;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: black;
            font-size: 26px;
            font-weight: bold;
            text-decoration: none;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
        }

        .back-circle-btn:hover {
            background-color: #f0f0f0;
            color: black;
            text-decoration: none;
        }

        /* FIX KESEJAJARAN: Search Container dikunci 750px menggunakan border-box */
        .search-container {
            max-width: 750px;
            margin: 35px auto;
            position: relative;
            box-sizing: border-box;
        }

        .search-input {
            border: 2px solid var(--border-teal);
            border-radius: 25px;
            padding: 12px 25px;
            padding-right: 150px; /* Jarak aman ketikan teks dari tombol */
            width: 100%;
            font-size: 16px;
            outline: none;
            color: var(--main-teal);
            box-sizing: border-box;
        }

        .search-input::placeholder {
            color: #a0c2be;
        }

        /* FIX KESEJAJARAN: Tombol Cari dikunci di dalam border kanan input text */
        .search-btn {
            position: absolute;
            right: 4px;
            top: 4px;
            bottom: 4px; /* Tinggi otomatis presisi mengikuti input */
            background-color: var(--main-teal);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 0 45px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .search-btn:hover {
            background-color: #154d46;
        }

        /* Table Design - Menggunakan border tebal 3px khas sistem SIS lu */
        .table-container {
            max-width: 750px;
            margin: 0 auto;
            border: 3px solid var(--border-teal);
            border-radius: 12px;
            overflow: hidden;
            box-sizing: border-box;
        }

        .table-sis {
            margin-bottom: 0;
            width: 100%;
        }

        .table-sis th {
            color: var(--main-teal);
            text-align: center;
            font-weight: 800;
            border-bottom: 3px solid var(--border-teal) !important;
            border-top: none;
            text-transform: uppercase;
            font-size: 15px;
            background-color: #ffffff;
            padding: 15px;
        }

        .table-sis td {
            text-align: center;
            vertical-align: middle;
            color: #333;
            border-top: 1px solid #c9dfdc;
            padding: 15px;
            font-size: 15px;
        }
    </style>
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