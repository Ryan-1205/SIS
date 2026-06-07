<?php
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

// Menangkap data pengawas dan array status keputusan per item barang
if (isset($_POST['pengawas']) && isset($_POST['status_item'])) {
    $pengawas    = mysqli_real_escape_string($conn, $_POST['pengawas']);
    $status_item = $_POST['status_item']; // Berisi array [id_pinjam => 'disetujui'/'ditolak']

    $sukses_count = 0;
    $gagal_count  = 0;

    // 🔥 Proteksi Integritas Data: Mulai transaksi database
    mysqli_begin_transaction($conn);

    try {
        // LOOPING KEPUTUSAN BARANG SELEKTIF
        foreach ($status_item as $id_pinjam => $keputusan) {
            $id_pinjam = mysqli_real_escape_string($conn, $id_pinjam);
            $keputusan = mysqli_real_escape_string($conn, $keputusan);

            if ($keputusan === 'disetujui') {
                // 1. Ambil id_barang terlebih dahulu dari record peminjaman terkait
                $query_cari_barang = mysqli_query($conn, "SELECT id_barang FROM peminjaman WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending'");
                $data_barang = mysqli_fetch_assoc($query_cari_barang);

                if ($data_barang) {
                    $id_barang_terkait = $data_barang['id_barang'];

                    // 2. JALUR PROTEKSI CONCURRENCY: Cek realtime apakah barang ini sudah terlanjur berstatus 'dipinjam'
                    $cek_status_barang = mysqli_query($conn, "SELECT id_barang FROM barang WHERE id_barang = '$id_barang_terkait' AND status = 'dipinjam'");
                    if (mysqli_num_rows($cek_status_barang) > 0) {
                        // Jika sudah 'dipinjam', otomatis paksa pengajuan ini menjadi 'ditolak' dan isi catatan ke kolom keperluan
                        mysqli_query($conn, "UPDATE peminjaman 
                                             SET status_pengajuan = 'ditolak', 
                                                 diverifikasi_oleh = '$pengawas',
                                                 keperluan = 'Otomatis Ditolak: Barang sudah dibawa peminjam lain.' 
                                             WHERE id_pinjam = '$id_pinjam'");
                        $sukses_count++;
                        continue; // Langsung lompat ke baris iterasi item barang berikutnya
                    }

                    // 3. Jalankan kueri update status transaksi utama menjadi 'disetujui'
                    // Menggunakan kolom 'diverifikasi_oleh' sesuai database asli lu
                    $query_update = "UPDATE peminjaman 
                                     SET status_pengajuan = 'disetujui', diverifikasi_oleh = '$pengawas' 
                                     WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending'";
                    
                    if (mysqli_query($conn, $query_update)) {
                        $sukses_count++;

                        // 4. SINKRONISASI LOGISTIK: Ubah status barang menjadi 'dipinjam'
                        mysqli_query($conn, "UPDATE barang SET status = 'dipinjam' WHERE id_barang = '$id_barang_terkait'");

                        // 5. 🔥 LOGIKA AUTO-REJECT DOUBLE BOOKING: 
                        // Otomatis ubah semua status pengajuan pending lain dengan barang yang sama menjadi 'ditolak'
                        mysqli_query($conn, "UPDATE peminjaman 
                                             SET status_pengajuan = 'ditolak', 
                                                 diverifikasi_oleh = '$pengawas',
                                                 keperluan = 'Otomatis Ditolak: Barang sudah disetujui untuk peminjam lain.'
                                             WHERE id_barang = '$id_barang_terkait' 
                                               AND status_pengajuan = 'pending' 
                                               AND id_pinjam != '$id_pinjam'");
                    } else {
                        $gagal_count++;
                    }
                } else {
                    $gagal_count++;
                }
            } else {
                // 6. Jalankan kueri update jika admin memilih opsi 'ditolak' secara manual sejak awal
                $query_update_tolak = "UPDATE peminjaman 
                                       SET status_pengajuan = 'ditolak', diverifikasi_oleh = '$pengawas' 
                                       WHERE id_pinjam = '$id_pinjam' AND status_pengajuan = 'pending'";
                if (mysqli_query($conn, $query_update_tolak)) {
                    $sukses_count++;
                } else {
                    $gagal_count++;
                }
            }
        }

        // Jika semua perulangan query sukses dieksekusi, komit data secara permanen
        mysqli_commit($conn);

    } catch (Exception $e) {
        // Jika ada kegagalan tak terduga, batalkan semua perubahan data (Rollback)
        mysqli_rollback($conn);
        $gagal_count++;
    }

    // Tampilkan notifikasi umpan balik hasil eksekusi admin
    if ($gagal_count == 0) {
        header("Location: admin_verifikasi_peminjaman.php?status=sukses_verif&pengawas=" . urlencode($pengawas));
        exit;
    } else {
        header("Location: admin_verifikasi_peminjaman.php?status=parsial_verif&sukses=$sukses_count&gagal=$gagal_count");
        exit;
    }

} else {
    header("Location: admin_verifikasi_peminjaman.php");
    exit;
}
?>