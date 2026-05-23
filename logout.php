<?php
// 1. Jalankan session agar sistem tahu sesi siapa yang akan dihapus
session_start();

// 2. Bersihkan semua data di dalam array $_SESSION
$_SESSION = [];

// 3. Hancurkan session yang tersimpan di memori server lokal
session_unset();
session_destroy();

// 4. Tendang balik pengguna ke halaman index.php
header("Location: index.php");
exit();
?>