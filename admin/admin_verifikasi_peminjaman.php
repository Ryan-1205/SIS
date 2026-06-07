<?php 
session_start();
// Penyesuaian Jalur: Mundur satu folder untuk memuat konfigurasi database
include '../koneksi.php';

// Validasi hak akses admin berdasarkan awalan kata 'admin_' pada role session
if (!isset($_SESSION['id_user']) || strpos($_SESSION['role'], 'admin') === false) {
    header("Location: ../login.php");
    exit;
}

$current_admin_role = $_SESSION['role'];
// Mengambil nama admin yang sedang login secara realtime dari session
$nama_admin_login = $_SESSION['nama_lengkap'];
$id_kategori_admin = isset($_SESSION['id_kategori']) ? $_SESSION['id_kategori'] : 1;

// Ambil nama kategori lab saat ini untuk judul dashboard admin
$query_nama_lab = mysqli_query($conn, "SELECT nama_kategori FROM kategori WHERE id_kategori = '$id_kategori_admin'");
$data_lab = mysqli_fetch_array($query_nama_lab);

if ($current_admin_role === 'admin') {
    $nama_lab_tampil = "Semua Laboratorium (Global)";
} else {
    $nama_lab_tampil = $data_lab ? $data_lab['nama_kategori'] : "Laboratorium";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin <?= $nama_lab_tampil; ?> - Verifikasi Peminjaman</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=2.6">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .admin-table-wrapper, .section-title-admin {
            max-width: 1000px !important;
            margin-left: auto;
            margin-right: auto;
        }
        .section-title-admin { color: var(--tosca-tua); font-weight: 700; }
        
        .admin-table-wrapper { margin-top: 30px; border: 2px solid var(--tosca-tua); background: white; border-radius: 10px; overflow: hidden; }
        .admin-table { width: 100%; border-collapse: collapse; text-align: center; }
        .admin-table th { color: white; background-color: var(--tosca-tua); font-weight: 700; padding: 20px 15px; font-size: 14px; }
        .admin-table td { padding: 20px 15px; border-bottom: 1px solid var(--tosca-muda); color: #333; font-size: 15px; vertical-align: middle; }
        .admin-table tr:last-child td { border-bottom: none; }

        .btn-detail-barang { background-color: #17a2b8; color: white; border: none; padding: 6px 15px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; cursor: pointer; }
        .btn-detail-barang:hover { background-color: #138496; color: white; }
        .btn-verif { background-color: var(--tosca-tua); color: white; border: none; padding: 6px 20px; border-radius: 20px; font-weight: 600; font-size: 13px; text-decoration: none; display: inline-block; cursor: pointer; }
        .btn-verif:hover { background-color: #14433e; color: white; }
        .badge-waiting { background-color: #ffc107; color: #212529; padding: 5px 12px; border-radius: 15px; font-size: 13px; font-weight: 600; display: inline-block; }

        /* Desain Thumbnail Foto Biometrik */
        .img-biometrik-thumb {
            width: 45px;
            height: 45px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid var(--tosca-tua);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .img-biometrik-thumb:hover {
            transform: scale(1.1);
        }
    </style>
</head>
<body>
    
    <?php include '../components/header.php'; ?>
    
    <div class="safe-container px-3 pb-5">
        <?php include '../components/sub_header_admin.php'; ?>

        <div class="mx-auto mt-4 mb-2" style="max-width: 1000px;">
            <h4 class="section-title-admin m-0">📥 Antrean Persetujuan Peminjaman: <?= htmlspecialchars($nama_lab_tampil); ?></h4>
        </div>

        <div class="admin-table-wrapper">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th class="py-3 px-3 text-start" style="padding-left: 20px !important;">NAMA PEMINJAM</th>
                        <th class="py-3 px-3 text-center">BIOMETRIK</th>
                        <th class="py-3 px-3 text-center" width="180">WAKTU PINJAM</th>
                        <th class="py-3 px-3 text-center" width="180">RENCANA KEMBALI</th> 
                        <th class="py-3 px-3 text-center">DETAIL BARANG</th>
                        <th class="py-3 px-3 text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $where_clause = ($current_admin_role === 'admin') ? "WHERE peminjaman.status_pengajuan = 'pending'" : "WHERE peminjaman.status_pengajuan = 'pending' AND barang.id_kategori = '$id_kategori_admin'";

                    // MODIFIKASI QUERY: Menarik data bukti_wajah untuk validasi visual admin
                    $query = "SELECT peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana, peminjaman.id_user, users.nama_lengkap, peminjaman.bukti_wajah,
                                     MIN(peminjaman.id_pinjam) as order_id,
                                     GROUP_CONCAT(barang.nama_barang ORDER BY peminjaman.id_pinjam ASC SEPARATOR '||') as list_barang,
                                     GROUP_CONCAT(peminjaman.id_pinjam ORDER BY peminjaman.id_pinjam ASC SEPARATOR ',') as list_id_pinjam,
                                     GROUP_CONCAT(IFNULL(peminjaman.no_hp, '-') ORDER BY peminjaman.id_pinjam ASC SEPARATOR '||') as list_no_hp,
                                     GROUP_CONCAT(IFNULL(peminjaman.keperluan, '-') ORDER BY peminjaman.id_pinjam ASC SEPARATOR '||') as list_keperluan
                              FROM peminjaman 
                              JOIN users ON peminjaman.id_user = users.id_user 
                              JOIN barang ON peminjaman.id_barang = barang.id_barang 
                              $where_clause
                              GROUP BY peminjaman.id_user, peminjaman.tgl_pinjam, peminjaman.tgl_kembali_rencana 
                              ORDER BY order_id ASC";
                              
                    $sql = mysqli_query($conn, $query);

                    if (mysqli_num_rows($sql) > 0) {
                        while($row = mysqli_fetch_assoc($sql)) {
                            $arr_no_hp = explode('||', $row['list_no_hp']);
                            $arr_keperluan = explode('||', $row['list_keperluan']);
                            
                            $no_hp_tampil = (!empty($arr_no_hp[0]) && $arr_no_hp[0] !== '-') ? $arr_no_hp[0] : 'Tidak Diisi Peminjam';
                            $keperluan_tampil = (!empty($arr_keperluan[0]) && $arr_keperluan[0] !== '-') ? $arr_keperluan[0] : 'Tidak ada deskripsi keperluan';
                            
                            // Penentuan file gambar biometrik wajah siswa
                            $foto_bukti = (!empty($row['bukti_wajah']) && file_exists("../assets/img/bukti_pinjam/" . $row['bukti_wajah'])) ? "../assets/img/bukti_pinjam/" . $row['bukti_wajah'] : "../assets/img/default_user.jpg";
                    ?>
                    <tr>
                        <td class="fw-bold text-dark px-3 text-start" style="padding-left: 20px !important;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                        
                        <td class="text-center">
                            <img src="<?= $foto_bukti; ?>" class="img-biometrik-thumb tombol-foto-popup" data-nama="<?= htmlspecialchars($row['nama_lengkap']) ?>" data-src="<?= $foto_bukti; ?>" alt="Wajah">
                        </td>

                        <td class="font-monospace text-secondary text-center" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($row['tgl_pinjam'])) ?> WIB</td>
                        <td class="font-monospace text-danger fw-bold text-center" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($row['tgl_kembali_rencana'])) ?> WIB</td>
                        <td class="text-center">
                            <button type="button" class="btn-detail-barang tombol-detail" 
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-barang="<?= htmlspecialchars(str_replace('||', ', ', $row['list_barang'])) ?>">
                                🔍 Lihat Barang
                            </button>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn-verif tombol-setuju" 
                                    data-user="<?= $row['id_user'] ?>" 
                                    data-tgl="<?= $row['tgl_pinjam'] ?>"
                                    data-peminjam="<?= htmlspecialchars($row['nama_lengkap']) ?>"
                                    data-barang-raw="<?= htmlspecialchars($row['list_barang']) ?>"
                                    data-id-pinjam-raw="<?= htmlspecialchars($row['list_id_pinjam']) ?>"
                                    data-no-hp="<?= htmlspecialchars($no_hp_tampil) ?>"
                                    data-keperluan="<?= htmlspecialchars($keperluan_tampil) ?>">
                               Verifikasi Sesi
                            </button>
                        </td>
                    </tr>
                    <?php 
                        }
                    } else {
                        echo "<tr><td colspan='6' style='padding: 60px;' class='text-muted text-center'><h5>Tidak ada antrean pengajuan peminjaman baru untuk laboratorium ini.</h5></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="modalDetail" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #17a2b8; border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: #17a2b8; border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">Daftar Barang Dipinjam</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="text-muted">Peminjam: <strong id="detail_nama_peminjam" class="text-dark"></strong></p>
                    <hr>
                    <ul id="container_list_barang" class="list-group list-group-flush fw-bold text-secondary"></ul>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalFotoBukti" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 800px;">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h6 class="modal-title fw-bold" id="title_nama_bukti">Potret Otentikasi Wajah</h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 text-center bg-dark rounded-bottom-4">
                    <img id="img_popup_besar" src="" class="img-fluid rounded-3 border border-secondary shadow-lg" style="width: 100%; max-height: 450px; object-fit: contain;" alt="Otentikasi Gagal">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalVerifikasi" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title text-white fw-bold">⚙️ Verifikasi Selektif Barang</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="admin_verifikasi_proses_peminjaman.php" method="POST">
                    
                    <input type="hidden" name="pengawas" value="<?= htmlspecialchars($nama_admin_login); ?>">

                    <div class="modal-body p-4">
                        <p class="text-muted fs-6 mb-3">Siswa Peminjam: <strong id="nama_peminjam_text" class="text-dark"></strong></p>
                        
                        <div class="p-3 mb-4 rounded-3" style="background-color: #f4faf8; border-left: 4px solid var(--tosca-tua);">
                            <div class="row">
                                <div class="col-md-4 mb-2 mb-md-0">
                                    <span class="text-muted small d-block">📱 No. HP / WhatsApp:</span>
                                    <strong style="color: var(--tosca-tua);" id="modal_no_hp_text">-</strong>
                                </div>
                                <div class="col-md-8">
                                    <span class="text-muted small d-block">📝 Deskripsi Keperluan:</span>
                                    <p class="mb-0 text-dark fw-semibold small" id="modal_keperluan_text" style="line-height: 1.4;">-</p>
                                </div>
                            </div>
                        </div>

                        <label class="form-label fw-bold" style="color: var(--tosca-tua);">Tentukan Keputusan Per Item Barang:</label>
                        <div class="table-responsive mt-1">
                            <table class="table table-bordered align-middle text-center" style="border-color: #c4e1db;">
                                <thead style="background-color: #f4faf8; color: var(--tosca-tua); font-weight: 700;">
                                    <tr>
                                        <th class="text-start" style="padding-left: 15px;">Nama Aset Barang</th>
                                        <th width="300">Tindakan Admin</th>
                                    </tr>
                                </thead>
                                <tbody id="container_item_verifikasi"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);">Simpan Hasil Verifikasi</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // TRIGGER SCRIPT MODAL POPUP GAMBAR BESAR
        const tombolFoto = document.querySelectorAll('.tombol-foto-popup');
        const modalFotoBS = new bootstrap.Modal(document.getElementById('modalFotoBukti'));

        tombolFoto.forEach(img => {
            img.addEventListener('click', function() {
                const srcGambar = this.getAttribute('data-src');
                const namaSiswa = this.getAttribute('data-nama');
                
                document.getElementById('title_nama_bukti').innerText = "🛡️ Bukti Wajah: " + namaSiswa;
                document.getElementById('img_popup_besar').setAttribute('src', srcGambar);
                modalFotoBS.show();
            });
        });

        const tombolDetail = document.querySelectorAll('.tombol-detail');
        const modalDetailBS = new bootstrap.Modal(document.getElementById('modalDetail'));
        
        tombolDetail.forEach(btn => {
            btn.addEventListener('click', function() {
                const nama = this.getAttribute('data-peminjam');
                const barangString = this.getAttribute('data-barang');
                document.getElementById('detail_nama_peminjam').innerText = nama;
                
                const arrayBarang = barangString.split(', ');
                const container = document.getElementById('container_list_barang');
                container.innerHTML = ''; 
                
                arrayBarang.forEach(item => {
                    container.innerHTML += `<li class="list-group-item d-flex align-items-center gap-2">📦 ${item}</li>`;
                });
                modalDetailBS.show();
            });
        });

        const modalVerifBS = new bootstrap.Modal(document.getElementById('modalVerifikasi'));
        const tombolSetuju = document.querySelectorAll('.tombol-setuju');
        tombolSetuju.forEach(button => {
            button.addEventListener('click', function() {
                const namaPeminjam = this.getAttribute('data-peminjam');
                const rawBarang = this.getAttribute('data-barang-raw').split('||');
                const rawIdPinjam = this.getAttribute('data-id-pinjam-raw').split(',');
                
                const noHpSiswa = this.getAttribute('data-no-hp');
                const keperluanSiswa = this.getAttribute('data-keperluan');
                
                document.getElementById('nama_peminjam_text').innerText = namaPeminjam;
                document.getElementById('modal_no_hp_text').innerText = noHpSiswa;
                document.getElementById('modal_keperluan_text').innerText = keperluanSiswa;
                
                const container = document.getElementById('container_item_verifikasi');
                container.innerHTML = '';
                
                rawBarang.forEach((namaBarang, index) => {
                    const idPinjam = rawIdPinjam[index];
                    
                    const rowHTML = `
                        <tr>
                            <td class="text-start fw-bold text-dark" style="padding-left: 15px;">📦 ${namaBarang}</td>
                            <td>
                                <div class="d-flex justify-content-center gap-3">
                                    <input type="radio" class="btn-check" name="status_item[${idPinjam}]" id="accept_${idPinjam}" value="disetujui" checked autocomplete="off">
                                    <label class="btn btn-sm btn-outline-success px-3 rounded-pill fw-bold" for="accept_${idPinjam}">✅ Setujui</label>
                                    
                                    <input type="radio" class="btn-check" name="status_item[${idPinjam}]" id="reject_${idPinjam}" value="ditolak" autocomplete="off">
                                    <label class="btn btn-sm btn-outline-danger px-3 rounded-pill fw-bold" for="reject_${idPinjam}">❌ Tolak</label>
                                </div>
                            </td>
                        </tr>
                    `;
                    container.innerHTML += rowHTML;
                });
                modalVerifBS.show();
            });
        });

        const adminParams = new URLSearchParams(window.location.search);
        const statusVerif = adminParams.get('status');
        if (statusVerif === 'sukses_verif') {
            const namaPengawas = adminParams.get('pengawas');
            Swal.fire({
                title: 'Verifikasi Berhasil!',
                text: 'Verifikasi selektif berkas pinjaman berhasil diproses oleh ' + decodeURIComponent(namaPengawas) + '.',
                icon: 'success',
                confirmButtonColor: '#1e6f65'
            }).then(() => {
                window.history.replaceState({}, document.title, window.location.pathname);
            });
        }
    </script>
    <?php include '../components/footer.php'; ?>
</body>
</html>