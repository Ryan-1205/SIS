<?php 
session_start();
include 'koneksi.php';

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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="safe-container px-3">
       <?php include 'sub_header_admin.php'; ?>

        <div class="admin-search-container">
            <form action="" method="GET" class="admin-search-form">
                <input type="text" name="search" placeholder="Tuliskan Nama/NIS/Angkatan" value="<?= $search ?>">
                <button type="submit">Cari</button>
            </form>
        </div>

        <div class="admin-table-wrapper" style="max-width: 600px;"> <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>NIS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $query = "SELECT * FROM users WHERE role = 'siswa'";
                    if ($search != '') {
                        $query .= " AND (nama_lengkap LIKE '%$search%' OR username LIKE '%$search%')";
                    }
                    $sql = mysqli_query($conn, $query);

                    // Hardcode Admin row sesuai mockup (opsional)
                    echo "<tr><td>Admin</td><td>-</td></tr>";

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                    ?>
                    <tr>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $row['username'] ?></td>
                    </tr>
                    <?php 
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>