<?php 
session_start();
include 'koneksi.php';

// Validasi akses admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Data Barang</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.5">
    <style>
        /* CSS internal bawaan rancangan tabel admin kamu */
        .admin-header {
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            margin: 50px 0 40px 0;
        }
        .admin-back-btn {
            position: absolute;
            left: 0;
            color: var(--tosca-tua);
        }
        .admin-tabs {
            display: flex;
            gap: 20px; /* Dipersempit sedikit agar 4 tab muat sebaris */
            font-weight: 700;
            font-size: 20px;
        }
        .admin-tab {
            text-decoration: none;
            color: var(--tosca-tua);
            padding: 8px 25px;
            border-radius: 50px;
            transition: 0.3s;
        }
        .admin-tab.active {
            background-color: var(--tosca-muda);
        }
        .admin-search-container {
            max-width: 1000px;
            margin: 0 auto 30px auto;
        }
        .admin-search-form {
            display: flex;
            border: 2px solid var(--tosca-tua);
            border-radius: 50px;
            padding: 4px;
            background: white;
        }
        .admin-search-form input {
            flex-grow: 1;
            border: none;
            padding: 10px 25px;
            outline: none;
            color: var(--tosca-tua);
            font-size: 16px;
            background: transparent;
        }
        .admin-search-form input::placeholder {
            color: #6c8a87;
        }
        .admin-search-form button {
            background-color: var(--tosca-tua);
            color: white;
            border: none;
            padding: 8px 50px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 18px;
            cursor: pointer;
        }
        .admin-table-wrapper {
            max-width: 1000px;
            margin: 0 auto 50px auto;
            border: 2px solid var(--tosca-tua);
            background: white;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .admin-table th {
            color: var(--tosca-tua);
            font-weight: 700;
            padding: 20px 15px;
            border-bottom: 2px solid var(--tosca-tua);
            font-size: 16px;
        }
        .admin-table td {
            padding: 20px 15px;
            border-bottom: 1px solid var(--tosca-tua);
            color: #333;
            font-size: 16px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        .btn-admin-action {
            background-color: var(--tosca-tua);
            color: white;
            text-decoration: none;
            padding: 10px 45px;
            border-radius: 30px;
            font-weight: 700;
            font-size: 18px;
            border: none;
            cursor: pointer;
            display: inline-block;
            text-align: center;
        }
        .btn-admin-action:hover {
            opacity: 0.9;
            color: white;
        }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3">
        
        <?php include 'sub_header_admin.php'; ?>
        
    <div class="admin-search-container">
        <form action="" method="GET" class="admin-search-form">
            <input type="text" name="search" placeholder="Tuliskan kata kunci pencarian data master..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Cari</button>
        </form>
    </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>KODE BARANG</th>
                        <th>NAMA BARANG</th>
                        <th>DESKRIPSI</th>
                        <th>KONDISI BARANG</th>
                        <th>FOTO</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM barang";
                    if ($search != '') {
                        $query .= " WHERE id_barang LIKE '%$search%' OR nama_barang LIKE '%$search%' OR deskripsi LIKE '%$search%' OR status LIKE '%$search%'";
                    }
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $kondisi = ($row['status'] == 'perbaikan') ? 'Rusak' : 'Baik';
                    ?>
                    <tr>
                        <td><?= sprintf("%02d", $row['id_barang']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= htmlspecialchars($row['deskripsi']) ?></td>
                        <td><?= $kondisi ?></td>
                        <td>+</td> 
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px;'>Tidak ada data barang.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-end gap-3" style="max-width: 1000px; margin: 0 auto 50px auto;">
            <a href="admin_barang_tambah.php" class="btn-admin-action">Tambah</a>
            <a href="#" class="btn-admin-action">Edit</a>
        </div>
    </div>
</body>
</html>