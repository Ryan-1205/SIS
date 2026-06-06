<?php
session_start();
$id = $_GET['id'];

if (($key = array_search($id, $_SESSION['keranjang'])) !== false) {
    unset($_SESSION['keranjang'][$key]);
}

header("Location: list_pinjam.php");
?>