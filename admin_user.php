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
    <title>Admin - Data User</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.6">
    <style>
        /* Memperbaiki alignment tabel agar rapi */
        .admin-table th, .admin-table td {
            text-align: center;
        }
        .badge-role {
            padding: 4px 12px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 600;
        }
        .badge-admin { background-color: var(--tosca-muda); color: var(--tosca-tua); }
        .badge-siswa { background-color: #e2f1ee; color: #1e6f65; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="safe-container px-3 pb-5">
       <?php include 'sub_header_admin.php'; ?>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tuliskan Nama / NIS Pengguna..." value="<?= htmlspecialchars($search) ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper" style="max-width: 750px; margin: 0 auto;"> 
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>NAMA LENGKAP</th>
                        <th>NOMOR INDUK (NIS)</th>
                        <th>HAK AKSES / ROLE</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Menampilkan semua user (Admin & Siswa) secara dinamis dari DB
                    $query = "SELECT * FROM users WHERE 1=1";
                    
                    if ($search != '') {
                        $query .= " AND (nama_lengkap LIKE '%$search%' OR nis LIKE '%$search%')";
                    }
                    
                    $query .= " ORDER BY role ASC, nama_lengkap ASC";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            if ($row['role'] == 'admin') {
                                $role_badge = '<span class="badge-role badge-admin">Admin Lab</span>';
                                $display_nis = '-'; 
                            } else {
                                $role_badge = '<span class="badge-role badge-siswa">Siswa</span>';
                                $display_nis = htmlspecialchars($row['nis']);
                            }
                    ?>
                    <tr>
                        <td class="fw-bold text-start" style="padding-left: 30px;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        <td><?= $display_nis ?></td>
                        <td><?= $role_badge ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='3' style='padding: 30px;' class='text-muted'>Data pengguna tidak ditemukan.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>