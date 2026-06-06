<?php
// 1. Jalankan session agar sistem tahu sesi siapa yang akan dihapus
session_start();

// 2. Bersihkan semua data di dalam array $_SESSION secara menyeluruh
$_SESSION = array();

// Jika ingin membersihkan cookie session di browser juga (Opsional tapi sangat direkomendasikan untuk keamanan)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hancurkan session yang tersimpan di memori server lokal
session_unset();
session_destroy();

// 4. Tendang balik pengguna ke halaman utama root dengan membawa parameter status logout
header("Location: index.php?status=logout");
exit();
?>