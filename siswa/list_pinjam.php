<?php 
session_start();
// PENYESUAIAN JALUR: Mundur satu folder karena file ini berjalan di dalam folder siswa/
include '../koneksi.php'; 

// Pastikan user sudah login untuk mengambil data antrean pribadinya
$id_user_login = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

// ================= AMBIL FOTO MASTER USER DARI DATABASE SECARA REALTIME =================
$foto_master_db = "default_user.jpg"; 
if ($id_user_login > 0) {
    $query_user = mysqli_query($conn, "SELECT foto_resmi FROM users WHERE id_user = '$id_user_login'");
    if ($row_user = mysqli_fetch_assoc($query_user)) {
        // PENYESUAIAN JALUR FOTO: Ditambahkan ../assets/ untuk pengecekan file fisik dari subfolder
        if (!empty($row_user['foto_resmi']) && file_exists("../assets/img/" . $row_user['foto_resmi'])) {
            $foto_master_db = $row_user['foto_resmi'];
        }
    }
}
// =======================================================================================

// Inisialisasi keranjang jika belum ada
if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

// Ambil data referer (halaman asal) untuk mengarahkan tombol "+ Tambah Barang" secara dinamis
$halaman_kembali = '../index.php'; 
if (isset($_SERVER['HTTP_REFERER'])) {
    $halaman_kembali = $_SERVER['HTTP_REFERER'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List Pinjam Barang - SIS</title>
    <link rel="stylesheet" href="../assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css?v=1.4">
    <style>
        .admin-table-wrapper {
            max-width: 1000px;
            margin: 0 auto 30px auto;
            border: 2px solid var(--tosca-tua);
            background: white;
            border-radius: 10px;
            overflow: hidden;
        }
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
        }
        .admin-table th {
            color: white;
            background-color: var(--tosca-tua);
            font-weight: 700;
            padding: 15px 15px;
            font-size: 15px;
        }
        .admin-table td {
            padding: 15px 15px;
            border-bottom: 1px solid var(--tosca-muda);
            color: #333;
            font-size: 15px;
            vertical-align: middle;
        }
        .admin-table tr:last-child td {
            border-bottom: none;
        }
        .badge-lab {
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 13px;
            font-weight: 600;
            background-color: var(--tosca-muda);
            color: var(--tosca-tua);
            display: inline-block;
            white-space: nowrap;
        }
        .badge-status {
            padding: 6px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .status-pending { background-color: #ffeaa7; color: #e67e22; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-returning { background-color: #d1ecf1; color: #0c5460; }
        
        .webcam-section { display: none; }
    </style>
</head>
<body>

    <?php include '../components/header.php'; ?>
    <?php include '../components/sub_header_siswa.php'; ?>

    <form action="form_final_pinjam.php" method="POST" id="formPinjam">
        
        <div class="safe-container px-3 mt-5" style="margin-bottom: 150px;">
            
            <h4 class="fw-bold mb-3" style="color: var(--tosca-tua); max-width: 1000px; margin: 0 auto 15px auto;">
                🛒 Keranjang Peminjaman Anda
            </h4>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-start" style="padding-left: 25px;">NAMA BARANG</th>
                            <th class="text-start">DESKRIPSI</th>
                            <th>KATEGORI ASET</th> 
                            <th>AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if (!empty($_SESSION['keranjang'])) {
                            foreach ($_SESSION['keranjang'] as $id_item) {
                                $id_item = mysqli_real_escape_string($conn, $id_item);
                                $query_item = "SELECT b.*, k.nama_kategori 
                                               FROM barang b 
                                               JOIN kategori k ON b.id_kategori = k.id_kategori 
                                               WHERE b.id_barang = '$id_item'";
                                
                                $res = mysqli_query($conn, $query_item);
                                $row = mysqli_fetch_assoc($res);
                                if ($row) {
                        ?>
                            <tr>
                                <td class="fw-bold text-start" style="padding-left: 25px; color: var(--tosca-tua);">
                                    <input type="hidden" name="item_pilihan[]" value="<?= $row['id_barang']; ?>">
                                    <?= htmlspecialchars($row['nama_barang']); ?>
                                </td>
                                <td class="text-start"><?= htmlspecialchars($row['deskripsi']); ?></td>
                                <td>
                                    <span class="badge-lab">📍 <?= htmlspecialchars($row['nama_kategori']); ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-link text-danger fw-bold text-decoration-none p-0 tombol-hapus" 
                                            data-id="<?= $row['id_barang']; ?>" 
                                            data-nama="<?= htmlspecialchars($row['nama_barang']); ?>">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                        <?php 
                                }
                            } 
                        } else { ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <span class="fs-6">Keranjang kosong. Belum ada barang yang kamu pilih untuk diajukan.</span>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <h4 class="fw-bold mb-3 mt-5" style="color: var(--tosca-tua); max-width: 1000px; margin: 40px auto 15px auto;">
                ⏳ Pengajuan yang Menunggu Verifikasi Admin
            </h4>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>WAKTU PENGAJUAN</th>
                            <th class="text-start">NAMA BARANG</th>
                            <th>KATEGORI</th>
                            <th>STATUS PROSES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query_antrean = "SELECT p.*, b.nama_barang, k.nama_kategori 
                                          FROM peminjaman p
                                          JOIN barang b ON p.id_barang = b.id_barang
                                          JOIN kategori k ON b.id_kategori = k.id_kategori
                                          WHERE p.id_user = '$id_user_login' AND p.status_pengajuan = 'pending'
                                          ORDER BY p.id_pinjam DESC";
                        
                        $res_antrean = mysqli_query($conn, $query_antrean);

                        if (mysqli_num_rows($res_antrean) > 0) {
                            while ($antrean = mysqli_fetch_assoc($res_antrean)) {
                        ?>
                            <tr>
                                <td class="text-muted font-monospace" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($antrean['tgl_pinjam'])); ?> WIB</td>
                                <td class="fw-bold text-start text-dark"><?= htmlspecialchars($antrean['nama_barang']); ?></td>
                                <td>
                                    <span class="badge-lab">📍 <?= htmlspecialchars($antrean['nama_kategori']); ?></span>
                                </td>
                                <td>
                                    <span class="badge-status status-pending">⏳ Menunggu Verifikasi</span>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <span>Tidak ada pengajuan aktif yang sedang mengantre.</span>
                                </td>
                            </tr>
                        <?php 
                        } 
                        ?>
                    </tbody>
                </table>
            </div>

            <h4 class="fw-bold mb-3 mt-5" style="color: var(--tosca-tua); max-width: 1000px; margin: 40px auto 15px auto;">
                🖥️ Aset yang Sedang Anda Pinjam saat Ini
            </h4>
            <div class="admin-table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th class="text-start" style="padding-left: 20px;">NAMA BARANG</th>
                            <th>WAKTU PINJAM</th>
                            <th>BATAS KEMBALI</th>
                            <th class="text-start">KEPERLUAN</th>
                            <th>NO. HANDPHONE</th>
                            <th>STATUS</th>
                            <th>AKSI</th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $query_disetujui = "SELECT p.*, b.nama_barang FROM peminjaman p
                                            JOIN barang b ON p.id_barang = b.id_barang
                                            WHERE p.id_user = '$id_user_login' AND p.status_pengajuan IN ('disetujui', 'pending_kembali')
                                            ORDER BY p.id_pinjam DESC";
                        $res_disetujui = mysqli_query($conn, $query_disetujui);

                        if (mysqli_num_rows($res_disetujui) > 0) {
                            while ($disetujui = mysqli_fetch_assoc($res_disetujui)) {
                        ?>
                            <tr>
                                <td class="fw-bold text-start text-dark" style="padding-left: 20px;"><?= htmlspecialchars($disetujui['nama_barang']); ?></td>
                                <td class="font-monospace text-secondary" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($disetujui['tgl_pinjam'])); ?> WIB</td>
                                <td class="font-monospace text-danger fw-bold" style="font-size:13px;"><?= date('d M Y, H:i', strtotime($disetujui['tgl_kembali_rencana'])); ?> WIB</td>
                                <td class="text-start text-muted small"><?= htmlspecialchars($disetujui['keperluan'] ? $disetujui['keperluan'] : '-'); ?></td>
                                <td class="fw-bold text-secondary" style="font-size:13px;"><?= htmlspecialchars($disetujui['no_hp'] ? $disetujui['no_hp'] : '-'); ?></td>
                                <td>
                                    <?php if ($disetujui['status_pengajuan'] == 'disetujui'): ?>
                                        <span class="badge-status status-approved">✔️ Dipinjam</span>
                                    <?php else: ?>
                                        <span class="badge-status status-pending">⏳ Proses Pengembalian</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($disetujui['status_pengajuan'] == 'disetujui'): ?>
                                        <a href="ajukan_kembali.php?id=<?= $disetujui['id_pinjam']; ?>" class="btn btn-sm btn-danger fw-bold px-3 rounded-pill" onclick="return confirm('Apakah Anda yakin ingin mengajukan pengembalian untuk aset ini?')">
                                            Kembalikan
                                        </a>
                                    <?php else: ?>
                                        <span class="badge-status status-returning small fw-bold">Menunggu Admin</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='7' class='py-4 text-center text-muted'>Belum ada aset ter-verifikasi yang sedang kamu bawa.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="fixed-bottom action-footer" style="background-color: var(--tosca-tua, #1e6f65); height: 70px; z-index: 1030;">
            <div class="safe-container d-flex align-items-center justify-content-between px-3 h-100">
                <div>
                    <?php if (!empty($_SESSION['keranjang'])): ?>
                        <span class="text-white fw-bold">Total: <?= count($_SESSION['keranjang']); ?> Aset Siap Diajukan</span>
                    <?php endif; ?>
                </div>

                <div class="d-flex gap-3">
                    <a href="<?= $halaman_kembali; ?>" class="btn btn-outline-light fw-bold px-4 py-2 rounded-3">
                        + Tambah Barang
                    </a>
                    
                    <?php if (!empty($_SESSION['keranjang'])): ?>
                        <button type="button" class="btn btn-light fw-bold px-4 py-2 rounded-3" style="color: var(--tosca-tua) !important;" onclick="bukaModalTanggal()">
                            Pinjam Sekarang
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalInputTanggal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered modal-md">
                <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                    <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                        <h5 class="modal-title text-white fw-bold">📅 Formulir Kelengkapan Pinjam</h5>
                        <button type="button" id="btnXCloseModal" class="btn-close btn-close-white" onclick="kembaliKeFormulirAtauTutup()"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div id="formFieldsContainer">
                            <p class="text-muted small mb-3">Silakan tentukan waktu pemakaian aset lab beserta data kontak operasional.</p>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: var(--tosca-tua);">Waktu Mulai Pinjam :</label>
                                <input type="datetime-local" name="tgl_pinjam" id="modal_tgl_pinjam" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: var(--tosca-tua);">Rencana Waktu Kembali :</label>
                                <input type="datetime-local" name="tgl_kembali_rencana" id="modal_tgl_kembali" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: var(--tosca-tua);">No. Handphone / WhatsApp Aktif :</label>
                                <input type="text" name="no_hp" id="modal_no_hp" placeholder="Contoh: 081234567xxx" class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-bold" style="color: var(--tosca-tua);">Deskripsi Keperluan Meminjam :</label>
                                <textarea name="keperluan" id="modal_keperluan" rows="2" placeholder="Sebutkan alasan penggunaan aset ini..." class="form-control" style="border: 2px solid var(--tosca-tua); border-radius: 10px;" required></textarea>
                            </div>
                        </div>

                        <div id="webcamSection" class="webcam-section mb-2 text-center">
                            <label class="form-label d-block fw-bold" style="color: var(--tosca-tua);">Pindai Wajah Anda (Validasi Akhir) :</label>
                            <div style="position: relative; width: 320px; height: 240px; margin: 0 auto; background: #000; border-radius: 12px; overflow: hidden; border: 2px solid var(--tosca-tua);">
                                <video id="webcam" autoplay muted width="320" height="240" style="position: absolute; left:0; top:0; width: 100%; height: 100%; object-fit: cover; z-index: 1;"></video>
                                <canvas id="previewFoto" style="position: absolute; left:0; top:0; width: 100%; height: 100%; object-fit: cover; z-index: 3; display: none;"></canvas>
                                <div id="scanStatus" class="text-white p-2 d-flex align-items-center justify-content-center text-center" style="position: absolute; width:100%; height:100%; background: rgba(0,0,0,0.75); font-size: 13px; left:0; top:0; z-index: 5;">
                                    Memuat Sistem Kecerdasan AI Wajah...
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="snapshot_wajah" id="snapshot_wajah">
                    </div>
                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" id="btnBatalModal" class="btn btn-secondary rounded-pill px-4" onclick="kembaliKeFormulirAtauTutup()">Batal</button>
                        <button type="button" id="btnLanjutVerifikasi" class="btn text-white rounded-pill px-4" style="background-color: var(--tosca-tua);" onclick="validasiFormDanLanjutScan()">Kirim Pengajuan</button>
                    </div>
                </div>
            </div>
        </div>
    </form> 
    
    <div class="modal fade" id="modalKonfirmasiHapus" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid #dc3545; border-radius: 20px; text-align: left;">
                <div class="modal-header text-white" style="background-color: #dc3545; border-top-left-radius: 17px; border-top-right-radius: 17px; border-bottom: none;">
                    <h5 class="modal-title fw-bold">🗑️ Konfirmasi Hapus</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <span style="font-size: 50px; display: block; margin-bottom: 10px;">🗑️</span>
                    <h5 class="fw-bold mt-2 mb-2" style="color: #dc3545;">Keluarkan dari Daftar?</h5>
                    <p class="text-muted fs-6 mb-2">Apakah Anda yakin ingin menghapus barang berikut dari rencana peminjaman?</p>
                    <strong class="d-block fs-5 text-dark mb-4" id="nama_barang_hapus"></strong>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4 gap-3">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill" style="font-weight: 600;" data-bs-dismiss="modal">Batal</button>
                    <a href="#" id="link_eksekusi_hapus" class="btn btn-danger px-4 rounded-pill" style="font-weight: 600;">Ya, Hapus</a>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/js/face-api.min.js"></script>
    
    <script>
        const tombolHapus = document.querySelectorAll('.tombol-hapus');
        const modalHapusBS = new bootstrap.Modal(document.getElementById('modalKonfirmasiHapus'));

        tombolHapus.forEach(btn => {
            btn.addEventListener('click', function() {
                const idBarang = this.getAttribute('data-id');
                const namaBarang = this.getAttribute('data-nama');
                document.getElementById('nama_barang_hapus').innerText = namaBarang;
                
                document.getElementById('link_eksekusi_hapus').setAttribute('href', 'keranjang_hapus.php?id=' + idBarang);
                modalHapusBS.show();
            });
        });

        const modalTanggalBS = new bootstrap.Modal(document.getElementById('modalInputTanggal'));
        let streamWebcam = null;
        let intervalScan = null;

        // FUNGSI UTK MENDAPATKAN FORMAT DATETIME LOCAL (YYYY-MM-DDTHH:MM)
        function dapetWaktuSekarangLokal() {
            const sekarang = new Date();
            const offset = sekarang.getTimezoneOffset() * 60000; // Penyesuaian ke zona waktu lokal (WIB/sesuai device)
            const waktuLokal = new Date(sekarang.getTime() - offset);
            return waktuLokal.toISOString().slice(0, 16);
        }

        function bukaModalTanggal() {
            const sekarangWaktuLokal = dapetWaktuSekarangLokal();
            
            // Waktu mulai pinjam tetap default-nya saat ini
            document.getElementById('modal_tgl_pinjam').value = sekarangWaktuLokal;
            
            // HAPUS ATAU KOMENTARI BARIS VALUE KEMBALI
            document.getElementById('modal_tgl_kembali').value = ""; // Membuatnya kosong secara default
            
            // Batas minimum tetap berjalan agar siswa tidak bisa pilih tanggal backdate (hari kemarin)
            document.getElementById('modal_tgl_pinjam').min = sekarangWaktuLokal;
            document.getElementById('modal_tgl_kembali').min = sekarangWaktuLokal;
            
            // ... kode sisanya ke bawah biarkan tetap sama
            
            document.getElementById('formFieldsContainer').style.display = 'block';
            document.getElementById('webcamSection').style.display = 'none';
            
            document.querySelector('#modalInputTanggal .modal-title').innerText = "📅 Formulir Kelengkapan Pinjam";
            document.getElementById('btnLanjutVerifikasi').style.display = 'inline-block';
            document.getElementById('btnXCloseModal').style.display = 'inline-block';
            
            const btnBatal = document.getElementById('btnBatalModal');
            btnBatal.innerText = "Batal";
            btnBatal.className = "btn btn-secondary rounded-pill px-4";
            
            modalTanggalBS.show();
        }

        function kembaliKeFormulirAtauTutup() {
            const sectionKamera = document.getElementById('webcamSection');
            
            if (sectionKamera.style.display === 'block') {
                stopWebcam();
                document.getElementById('formFieldsContainer').style.display = 'block';
                sectionKamera.style.display = 'none';
                document.getElementById('btnLanjutVerifikasi').style.display = 'inline-block';
                document.querySelector('#modalInputTanggal .modal-title').innerText = "📅 Formulir Kelengkapan Pinjam";
                
                const btnBatal = document.getElementById('btnBatalModal');
                btnBatal.innerText = "Batal";
                btnBatal.className = "btn btn-secondary rounded-pill px-4";
            } else {
                stopWebcam();
                modalTanggalBS.hide();
            }
        }

        function validasiFormDanLanjutScan() {
            const tglPinjam = document.getElementById('modal_tgl_pinjam').value;
            const tglKembali = document.getElementById('modal_tgl_kembali').value;
            const noHp = document.getElementById('modal_no_hp').value.trim();
            const keperluan = document.getElementById('modal_keperluan').value.trim();

            if (tglPinjam === "" || tglKembali === "" || noHp === "" || keperluan === "") {
                Swal.fire({ title: '⚠️ Input Belum Lengkap', text: 'Mohon lengkapi Waktu Pinjam, No. HP, dan Deskripsi Keperluan!', icon: 'warning', confirmButtonColor: '#1e6f65' });
                return;
            }

            if (new Date(tglKembali) < new Date(tglPinjam)) {
                Swal.fire({ title: '❌ Logika Waktu Salah', text: 'Rencana waktu pengembalian tidak boleh mendahului waktu mulai meminjam.', icon: 'error', confirmButtonColor: '#1e6f65' });
                return;
            }

            document.getElementById('formFieldsContainer').style.display = 'none';
            document.getElementById('btnLanjutVerifikasi').style.display = 'none';
            document.getElementById('btnXCloseModal').style.display = 'none';
            
            const btnBatal = document.getElementById('btnBatalModal');
            btnBatal.innerText = "🔄 Kembali";
            btnBatal.className = "btn btn-warning rounded-pill px-4 fw-bold text-dark";
            
            document.querySelector('#modalInputTanggal .modal-title').innerText = "🔒 Verifikasi Wajah Pemilik Akun";
            document.getElementById('webcamSection').style.display = 'block';
            document.getElementById('scanStatus').innerText = "Memuat Sistem Kecerdasan AI Wajah... 🛡️";

            startWebcam();
        }

        function startWebcam() {
            const video = document.getElementById('webcam');
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
                .then(stream => {
                    streamWebcam = stream;
                    video.srcObject = stream;
                    initFaceAPI(); 
                })
                .catch(err => {
                    Swal.fire({ title: '❌ Kamera Gagal', text: 'Izin kamera ditolak atau hardware tidak ditemukan.', icon: 'error', confirmButtonColor: '#1e6f65' }).then(() => {
                        window.location.reload();
                    });
                });
        }

        function stopWebcam() {
            if (intervalScan) clearInterval(intervalScan);
            if (streamWebcam) {
                streamWebcam.getTracks().forEach(track => track.stop());
            }
        }

        async function initFaceAPI() {
            const statusDiv = document.getElementById('scanStatus');
            try {
                await faceapi.nets.ssdMobilenetv1.loadFromUri('../assets/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('../assets/models');
                await faceapi.nets.faceRecognitionNet.loadFromUri('../assets/models');
                
                statusDiv.innerText = "Mencari & mengunci wajah depan kamera...";

                const namaFileMaster = "<?= $foto_master_db; ?>";
                const imgReference = await faceapi.fetchImage('../assets/img/' + namaFileMaster);
                
                const refDescriptor = await faceapi.detectSingleFace(imgReference).withFaceLandmarks().withFaceDescriptor();

                if (!refDescriptor) {
                    statusDiv.innerHTML = "<span class='text-danger fw-bold'>❌ Foto Master Akun di DB Terlalu Buram! Hubungi Admin Lab.</span>";
                    return;
                }

                const faceMatcher = new faceapi.FaceMatcher(refDescriptor, 0.5);
                const video = document.getElementById('webcam');
                const canvasPreview = document.getElementById('previewFoto');
                
                video.style.display = "block";
                canvasPreview.style.display = "none";

                intervalScan = setInterval(async () => {
                    if (video.paused || video.ended) return;
                    
                    const detections = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();
                    
                    if (detections) {
                        const match = faceMatcher.findBestMatch(detections.descriptor);
                        
                        if (match.label !== 'unknown') {
                            clearInterval(intervalScan);

                            const vidWidth = video.videoWidth;
                            const vidHeight = video.videoHeight;

                            if (vidWidth > 0 && vidHeight > 0) {
                                canvasPreview.width = vidWidth;
                                canvasPreview.height = vidHeight;
                                
                                const ctx = canvasPreview.getContext('2d');
                                ctx.drawImage(video, 0, 0, vidWidth, vidHeight);
                                
                                const dataURL = canvasPreview.toDataURL('image/jpeg', 0.9);
                                document.getElementById('snapshot_wajah').value = dataURL;
                            }

                            video.style.display = "none";
                            canvasPreview.style.display = "block";

                            canvasPreview.style.zIndex = "4"; 
                            statusDiv.style.zIndex = "5"; 
                            statusDiv.style.background = "transparent"; 

                            statusDiv.innerHTML = "<div class='text-white fw-bold px-4 py-2 rounded-pill shadow-sm' style='background: #1e6f65; position: absolute; bottom: 15px;'>✅ Wajah Terverifikasi! Mengirimkan Berkas...</div>";
                            document.getElementById('btnBatalModal').classList.add('d-none');

                            if (streamWebcam) {
                                streamWebcam.getTracks().forEach(track => track.stop());
                            }

                            setTimeout(() => {
                                document.getElementById('formPinjam').submit();
                            }, 1200);

                        } else {
                            statusDiv.style.background = "transparent";
                            statusDiv.innerHTML = "<div class='text-white fw-bold px-3 py-1 rounded-pill shadow-sm' style='background: rgba(220, 53, 69, 0.85); position: absolute; bottom: 10px; font-size: 11px; z-index: 6;'>❌ Wajah Tidak Sesuai Pemilik Akun!</div>";
                        }
                    } else {
                        statusDiv.style.background = "rgba(0,0,0,0.75)";
                        statusDiv.innerHTML = "<span class='text-warning fw-bold'>⚠️ Wajah tidak terdeteksi. Hadapkan muka lurus ke kamera.</span>";
                    }
                }, 400);

            } catch (error) {
                console.error(error);
                statusDiv.innerText = "Gagal memuat model berkas face-api lokal.";
            }
        }

        const urlParams = new URLSearchParams(window.location.search);
        const statusParam = urlParams.get('status');

        if (statusParam === 'sukses') {
            Swal.fire({ title: 'Berhasil Terpilih!', text: 'Barang masuk ke daftar pinjam.', icon: 'success', confirmButtonColor: '#1e6f65', timer: 2500, timerProgressBar: true }).then(() => { window.history.replaceState({}, document.title, window.location.pathname); });
        }
        if (statusParam === 'ada') {
            Swal.fire({ title: 'Sudah Dipilih!', text: 'Barang ini sudah ada di dalam daftar pinjam lu, Lek.', icon: 'info', confirmButtonColor: '#1e6f65', timer: 2500, timerProgressBar: true }).then(() => { window.history.replaceState({}, document.title, window.location.pathname); });
        }
        if (statusParam === 'sukses_pinjam') {
            const jumlah = urlParams.get('count');
            Swal.fire({ title: 'Pengajuan Sukses!', text: 'Berhasil mengirimkan ' + jumlah + ' pengajuan aset ke antrean verifikasi admin.', icon: 'success', confirmButtonColor: '#1e6f65' }).then(() => { window.history.replaceState({}, document.title, window.location.pathname); });
        } else if (statusParam === 'gagal_pinjam') {
            Swal.fire({ title: 'Gagal!', text: 'Gagal memproses pengajuan ke database.', icon: 'error', confirmButtonColor: '#1e6f65' }).then(() => { window.history.replaceState({}, document.title, window.location.pathname); });
        }
    </script>

    <?php include '../components/footer.php'; ?>
</body>
</html>