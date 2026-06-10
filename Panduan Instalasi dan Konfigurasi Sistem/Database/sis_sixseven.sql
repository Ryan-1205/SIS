-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 10, 2026 at 05:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sis_sixseven`
--

-- --------------------------------------------------------

--
-- Table structure for table `barang`
--

CREATE TABLE `barang` (
  `id_barang` int(11) NOT NULL,
  `kode_barang` varchar(50) DEFAULT NULL,
  `id_kategori` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `merk` varchar(50) DEFAULT NULL,
  `foto` varchar(255) DEFAULT 'default.jpg',
  `status` enum('tersedia','dipinjam','perbaikan') NOT NULL DEFAULT 'tersedia',
  `deskripsi` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `barang`
--

INSERT INTO `barang` (`id_barang`, `kode_barang`, `id_kategori`, `nama_barang`, `merk`, `foto`, `status`, `deskripsi`) VALUES
(27, 'SMK-VID-001', 1, 'Kamera Mirrorless Sony Alpha A6400 Kit', 'Sony', '1780842719_27.jpeg', 'dipinjam', 'Kamera mirrorless standar praktik siswa untuk materi dasar videografi dan vlog.'),
(28, 'SMK-VID-002', 1, 'Lensa Sony E 18-105mm f/4 G OSS Power Zoom', 'Sony', '1780842932_28.jpg', 'dipinjam', 'Lensa zoom motorized, sangat stabil untuk latihan pergerakan kamera video siswa.'),
(29, 'SMK-VID-003', 1, 'Drone DJI Neo Fly More Combo', 'DJI', '1780842979_29.jpg', 'dipinjam', 'Drone compact ringkas dan aman untuk praktik dasar pengambilan gambar udara siswa.'),
(30, 'SMK-VID-004', 1, 'Drone DJI Avata FPV Explorer Combo', 'DJI', '1780843082_30.jpg', 'tersedia', 'Unit drone FPV untuk modul pembelajaran sinematografi sudut ekstrim.'),
(31, 'SMK-VID-005', 1, 'Gimbal Stabilizer DJI Ronin-SC', 'DJI', '1780843134_31.jpg', 'perbaikan', 'Stabilizer kamera 3-axis untuk latihan kestabilan rekaman video cinematic lapangan.'),
(32, 'SMK-VID-006', 1, 'Wireless Microphone Saramonic Blink 500 B2', 'Saramonic', '1780843194_32.jpg', 'tersedia', 'Clip-on wireless dual-channel untuk praktik liputan berita dan wawancara siswa.'),
(33, 'SMK-VID-007', 1, 'Microphone Shotgun Rode VideoMicro', 'Rode', '1780843343_33.jpg', 'tersedia', 'Mikrofon harian portable mini untuk dipasang di atas kamera mirrorless praktik.'),
(34, 'SMK-VID-008', 1, 'Tripod Video Takara Vit-234 Fluid Head', 'Takara', '1780843392_34.jpg', 'tersedia', 'Tripod standar penyiaran dengan fluid head murah untuk latihan pan dan tilt rapi.'),
(35, 'SMK-VID-009', 1, 'Lampu LED Studio Godox SL60W Duo Kit', 'Godox', '1780843479_35.jpeg', 'tersedia', 'Sepasang lampu continuous LED 60W untuk pencahayaan dasar produksi video dalam ruangan.'),
(36, 'SMK-VID-010', 1, 'Softbox Godox SB-UE 80cm Grid Umbrella', 'Godox', '1780843580_36.jpg', 'tersedia', 'Penyebar cahaya model payung untuk melembutkan pancaran lampu LED studio.'),
(37, 'SMK-VID-011', 1, 'Audio Recorder Portable Zoom H1n Handy', 'Zoom', '1780843952_37.jpg', 'tersedia', 'Alat perekam suara portable untuk latihan pengambilan foley sound dan podcast siswa.'),
(38, 'SMK-VID-012', 1, 'Monitor Eksternal Feelworld F6 Plus 5.5 Inch', 'Feelworld', '1780844166_38.jpg', 'tersedia', 'Monitor on-camera tambahan membantu siswa memantau fokus dan komposisi frame.'),
(39, 'SMK-VID-013', 1, 'Switcher Video Blackmagic ATEM Mini', 'Blackmagic', '1780844253_39.jpg', 'tersedia', 'Alat perekam Multi-kamera portable untuk simulasi praktik live streaming broadcasting sekolah.'),
(40, 'SMK-VID-014', 1, 'Green Screen Kain Background Portable 3x3m', 'Generic', '1780844286_40.jpeg', 'tersedia', 'Latar kain hijau lengkap dengan stand untuk praktik materi visual effects (Chroma Key).'),
(41, 'SMK-VID-015', 1, 'Reflektor Cahaya 5-in-1 Bulat 110cm', 'Grip', '1780844359_41.jpeg', 'tersedia', 'Papan pemantul cahaya serbaguna untuk latihan tata cahaya outdoor matahari siang.'),
(42, 'SMK-VID-016', 1, 'Baterai Kamera Sony NP-FW50 Cadangan', 'Sony', '1780844402_42.jpg', 'tersedia', 'Baterai tambahan untuk mendukung durasi praktik lapangan videografi siswa.'),
(43, 'SMK-VID-017', 1, 'Memory Card SanDisk Extreme Pro 64GB 200MB/s', 'SanDisk', '1780844446_43.jpeg', 'tersedia', 'Kartu memori SDXC kecepatan tinggi pendukung perekaman video resolusi 4K tanpa interupsi.'),
(44, 'SMK-VID-018', 1, 'Koper Kamera Hardcase Safety Box Foam', 'Generic', '1780844472_44.jpeg', 'tersedia', 'Tas koper pelindung anti guncangan untuk mobilitas peminjaman alat ke luar sekolah.'),
(45, 'SMK-VID-019', 1, 'Stand Boom Microphone Takara', 'Takara', '1780844789_45.png', 'tersedia', 'Tiang penyangga mikrofon jarak jauh untuk kebutuhan shooting film pendek siswa.'),
(46, 'SMK-VID-020', 1, 'Headphone Monitor Audio Proel Eikon H800', 'Proel', '1780844943_46.png', 'tersedia', 'Headphone flat monitoring untuk siswa operator suara saat memantau kualitas audio.'),
(47, 'SMK-FTO-001', 2, 'Kamera DSLR Canon EOS 200D II Kit', 'Canon', '1780848168_47.jpg', 'tersedia', 'Kamera DSLR andalan sekolah untuk materi dasar pencahayaan dan eksposur fotografi.'),
(48, 'SMK-FTO-002', 2, 'Lensa Canon EF 50mm f/1.8 STM Portrait', 'Canon', '1780848225_48.jpg', 'dipinjam', 'Lensa prime sejuta umat untuk praktik foto produk dan portrait bokeh maksimal.'),
(49, 'SMK-FTO-003', 2, 'Lensa Sigma 17-50mm f/2.8 EX DC OS HSM', 'Sigma', '1780848270_49.jpg', 'tersedia', 'Lensa zoom bukaan lebar konstan untuk ketajaman foto dokumentasi acara sekolah.'),
(50, 'SMK-FTO-004', 2, 'Lampu Flash Studio Godox SK400II Strobe', 'Godox', '1780848336_50.jpeg', 'tersedia', 'Lampu kilat strobe studio 400W untuk modul utama tata cahaya studio komersial.'),
(51, 'SMK-FTO-005', 2, 'Wireless Flash Trigger Godox X2T Canon', 'Godox', '1780848396_51.jpg', 'tersedia', 'Trigger pemancar nirkabel untuk sinkronisasi jepretan kamera dengan flash studio.'),
(52, 'SMK-FTO-006', 2, 'Speedlight Flash Eksternal Godox TT600', 'Godox', '1780848441_52.jpg', 'tersedia', 'Lampu flash eksternal hotshoe untuk praktik materi teknik strobist lapangan.'),
(53, 'SMK-FTO-007', 2, 'Softbox Parabolik Godox SB-GUE 80cm Octa', 'Godox', '1780848541_53.jpg', 'tersedia', 'Softbox oktagonal payung untuk menghasilkan pencahayaan portrait manusia yang lembut.'),
(54, 'SMK-FTO-008', 2, 'Tripod Fotografi Beike Q999H Horizontal Stand', 'Beike', '1780848608_54.jpeg', 'tersedia', 'Tripod kokoh dengan tiang tengah yang bisa ditekuk horizontal untuk foto flat-lay produk.'),
(55, 'SMK-FTO-009', 2, 'Light Tent Mini Boks Studio Foto Produk 40cm', 'Generic', '1780848819_55.png', 'tersedia', 'Tenda studio mini berlampu LED terintegrasi untuk latihan foto produk katalog komersial.'),
(56, 'SMK-FTO-010', 2, 'Background Kertas Seamless Roll 2.72 x 11m', 'Superior', '1780848912_56.png', 'tersedia', 'Gulungan kertas latar foto studio tanpa sambungan untuk keseragaman warna pas foto.'),
(57, 'SMK-FTO-011', 2, 'Stand Background Manual Tipe Chain Gantung', 'Generic', '1780849041_57.png', 'tersedia', 'Penyangga besi gantung berantai dinding untuk menggulung background foto studio.'),
(58, 'SMK-FTO-012', 2, 'Trigger Receiver Godox X1R Paket Tambahan', 'Godox', '1780849109_58.jpg', 'tersedia', 'Alat penangkap sinyal nirkabel tambahan untuk mengaktifkan flash eksternal jadul.'),
(59, 'SMK-FTO-013', 2, 'Light Meter Sekonic L-308X Flashmate', 'Sekonic', '1780849163_59.jpg', 'tersedia', 'Alat pengukur kekuatan cahaya kilat instan studio untuk akurasi setting manual.'),
(60, 'SMK-FTO-014', 2, 'Payung Studio Translucent White Reflector 33\"', 'Godox', '1780849280_60.jpg', 'tersedia', 'Payung studio putih tembus cahaya untuk meratakan sebaran kilatan flash studio.'),
(61, 'SMK-FTO-015', 2, 'Beauty Dish Bowens Mount 42cm + Grid', 'Generic', '1780849389_61.jpg', 'tersedia', 'Modifier lampu studio berkarakter kontras tinggi untuk modul fotografi model / fashion.'),
(62, 'SMK-FTO-016', 2, 'Dry Box Kamera Portable Ruggard 30L', 'Ruggard', '1780849417_62.jpeg', 'tersedia', 'Kotak penyimpanan kedap udara elektronik pelindung lensa kamera dari jamur akibat lembab.'),
(63, 'SMK-FTO-017', 2, 'Blower Pembersih Lensa VSGO Hurricane', 'VSGO', '1780849514_63.png', 'tersedia', 'Pompa udara pembersih debu fisik pada sensor body kamera dan elemen kaca lensa.'),
(64, 'SMK-FTO-018', 2, 'Kabel Tethering Tether Tools USB 2.0 to Mini', 'Tether Tools', '1780849551_64.jpg', 'tersedia', 'Kabel data 4.6 meter untuk melihat hasil jepretan kamera langsung di layar PC monitor.'),
(65, 'SMK-FTO-019', 2, 'Snoot Kerucut Bowens Mount Studio Flash', 'Generic', '1780849582_65.jpeg', 'tersedia', 'Aksesoris corong lampu studio untuk mempersempit cahaya terpusat pada aksen rambut.'),
(66, 'SMK-FTO-020', 2, 'Papan Akrilik Reflektor Alas Foto Produk 30cm', 'Generic', '1780849635_66.jpg', 'tersedia', 'Alas akrilik kilap efek cermin (hitam & putih) untuk estetika foto aksesoris / jam tangan.'),
(67, 'SMK-DKV-001', 3, 'Pen Tablet Wacom Intuos Small Creative', 'Wacom', '1780849712_67.jpeg', 'tersedia', 'Tablet gambar standar awal untuk siswa latihan tracing digital vector logo dan sketsa.'),
(68, 'SMK-DKV-002', 3, 'Pen Tablet Huion Inspiroy H640P Wireless', 'Huion', '1780849760_68.jpg', 'tersedia', 'Pen tablet nirkabel ekonomis responsif untuk materi ilustrasi digital harian.'),
(69, 'SMK-DKV-003', 3, 'Pen Display XP-Pen Artist 12 Pro Screen', 'XP-Pen', '1780849829_69.jpg', 'tersedia', 'Monitor pen display menggambar langsung di layar untuk mengasah detail pewarnaan digital.'),
(70, 'SMK-DKV-004', 3, 'Stylus Pencil Universal Baseus Capacitive', 'Baseus', '1780849863_70.jpeg', 'tersedia', 'Pena stylus penunjang desain coretan sketsa kasar layout pada tablet Android sekolah.'),
(71, 'SMK-DKV-005', 3, 'Printer Inkjet Epson L1300 Ink Tank A3', 'Epson', '1780849893_71.jpg', 'tersedia', 'Printer sistem tangki tinta ukuran lebar A3 untuk cetak portofolio layout poster siswa.'),
(72, 'SMK-DKV-006', 3, 'Scanner Flatbed Canon Canoscan LiDE 300', 'Canon', '1780849929_72.jpg', 'tersedia', 'Alat pemindai dokumen flatbed resolusi tinggi untuk konversi sketsa manual ke PC.'),
(73, 'SMK-DKV-007', 3, 'Pantone Color Bridge Coated Guide Book', 'Pantone', '1780849978_73.jpg', 'tersedia', 'Katalog buku panduan standardisasi pencocokan warna cetak industri dan desain corporate identity.'),
(74, 'SMK-DKV-008', 3, 'Paper Cutter Mesin Pemotong Kertas A4', 'Joyko', '1780850023_74.jpeg', 'tersedia', 'Alat pemotong kertas manual presisi untuk modul praktek mockup lipat packaging produk.'),
(75, 'SMK-DKV-009', 3, 'Papan Tracing LED Light Box Pad Ukuran A4', 'Generic', '1780850128_75.png', 'tersedia', 'Papan meja gambar tipis berlampu LED pembantu menjiplak sketsa anatomi / tipografi manual.'),
(76, 'SMK-DKV-010', 3, 'Monitor Desain ViewSonic VP2456 sRGB ProArt', 'ViewSonic', '1780850187_76.jpg', 'tersedia', 'Monitor PC kalibrasi pabrik akurasi tinggi sRGB 100% anti penyimpangan warna desain cetak.'),
(77, 'SMK-DKV-011', 3, 'Mouse Ergonomis Wireless Logitech M720 Triathlon', 'Logitech', '1780850337_77.jpg', 'tersedia', 'Mouse nirkabel multi-device akurasi tinggi penghemat waktu pengeditan layout halaman.'),
(78, 'SMK-DKV-012', 3, 'Mesin Laminating GBC CLA402 Roll Panas', 'GBC', '1780850471_78.jpg', 'tersedia', 'Mesin laminasi panas dingin skala kelas pelindung permukaan cetak ID card / stiker.'),
(79, 'SMK-DKV-013', 3, 'Pen Punch Lubang Ring Plat Kartu Nama', 'Generic', '1780850656_79.jpeg', 'tersedia', 'Alat pembolong plat besi sudut siku untuk kebutuhan finishing cetak kreatif portofolio siswa.'),
(80, 'SMK-DKV-014', 3, 'Penggaris Besi Stainless Steel Heavy Duty 60cm', 'Joyko', '1780850714_80.jpg', 'tersedia', 'Mistar besi baja panjang pengaman jemari tangan siswa saat memotong bahan kertas tebal.'),
(81, 'SMK-DKV-015', 3, 'Cutting Mat Alas Potong Kertas Ukuran A2 Rubber', 'Joyko', '1780850776_81.png', 'tersedia', 'Alas potong berbahan karet elastis self-healing penahan goresan mata pisau cutter.'),
(82, 'SMK-DKV-016', 3, 'X-Acto Pen Knife Cutter Set Pisau Ukir', 'X-Acto', '1780850817_82.jpeg', 'tersedia', 'Pisau ukir model pena dengan mata pisau bervariasi untuk detail kerajinan stiker/papercraft.'),
(83, 'SMK-DKV-017', 3, 'Drawing Pen Sakura Pigma Micron Set (005 - 08)', 'Sakura', '1780850893_83.jpg', 'tersedia', 'Paket pena gambar tinta pigmen tahan air untuk pembuatan outline komik manual.'),
(84, 'SMK-DKV-018', 3, 'Harddisk Eksternal Seagate One Touch 1TB USB 3.0', 'Seagate', '1780850932_84.jpg', 'tersedia', 'Media penyimpanan backup massal kolektif file tugas proyek akhir DKV siswa kelas XII.'),
(85, 'SMK-DKV-019', 3, 'Digital Jangka Sorong Kaliper LCD 150mm', 'Generic', '1780850963_85.jpg', 'tersedia', 'Alat ukur milimeter digital untuk kalkulasi presisi bentangan mockup pisau kemasan produk.'),
(86, 'SMK-DKV-020', 3, 'Kamera Pocket Canon Ixus 285 HS Digital', 'Canon', '1780850994_86.jpeg', 'tersedia', 'Kamera saku operasional cepat siswa untuk berburu objek referensi tekstur / aset desain.'),
(87, 'SMK-ANM-001', 4, 'Pen Display Wacom Cintiq 16 Creative Monitor', 'Wacom', '1780851065_87.jpeg', 'tersedia', 'Monitor gambar pen display andalan lab animasi untuk modul modeling, rigging, dan animating.'),
(88, 'SMK-ANM-002', 4, 'Pen Tablet Huion Kamvas Pro 13 2.5K Screen', 'Huion', '1780851089_88.jpg', 'dipinjam', 'Interactive screen tablet beresolusi tajam penunjang detail menggambar frame-by-frame 2D.'),
(89, 'SMK-ANM-003', 4, 'Meja Animasi Tradisional Lightbox Kayu Kayu', 'Generic', '1780851553_89.jpg', 'tersedia', 'Meja gambar lampu kayu kaca tradisional untuk modul dasar pembelajaran prinsip animasi 2D.'),
(90, 'SMK-ANM-004', 4, '3D Connection SpaceMouse Compact Navigation', '3connexion', '1780851412_90.jpeg', 'dipinjam', 'Mouse navigasi viewport 3D taktil mempermudah siswa saat mengontrol kamera Blender / Maya.'),
(91, 'SMK-ANM-005', 4, 'Controller Macro TourBox Lite Shortcut Pad', 'TourBox', '1780851588_91.jpeg', 'dipinjam', 'Konsol tombol shortcut tangan kiri membantu siswa mempercepat pengerjaan digital sculpting.'),
(92, 'SMK-ANM-006', 4, 'Printer 3D Creality Ender-3 V3 SE FDM', 'Creality', '1780851637_92.jpg', 'tersedia', 'Printer 3D filamen untuk mencetak fisik hasil akhir karya modeling aset karakter 3D siswa.'),
(93, 'SMK-ANM-007', 4, 'GPU Eksternal Enclosure Razer Core X Chroma', 'Razer', '1780851695_93.jpg', 'tersedia', 'Dudukan kartu grafis tambahan eksternal untuk mempercepat akselerasi render laptop guru/siswa.'),
(94, 'SMK-ANM-008', 4, 'Graphic Card ASUS Dual GeForce RTX 3060 12GB', 'ASUS', '1780851752_94.jpg', 'tersedia', 'VRAM besar 12GB sangat optimal untuk kebutuhan kelancaran baking texture dan rendering Cycles.'),
(95, 'SMK-ANM-009', 4, 'Kacamata VR Headset Meta Quest 2 128GB Standalone', 'Meta', '1780851794_95.jpeg', 'tersedia', 'Media pengujian preview hasil akhir animasi lingkungan 3D berbasis Virtual Reality sekolah.'),
(96, 'SMK-ANM-010', 4, 'Mikrofon Kondensor Samson C01U Pro USB Studio', 'Samson', '1780851830_96.jpeg', 'tersedia', 'Microphone USB plug-and-play untuk latihan pengisian suara dialog karakter animasi (*dubbing*).'),
(97, 'SMK-ANM-011', 4, 'Pop Filter Microphone Dual Layer Stand Shield', 'Generic', '1780851901_97.jpeg', 'tersedia', 'Saringan udara penahan desis angin hembusan napas untuk kejernihan rekaman suara dubbing.'),
(98, 'SMK-ANM-012', 4, 'Audio Interface Focusrite Scarlett Solo 3rd Gen', 'Focusrite', '1780851959_98.jpg', 'tersedia', 'Soundcard eksternal penunjang modul sound design pencampuran instrumen musik animasi.'),
(99, 'SMK-ANM-013', 4, 'Speaker Monitor Aktif Mackie CR3-X Multimedia', 'Mackie', '1780851985_99.jpeg', 'tersedia', 'Sepasang speaker monitor studio suara flat jernih untuk ketepatan sinkronisasi audio foley.'),
(100, 'SMK-ANM-014', 4, 'Headphone Monitoring Audio Technica ATH-M20x', 'Audio-Technica', '1780852090_100.png', 'tersedia', 'Headphone flat andalan lab untuk merancang tata suara latar belakang film animasi.'),
(101, 'SMK-ANM-015', 4, 'UPS Pengaman Listrik Prolink PRO1201S 1200VA', 'Prolink', '1780852159_101.jpg', 'tersedia', 'Penyimpan daya listrik darurat pencegah PC mati mendadak saat siswa mengantre proses rendering.'),
(102, 'SMK-ANM-016', 4, 'Meja Gambar Arsitektur Meja Draftsman Ajustable', 'Generic', '1780852464_102.jpeg', 'tersedia', 'Meja gambar dengan kemiringan papan papan fleksibel khusus latihan sketsa key-pose animasi manual.'),
(103, 'SMK-ANM-017', 4, 'Manekin Kayu Artikulasi Tubuh Manusia Model 30cm', 'Generic', '1780852663_103.jpeg', 'tersedia', 'Boneka kayu bersendi gerak sebagai alat bantu acuan visual siswa belajar pose gesture anatomi.'),
(104, 'SMK-ANM-018', 4, 'Stopwatch Digital Chronograph Stopwatch Sport', 'Casio', '1780852690_104.jpeg', 'tersedia', 'Alat hitung detik manual pengukur ketepatan durasi waktu gerak akting riel (*timing & spacing*).'),
(105, 'SMK-ANM-019', 4, 'Clay Plastisin Modeling Wax Sculpey Clay 500g', 'Sculpey', '1780852727_105.jpeg', 'tersedia', 'Lilin tanah liat industri khusus praktik dasar materi pengenalan volume bentuk karakter 3D.'),
(106, 'SMK-ANM-020', 4, 'Chroma Key Suit Baju Kostum Hijau MoCap Manual', 'Generic', '1780852754_106.jpeg', 'tersedia', 'Baju ketat warna hijau polos untuk membantu tracking manual referensi gerak akting tubuh animator.');

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `nama_kategori`) VALUES
(1, 'Tim Berangkat'),
(2, 'Lab DKV 1'),
(3, 'Lab DKV 2'),
(4, 'Lab Animasi');

-- --------------------------------------------------------

--
-- Table structure for table `peminjaman`
--

CREATE TABLE `peminjaman` (
  `id_pinjam` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_barang` int(11) NOT NULL,
  `tgl_pinjam` datetime DEFAULT NULL,
  `tgl_kembali_rencana` datetime DEFAULT NULL,
  `tgl_kembali_asli` datetime DEFAULT NULL,
  `keperluan` varchar(255) DEFAULT NULL,
  `no_hp` varchar(20) NOT NULL,
  `status_pengajuan` enum('pending','disetujui','ditolak','pending_kembali','kembali') NOT NULL DEFAULT 'pending',
  `diverifikasi_oleh` varchar(100) DEFAULT NULL,
  `bukti_wajah` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `peminjaman`
