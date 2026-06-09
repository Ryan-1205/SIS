<?php 
session_start();
include 'koneksi.php';

$status_proses = '';

// ================= UTILITY HELPER: FUNGSI KOMPRESI BASE64 KE JPG =================
function dekodeKompresDanSimpan($base64_string, $target_path, $max_width = 800, $quality = 75) {
    if (!function_exists('imagecreatefromstring')) {
        return "GD_ERROR";
    }

    $data_pecah = explode(',', $base64_string);
    if (!isset($data_pecah[1])) return false;
    
    $gambar_biner = base64_decode($data_pecah[1]);
    $image = imagecreatefromstring($gambar_biner);
    if (!$image) return false;

    $width = imagesx($image);
    $height = imagesy($image);

    if ($width > $max_width) {
        $new_width = $max_width;
        $new_height = floor($height * ($max_width / $width));
        
        $canvas = imagecreatetruecolor($new_width, $new_height);
        imagecopyresampled($canvas, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
        imagedestroy($image);
        $image = $canvas;
    }

    $sukses = imagejpeg($image, $target_path, $quality);
    imagedestroy($image);
    
    return $sukses;
}
// =================================================================================

if (isset($_POST['daftar'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis = mysqli_real_escape_string($conn, $_POST['nis']); 
    $snapshot_wajah = isset($_POST['snapshot_wajah']) ? $_POST['snapshot_wajah'] : '';

    $cek_user = mysqli_query($conn, "SELECT * FROM users WHERE nis = '$nis'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $error = "Nomor Induk Siswa (NIS) sudah terdaftar dalam sistem.";
    } elseif (empty($snapshot_wajah)) {
        $error = "Gagal mendaftar. Anda belum melakukan verifikasi potret kamera.";
    } else {
        $nama_file_final = "user_" . $nis . "_" . time() . ".jpg";
        $target_folder   = "assets/img/pengguna/" . $nama_file_final;
        
        $proses_kompres = dekodeKompresDanSimpan($snapshot_wajah, $target_folder, 800, 75);

        if ($proses_kompres === "GD_ERROR") {
            $error = "SISTEM ERROR: Ekstensi GD Library belum aktif di XAMPP Anda. Silakan aktifkan 'extension=gd' di file php.ini lalu restart Apache.";
        } elseif ($proses_kompres) {
            $query = "INSERT INTO users (nis, nama_lengkap, role, foto_resmi) 
                      VALUES ('$nis', '$nama', 'siswa', '$nama_file_final')";
            
            if (mysqli_query($conn, $query)) {
                $status_proses = 'sukses';
            } else {
                if (file_exists($target_folder)) unlink($target_folder);
                $error = "Terjadi kesalahan teknis saat menyimpan data pendaftaran.";
            }
        } else {
            $error = "Sistem gagal mengolah data biner tangkapan kamera wajah.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIS - Sixseven Inventory System</title>
    <link rel="icon" type="image/png" href="assets/img/logo/smk.png">

    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.8">
    <style>
        .login-input {
            border: 2px solid transparent !important;
            transition: 0.3s;
            background-color: #ffffff !important;
            color: #333 !important;
        }
        .login-input:focus {
            border: 2px solid var(--tosca-tua) !important;
            outline: none;
        }
        .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .modal-header { background-color: #1d5c56; color: white; border-top-left-radius: 15px; border-top-right-radius: 15px; border-bottom: none; }
        
        .btn-tosca { 
            background-color: #1d5c56 !important; 
            color: #ffffff !important; 
            border-radius: 8px; 
            font-weight: 600; 
            padding: 10px 30px; 
            border: none;
            transition: background-color 0.2s ease, opacity 0.2s ease;
        }
        
        .btn-tosca:hover { 
            background-color: #143f3b !important; 
            color: #ffffff !important; 
            opacity: 1 !important;
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <form action="" method="POST" id="formRegister" onsubmit="return jalankanProteksiKirim();">

        <div class="safe-container login-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
            <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 50px; width: 100%; max-width: 400px; text-align: center;">
                <h2 style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 30px;">Daftar</h2>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger py-2 text-start small" role="alert"><?= $error ?></div>
                <?php endif; ?>

                <input type="text" name="nama" id="reg_nama" class="login-input" placeholder="Nama Lengkap" value="<?= isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : '' ?>" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 20px; width: 100%;">
                
                <input type="text" name="nis" id="reg_nis" class="login-input" placeholder="Nomor Induk Siswa (NIS)" value="<?= isset($_POST['nis']) ? htmlspecialchars($_POST['nis']) : '' ?>" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 25px; width: 100%;">
                
                <input type="hidden" name="snapshot_wajah" id="snapshot_wajah">
                
                <button type="button" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 10px;" onclick="validasiTeksDanBukaKamera()">Daftar</button>
                
                <a href="login.php" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Punya Akun? <strong style="font-weight: 800;">Masuk</strong></a>
            </div>
        </div>

        <div class="modal fade" id="modalKameraDaftar" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px;">
                    <div class="modal-header" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                        <h5 class="modal-title text-white fw-bold" id="judulModal">🔒 Langkah Terakhir: Verifikasi Wajah</h5>
                        <button type="button" id="btnXClose" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopWebcam()"></button>
                    </div>
                    <div class="modal-body p-4 text-center">
                        <p class="text-muted small mb-3" id="petunjukModal">Posisikan wajah Anda pas di tengah frame, lalu klik tombol **Ambil Foto**.</p>
                        
                        <div style="position: relative; width: 320px; height: 240px; margin: 0 auto 15px auto; background: #000; border-radius: 12px; overflow: hidden; border: 2px solid var(--tosca-tua);">
                            <video id="webcam" autoplay muted width="320" height="240" style="position: absolute; left:0; top:0; width: 100%; height: 100%; object-fit: cover; z-index: 1;"></video>
                            
                            <canvas id="previewFoto" style="position: absolute; left:0; top:0; width: 100%; height: 100%; object-fit: cover; z-index: 3; display: none;"></canvas>
                            
                            <div id="scanStatus" class="text-white p-2 d-flex align-items-center justify-content-center text-center" style="position: absolute; width:100%; height:100%; background: rgba(0,0,0,0.75); font-size: 13px; left:0; top:0; z-index: 5; display: flex;">
                                Menginisialisasi modul kecerdasan buatan wajah... ⏳
                            </div>
                        </div>

                        <div id="alertKameraBawah" class="alert alert-danger py-2 mt-3 text-center small d-none" role="alert"></div>
                    </div>
                    <div class="modal-footer border-0 justify-content-center pb-4 gap-2">
                        <button type="button" id="btnBatalModal" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal" onclick="stopWebcam()">Batal</button>
                        <button type="button" id="btnJepret" class="btn btn-warning text-dark fw-bold rounded-pill px-4 d-none" onclick="ambilFotoManual()">📸 Ambil Foto</button>
                        <button type="button" id="btnUlang" class="btn btn-danger text-white fw-bold rounded-pill px-4 d-none" onclick="ulangFotoKamera()">🔄 Foto Ulang</button>
                        <button type="submit" name="daftar" id="btnKirimFinal" class="btn text-white fw-bold rounded-pill px-4 d-none" style="background-color: var(--tosca-tua);">Kirim Pendaftaran 🚀</button>
                    </div>
                </div>
            </div>
        </div>

    </form>

    <?php if ($status_proses == 'sukses') : ?>
    <div class="modal fade" id="registrasiModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Notifikasi Sistem</h5>
                </div>
                <div class="modal-body text-center py-4">
                    <h4 class="mt-3 fw-bold" style="color: #1d5c56;">Pendaftaran Berhasil</h4>
                    <p class="text-muted mb-0">Akun siswa Anda telah sukses dibuat menggunakan validasi biometrik. Silakan masuk menggunakan NIS Anda.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <a href="login.php" class="btn btn-tosca text-decoration-none">Selesai</a>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/face-api.min.js"></script>

    <script>
        const modalKameraBS = new bootstrap.Modal(document.getElementById('modalKameraDaftar'));
        let streamWebcam = null;
        let isFaceModelLoaded = false;

        document.addEventListener("DOMContentLoaded", function() {
            var modalElemen = document.getElementById('registrasiModal');
            if (modalElemen) {
                var pemicuModal = new bootstrap.Modal(modalElemen);
                pemicuModal.show();
            }
            loadFaceAPIModels();
        });

        async function loadFaceAPIModels() {
            try {
                await faceapi.nets.ssdMobilenetv1.loadFromUri('http://localhost/SIS/assets/models');
                await faceapi.nets.faceLandmark68Net.loadFromUri('http://localhost/SIS/assets/models');
                isFaceModelLoaded = true;
            } catch (error) {
                console.error("Gagal memuat model face-api:", error);
            }
        }

        function validasiTeksDanBukaKamera() {
            const nama = document.getElementById('reg_nama').value.trim();
            const nis = document.getElementById('reg_nis').value.trim();

            if (nama === "" || nis === "") {
                alert("⚠️ Harap lengkapi Nama Lengkap dan Nomor Induk Siswa (NIS)!");
                return;
            }

            resetTampilanModal();
            modalKameraBS.show();
            startWebcam();
        }

        function resetTampilanModal() {
            document.getElementById('judulModal').innerText = "🔒 Langkah Terakhir: Verifikasi Wajah";
            document.getElementById('petunjukModal').style.display = "block";
            document.getElementById('petunjukModal').innerText = "Posisikan wajah Anda pas di tengah frame, lalu klik tombol Ambil Foto.";
            
            document.getElementById('btnKirimFinal').classList.add('d-none');
            document.getElementById('btnUlang').classList.add('d-none');
            document.getElementById('btnJepret').classList.add('d-none');
            document.getElementById('alertKameraBawah').classList.add('d-none'); 
            
            const btnBatal = document.getElementById('btnBatalModal');
            btnBatal.classList.remove('d-none');
            btnBatal.innerText = "Batal";
            
            document.getElementById('btnXClose').style.display = "block";
            
            const video = document.getElementById('webcam');
            const canvasPreview = document.getElementById('previewFoto');
            video.style.display = "block";
            canvasPreview.style.display = "none";
        }

        function startWebcam() {
            const video = document.getElementById('webcam');
            const statusDiv = document.getElementById('scanStatus');
            
            statusDiv.style.setProperty('display', 'flex', 'important');
            statusDiv.style.background = "rgba(0,0,0,0.85)";
            statusDiv.innerText = "Menginisialisasi modul webcam... ⏳";
            
            navigator.mediaDevices.getUserMedia({ video: { facingMode: "user", width: 640, height: 480 } })
                .then(stream => {
                    streamWebcam = stream;
                    video.srcObject = stream;
                    
                    video.onplaying = () => {
                        statusDiv.style.setProperty('display', 'none', 'important');
                        document.getElementById('btnJepret').classList.remove('d-none');
                    };
                })
                .catch(err => {
                    console.error(err);
                    statusDiv.innerHTML = "<span class='text-danger fw-bold'>❌ Gagal Mengakses Kamera! Pastikan izin browser aktif.</span>";
                });
        }

        // 🔥 MODUL BARU: PROTEKSI MULTI-FACE (WAJIB 1 WAJAH) & SIMETRIS POSE
        async function ambilFotoManual() {
            const video = document.getElementById('webcam');
            const canvasPreview = document.getElementById('previewFoto');
            const statusDiv = document.getElementById('scanStatus');
            const alertBawah = document.getElementById('alertKameraBawah');
            
            alertBawah.classList.add('d-none');

            if (!isFaceModelLoaded) {
                alertBawah.classList.remove('d-none');
                alertBawah.innerText = "Sistem kecerdasan buatan sedang dimuat, harap tunggu beberapa detik.";
                return;
            }

            statusDiv.style.setProperty('display', 'flex', 'important');
            statusDiv.style.background = "rgba(0,0,0,0.6)";
            statusDiv.innerHTML = "<span class='text-warning fw-bold'>Menganalisis kuantitas & sudut wajah... 🔍</span>";

            // 1. Ambil deteksi massal untuk menghitung total wajah di dalam kamera frame
            const allDetections = await faceapi.detectAllFaces(video).withFaceLandmarks();

            // Proteksi Lapis Pertama: Jika tidak ada objek sama sekali atau lebih dari satu orang terdeteksi
            if (allDetections.length === 0) {
                statusDiv.style.setProperty('display', 'none', 'important');
                alertBawah.classList.remove('d-none');
                alertBawah.innerHTML = "<strong>❌ Potret Gagal!</strong> Wajah tidak terdeteksi. Posisikan muka menghadap lurus ke arah kamera.";
                return;
            } else if (allDetections.length > 1) {
                statusDiv.style.setProperty('display', 'none', 'important');
                alertBawah.classList.remove('d-none');
                alertBawah.innerHTML = "<strong>❌ Proteksi Keamanan Terbuka!</strong> Terdeteksi ada <strong>" + allDetections.length + " wajah</strong> di dalam frame. Proses pendaftaran master wajib dilakukan mandiri oleh satu orang.";
                return;
            }

            // 2. Jika lolos (tepat ada 1 wajah), ambil indeks data pertama untuk pengujian struktur simetris
            const detection = allDetections[0];
            const landmarks = detection.landmarks;
            const mataKiri  = landmarks.getLeftEye()[0];
            const mataKanan = landmarks.getRightEye()[3];
            const ujungHidung = landmarks.getNose()[6];

            const jarakKeKiri  = Math.abs(ujungHidung.x - mataKiri.x);
            const jarakKeKanan = Math.abs(mataKanan.x - ujungHidung.x);

            const rasioSimetris = jarakKeKiri / jarakKeKanan;

            // Proteksi Lapis Kedua: Uji orientasi kelayakan sudut (Frontal Pose Checking)
            if (rasioSimetris < 0.65 || rasioSimetris > 1.55) {
                statusDiv.style.setProperty('display', 'none', 'important');
                alertBawah.classList.remove('d-none');
                alertBawah.innerHTML = "<strong>❌ Posisi Wajah Salah!</strong> Kepala Anda terdeteksi miring/nyerong. Harap hadapkan muka bener-bener **lurus ke depan kamera** dengan simetris.";
                return; 
            }

            // 3. Eksekusi penyimpanan data snapshot setelah lolos kedua lapis filter pengujian
            const vidWidth = video.videoWidth;
            const vidHeight = video.videoHeight;

            canvasPreview.width = vidWidth;
            canvasPreview.height = vidHeight;
            
            const ctx = canvasPreview.getContext('2d');
            ctx.drawImage(video, 0, 0, vidWidth, vidHeight);
            
            const dataURL = canvasPreview.toDataURL('image/jpeg', 0.92);
            document.getElementById('snapshot_wajah').value = dataURL;

            video.style.display = "none";
            canvasPreview.style.display = "block";
            statusDiv.style.setProperty('display', 'none', 'important');

            document.getElementById('judulModal').innerText = "📸 Pratinjau Foto Verifikasi Anda";
            document.getElementById('petunjukModal').innerText = "Pastikan potret tidak blur dan pas di tengah sebelum dikirim.";
            document.getElementById('btnJepret').classList.add('d-none');
            document.getElementById('btnXClose').style.display = "none";
            document.getElementById('btnBatalModal').classList.add('d-none');
            
            document.getElementById('btnUlang').classList.remove('d-none');
            document.getElementById('btnKirimFinal').classList.remove('d-none');
        }

        function ulangFotoKamera() {
            document.getElementById('snapshot_wajah').value = "";
            resetTampilanModal();
            
            const video = document.getElementById('webcam');
            video.style.display = "block";
            document.getElementById('btnJepret').classList.remove('d-none');
        }

        function stopWebcam() {
            if (streamWebcam) {
                streamWebcam.getTracks().forEach(track => track.stop());
            }
        }

        function jalankanProteksiKirim() {
            const btn = document.getElementById('btnKirimFinal');
            if (!btn.classList.contains('d-none')) {
                btn.style.pointerEvents = "none"; 
                btn.innerText = "Mengirim Berkas...";
                return true; 
            }
            return false;
        }
    </script>
</body>
</html>