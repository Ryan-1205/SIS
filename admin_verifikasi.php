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
    <title>Admin - Verifikasi Peminjaman</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=1.6">
    <style>
        /* Menggunakan style yang sama dengan rancangan admin kamu sebelumnya */
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
            gap: 20px;
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
        .btn-verif {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 6px 20px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        .btn-verif:hover {
            background-color: #218838;
            color: white;
        }
        .badge-waiting {
            background-color: #ffc107;
            color: #212529;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 14px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    
    <?php include 'header.php'; ?>
    
    <div class="safe-container px-3">
        
        <?php include 'sub_header_admin.php'; ?>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA PEMINJAM</th>
                        <th>BARANG</th>
                        <th>TANGGAL PINJAM</th>
                        <th>STATUS</th>
                        <th>AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Query mengambil data peminjaman yang statusnya masih 'menunggu'
                    // Sesuaikan nama tabel & kolom dengan database asli kamu ya Ry
                    $query = "SELECT peminjaman.*, user.nama_lengkap, barang.nama_barang 
                              FROM peminjaman 
                              JOIN user ON peminjaman.id_user = user.id_user 
                              JOIN barang ON peminjaman.id_barang = barang.id_barang 
                              WHERE peminjaman.status = 'menunggu'";

                    if ($search != '') {
                        $query .= " AND (user.nama_lengkap LIKE '%$search%' OR barang.nama_barang LIKE '%$search%')";
                    }
                    
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                    ?>
                    <tr>
                        <td class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= htmlspecialchars($row['nama_barang']) ?></td>
                        <td><?= date('d M Y', strtotime($row['tanggal_pinjam'])) ?></td>
                        <td><span class="badge-waiting">Menunggu</span></td>
                        <td>
                            <a href="admin_verifikasi_proses.php?id=<?= $row['id_peminjaman'] ?>&aksi=setuju" 
                               class="btn-verif" 
                               onclick="return confirm('Setujui peminjaman barang ini?')">
                               Setujui
                            </a>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='5' style='padding: 30px;'>Tidak ada antrean peminjaman yang perlu diverifikasi.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</body>
</html>