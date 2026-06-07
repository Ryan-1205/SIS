<?php 
session_start();
include 'koneksi.php';

// Menangani request AJAX untuk verifikasi akun tahap pertama (Nama & NIS)
if (isset($_POST['action']) && $_POST['action'] === 'cek_kredensial') {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis  = mysqli_real_escape_string($conn, $_POST['nis']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE nama_lengkap = '$nama' AND nis = '$nis'");
    $cek = mysqli_num_rows($query);

    if ($cek > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Simpan data mentah sementara ke dalam array response JSON
        echo json_encode([
            'status' => 'success',
            'role' => $data['role'],
            'foto_resmi' => $data['foto_resmi']
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kombinasi Nama Lengkap atau NIS tidak valid.'
        ]);
    }
    exit;
}

// Menangani submission final setelah verifikasi biometrik wajah selesai (Khusus Admin atau Siswa otomatis)
if (isset($_POST['final_login'])) {
    $nama = mysqli_real_escape_string($conn, $_POST['nama']);
    $nis  = mysqli_real_escape_string($conn, $_POST['nis']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE nama_lengkap = '$nama' AND nis = '$nis'");
    if (mysqli_num_rows($query) > 0) {
        $data = mysqli_fetch_assoc($query);
        
        // Inisialisasi session resmi pengguna
        $_SESSION['id_user'] = $data['id_user'];
        $_SESSION['role'] = $data['role'];
        $_SESSION['nama_lengkap'] = $data['nama_lengkap'];
        $_SESSION['foto_resmi'] = $data['foto_resmi'];
        
        $role_user = $data['role'];
        $id_lab_mapped = 1;

        if ($role_user == 'admin_timber') {
            $id_lab_mapped = 1;
        } elseif ($role_user == 'admin_dkv1') {
            $id_lab_mapped = 2;
        } elseif ($role_user == 'admin_dkv2') {
            $id_lab_mapped = 3;
        } elseif ($role_user == 'admin_animasi') {
            $id_lab_mapped = 4;
        }

        $_SESSION['id_kategori'] = $id_lab_mapped;
        
        if (strpos($role_user, 'admin') !== false) {
            header("Location: admin/admin_barang.php"); 
        } else {
            header("Location: index.php?msg=welcome"); 
        }
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - SIS</title>
    <link rel="stylesheet" href="assets/bootstrap-5.3.8-dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2.9">
    <script defer src="https://cdn.jsdelivr.net/npm/@vladmandic/face-api/dist/face-api.js"></script>
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
        .link-bantuan {
            color: var(--tosca-tua);
            font-size: 13px;
            text-decoration: none;
            display: block;
            text-align: right;
            margin-top: -10px;
            margin-bottom: 20px;
            font-weight: 600;
            cursor: pointer;
        }
        .link-bantuan:hover {
            text-decoration: underline;
        }
        #webcam-container {
            position: relative;
            width: 100%;
            max-width: 320px;
            margin: 0 auto 15px auto;
            border-radius: 10px;
            overflow: hidden;
            border: 3px solid var(--tosca-tua);
            display: none;
        }
        #video-feed {
            width: 100%;
            height: auto;
            transform: scaleX(-1);
        }
        #canvas-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            transform: scaleX(-1);
        }
    </style>
</head>
<body>
    <?php include 'components/header.php'; ?>

    <div class="safe-container login-wrapper" style="min-height: 80vh; display: flex; align-items: center; justify-content: center;">
        <div class="login-card shadow-sm" style="background-color: var(--tosca-muda); border-radius: 15px; padding: 40px 50px; width: 100%; max-width: 400px; text-align: center;">
            <h2 id="login-title" style="color: var(--tosca-tua); font-weight: 800; font-size: 28px; margin-bottom: 30px;">Login Sistem</h2>
            
            <div id="alert-container"></div>

            <div id="webcam-container">
                <video id="video-feed" autoplay muted playsinline></video>
                <canvas id="canvas-overlay"></canvas>
            </div>
            <p id="status-scan" class="fw-bold small text-muted" style="display: none;">Inisialisasi Pemindai Wajah...</p>

            <form id="form-login-main" action="" method="POST">
                <div id="input-fields-group">
                    <input type="text" id="nama" name="nama" class="login-input" placeholder="Nama Lengkap" autocomplete="off" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 20px; width: 100%;">
                    <input type="text" id="nis" name="nis" class="login-input" placeholder="Nomor Induk Siswa (NIS)" required style="border-radius: 10px; padding: 12px 20px; margin-bottom: 15px; width: 100%;">
                    
                    <span class="link-bantuan" data-bs-toggle="modal" data-bs-target="#modalBantuanLogin">Butuh Bantuan?</span>
                </div>
                
                <input type="hidden" name="final_login" value="1">
                <button type="submit" id="btn-submit-action" class="btn-login" style="background-color: var(--tosca-tua); color: white; font-weight: 700; width: 100%; padding: 12px; border-radius: 10px; border: none; margin-top: 10px;">Masuk</button>
            </form>
            
            <a href="register.php" id="link-daftar" class="login-link" style="color: var(--tosca-tua); text-decoration: none; font-size: 14px; display: block; margin-top: 20px;">Belum Punya Akun? <strong style="font-weight: 800;">Daftar</strong></a>
        </div>
    </div>

    <div class="modal fade" id="modalBantuanLogin" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border: 2px solid var(--tosca-tua); border-radius: 20px; text-align: left;">
                <div class="modal-header text-white" style="background-color: var(--tosca-tua); border-top-left-radius: 17px; border-top-right-radius: 17px;">
                    <h5 class="modal-title fw-bold">Panduan Akses Masuk</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-center">
                    <h5 class="fw-bold mt-2 mb-3" style="color: var(--tosca-tua);">Hubungi Layanan Pengurus Lab</h5>
                    <p class="text-muted fs-6 mb-4">Jika NIS Anda tidak dikenali oleh sistem, silakan hubungi teknisi atau pengawas laboratorium yang sedang bertugas.</p>
                </div>
                <div class="modal-footer border-0 justify-content-center pb-4">
                    <button type="button" class="btn text-white px-5 rounded-pill" style="background-color: var(--tosca-tua); font-weight: 600;" data-bs-dismiss="modal">Dimengerti</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/bootstrap-5.3.8-dist/js/bootstrap.bundle.min.js"></script>

    <script>
        const form = document.getElementById('form-login-main');
        const inputGroup = document.getElementById('input-fields-group');
        const btnSubmit = document.getElementById('btn-submit-action');
        const titleLogin = document.getElementById('login-title');
        const linkDaftar = document.getElementById('link-daftar');
        const alertContainer = document.getElementById('alert-container');
        
        const webcamContainer = document.getElementById('webcam-container');
        const videoFeed = document.getElementById('video-feed');
        const canvasOverlay = document.getElementById('canvas-overlay');
        const statusScan = document.getElementById('status-scan');

        let localStream = null;
        let scanInterval = null;

        form.addEventListener('submit', async function(e) {
            // Jika bidang input masih terlihat, tahan pengiriman form untuk validasi AJAX
            if (inputGroup.style.display !== 'none') {
                e.preventDefault();
                alertContainer.innerHTML = '';

                const formData = new FormData();
                formData.append('action', 'cek_kredensial');
                formData.append('nama', document.getElementById('nama').value);
                formData.append('nis', document.getElementById('nis').value);

                try {
                    const response = await fetch('', { method: 'POST', body: formData });
                    const resData = await response.json();

                    if (resData.status === 'success') {
                        // Jika hak akses bukan admin, abaikan biometrik langsung lolos
                        if (!resData.role.includes('admin')) {
                            form.submit();
                            return;
                        }

                        // Jika akun terdeteksi sebagai Admin, aktifkan subsistem kamera Face-API
                        jalankanScannerBiometrik(resData.foto_resmi);
                    } else {
                        alertContainer.innerHTML = `<div class="alert alert-danger py-2 small text-start">${resData.message}</div>`;
                    }
                } catch (err) {
                    alertContainer.innerHTML = `<div class="alert alert-danger py-2 small text-start">Koneksi server gagal terhubung.</div>`;
                }
            }
        });

        async function jalankanScannerBiometrik(namaFotoMaster) {
            // Sembunyikan elemen input form standar
            inputGroup.style.display = 'none';
            linkDaftar.style.display = 'none';
            btnSubmit.style.display = 'none';
            
            // Tampilkan layar antarmuka scanner
            titleLogin.innerText = 'Verifikasi 2FA';
            webcamContainer.style.display = 'block';
            statusScan.style.display = 'block';

            const currentHost = window.location.hostname;
            const pathModels = `http://${currentHost}/SIS/assets/models`;
            const pathMasterUser = `http://${currentHost}/SIS/assets/img/pengguna/${namaFotoMaster}`;

            try {
                // Unduh bobot arsitektur model neural network Face-API
                await faceapi.nets.ssdMobilenetv1.loadFromUri(pathModels);
                await faceapi.nets.faceLandmark68Net.loadFromUri(pathModels);
                await faceapi.nets.faceRecognitionNet.loadFromUri(pathModels);

                statusScan.innerText = 'Memuat Foto Acuan...';
                
                // Ambil descritor vektor wajah master admin
                const imgMaster = await faceapi.fetchImage(pathMasterUser);
                const deskriptorMaster = await faceapi.detectSingleFace(imgMaster)
                                            .withFaceLandmarks()
                                            .withFaceDescriptor();

                if (!deskriptorMaster) {
                    alertContainer.innerHTML = `<div class="alert alert-warning py-2 small text-start">Foto master di server tidak terbaca. Hubungi IT Pengurus Lab.</div>`;
                    resetFormState();
                    return;
                }

                const faceMatcher = new faceapi.FaceMatcher(deskriptorMaster, 0.55);
                statusScan.innerText = 'Mengaktifkan Webcam Anda...';

                // Hidupkan aliran video webcam laptop
                localStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 } });
                videoFeed.srcObject = localStream;

                videoFeed.onloadedmetadata = () => {
                    statusScan.innerText = 'Pindai Wajah Sedang Berlangsung...';
                    
                    const displaySize = { width: videoFeed.videoWidth, height: videoFeed.videoHeight };
                    faceapi.matchDimensions(canvasOverlay, displaySize);

                    // Memulai interval pemindaian kecocokan wajah real-time
                    scanInterval = setInterval(async () => {
                        const deteksiLive = await faceapi.detectSingleFace(videoFeed)
                                                .withFaceLandmarks()
                                                .withFaceDescriptor();

                        if (deteksiLive) {
                            const hasilCocok = faceMatcher.findBestMatch(deteksiLive.descriptor);
                            
                            // Bersihkan & gambar ulang bounding box penanda wajah
                            const resizedDetections = faceapi.resizeResults(deteksiLive, displaySize);
                            canvasOverlay.getContext('2d').clearRect(0, 0, canvasOverlay.width, canvasOverlay.height);
                            faceapi.draw.drawDetections(canvasOverlay, resizedDetections);

                            // Jika label kecocokan bernilai valid (bukan unknown), verifikasi dinyatakan lulus!
                            if (hasilCocok.label !== 'unknown') {
                                statusScan.className = 'fw-bold small text-success';
                                statusScan.innerText = '🔒 Akses Admin Terverifikasi! Mengalihkan...';
                                
                                // Matikan siklus hardware webcam
                                clearInterval(scanInterval);
                                localStream.getTracks().forEach(track => track.stop());

                                // Kirim form menuju backend untuk inisialisasi session penuh
                                form.submit();
                            } else {
                                statusScan.innerText = 'Wajah Tidak Cocok. Silakan Posisikan Wajah dengan Benar.';
                            }
                        } else {
                            statusScan.innerText = 'Wajah Tidak Terdeteksi di Depan Kamera.';
                        }
                    }, 450);
                };

            } catch (error) {
                alertContainer.innerHTML = `<div class="alert alert-danger py-2 small text-start">Gagal menginisialisasi pustaka biometrik.</div>`;
                resetFormState();
            }
        }

        function resetFormState() {
            if (localStream) localStream.getTracks().forEach(track => track.stop());
            if (scanInterval) clearInterval(scanInterval);
            
            inputGroup.style.display = 'block';
            linkDaftar.style.display = 'block';
            btnSubmit.style.display = 'block';
            titleLogin.innerText = 'Login Sistem';
            webcamContainer.style.display = 'none';
            statusScan.style.display = 'none';
        }
    </script>
</body>
</html>