--

INSERT INTO `peminjaman` (`id_pinjam`, `id_user`, `id_barang`, `tgl_pinjam`, `tgl_kembali_rencana`, `tgl_kembali_asli`, `keperluan`, `no_hp`, `status_pengajuan`, `diverifikasi_oleh`, `bukti_wajah`) VALUES
(75, 143, 88, '2026-06-11 09:26:00', '2026-06-25 09:26:00', NULL, 'pengenn minjem aja', '1245785', 'disetujui', 'adminsuper', 'bukti_pinjam_143_1780885629.jpg'),
(76, 129, 28, '2026-06-08 09:51:00', '2026-06-11 09:51:00', NULL, 'Otomatis Ditolak: Barang sudah disetujui untuk peminjam lain.', '12', 'ditolak', 'adminsuper', 'bukti_pinjam_129_1780887092.jpg'),
(77, 129, 91, '2026-06-08 09:51:00', '2026-06-11 09:51:00', NULL, 'ya', '12', 'disetujui', 'adminsuper', 'bukti_pinjam_129_1780887092.jpg'),
(78, 33, 29, '2026-06-08 10:02:00', '2026-06-09 10:02:00', NULL, 'ya', '12', 'disetujui', 'adminsuper', 'bukti_pinjam_33_1780887749.jpg'),
(79, 33, 90, '2026-06-08 10:02:00', '2026-06-09 10:02:00', NULL, 'ya', '12', 'disetujui', 'adminsuper', 'bukti_pinjam_33_1780887749.jpg'),
(80, 146, 27, '2026-06-09 12:09:00', '2026-06-19 12:09:00', '2026-06-09 13:33:58', 'apa aja dah', '123456789', 'kembali', 'adminsuper', 'bukti_pinjam_146_1780981811.jpg'),
(81, 32, 27, '2026-06-09 13:21:00', '2026-06-09 13:21:00', NULL, 'Otomatis Ditolak: Barang sudah dibawa peminjam lain.', '123', 'ditolak', 'adminsuper', 'bukti_pinjam_32_1780986080.jpg'),
(82, 32, 28, '2026-06-09 13:21:00', '2026-06-09 13:21:00', '2026-06-09 13:33:45', 'yaya', '123', 'kembali', 'adminsuper', 'bukti_pinjam_32_1780986080.jpg'),
(83, 32, 47, '2026-06-09 13:21:00', '2026-06-09 13:21:00', '2026-06-09 13:33:45', 'yaya', '123', 'kembali', 'adminsuper', 'bukti_pinjam_32_1780986080.jpg'),
(84, 129, 28, '2026-06-09 13:59:00', '2026-06-10 13:59:00', NULL, 'ya', '123', 'disetujui', 'adminsuper', 'bukti_pinjam_129_1780988377.jpg'),
(85, 152, 27, '2026-06-09 14:23:00', '2026-06-18 14:23:00', NULL, 'asdfgh', '123456', 'disetujui', 'ryan timber', 'bukti_pinjam_152_1780989839.jpg'),
(86, 153, 27, '2026-06-09 14:25:00', '2026-06-11 14:25:00', NULL, 'Otomatis Ditolak: Barang sudah disetujui untuk peminjam lain.', '1234', 'ditolak', 'ryan timber', 'bukti_pinjam_153_1780989947.jpg'),
(87, 153, 48, '2026-06-09 14:25:00', '2026-06-15 14:25:00', NULL, 'cvbnm,', '1234', 'disetujui', 'adminsuper', 'bukti_pinjam_153_1780989970.jpg'),
(88, 154, 30, '2026-06-09 14:27:00', '2026-06-12 14:27:00', '2026-06-09 14:30:05', 'anjay', '5258', 'kembali', 'adminsuper', 'bukti_pinjam_154_1780990094.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nis` varchar(50) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `role` enum('admin','siswa','admin_timber','admin_dkv1','admin_dkv2','admin_animasi') NOT NULL DEFAULT 'siswa',
  `foto_resmi` varchar(255) DEFAULT 'default_user.jpg',
  `id_kategori` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nis`, `nama_lengkap`, `role`, `foto_resmi`, `id_kategori`) VALUES
(30, '1', 'adminsuper', 'admin', 'user_master_1780833109_30.jpeg', 1),
(32, '111', 'ryan', 'siswa', 'user_master_1780813808_32.jpeg', NULL),
(33, '3', 'bimo', 'siswa', 'user_master_1780813831_33.jpeg', NULL),
(34, '1001', 'Siswa 1', 'siswa', 'default_user.jpg', NULL),
(35, '1002', 'Siswa 2', 'siswa', 'default_user.jpg', NULL),
(36, '1003', 'Siswa 3', 'siswa', 'default_user.jpg', NULL),
(37, '1004', 'Siswa 4', 'siswa', 'default_user.jpg', NULL),
(38, '1005', 'Siswa 5', 'siswa', 'default_user.jpg', NULL),
(39, '1006', 'Siswa 6', 'siswa', 'default_user.jpg', NULL),
(40, '1007', 'Siswa 7', 'siswa', 'default_user.jpg', NULL),
(41, '1008', 'Siswa 8', 'siswa', 'default_user.jpg', NULL),
(42, '1009', 'Siswa 9', 'siswa', 'default_user.jpg', NULL),
(43, '1010', 'Siswa 10', 'siswa', 'default_user.jpg', NULL),
(44, '1011', 'Siswa 11', 'siswa', 'default_user.jpg', NULL),
(45, '1012', 'Siswa 12', 'siswa', 'default_user.jpg', NULL),
(46, '1013', 'Siswa 13', 'siswa', 'default_user.jpg', NULL),
(47, '1014', 'Siswa 14', 'siswa', 'default_user.jpg', NULL),
(48, '1015', 'Siswa 15', 'siswa', 'default_user.jpg', NULL),
(49, '1016', 'Siswa 16', 'siswa', 'default_user.jpg', NULL),
(50, '1017', 'Siswa 17', 'siswa', 'default_user.jpg', NULL),
(51, '1018', 'Siswa 18', 'siswa', 'default_user.jpg', NULL),
(52, '1019', 'Siswa 19', 'siswa', 'default_user.jpg', NULL),
(53, '1020', 'Siswa 20', 'siswa', 'default_user.jpg', NULL),
(54, '1021', 'Siswa 21', 'siswa', 'default_user.jpg', NULL),
(55, '1022', 'Siswa 22', 'siswa', 'default_user.jpg', NULL),
(56, '1023', 'Siswa 23', 'siswa', 'default_user.jpg', NULL),
(57, '1024', 'Siswa 24', 'siswa', 'default_user.jpg', NULL),
(58, '1025', 'Siswa 25', 'siswa', 'default_user.jpg', NULL),
(59, '1026', 'Siswa 26', 'siswa', 'default_user.jpg', NULL),
(60, '1027', 'Siswa 27', 'siswa', 'default_user.jpg', NULL),
(61, '1028', 'Siswa 28', 'siswa', 'default_user.jpg', NULL),
(62, '1029', 'Siswa 29', 'siswa', 'default_user.jpg', NULL),
(63, '1030', 'Siswa 30', 'siswa', 'default_user.jpg', NULL),
(64, '1031', 'Siswa 31', 'siswa', 'default_user.jpg', NULL),
(65, '1032', 'Siswa 32', 'siswa', 'default_user.jpg', NULL),
(66, '1033', 'Siswa 33', 'siswa', 'default_user.jpg', NULL),
(67, '1034', 'Siswa 34', 'siswa', 'default_user.jpg', NULL),
(68, '1035', 'Siswa 35', 'siswa', 'default_user.jpg', NULL),
(69, '1036', 'Siswa 36', 'siswa', 'default_user.jpg', NULL),
(70, '1037', 'Siswa 37', 'siswa', 'default_user.jpg', NULL),
(71, '1038', 'Siswa 38', 'siswa', 'default_user.jpg', NULL),
(72, '1039', 'Siswa 39', 'siswa', 'default_user.jpg', NULL),
(73, '1040', 'Siswa 40', 'siswa', 'default_user.jpg', NULL),
(74, '1041', 'Siswa 41', 'siswa', 'default_user.jpg', NULL),
(75, '1042', 'Siswa 42', 'siswa', 'default_user.jpg', NULL),
(76, '1043', 'Siswa 43', 'siswa', 'default_user.jpg', NULL),
(77, '1044', 'Siswa 44', 'siswa', 'default_user.jpg', NULL),
(78, '1045', 'Siswa 45', 'siswa', 'default_user.jpg', NULL),
(79, '1046', 'Siswa 46', 'siswa', 'default_user.jpg', NULL),
(80, '1047', 'Siswa 47', 'siswa', 'default_user.jpg', NULL),
(81, '1048', 'Siswa 48', 'siswa', 'default_user.jpg', NULL),
(82, '1049', 'Siswa 49', 'siswa', 'default_user.jpg', NULL),
(83, '1050', 'Siswa 50', 'siswa', 'default_user.jpg', NULL),
(84, '1051', 'Siswa 51', 'siswa', 'default_user.jpg', NULL),
(85, '1052', 'Siswa 52', 'siswa', 'default_user.jpg', NULL),
(86, '1053', 'Siswa 53', 'siswa', 'default_user.jpg', NULL),
(87, '1054', 'Siswa 54', 'siswa', 'default_user.jpg', NULL),
(88, '1055', 'Siswa 55', 'siswa', 'default_user.jpg', NULL),
(89, '1056', 'Siswa 56', 'siswa', 'default_user.jpg', NULL),
(90, '1057', 'Siswa 57', 'siswa', 'default_user.jpg', NULL),
(91, '1058', 'Siswa 58', 'siswa', 'default_user.jpg', NULL),
(92, '1059', 'Siswa 59', 'siswa', 'default_user.jpg', NULL),
(93, '1060', 'Siswa 60', 'siswa', 'default_user.jpg', NULL),
(94, '1061', 'Siswa 61', 'siswa', 'default_user.jpg', NULL),
(95, '1062', 'Siswa 62', 'siswa', 'default_user.jpg', NULL),
(96, '1063', 'Siswa 63', 'siswa', 'default_user.jpg', NULL),
(97, '1064', 'Siswa 64', 'siswa', 'default_user.jpg', NULL),
(98, '1065', 'Siswa 65', 'siswa', 'default_user.jpg', NULL),
(99, '1066', 'Siswa 66', 'siswa', 'default_user.jpg', NULL),
(100, '1067', 'Siswa 67', 'siswa', 'default_user.jpg', NULL),
(101, '1068', 'Siswa 68', 'siswa', 'default_user.jpg', NULL),
(102, '1069', 'Siswa 69', 'siswa', 'default_user.jpg', NULL),
(103, '1070', 'Siswa 70', 'siswa', 'default_user.jpg', NULL),
(104, '1071', 'Siswa 71', 'siswa', 'default_user.jpg', NULL),
(105, '1072', 'Siswa 72', 'siswa', 'default_user.jpg', NULL),
(106, '1073', 'Siswa 73', 'siswa', 'default_user.jpg', NULL),
(107, '1074', 'Siswa 74', 'siswa', 'default_user.jpg', NULL),
(108, '1075', 'Siswa 75', 'siswa', 'default_user.jpg', NULL),
(109, '1076', 'Siswa 76', 'siswa', 'default_user.jpg', NULL),
(110, '1077', 'Siswa 77', 'siswa', 'default_user.jpg', NULL),
(111, '1078', 'Siswa 78', 'siswa', 'default_user.jpg', NULL),
(112, '1079', 'Siswa 79', 'siswa', 'default_user.jpg', NULL),
(113, '1080', 'Siswa 80', 'siswa', 'default_user.jpg', NULL),
(114, '1081', 'Siswa 81', 'siswa', 'default_user.jpg', NULL),
(115, '1082', 'Siswa 82', 'siswa', 'default_user.jpg', NULL),
(116, '1083', 'Siswa 83', 'siswa', 'default_user.jpg', NULL),
(117, '1084', 'Siswa 84', 'siswa', 'default_user.jpg', NULL),
(118, '1085', 'Siswa 85', 'siswa', 'default_user.jpg', NULL),
(119, '1086', 'Siswa 86', 'siswa', 'default_user.jpg', NULL),
(120, '1087', 'Siswa 87', 'siswa', 'default_user.jpg', NULL),
(121, '1088', 'Siswa 88', 'siswa', 'default_user.jpg', NULL),
(122, '1089', 'Siswa 89', 'siswa', 'default_user.jpg', NULL),
(123, '1090', 'Siswa 90', 'siswa', 'default_user.jpg', NULL),
(124, '2', 'fikri', 'siswa', 'user_master_1780813824_124.jpeg', NULL),
(129, '123', 'siswa', 'siswa', 'user_master_1780833368_129.jpeg', NULL),
(130, '11', 'ryan timber', 'admin_timber', 'user_11_1780827959.jpeg', NULL),
(131, '12', 'ryan dkv1', 'admin_dkv1', 'user_12_1780827984.jpeg', NULL),
(132, '13', 'ryan dkv2', 'admin_dkv2', 'user_13_1780828003.jpeg', NULL),
(133, '14', 'ryan animasi', 'admin_animasi', 'user_14_1780828042.jpeg', NULL),
(134, '21', 'fikri timber', 'admin_timber', 'user_master_1780828492_134.jpeg', NULL),
(135, '22', 'fikri dkv1', 'admin_dkv1', 'user_22_1780828539.jpeg', NULL),
(136, '23', 'fikri dkv2', 'admin_dkv2', 'user_23_1780828562.jpeg', NULL),
(137, '24', 'fikri animasi', 'admin_animasi', 'user_24_1780828623.jpeg', NULL),
(138, '31', 'bimo timber', 'admin_timber', 'user_31_1780828651.jpeg', NULL),
(139, '32', 'bimo dkv1', 'admin_dkv1', 'user_32_1780828664.jpeg', NULL),
(140, '33', 'bimo dkv2', 'admin_dkv2', 'user_33_1780828693.jpeg', NULL),
(141, '34', 'bimo animasi', 'admin_animasi', 'user_34_1780828714.jpeg', NULL),
(142, '026', 'fikri admin', 'admin', 'user_master_1780842345_142.jpg', NULL),
(143, '100', 'nina', 'siswa', 'user_100_1780885565.jpg', NULL),
(144, '234', 'najip', 'admin_timber', 'user_master_1780927284_144.png', NULL),
(145, '021', 'Nadzif aja', 'siswa', 'user_021_1780981023.jpg', NULL),
(146, '022', 'abiyu', 'siswa', 'user_022_1780981747.jpg', NULL),
(147, '01', 'tes 1', 'siswa', 'user_01_1780987536.jpg', NULL),
(148, '02', 'tes 2', 'siswa', 'user_02_1780987663.jpg', NULL),
(149, '03', 'tes 3', 'siswa', 'user_03_1780988133.jpg', NULL),
(150, '04', 'tes 4', 'siswa', 'user_04_1780988163.jpg', NULL),
(151, '05', 'tes 5', 'siswa', 'user_05_1780988294.jpg', NULL),
(152, '025', 'raydita', 'siswa', 'user_025_1780989800.jpg', NULL),
(153, '222', 'duo', 'siswa', 'user_222_1780989900.jpg', NULL),
(154, '19', 'balqis', 'siswa', 'user_19_1780990019.jpg', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `barang`
--
ALTER TABLE `barang`
  ADD PRIMARY KEY (`id_barang`),
  ADD UNIQUE KEY `kode_barang` (`kode_barang`),
  ADD KEY `fk_barang_kategori` (`id_kategori`);

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indexes for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD PRIMARY KEY (`id_pinjam`),
  ADD KEY `fk_pinjam_user` (`id_user`),
  ADD KEY `fk_pinjam_barang` (`id_barang`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`nis`),
  ADD KEY `fk_users_kategori` (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `barang`
--
ALTER TABLE `barang`
  MODIFY `id_barang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=107;

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `peminjaman`
--
ALTER TABLE `peminjaman`
  MODIFY `id_pinjam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=156;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `barang`
--
ALTER TABLE `barang`
  ADD CONSTRAINT `fk_barang_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `peminjaman`
--
ALTER TABLE `peminjaman`
  ADD CONSTRAINT `fk_pinjam_barang` FOREIGN KEY (`id_barang`) REFERENCES `barang` (`id_barang`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_pinjam_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_users_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
