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
    <title>Admin - Data Peminjam</title>
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

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>TANGGAL PEMINJAMAN</th>
                        <th>BARANG</th>
                        <th>STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    // Query join antara peminjaman dan users
                    $query = "SELECT p.*, u.nama_lengkap FROM peminjaman p JOIN users u ON p.id_user = u.id_user";
                    if ($search != '') {
                        $query .= " WHERE u.nama_lengkap LIKE '%$search%' OR p.tgl_pinjam LIKE '%$search%'";
                    }
                    $query .= " ORDER BY p.id_pinjam DESC";
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            // Logika status warna
                            $status_text = ($row['status_pengajuan'] == 'kembali') ? 'Sudah Kembali' : 'Belum Kembali';
                            $status_class = ($row['status_pengajuan'] == 'kembali') ? 'text-hijau' : 'text-merah';
                            
                            // Format tanggal (opsional)
                            $tgl = date('d-m-Y', strtotime($row['tgl_pinjam']));
                    ?>
                    <tr>
                        <td><?= $row['nama_lengkap'] ?></td>
                        <td><?= $tgl ?></td>
                        <td><a href="admin_peminjam_detail.php?id=<?= $row['id_pinjam'] ?>" class="text-decoration-none" style="color: var(--tosca-tua); font-weight: 600;">Detail</a></td>
                        <td class="<?= $status_class ?>"><?= $status_text ?></td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='4'>Belum ada data peminjaman.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>