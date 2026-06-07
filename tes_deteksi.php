<?php
session_start();
include 'koneksi.php';

// ====== SILAKAN UBAH NIS INI SESUAI DENGAN YANG ADA DI DATABASE LU ======
$nis_tes = '111'; 

$query = mysqli_query($conn, "SELECT * FROM users WHERE nis = '$nis_tes'");
$user = mysqli_fetch_assoc($query);

$nama_file_master = '';
$error_db = '';

if (!$user) {
    $error_db = "Data user dengan NIS <strong>$nis_tes</strong> tidak ditemukan di database!";
} else {
    $nama_file_master = $user['foto_resmi'];
    if (empty($nama_file_master)) {
        $error_db = "User ditemukan, tapi kolom <strong>foto_resmi</strong> di database kosong!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uji Pencocokan Wajah (Face Matcher Test)</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; padding-top: 30px; }
        .card-uji { background: white; border-radius: 15px; border: 2px solid #1d5c56; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .status-box { padding: 12px; border-radius: 10px; font-weight: 600; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .media-box { width: 100%; max-width: 320px; height: 240px; margin: 0 auto; border: 2px dashed #1d5c56; border-radius: 12px; overflow: hidden; background: #000; position: relative; }
        .media-box img, .media-box video { width: 100%; height: 100%; object-fit: cover; }
        .match-indicator { font-size: 24px; font-weight: 800; padding: 15px; border-radius: 12px; text-align: center; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card-uji p-4 mb-5">
                <h3 class="text-center fw-bold mb-4" style="color: #1d5c56;">🤖 Live Face Matcher Debugger</h3>

                <?php if (!empty($error_db)): ?>
                    <div class="alert alert-danger text-center shadow-sm" role="alert">
                        ❌ <?= $error_db ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success small mb-4">
                        <strong>Target Verifikasi:</strong> <?= htmlspecialchars($user['nama_lengkap']) ?> (NIS: <?= htmlspecialchars($user['nis']) ?>) | 
                        <strong>File DB:</strong> <span class="text-primary"><?= htmlspecialchars($nama_file_master) ?></span>
                    </div>

                    <div id="boxStatus" class="status-box bg-warning text-dark shadow-sm">
                        ⏳ Menginisialisasi Face-API & Memuat Arsitektur Model...
                    </div>

                    <div class="row text-center">
                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-secondary mb-2">1. Foto Master (Database)</h6>
                            <div class="media-box" style="background: #fafafa;">
                                <?php 
                                $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
                                $url_foto = $actual_link . "/SIS/assets/img/pengguna/" . $nama_file_master;
                                ?>
                                <img id="fotoMaster" src="<?= $url_foto ?>" alt="Foto Master Gagal Load">
                            </div>
                            <div id="statusMaster" class="badge bg-secondary mt-2">Menunggu ekstraksi gambar...</div>
                        </div>

                        <div class="col-md-6 mb-4">
                            <h6 class="fw-bold text-secondary mb-2">2. Kamera Kamera Live (Webcam)</h6>
                            <div class="media-box">
                                <video id="webcam" autoplay muted></video>
                            </div>
                            <div id="statusKamera" class="badge bg-secondary mt-2">Kamera belum aktif</div>
                        </div>
                    </div>

                    <div class="row justify-content-center mt-2">
                        <div class="col-md-8">
                            <div id="hasilKomparasi" class="match-indicator bg-light text-muted border text-center">
                                Menunggu Perhitungan Vektor Wajah... 🤔
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<script src="assets/js/face-api.min.js"></script>
<script>
    let faceMatcher = null;
    let streamWebcam = null;
    let intervalScan = null;

    document.addEventListener("DOMContentLoaded", async function() {
        const boxStatus = document.getElementById('boxStatus');
        const imgElement = document.getElementById('fotoMaster');
        const statusMaster = document.getElementById('statusMaster');

        if (!imgElement) return;

        try {
            const currentHost = window.location.hostname;
            const baseUrlModels = `http://${currentHost}/SIS/assets/models`;

            // 1. Load semua model Face-API yang dibutuhkan
            await faceapi.nets.ssdMobilenetv1.loadFromUri(baseUrlModels);
            await faceapi.nets.faceLandmark68Net.loadFromUri(baseUrlModels);
            await faceapi.nets.faceRecognitionNet.loadFromUri(baseUrlModels);

            boxStatus.className = "status-box bg-info text-white shadow-sm";
            boxStatus.innerText = "🔍 Model AI sukses dimuat. Mengekstrak biometrik Foto Master...";

            // 2. Pastikan gambar ter-load sempurna lalu jadikan acuan FaceMatcher
            if (imgElement.complete) {
                await inisialisasiFaceMatcher(imgElement, statusMaster, boxStatus);
            } else {
                imgElement.onload = async function() {
                    await inisialisasiFaceMatcher(imgElement, statusMaster, boxStatus);
                };
            }

        } catch (error) {
            console.error(error);
            boxStatus.className = "status-box bg-danger text-white shadow-sm";
            boxStatus.innerText = "❌ Gagal memuat modul/model Face-API. Cek console log.";
        }
    });

    async function inisialisasiFaceMatcher(imgElement, statusMaster, boxStatus) {
        // Deteksi wajah tunggal dari foto DB lengkap dengan landmark & descriptor
        const d_master = await faceapi.detectSingleFace(imgElement).withFaceLandmarks().withFaceDescriptor();
        
        if (d_master) {
            statusMaster.className = "badge bg-success mt-2";
            statusMaster.innerText = "🟢 Ter-ekstraksi (Valid)";
            
            // Buat objek pencocokan wajah dengan threshold toleransi eror standard 0.6
            // Semakin kecil angka threshold (misal 0.4), pencocokan akan semakin ketat
            faceMatcher = new faceapi.FaceMatcher(d_master, 0.6);
            
            boxStatus.className = "status-box bg-primary text-white shadow-sm";
            boxStatus.innerText = "📷 Foto Master siap! Menyalakan kamera webcam untuk komparasi...";
            
            startWebcam();
        } else {
            statusMaster.className = "badge bg-danger mt-2";
            statusMaster.innerText = "❌ AI Gagal Membaca Wajah di Foto Ini";
            boxStatus.className = "status-box bg-danger text-white shadow-sm";
            boxStatus.innerText = "❌ Eror: Foto Master di DB terdeteksi buram/tidak terbaca oleh AI.";
        }
    }

    function startWebcam() {
        const video = document.getElementById('webcam');
        const statusKamera = document.getElementById('statusKamera');

        navigator.mediaDevices.getUserMedia({ video: { facingMode: "user" } })
            .then(stream => {
                streamWebcam = stream;
                video.srcObject = stream;
                statusKamera.className = "badge bg-success mt-2";
                statusKamera.innerText = "🟢 Kamera Aktif (Scanning...)";
                
                // Mulai lakukan komparasi berkala setiap 600ms
                jalankanLiveMatcher(video);
            })
            .catch(err => {
                console.error(err);
                statusKamera.className = "badge bg-danger mt-2";
                statusKamera.innerText = "❌ Kamera Gagal Diakses";
            });
    }

    function jalankanLiveMatcher(video) {
        const hasilKomparasi = document.getElementById('hasilKomparasi');

        intervalScan = setInterval(async () => {
            if (video.paused || video.ended || !faceMatcher) return;

            // Deteksi wajah yang ada di depan webcam
            const d_live = await faceapi.detectSingleFace(video).withFaceLandmarks().withFaceDescriptor();

            if (d_live) {
                // Cocokkan descriptor wajah webcam dengan faceMatcher foto master
                const hasilCocok = faceMatcher.findBestMatch(d_live.descriptor);
                
                // Jarak/Distance: Semakin dekat ke angka 0, artinya wajah semakin super mirip/identik
                const nilaiJarak = hasilCocok.distance; 
                const akurasiPersen = ((1 - nilaiJarak) * 100).toFixed(1);

                // Jika label mengembalikan "unknown", berarti wajah dinilai BERBEDA jauh (melewati batas threshold)
                if (hasilCocok.label === "unknown") {
                    hasilKomparasi.className = "match-indicator bg-danger text-white shadow shadow-sm";
                    hasilKomparasi.innerHTML = `❌ WAJAH BERBEDA! (${akurasiPersen}% Mirip)<br><span style='font-size:14px; font-weight:400;'>Nilai Jarak Jauh: ${nilaiJarak.toFixed(3)} (Batas Toleransi: &lt; 0.600)</span>`;
                } else {
                    hasilKomparasi.className = "match-indicator bg-success text-white shadow shadow-sm";
                    hasilKomparasi.innerHTML = `🟢 WAJAH SAMA / COCOK! (${akurasiPersen}% Mirip)<br><span style='font-size:14px; font-weight:400;'>Siswa Terverifikasi Valid (Distance: ${nilaiJarak.toFixed(3)})</span>`;
                }
            } else {
                hasilKomparasi.className = "match-indicator bg-warning text-dark border text-center";
                hasilKomparasi.innerHTML = "⚠️ Hadapkan wajah Anda tegak lurus ke kamera...";
            }
        }, 600);
    }
</script>
</body>
</html>